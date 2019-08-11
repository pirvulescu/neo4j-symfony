<?php

namespace App\Entity;


class UserEntity
{
    private $id;
    private $userName;
    private $name;
    private $description;

    //for a more complex situation this could be moved to a Factory
    public function __construct(array $node, int $id)
    {
        if (empty($node['username']) || empty($node['username']) || empty($node['username'])) {
            throw new \Exception('Invalid data');
        }

        $this->id = $id;
        $this->userName = $node['username'] ?? null;
        $this->name = $node['name'] ?? null;
        $this->description = $node['description'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->userName,
            'name' => $this->name,
            'description' => $this->description
        ];
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}