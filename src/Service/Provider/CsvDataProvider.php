<?php

namespace App\Service\Provider;

use App\Entity\Abstraction\StructuredEntity;
use App\Exception\EntityException;
use App\Interfaces\EntityToArrayInterface;
use App\Service\Provider\Abstraction\DataProvider;

class CsvDataProvider extends DataProvider
{
    /**
     * @param string|null $className
     *
     * @return \Generator|null
     * @throws EntityException
     */
    protected function fetchFile(string $className = null): ?\Generator
    {
        $handle = fopen($this->dataPaths[$className], 'rb');

        $header = $this->options['header'];

        if ($handle !== false) {
            do {
                $data = fgetcsv($handle, 5000, $this->options['separator']);

                try {
                    if ($data !== false) {
                        if ($header === false) {
                            yield ($className !== null) ? new $className(StructuredEntity::DATA_FORMAT_CSV, $data) : $data;
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

    public function insertAll(string $className, $data): void
    {
        $csvPointer = fopen($this->dataPaths[$className], 'wb');

        /** @var EntityToArrayInterface $object */
        foreach ($data as $object) {
            fputcsv($csvPointer, $object->toArray());
        }

        fclose($csvPointer);
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
