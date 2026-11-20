<?php

namespace App\Data;

readonly class UserData
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $username,
        public string $role,
        public string $createdAt,
    ) {}

    public static function fromArray(array $d): self
    {
        return new self(
            id:        $d['id'],
            name:      $d['name'],
            username:  $d['username'],
            role:      $d['role'],
            createdAt: $d['created_at'],
        );
    }

    public static function fromModel(\App\Models\User $m): self
    {
        return new self(
            id:        $m->id,
            name:      $m->name,
            username:  $m->username,
            role:      $m->role,
            createdAt: $m->created_at?->format('Y-m-d H:i:s') ?? '',
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'Admin' : 'Staf Administrasi';
    }

    public function roleColor(): string
    {
        return $this->isAdmin() ? 'blue' : 'green';
    }

    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }

    public function createdAtFormatted(): string
    {
        return date('d M Y', strtotime($this->createdAt));
    }
}
