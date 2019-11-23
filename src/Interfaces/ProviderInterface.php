<?php

namespace App\Interfaces;

interface ProviderInterface
{
    public function findAll(string $className, array $options = []): ?array;
}