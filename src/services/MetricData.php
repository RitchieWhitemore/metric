<?php


namespace whitemore\metric\services;


class MetricData
{
    /**
     * @var array
     */
    private $values;

    /**
     * @var array
     */
    private $categories;

    public function __construct($categories = [], $values = [])
    {
        $this->categories = $categories;
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValuesMetric(): array
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getCategoriesMetric(): array
    {
        return $this->categories;
    }
}