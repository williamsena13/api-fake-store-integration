<?php

namespace App\Integrator\FakeStore\DTOs;

class SyncResultDTO
{
    public function __construct(
        public int $imported = 0,
        public int $updated = 0,
        public int $skipped = 0,
        public array $errors = []
    ) {}

    public function addImported(): void
    {
        $this->imported++;
    }

    public function addUpdated(): void
    {
        $this->updated++;
    }

    public function addSkipped(): void
    {
        $this->skipped++;
    }

    public function addError(string $message, array $context = []): void
    {
        $this->errors[] = [
            'message' => $message,
            'context' => $context
        ];
    }

    public function toArray(): array
    {
        return [
            'imported' => $this->imported,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'errors' => $this->errors
        ];
    }
}