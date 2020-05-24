<?php

namespace App\Controller\Rest;

use App\Entity\User;
use App\Logger\SessionRequestProcessor;
use App\Security\Service\UpdateApiTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestController extends AbstractController
{
    /**
     * @Route("/", name="rest_index", methods={"GET"})
     * @return JsonResponse
     */
    public function index(): Response
    {
        return $this->json('Index page');
    }

    /**
     * @Route("/auth", name="rest_authorize")
     *
     * @param $updateApiTokenService UpdateApiTokenService
     * @return Response
     */
    public function authorize(UpdateApiTokenService $updateApiTokenService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $newApiToken = $updateApiTokenService->updateToken($user);

        return $this->json([
            'token' => $newApiToken
        ]);
    }

    /**
     * @Route("/users", name="get_users", methods={"GET"})
     */
    public function getUsers()
    {
        return $this->json('users');
    }
}