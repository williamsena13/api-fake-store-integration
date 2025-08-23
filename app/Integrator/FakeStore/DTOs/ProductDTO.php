<?php

namespace App\Integrator\FakeStore\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly float $price,
        public readonly string $image,
        public readonly string $category
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['category']
        );
    }

    public function toArray(): array
    {
        return [
            'external_id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->image
        ];
    }
}