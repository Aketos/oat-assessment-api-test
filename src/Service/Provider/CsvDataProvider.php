<?php

namespace App\Service\Provider;

use App\Exception\EntityException;
use App\Exception\ProviderException;
use App\Service\Provider\Abstraction\DataProvider;

class CsvDataProvider extends DataProvider
{
    /**
     * @param string $className
     * @param array  $options
     *
     * @return array|null
     * @throws EntityException
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
     * @param string $className
     *
     * @return \Generator|null
     * @throws EntityException
     */
    protected function fetchFile(string $className): ?\Generator
    {
        $handle = fopen($this->dataPaths[$className], 'rb');

        $header = $this->options['header'];

        if ($handle !== false) {
            do {
                $data = fgetcsv($handle, 5000, $this->options['separator']);

                try {
                    if ($data !== false) {
                        if ($header === false) {
                            yield ($className !== null) ? new $className($data) : $data;
                        } else {
                            $header = false;
                        }
                    }
                } catch (EntityException $e) {
                    throw new EntityException(
                        'Cannot generate new entity (' . $className . ') from row: ' . $e->getMessage()
                    );
                }
            } while ($data !== false);

            fclose($handle);
        }
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return [
            'separator' => ',',
            'header' => true
        ];
    }
}
