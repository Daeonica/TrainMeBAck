<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPSTORM_META\type;

class CrudController extends AbstractController
{
    
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/crud', name: 'app_crud')]
    public function index(): Response
    {
        return $this->render('crud/index.html.twig', [
            'controller_name' => 'CrudController',
        ]);
    }

    #[Route('/user', name: 'app_crud',methods: ['POST'])]
    public function userSave(Request $request)
    {
        $json = $request->get('data',null);
        $array = json_decode($json,true);

        $user = new User();
        $user->setName($array['name']);
        $user->setSurname($array['surname']);
        $user->setEmail($array['email']);
        $user->setPassword(password_hash($array['password'], PASSWORD_BCRYPT));
        $this->userRepository->save($user, true);


        return new JsonResponse($array);
    }
}
