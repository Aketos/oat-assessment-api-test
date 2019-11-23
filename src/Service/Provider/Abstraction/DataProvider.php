<?php

namespace App\Service\Provider\Abstraction;

use App\Exception\ProviderException;
use App\Interfaces\ProviderInterface;
use App\Traits\SerializerTrait;

abstract class DataProvider implements ProviderInterface
{
    use SerializerTrait;

    /** @var array */
    protected $dataPaths;

    /** @var array */
    protected $options;

    public function __construct(array $dataPaths)
    {
        $this->dataPaths = $dataPaths;
        $this->options = $this->getDefaultOptions();
    }

    /**
     * @param string $className
     * @param array  $options
     *
     * @return array|null
     * @throws ProviderException
     */
    public function findAll(string $className, array $options = []): ?array
    {
        $this->checkIfClassDataIsFindable($className);

        $entities = [];
        $this->options = array_merge($this->getDefaultOptions(), $options);

        foreach ($this->fetchFile($className) as $entity) {
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * @param string|null $className
     *
     * @return \Generator|null
     */
    abstract protected function fetchFile(string $className = null): ?\Generator;

    /**
     * @return array
     */
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
