<?php

namespace App\Entity;

class UserCollection implements \IteratorAggregate
{
    /** @var UserEntity[] */
    private $users = [];

    public function add(UserEntity $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @return UserEntity[]|\ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->users);
    }

    public function toArray(): array
    {
        $return = [];
        foreach ($this->users as $user) {
            $return[] = $user->toArray();
        }

        return $return;
    }
}