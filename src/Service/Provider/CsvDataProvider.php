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
        $backup = file_get_contents($this->dataPaths[$className]);

        try {
            $csvPointer = fopen($this->dataPaths[$className], 'wb');

            if (defined($className . '::HEADER') && $this->options['header']) {
                fputcsv($csvPointer, $className::HEADER[StructuredEntity::DATA_FORMAT_CSV]);
            }

            /** @var EntityToArrayInterface $object */
            foreach ($data as $object) {
                fputcsv($csvPointer, $object->toArray());
            }

            fclose($csvPointer);
        } catch (\Exception $e) {
            file_put_contents(
                $this->dataPaths[$className],
                $backup
            );

            throw $e;
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
