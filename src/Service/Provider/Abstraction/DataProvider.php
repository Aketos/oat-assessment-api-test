<?php

namespace App\Service\Provider\Abstraction;

use App\Exception\ProviderException;
use App\Interfaces\ProviderInterface;

abstract class DataProvider implements ProviderInterface
{
    protected $dataPaths;

    public function __construct(array $dataPaths)
    {
        $this->dataPaths = $dataPaths;
    }

    abstract public function findAll(string $className, array $options = []): ?array;

    /**
     * @param string $className
     *
     * @return bool
     * @throws ProviderException
     */
    protected function checkIfClassDataIsFindable(string $className): bool
    {
        if (!isset($this->dataPaths[$className])) {
            throw new ProviderException('Class ' . $className . ' does not have associated data set');
        }

        if (!is_readable($this->dataPaths[$className])) {
            throw new ProviderException('Data set associated to class ' . $className . ' is not readable');
        }

        return true;
    }
}
