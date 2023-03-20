<?php

namespace App\Controller;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Serializer\SerializerInterface;

class RoleController extends AbstractController
{
    private $serializer;
    public function __construct(private UserRepository $userRepository, private RoleRepository $roleRepository)
    {
        
    }



    #[Route('/roles/get', methods: ['GET'])]
    public function getRoles():JsonResponse{
        
        $rolesJson = $this->roleRepository->findAll();
        $roles = [];

        
        foreach ($rolesJson as $role) {
            $roles[] = $role->getDataInArray();
        }

        

        return new JsonResponse($roles); 
    }

    #[Route('/role/get-by-id/{id}', methods: ['GET'])]
    public function getRoleById($id)
    {
        $role = $this->roleRepository->find($id);

        return new JsonResponse($role->getDataInArray());
    }


    #[Route('/role/update', methods: ['PUT'])]
    public function setCategory(Request $request): JsonResponse
    {
        $json = $request->get('data');
        $data = json_decode($json, true);
        $return = [];

        if ($data != null) {
            if (!empty($data['key_value']) && !empty($data['name']) && !empty($data['id'])) {
                $role = $this->roleRepository->find($data['id']);

                if ($role != null) {
                    $role->setKeyValue($data['key_value']);
                    $role->setName($data['name']);
                    $this->roleRepository->save($role, true);
                    $return = [
                        'code' => '200',
                        'status' => 'success',
                        'role' => $role->getDataInArray(),
                        'messages' => ['Role updated successfully']
                    ];
                }else{
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Role not found']
                    ];
                }
            } else {
                $return['code'] = '400';
                $return['status'] = 'error';

                if (empty($data['id'])) {
                    $return['messages'][] = "Id not received";
                }

                if (empty($data['name'])) {
                    $return['messages'][] = "Name not received";
                }

                if (empty($data['key_value'])) {
                    $return['messages'][] = "Key value not received";
                }
            }
        }

        return new JsonResponse($return);
    }


    #[Route('/role/delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        //recibimos los datos en un json
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            //transformamos los datos a array
            $array = json_decode($json, true);
            $role = $this->roleRepository->find($array['id']);
            if ($role != null) {
                $this->roleRepository->remove($role, true);
                $return = [
                    "code" => '200',
                    "status" => 'success',
                ];
                $return['messages'][] = 'The role has been deleted successfully';
            } else {
                $return = [
                    "code" => '400',
                    "status" => 'error',
                ];
                $return['messages'][] = 'The role not found';
            }
        } else {
            //si los datos recibidos estan vacios
            $return['code'] = '400';
            $return['status'] = 'error';
            $return['messages'][] = 'Data is empty';
        }
        return new JsonResponse($return);
    }

    
    #[Route('/role/add', methods: ['POST'])]

    public function setCategories(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];

        if ($json != null) {
            $array = json_decode($json, true);
            $name = $array['name'];
            $key_value = $array['key_value'];

            $role = new Role;

            if (!empty($name)) {
                $role->setName($name);
                $return = [
                    'code' => 200,
                    'status' => 'success'
                ];
            } else {
                $return['messages'][] = 'The name is not valid';
            }

            if (!empty($key_value)) {
                $role->setKeyValue($key_value);
                $return = [
                    'code' => 200,
                    'status' => 'success'
                ];
            }else {
                $return['messages'][] = 'The key_value is not valid';
            }

            if ($return['code'] == 200) {
                $this->roleRepository->save($role, true);
                $return['role'] = $role->getDataInArray();
                $return['messages'][] = 'Role saved successfully';
            } else {
                $return['messages'][] = 'The role is not saved';
            }
        }

        return new JsonResponse($return);
    }

}

