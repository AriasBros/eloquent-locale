<?php

namespace Locale\Database;

use Illuminate\Database\Eloquent\Builder;
use Locale\Models\Localizable;

class LocalizableBuilder extends Builder
{
    /**
     * @var bool
     */
    protected $translationJoined = false;

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string $column
     * @param  string $direction
     * @return Builder|LocalizableBuilder
     */
    public function orderBy($column, $direction = 'asc')
    {
        /** @var Localizable $localizableModel */
        $localizableModel = $this->model;

        if ($localizableModel->isLocalizableAttribute($column)) {
            $this->joinWithTranslation();
        }

        return parent::orderBy($column, $direction);
    }

    /**
     * @since 1.0.0
     */
    public function joinWithTranslation()
    {
        if (!$this->translationJoined) {
            /** @var Localizable $localizableModel */
            $localizableModel = $this->model;

            $localeTable = config("locale.model");
            $modelTable = $localizableModel->getTable();
            $modelKeyName = $localizableModel->getKeyName();
            $joiningTable = $localizableModel->joiningLocaleTable($localeTable, $modelTable);
            $modelForeignKey = $localizableModel->getModelForeignKey();

            $this->join($joiningTable, "{$modelTable}.{$modelKeyName}", "=", "{$joiningTable}.{$modelForeignKey}");
        }
    }
}
