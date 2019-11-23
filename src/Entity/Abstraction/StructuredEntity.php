<?php

namespace App\Entity\Abstraction;

use App\Exception\EntityException;

abstract class StructuredEntity
{
    /**
     * Defines the column structure of the array passed to the entity constructor:
     * ['CSV' => ['column_name_1', 'column_name_2']]
     */
    protected const STRUCTURE = [];

    /**
     * Defines the functions to apply to attribute value we want to load:
     * ['column_name_2' => 'callback_name']
     * callback functions have to be defined on entities
     */
    protected const CALLBACKS = [];

    public const DATA_FORMAT_CSV = 'csv';
    public const DATA_FORMAT_JSON = 'json';

    /** @var array */
    private $inputData;

    /** @var string */
    private $inputDataType;

    /**
     * @param string $inputDataType
     * @param array  $row
     *
     * @throws EntityException
     */
    public function __construct(string $inputDataType = null, array $row = [])
    {
        $this->inputDataType = $inputDataType;

        if ($row !== []) {
            $this->inputData = $row;

            $this->checkIfEntityIsValid();

            $associativeRow = array_combine($this::STRUCTURE[$inputDataType], $row);

            foreach ($associativeRow as $attribute => $value) {
                if (!isset($this::CALLBACKS[$attribute])) {
                    $this->$attribute = $value;
                } else {
                    $this->{$this::CALLBACKS[$attribute]}($value);
                }
            }
        }
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function checkIfEntityIsValid(): bool
    {
        return $this->isStructureValid()
            && $this->isDataTypeDefined()
            && $this->areCallbacksValid();
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function isStructureValid(): bool
    {
        if (count($this::STRUCTURE[$this->inputDataType]) !== count($this->inputData)) {
            throw new EntityException(
                'Incorrect structure of extracted row: '
                . count($this->inputData) . '/' . count($this::STRUCTURE[$this->inputDataType])
                . ' columns'
            );
        }

        return true;
    }

    /**
     * @return bool
     * @throws EntityException
     */
    private function isDataTypeDefined(): bool
    {
        if (!isset($this::STRUCTURE[$this->inputDataType])) {
            throw new EntityException(
                'Incorrect data type asked to format (' . $this->inputDataType . ')'
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
