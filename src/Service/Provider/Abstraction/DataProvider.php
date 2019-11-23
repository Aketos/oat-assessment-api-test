<?php

namespace App\Service\Provider\Abstraction;

use App\Interfaces\ProviderInterface;

abstract class DataProvider implements ProviderInterface
{
    protected $dataPath;

    public function __construct(string $dataPath)
    {
        $this->dataPath = $dataPath;
    }

    abstract public function findAll(string $className, array $options = []): ?array;
}
