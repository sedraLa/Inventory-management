<?php

declare(strict_types=1);

namespace App\Entities;

use App\Traits\HasTimestamps;

class Supplier
{
    use HasTimestamps;

    public function __construct(
        private ?int    $id,
        private string  $name,
        private ?string $phone,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
