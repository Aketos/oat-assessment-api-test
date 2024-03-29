<?php

namespace App\Interfaces;

interface ProviderInterface
{
    public function findAll(string $className, array $options = []): ?array;
    public function insertAll(string $className, $data): void;
}
