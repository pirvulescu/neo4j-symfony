<?php

namespace App\Service;

use App\Entity\UserCollection;
use App\Entity\UserEntity;
use GraphAware\Neo4j\Client\ClientInterface;

class UserService
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function findAllUsers(): UserCollection
    {
        $query = 'MATCH (u:User) RETURN u, id(u) as id';
        $result = $this->client->run($query);

        $users = new UserCollection();

        foreach ($result->records() as $record) {
            try {
                $users->add(new UserEntity($record->get('u')->values(), $record->get('id')));
            } catch (\Throwable $e) {
                //do nothing, continue
            }
        }
        return $users;
    }

    public function findUser(string $username): ?UserEntity
    {
        $query = 'MATCH (u:User) WHERE u.username = {username} RETURN u, id(u) as id';
        $result = $this->client->run($query, ['username' => $username]);

        if (count($result->records()) === 0){
            return null;
        }
        $record = $result->firstRecord();

        try {
            return new UserEntity($record->get('u')->values(), $record->get('id'));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function findFriends(string $username): UserCollection
    {
        $query = 'MATCH (p:User { username: {username} })-[:KNOWS]->(u) RETURN u, id(u) as id';
        $result = $this->client->run($query, ['username' => $username]);

        $users = new UserCollection();

        foreach ($result->records() as $record) {
            try {
                $users->add(new UserEntity($record->get('u')->values(), $record->get('id')));
            } catch (\Throwable $e) {
                //do nothing, continue
            }
        }
        return $users;
    }

    public function createUser(UserEntity $user): void
    {
        /**
         * Constrain of uniques username should be added also on db
         */
        $query = 'CREATE (u:User {username: {username}, name:{name}, description: {description}})';
        $this->client->run($query, $user->toArray());
    }

    public function deleteUser(UserEntity $user): void
    {
        $query = 'MATCH (u:User { username: {username} }) DELETE u';
        $this->client->run($query, ['username' => $user->getUserName()]);
    }

    public function connectUsers(UserEntity $user, UserEntity $userFriend): void
    {
        $query = 'MATCH (a:User),(b:User) 
            WHERE a.username = {user} AND b.username = {friend} 
            CREATE (a)-[r:KNOWS]->(b)';
        $this->client->run($query, ['user' => $user->getUserName(), 'friend' => $userFriend->getUserName()]);
    }
}