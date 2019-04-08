<?php

namespace Chocofamily\QueryBuilderFilters;

use Phalcon\Mvc\Model\Query\Builder;
use Chocofamily\QueryBuilderFilters\Contracts\HasFilters;

class FilterHandler
{
    /**
     * @param Builder $builder
     * @param array $filters
     * @return Builder
     */
    public function handle(Builder $builder, array $filters)
    {
        $models = collect((array) $builder->getJoins())
            ->map(function ($value) {
                return reset($value);
            })
            ->merge((array) $builder->getFrom())
            ->toArray();

        foreach ($models as $model) {
            $modelClass = new $model;

            if ($modelClass instanceof HasFilters) {
                $filterClass = $modelClass->getFilterClass();

                if (!class_exists($filterClass)) {
                    continue;
                }

                /** @var BaseFilter $filter */
                $filter = new $filterClass($filters);
                $filter->apply($builder);
            }
        }

        return $builder;
    }
}
