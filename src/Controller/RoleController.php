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
    public function __construct(private UserRepository $userRepository, private RoleRepository $roleRepository)
    {
        
    }



    #[Route('/roles/get', name: '', methods: ['GET'])]
    public function returnRoles():JsonResponse{
        
        $rolesJson = $this->roleRepository->findAll();
        $roles = [];

        
        foreach ($rolesJson as $rol) {
            $roles[] = $rol->getDataInArray();
        }

        

        return new JsonResponse($roles); 
    }
}

