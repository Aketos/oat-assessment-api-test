<?php

namespace App\Entity\Abstraction;

use App\Exception\EntityException;

abstract class StructuredEntity
{
    /**
     * Defines the column structure of the array passed to the entity constructor:
     * ['column_name_1', 'column_name_2']
     */
    protected const STRUCTURE = [];

    /**
     * Defines the functions to apply to attribute value we want to load:
     * ['column_name_2' => 'callback_name']
     * callback functions have to be defined on entities
     */
    protected const CALLBACKS = [];

    /** @var array */
    private $inputData;

    /**
     * CsvEntity constructor.
     *
     * @param array $row
     *
     * @throws EntityException
     */
    public function __construct(array $row = [])
    {
        if ($row !== []) {
            $this->inputData = $row;

            $this->checkIfEntityIsValid();

            $associativeRow = array_combine($this::STRUCTURE, $row);

            foreach ($associativeRow as $attribute => $value) {
                $this->$attribute = isset($this::CALLBACKS[$attribute])
                    ? $this->{$this::CALLBACKS[$attribute]}($value)
                    : $value;
            }
        }
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function checkIfEntityIsValid(): bool
    {
        return $this->isStructureValid() && $this->areCallbacksValid();
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function isStructureValid(): bool
    {
        if (count($this::STRUCTURE) !== count($this->inputData)) {
            throw new EntityException(
                'Incorrect structure of extracted row: ' . count($this->inputData) . '/' . count($this::STRUCTURE) . ' columns'
            );
        }

        return true;
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function areCallbacksValid(): bool
    {
        foreach ($this::CALLBACKS as $attribute => $method) {
            if (!method_exists($this, $method)) {
                throw new EntityException(
                    'Callback method ' . $method . ' does not exist for ' . $attribute . ' attribute'
                );
            }
        }

        return true;
    }
}
