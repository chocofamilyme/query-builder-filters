<?php

namespace Chocofamily\QueryBuilderFilters;

use Phalcon\Mvc\Model\Query\Builder;

abstract class BaseFilter
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * BaseFilter constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters as $method => $value) {
            $methodName = camel_case($method);

            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        }

        return $this->builder;
    }
}
