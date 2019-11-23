<?php

namespace App\Validator;

use App\Validator\Abstraction\RequestValidator;

class ListQuestionsRequestValidator extends RequestValidator
{
    public const MANDATORY_FIELDS = ['lang'];

    public const FIELDS_PATTERN_RULES = [
        'lang' => '/^([a-z]{2})(-[A-Z]{2})?$/'
    ];
}