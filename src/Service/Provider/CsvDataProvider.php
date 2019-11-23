<?php

namespace App\Service\Provider;

use App\Exception\EntityException;
use App\Service\Provider\Abstraction\DataProvider;

class CsvDataProvider extends DataProvider
{
    /**
     * @param string $className
     * @param array  $options
     *
     * @return array|null
     * @throws EntityException
     */
    public function findAll(string $className, array $options = []): ?array
    {
        $entities = [];

        foreach ($this->fetchFile($className, $options) as $entity) {
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * @param string|null $className
     * @param array       $options
     *
     * @return \Generator|null
     * @throws EntityException
     */
    public function fetchFile(string $className = null, array $options = []): ?\Generator
    {
        $handle = fopen($this->dataPath, 'rb');

        if ($options === []) {
            $options = $this->getDefaultOptions();
        }

        $header = $options['header'];

        if ($handle !== false) {
            do {
                $data = fgetcsv($handle, 5000, $options['separator']);

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

    protected function getDefaultOptions(): array
    {
        return [
            'separator' => ',',
            'header' => true
        ];
    }
}
