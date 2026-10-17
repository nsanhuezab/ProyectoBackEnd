<?php

namespace App\Http\Controllers\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasDataTable
{
    protected function dataTableResponse(
        Request $request,
        Builder $query,
        array $searchable,
        array $sortable,
        Closure $transform,
        string $defaultSort = 'id',
        string $defaultDir = 'asc',
        ?Closure $extraSearch = null,
    ): JsonResponse {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);

        $total = (clone $query)->count();

        $term = trim((string) $request->input('search', ''));
        if ($term !== '') {
            $like = '%'.$term.'%';
            $query->where(function ($q) use ($searchable, $like, $extraSearch) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', $like);
                }
                if ($extraSearch) {
                    $extraSearch($q, $like);
                }
            });
        }

        $sort = (string) $request->input('sort', '');
        if (array_key_exists($sort, $sortable)) {
            $dir = $request->input('dir') === 'desc' ? 'desc' : 'asc';
        } else {
            $sort = $defaultSort;
            $dir = $defaultDir;
        }

        $rule = $sortable[$sort] ?? $defaultSort;
        $rule instanceof Closure
            ? $rule($query, $dir)
            : $query->orderBy($rule, $dir);

        $query->orderBy($query->getModel()->getQualifiedKeyName(), 'asc');

        $page = $query->paginate($perPage);

        return response()->json([
            'data' => collect($page->items())->map($transform)->values(),
            'meta' => [
                'total' => $total,
                'filtered' => $page->total(),
                'page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'last_page' => $page->lastPage(),
                'from' => $page->firstItem(),
                'to' => $page->lastItem(),
            ],
        ]);
    }
}
