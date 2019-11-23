<?php

namespace App\Service;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Stichoza\GoogleTranslate\Tokens\TokenProviderInterface;

class TranslatorService extends GoogleTranslate
{
    public function __construct(string $target = 'en', string $source = null, array $options = null, TokenProviderInterface $tokenProvider = null)
    {
        parent::__construct($target, $source, $options, $tokenProvider);
        $this->urlParams = array_merge($this->urlParams, $options);
    }
}