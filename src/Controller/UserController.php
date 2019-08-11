<?php
namespace App\Controller;

use App\Entity\UserEntity;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * User controller.
 * @Route("/api")
 */
class UserController extends AbstractFOSRestController
{

    private $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }


    /**
     * Lists all users.
     *
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getUsersAction()
    {
        $users = $this->userService->findAllUsers();
        return $this->handleView($this->view($users->toArray()));
    }

    /**
     * Find User
     *
     * @param $username
     * @Rest\Get("/users/{username}")
     *
     * @return Response| mixed
     */
    public function getUserAction(string $username)
    {
        if (!$user = $this->userService->findUser($username)) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }
        return $this->handleView($this->view($user->toArray()));
    }

    /**
     * Find User friends
     *
     * @param $username
     * @Rest\Get("/friends/{username}")
     *
     * @return Response| mixed
     */
    public function getFriendsAction(string $username)
    {
        $users = $this->userService->findFriends($username);
        return $this->handleView($this->view($users->toArray()));
    }

    /**
     * Create User
     *
     * @param $request
     * @Rest\Post("/users")
     *
     * @return Response| mixed
     */
    public function createUserAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = new UserEntity($data, -1);
        } catch (\Throwable $e) {
            return $this->handleView($this->view(['status'=>'error'], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        if ($existingUser = $this->userService->findUser($user->getUserName())) {
            return $this->handleView($this->view(['status'=>'error'], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        $this->userService->createUser($user);
        return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
    }

    /**
     * Delete User
     *
     * @param $username
     * @Rest\Delete("/users/{username}")
     *
     * @return Response| mixed
     */
    public function deleteUserAction(string $username)
    {
        if (!$user = $this->userService->findUser($username)) {
            return $this->handleView($this->view(['status'=>'error'], Response::HTTP_NOT_FOUND));
        }

        $this->userService->deleteUser($user);
        return $this->handleView($this->view(['status'=>'ok']));
    }

    /**
     * Connect Users
     *
     * @param $firstUser
     * @param $secondUser
     * @Rest\Patch("/connect/{firstUser}/{secondUser}")
     *
     * @return Response| mixed
     */
    public function connectUsersAction(string $firstUser, string $secondUser)
    {
        if (!$user = $this->userService->findUser($firstUser)) {
            return $this->handleView($this->view(['status'=>'error'], Response::HTTP_NOT_FOUND));
        }

        if (!$userFriend = $this->userService->findUser($secondUser)) {
            return $this->handleView($this->view(['status'=>'error'], Response::HTTP_NOT_FOUND));
        }

        $this->userService->connectUsers($user, $userFriend);
        return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
    }

}