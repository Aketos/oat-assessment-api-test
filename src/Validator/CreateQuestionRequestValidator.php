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
}
