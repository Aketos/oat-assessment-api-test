<?php

namespace App\Service\Provider\Abstraction;

use App\Exception\ProviderException;
use App\Interfaces\ProviderInterface;

abstract class DataProvider implements ProviderInterface
{
    /** @var array */
    protected $dataPaths;

    /** @var array */
    protected $options;

    public function __construct(array $dataPaths)
    {
        $this->dataPaths = $dataPaths;
        $this->options = $this->getDefaultOptions();
    }

    abstract public function findAll(string $className, array $options = []): ?array;
    abstract protected function getDefaultOptions(): array;

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
