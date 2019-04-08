<?php

namespace Chocofamily\QueryBuilderFilters\Contracts;

interface HasFilters
{
    /**
     * @return string
     */
    public function getFilterClass(): string;
}
