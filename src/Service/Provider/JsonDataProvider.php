<?php

namespace App\Service\Provider;

use App\Entity\Abstraction\StructuredEntity;
use App\Exception\EntityException;
use App\Service\Provider\Abstraction\DataProvider;

class JsonDataProvider extends DataProvider
{
    /**
     * @param string|null $className
     *
     * @return \Generator|null
     * @throws EntityException
     */
    protected function fetchFile(string $className = null): ?\Generator
    {
        foreach (json_decode(file_get_contents($this->dataPaths[$className]), true) as $data) {
            try {
                yield ($className !== null) ? new $className(StructuredEntity::DATA_FORMAT_JSON, $data) : $data;
            } catch (EntityException $e) {
                throw new EntityException(
                    'Cannot generate new entity (' . $className . ') from row: ' . $e->getMessage()
                );
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return [];
    }
}
