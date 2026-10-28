<?php

namespace App\Http\Controllers\Crud;

use App\Http\Controllers\Controller;
use App\Support\Rut;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador base para los CRUD del sistema.
 *
 * Cada CRUD solo declara: modelo, ruta base, textos, columnas de la tabla y
 * campos del formulario. Las vistas genéricas (resources/views/crud) hacen el resto.
 */
abstract class CrudController extends Controller
{
    /** @return class-string<Model> */
    abstract protected function modelClass(): string;

    /** Prefijo de nombres de ruta, ej: "admin.usuarios". */
    abstract protected function routeBase(): string;

    /** plural, singular, g (o/a), eyebrow, description, nuevo */
    abstract protected function labels(): array;

    /** [['label' => 'Nombre', 'value' => fn ($row) => ..., 'badge' => [..]|true, 'hide' => 'lg'|'xl']] */
    abstract protected function columns(): array;

    /** [['name','label','type','rules','options','full','placeholder','hint','attrs']] */
    abstract protected function fields(?Model $model = null): array;

    /** Relaciones a cargar en el listado. */
    protected function with(): array
    {
        return [];
    }

    /** Columnas (o relaciones "rel.campo") usadas por el buscador. */
    protected function searchable(): array
    {
        return [];
    }

    protected function query(Request $request): Builder
    {
        return $this->modelClass()::query()->with($this->with());
    }

    public function canCreate(): bool
    {
        return true;
    }

    public function canEdit(Model $model): bool
    {
        return true;
    }

    public function canDelete(Model $model): bool
    {
        return $this->canEdit($model);
    }

    /** Acciones extra por fila: [['label','url','method','data','style']] */
    public function rowActions(Model $model): array
    {
        return [];
    }

    /** Valores por defecto del formulario de creación. */
    protected function defaults(Request $request): array
    {
        return [];
    }

    /** Ajusta los datos validados antes de guardar. */
    protected function beforeSave(array $data, ?Model $model, Request $request): array
    {
        return $data;
    }

    protected function afterSave(Model $model, bool $created): void
    {
        //
    }

    // ------------------------------------------------------------------
    // Acciones
    // ------------------------------------------------------------------

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $query = $this->query($request);

        if ($q !== '') {
            $this->applySearch($query, $q);
        }

        $rows = $query->orderByDesc('id')->paginate(10)->withQueryString();

        view()->share('searchUrl', route($this->routeBase().'.index'));

        return view('crud.index', [
            'rows' => $rows,
            'columns' => $this->columns(),
            'labels' => $this->labels(),
            'base' => $this->routeBase(),
            'controller' => $this,
            'q' => $q,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->canCreate(), 403);

        return $this->formView(null, $this->defaults($request));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canCreate(), 403);

        $data = $this->validated($request, null);
        $model = $this->modelClass()::create($this->beforeSave($data, null, $request));
        $this->afterSave($model, true);

        return redirect()->route($this->routeBase().'.index')
            ->with('status', $this->message('cread'));
    }

    public function edit(Request $request, string $record): View
    {
        $model = $this->find($request, $record);
        abort_unless($this->canEdit($model), 403);

        return $this->formView($model, []);
    }

    public function update(Request $request, string $record): RedirectResponse
    {
        $model = $this->find($request, $record);
        abort_unless($this->canEdit($model), 403);

        $data = $this->validated($request, $model);
        $model->update($this->beforeSave($data, $model, $request));
        $this->afterSave($model, false);

        return redirect()->route($this->routeBase().'.index')
            ->with('status', $this->message('actualizad'));
    }

    public function destroy(Request $request, string $record): RedirectResponse
    {
        $model = $this->find($request, $record);
        abort_unless($this->canDelete($model), 403);

        try {
            $model->delete();
        } catch (QueryException) {
            return redirect()->route($this->routeBase().'.index')
                ->with('error', 'No se puede eliminar este registro porque tiene información asociada.');
        }

        return redirect()->route($this->routeBase().'.index')
            ->with('status', $this->message('eliminad'));
    }

    // ------------------------------------------------------------------
    // Internos
    // ------------------------------------------------------------------

    protected function find(Request $request, string $id): Model
    {
        return $this->query($request)->findOrFail($id);
    }

    protected function formView(?Model $model, array $defaults): View
    {
        $labels = $this->labels();
        $base = $this->routeBase();

        return view('crud.form', [
            'model' => $model,
            'fields' => $this->fields($model),
            'defaults' => $defaults,
            'labels' => $labels,
            'base' => $base,
            'title' => ($model ? 'Editar ' : 'Nuev'.$labels['g'].' ').$labels['singular'],
            'action' => $model ? route($base.'.update', $model) : route($base.'.store'),
            'method' => $model ? 'PUT' : 'POST',
        ]);
    }

    protected function validated(Request $request, ?Model $model): array
    {
        $fields = $this->fields($model);

        // Normaliza los RUT (12345678k -> 12.345.678-K) antes de validar.
        foreach ($fields as $field) {
            if (($field['type'] ?? 'text') === 'rut' && $request->filled($field['name'])) {
                $request->merge([$field['name'] => Rut::format($request->input($field['name']))]);
            }
        }

        $rules = [];
        $attributes = [];

        foreach ($fields as $field) {
            $fieldRules = $field['rules'] ?? ['nullable'];
            $rules[$field['name']] = $fieldRules instanceof Closure ? $fieldRules($model) : $fieldRules;
            $attributes[$field['name']] = mb_strtolower($field['label']);
        }

        $data = $request->validate($rules, [], $attributes);

        // Un campo contraseña en blanco al editar significa "no cambiar".
        foreach ($fields as $field) {
            if (($field['type'] ?? 'text') === 'password' && blank($data[$field['name']] ?? null)) {
                unset($data[$field['name']]);
            }
        }

        return $data;
    }

    protected function applySearch(Builder $query, string $term): void
    {
        $like = '%'.addcslashes($term, '%_\\').'%';

        $query->where(function (Builder $where) use ($like) {
            foreach ($this->searchable() as $column) {
                if (str_contains($column, '.')) {
                    $pos = strrpos($column, '.');
                    $relation = substr($column, 0, $pos);
                    $field = substr($column, $pos + 1);

                    $where->orWhereHas($relation, fn (Builder $r) => $r->where($field, 'like', $like));
                } else {
                    $where->orWhere($column, 'like', $like);
                }
            }
        });
    }

    protected function message(string $stem): string
    {
        $labels = $this->labels();

        return ucfirst($labels['singular']).' '.$stem.$labels['g'].' correctamente.';
    }
}
