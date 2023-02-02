<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class RoleController extends AbstractController
{
    private $serializer;
    public function __construct(private UserRepository $userRepository, private RoleRepository $roleRepository, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }



    #[Route('/user/register', name: 'user.register', methods: ['GET'])]
    public function returnRoles():JsonResponse{
        
        $allRoles = $this->roleRepository->findAll();

        return new JsonResponse($allRoles); 
    }
}

