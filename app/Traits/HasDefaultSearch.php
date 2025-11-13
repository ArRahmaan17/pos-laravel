<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HasDefaultSearch
{
    public function scopeSearch($query, $term)
    {
        $term = trim(preg_replace('/\s+/', ' ', (string) $term));
        if ($term === '') {
            return $query;
        }

        $operator  = $this->getSearchOperator();
        $columns   = $this->getSearchableColumns();
        $relations = $this->getSearchableRelations();

        return $query->where(function ($q) use ($columns, $relations, $term, $operator) {
            foreach ($columns as $column) {
                $q->orWhere($this->getTable() . '.' . $column, $operator, "%{$term}%");
            }
            // foreach ($relations as $relationPath => $relColumns) {
            //     $q->orWhereHas($relationPath, function ($relQ) use ($relColumns, $term, $operator) {
            //         foreach ($relColumns as $column) {
            //             $relQ->orWhere($column, $operator, "%{$term}%");
            //         }
            //     });
            // }
            foreach ($relations as $relationPath => $relColumns) {
                $q->orWhere(function ($outer) use ($relationPath, $relColumns, $term, $operator) {
                    $outer->whereHas($relationPath, function ($relQ) use ($relColumns, $term, $operator) {
                        $relQ->where(function ($inner) use ($relColumns, $term, $operator) {
                            foreach ($relColumns as $col) {
                                $inner->orWhere($col, $operator, "%{$term}%");
                            }
                        });
                    });
                });
            }
        });
    }

    protected function getSearchableColumns()
    {
        if (property_exists($this, 'defaultSearchColumns')) {
            $columns = (array) $this->defaultSearchColumns;
        } elseif (property_exists($this, 'defaultSearchColumn')) {
            $columns = [$this->defaultSearchColumn];
        } else {
            $columns = Schema::getColumnListing($this->getTable());
        }

        $pattern = property_exists($this, 'searchExcludePattern')
            ? $this->searchExcludePattern
            : '/(id|_at)$/i';

        return collect($columns)
            ->reject(fn($col) => preg_match($pattern, $col))
            ->values()
            ->all();
    }

    protected function getSearchableRelations()
    {
        if (property_exists($this, 'defaultSearchRelations')) {
            return (array) $this->defaultSearchRelations;
        }
        return [];
    }

    protected function getSearchOperator()
    {
        return DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
    }
}
