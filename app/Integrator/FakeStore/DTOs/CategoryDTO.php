<?php

namespace App\Integrator\FakeStore\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly string $name
    ) {}

    public static function fromArray(array $data): self
    {
        return new self($data['name'] ?? $data);
    }

    public function toArray(): array
    {
        return ['name' => $this->name];
    }
}