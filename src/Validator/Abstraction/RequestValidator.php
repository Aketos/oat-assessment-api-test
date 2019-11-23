<?php

namespace App\Validator\Abstraction;

use Symfony\Component\HttpFoundation\Request;

abstract class RequestValidator
{
    /**
     * Defines all mandatory fields for the incoming Request
     */
    public const MANDATORY_FIELDS = [];

    /**
     * Defines all Patterns that should be applied to fields for the incoming Request
     */
    public const FIELDS_PATTERN_RULES = [];

    /**
     * @param Request $request
     *
     * @return array
     */
    public function checkIfRequestIsValid(Request $request): array
    {
        $mandatoryFieldsCheck = $this->checkMandatoryFields($request);

        if ($mandatoryFieldsCheck !== []) {
            return $mandatoryFieldsCheck;
        }

        $patternRulesCheck = $this->checkPatternRules($request);

        if ($patternRulesCheck !== []) {
            return $patternRulesCheck;
        }

        return [];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function checkMandatoryFields(Request $request): array
    {
        foreach ($this::MANDATORY_FIELDS as $mandatoryField) {
            if ($request->get($mandatoryField) === null) {
                return [
                    'status'  => 400,
                    'message' => 'Parameter ' . $mandatoryField . ' is missing from request'
                ];
            }
        }

        return [];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function checkPatternRules(Request $request): array
    {
        foreach ($this::FIELDS_PATTERN_RULES as $field => $rule) {
            if (!preg_match($rule, $request->get($field))) {
                return [
                    'status' => 400,
                    'message' => 'Incorrect ' . $field . ' parameter value'
                ];
            }
        }

        return [];
    }
}