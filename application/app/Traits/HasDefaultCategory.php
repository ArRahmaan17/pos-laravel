<?php

namespace App\Traits;

trait HasDefaultCategory
{
    public function scopeDefaultCategory($query, $categoryId = 'all')
    {
        $column = $this->getDefaultCategoryColumn();
        $defaultId = $categoryId ?: $this->getDefaultCategoryId();

        if ($defaultId === 'all') {
            return $query;
        }

        if ($defaultId) {
            return $query->where($column, $defaultId);
        }

        return $query;
    }

    protected function getDefaultCategoryColumn()
    {
        if (property_exists($this, 'defaultCategoryColumn')) {
            return $this->defaultCategoryColumn;
        }

        return 'category_id'; // fallback
    }

    protected function getDefaultCategoryId()
    {
        if (property_exists($this, 'defaultCategoryId')) {
            return $this->defaultCategoryId;
        }

        return null; // no default if not set
    }
}
