<?php

namespace App\Validator;

use App\Validator\Abstraction\RequestValidator;

class CreateQuestionRequestValidator extends RequestValidator
{
    public const MANDATORY_FIELDS = [
        'text',
        'choice1',
        'choice2',
        'choice3'
    ];

    public const OPTIONALS_FIELDS = [
        'createdAt'
    ];

    public const FIELDS_PATTERN_RULES = [
        'createdAt' => '/^[\d]{4}-[\d]{2}-[\d]{2}( [\d]{2}:[\d]{2}:[\d]{2})?$/'
    ];
}
