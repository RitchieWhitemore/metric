<?php


namespace whitemore\metric\services;


interface MetricInterface
{

    public function getVisits(): MetricData;

    public function getHits(): MetricData;
}