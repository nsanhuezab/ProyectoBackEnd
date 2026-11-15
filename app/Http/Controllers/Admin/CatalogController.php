<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Practice;
use App\Models\PracticeEvaluation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Mantenedores (CRUD) genéricos definidos en config/catalogs.php:
 * bitácoras, evaluaciones, reseñas de empresa, mensajes de postulación y roles.
 */
class CatalogController extends Controller
{
    public function index(string $catalog): View
    {
        $cfg = $this->config($catalog);
        $model = $cfg['model'];

        $query = $model::query();
        if (! empty($cfg['with'])) {
            $query->with($cfg['with']);
        }
        if (! empty($cfg['count'])) {
            $query->withCount($cfg['count']);
        }
        [$column, $direction] = $cfg['order'];
        $query->orderBy($column, $direction);

        $rows = $query->get()->map(fn (Model $record) => $this->row($catalog, $cfg, $record))->values();
        $selects = $this->selectOptions($cfg);

        return view('catalogs.index', [
            'catalog' => $catalog,
            'cfg' => $cfg,
            'rows' => $rows,
            'selects' => $selects,
        ]);
    }

    public function store(Request $request, string $catalog): RedirectResponse
    {
        $cfg = $this->config($catalog);
        $data = $this->validated($request, $cfg, null);

        $model = $cfg['model'];
        $record = $model::create(array_merge($cfg['defaults'] ?? [], $data));
        $this->syncFinalGrade($record);

        return redirect()
            ->route("catalogs.$catalog.index")
            ->with('success', 'Registro creado correctamente en «'.$cfg['title'].'».');
    }

    public function update(Request $request, string $catalog, string $id): RedirectResponse
    {
        $cfg = $this->config($catalog);
        $model = $cfg['model'];
        $record = $model::findOrFail($id);

        if ($this->isLocked($cfg, $record)) {
            return redirect()->route("catalogs.$catalog.index")
                ->with('error', 'Este registro está protegido y no se puede modificar.');
        }

        $record->update($this->validated($request, $cfg, $record));
        $this->syncFinalGrade($record);

        return redirect()
            ->route("catalogs.$catalog.index")
            ->with('success', 'Registro actualizado correctamente en «'.$cfg['title'].'».');
    }

    public function destroy(string $catalog, string $id): RedirectResponse
    {
        $cfg = $this->config($catalog);
        $model = $cfg['model'];
        $record = $model::findOrFail($id);

        if ($this->isLocked($cfg, $record)) {
            return redirect()->route("catalogs.$catalog.index")
                ->with('error', 'Este registro está protegido y no se puede eliminar.');
        }

        foreach ($cfg['restrict_delete'] ?? [] as $relation => $label) {
            $total = $record->{$relation}()->count();
            if ($total > 0) {
                return redirect()->route("catalogs.$catalog.index")
                    ->with('error', "No se puede eliminar: tiene {$total} {$label} asociado(s).");
            }
        }

        $record->delete();
        $this->syncFinalGrade($record);

        return redirect()
            ->route("catalogs.$catalog.index")
            ->with('success', 'Registro eliminado correctamente de «'.$cfg['title'].'».');
    }

    // ------------------------------------------------------------------ helpers

    /**
     * Al cambiar una evaluación desde el mantenedor, recalcula la nota final
     * de su práctica (queda vacía si falta una de las dos rúbricas).
     */
    private function syncFinalGrade(Model $record): void
    {
        if (! $record instanceof PracticeEvaluation) {
            return;
        }

        $practice = Practice::find($record->practice_id);
        if ($practice) {
            $practice->final_grade = $practice->calculateFinalGrade();
            $practice->save();
        }
    }

    private function config(string $catalog): array
    {
        $cfg = config("catalogs.$catalog");
        abort_if($cfg === null, 404);

        return $cfg;
    }

    private function isLocked(array $cfg, Model $record): bool
    {
        return isset($cfg['locked'])
            && in_array($record->{$cfg['locked']['column']}, $cfg['locked']['values'], true);
    }

    /**
     * Valida según los campos del catálogo. $record es null al crear.
     */
    private function validated(Request $request, array $cfg, ?Model $record): array
    {
        $rules = [];
        $names = [];

        foreach ($cfg['fields'] as $field) {
            $rule = $field['rules'];
            if (isset($field['unique'])) {
                $rule[] = Rule::unique($field['unique'][0], $field['unique'][1])->ignore($record?->getKey());
            }
            $rules[$field['name']] = $rule;
            $names[$field['name']] = mb_strtolower($field['label']);
        }

        $validated = $request->validate($rules, [], $names);

        if (! empty($cfg['unique_together'])) {
            $model = $cfg['model'];
            $query = $model::query();
            foreach ($cfg['unique_together'] as $column) {
                $query->where($column, $validated[$column] ?? null);
            }
            if ($record) {
                $query->whereKeyNot($record->getKey());
            }
            if ($query->exists()) {
                throw ValidationException::withMessages([
                    $cfg['unique_together'][0] => 'Ya existe un registro con esa combinación de '.implode(' y ', $cfg['unique_together']).'.',
                ]);
            }
        }

        foreach ($cfg['fields'] as $field) {
            if ($field['type'] === 'json' && isset($validated[$field['name']])) {
                $decoded = json_decode($validated[$field['name']], true);
                if (! is_array($decoded)) {
                    throw ValidationException::withMessages([$field['name'] => 'Debe ser un objeto JSON válido.']);
                }
                $validated[$field['name']] = $decoded;
            }
        }

        return $validated;
    }

    private function row(string $catalog, array $cfg, Model $record): array
    {
        $values = [];
        foreach ($cfg['fields'] as $field) {
            $value = $record->{$field['name']};
            if ($field['type'] === 'json') {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } elseif ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d');
            }
            $values[$field['name']] = $value;
        }

        return [
            'id' => $record->getKey(),
            'cells' => array_map(fn (array $column) => $this->cell($record, $column), $cfg['columns']),
            'values' => $values,
            'locked' => $this->isLocked($cfg, $record),
            'update_url' => route("catalogs.$catalog.update", $record->getKey()),
            'delete_url' => route("catalogs.$catalog.destroy", $record->getKey()),
        ];
    }

    private function cell(Model $record, array $column): string
    {
        $value = data_get($record, $column['path']);

        return match ($column['format'] ?? null) {
            'label' => $this->label($value),
            'date' => $value instanceof \DateTimeInterface ? $value->format('d-m-Y') : '—',
            'limit' => Str::limit((string) $value, 70),
            default => ($value === null || $value === '') ? '—' : (string) $value,
        };
    }

    private function personName(?User $user): string
    {
        if (! $user) {
            return '—';
        }

        return trim($user->name.' '.$user->last_name) ?: (string) $user->email;
    }

    private function label(mixed $model): string
    {
        return match (true) {
            $model === null => '—',
            $model instanceof Practice => '#'.$model->id.' · '.$this->personName($model->application?->student?->user).' — '.($model->application?->offer?->titulo ?? 'sin oferta'),
            $model instanceof Application => '#'.$model->id.' · '.$this->personName($model->student?->user).' → '.($model->offer?->titulo ?? 'sin oferta'),
            $model instanceof User => $this->personName($model),
            $model instanceof Company => (string) $model->razon_social,
            default => (string) ($model->name ?? '#'.$model->getKey()),
        };
    }

    /**
     * Opciones de cada <select>: lista fija ('options') o tomada de la BD ('source').
     */
    private function selectOptions(array $cfg): array
    {
        $selects = [];
        foreach ($cfg['fields'] as $field) {
            if ($field['type'] !== 'select') {
                continue;
            }
            $selects[$field['name']] = $field['options'] ?? $this->source($field['source']);
        }

        return $selects;
    }

    private function source(string $source): array
    {
        return match ($source) {
            'practices' => Practice::with(['application.student.user', 'application.offer'])
                ->latest('id')->limit(500)->get()
                ->mapWithKeys(fn (Practice $p) => [$p->id => $this->label($p)])->all(),
            'applications' => Application::with(['student.user', 'offer'])
                ->latest('id')->limit(500)->get()
                ->mapWithKeys(fn (Application $a) => [$a->id => $this->label($a)])->all(),
            'companies' => Company::orderBy('razon_social')->pluck('razon_social', 'id')->all(),
            'users' => User::orderBy('name')->get()
                ->mapWithKeys(fn (User $u) => [$u->id => $this->personName($u).' ('.$u->email.')'])->all(),
            default => [],
        };
    }
}
