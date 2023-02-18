<?php

namespace App\Controller;

use App\Entity\CustomerSupport;
use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\CustomerSupportRepository;
use DateTime;
use DateTimeInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private RoleRepository $roleRepository, private CustomerSupportRepository $customerSupportRepository)
    {
    }

    #[Route('/user/all-users', name: 'user.alluser', methods: ['GET'])]
    public function getAllUsers()
    {

        $users = [];
        $models = $this->userRepository->findAll();
        foreach ($models as $model) {
            $users[] = $model->getDataInArray();
        }
        return new JsonResponse($users);
    }


    #[Route('/user/login', name: 'user.login', methods: ['POST'])]
    public function login(Request $request)
    {

        $json = $request->get('data', null);
        $data = json_decode($json, true);
        $return = [];

        if ($data != null) {
            $email = $data['email'];
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user != null) {
                $pwd = $data['password'];
                if (password_verify($pwd, $user->getPassword())) {
                    $return = [
                        'code' => '200',
                        'status' => 'success',
                        'user'  => $user->getDataInArray()
                    ];
                } else {
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Password incorrect']
                    ];
                }
            } else {
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['Data not received']
                ];
            }
        }

        return new JsonResponse($return);
    }





    #[Route('/user/get-by-id/{id}', name: 'user', methods: ['GET'])]
    public function getUserById($id, Request $request): JsonResponse
    {

        $user = $this->userRepository->find($id)->getDataInArray();

        return new JsonResponse($user);
    }


    #[Route('/user/register', name: 'user.register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Getting all data by Request with 'data' access key, if access key not exists then $json have null value
        $json = $request->get('data', null);

        // If $json is different to null, then exists parameters from data key access
        if ($json != null) {

            // When we get data, this data is in JSON format, for this reason, we have to decode, in the second parameter have to put true for accept Associate array
            $array = json_decode($json, true);

            // Now have to validate if the required data is not empty
            if (!empty($array['name']) && !empty($array['surname']) && !empty($array['email'])) {

                // After validation, we try to find the received email by Request with UserRepository, with findOnBy(['table_attribtue' => 'attribute_value'])
                // If this user is null, then can continue the register process.
                if ($this->userRepository->findOneBy(['email' => $array['email']]) == null) {

                    // Extra validation if name and surname have numbers, if password and confirmPassword is same..
                    // The hasNumber() function is created in this file, return true or false.
                    if (!$this->hasNumber($array['name']) && !$this->hasNumber($array['surname'])) {
                        if ($array['password'] != '') {
                            if ($array['password'] == $array['confirmPassword']) {

                                // If all is okey, then create User object, and add all attributes
                                $user = new User();
                                $user->setName($array['name']);
                                $user->setSurname($array['surname']);
                                $user->setEmail($array['email']);

                                // Allways save hashed password
                                $user->setPassword(password_hash($array['password'], PASSWORD_BCRYPT));
                                $user->setRegisterDate(new \DateTime);

                                // Also, when we get data in Request, we receive role with her id
                                // Where we are searching role with by id. (I forgot to validate if exists or not the role id)
                                $role = $this->roleRepository->find($array['role']['id']);

                                // In User Entity, you can look we have a setRole function
                                // This function is just for save role, sending in the parameter the Role Entity !!important (send Entity, not just role id)
                                $user->setRole($role);
                                $this->userRepository->save($user, true);
                                unset($array);

                                $return = [
                                    // getDataInArray() is customed function for get all user data in array type than object type
                                    "user" => $user->getDataInArray(),
                                    "status" => 'success',
                                    "code" => '200',
                                ];

                                // In messages, allways we make with this estandar, because is more easy to loop all errors in the FrontEnd 
                                $return['messages'][] = 'User has been deleted successfully';
                            } else {
                                $return = [
                                    "status" => 'error',
                                    "code" => '400',
                                ];
                                $return['messages'][] = 'Confirm password is diferent';
                            }
                        } else {
                            $return = [
                                "status" => 'error',
                                "code" => '400',
                            ];
                            $return['messages'][] = 'Password not valid';
                        }
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                            "messages" => []
                        ];
                        if ($this->hasNumber($array['name'])) {
                            $return["messages"][] = "Name not valid";
                        };
                        if ($this->hasNumber($array['surname'])) {
                            $return["messages"][] = "Surname not valid";
                        };
                    };
                } else {
                    $return = [
                        "status" => 'error',
                        "code" => '400',
                    ];
                    $return["messages"][] = "Email exists";
                }
            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                    "messages" => []
                ];
                if (empty($array['name'])) {
                    $return["messages"][] = "Name empty";
                };
                if (empty($array['surname'])) {
                    $return["messages"][] = "Surname empty";
                };
                if (empty($array['email'])) {
                    $return["messages"][] = "Email empty";
                };
            }
        } else {
            $return = [
                "status" => 'error',
                "code" => '400',
            ];
            $return['messages'] = 'Data empty';
        }


        // Finally, we return all data in $return variable

        return new JsonResponse($return);
    }



    public function hasNumber($data)
    {
        for ($i = 0; $i < strlen($data); $i++) {
            if (is_numeric($data[$i])) {
                return true;
            }
        }
        return false;
    }

    #[Route('/user/delete', name: 'user.delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        //recibimos los datos en un json
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            //transformamos los datos a array
            $array = json_decode($json, true);
            $user = $this->userRepository->find($array['id']);
            if ($user != null) {
                $this->userRepository->remove($user, true);
                $return = [
                    "code" => '200',
                    "status" => 'success',
                ];
                $return['messages'][] = 'The user has been deleted successfully';
            } else {
                $return = [
                    "code" => '400',
                    "status" => 'error',
                ];
                $return['messages'][] = 'User not found';
            }
        } else {
            //si los datos recibidos estan vacios
            $return['code'] = '400';
            $return['status'] = 'error';
            $return['messages'][] = 'Data empty';
        }
        return new JsonResponse($return);
    }

    #[Route('/user/update', name: 'user.update', methods: ['PUT'])]
    public function update(Request $request): JsonResponse
    {

        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $user = $this->userRepository->find($array['id']);

            if ($user != null) {

                if (!empty($array['name'])) {
                    if (!$this->hasNumber($array['name'])) {
                        $user->setName($array['name']);
                        $return["status"] = 'success';
                        $return["code"] = '200';
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                        ];
                        $return["messages"][] = 'The format name is incorrect';
                    }
                }

                if (!empty($array['surname'])) {
                    if (!$this->hasNumber($array['surname'])) {
                        $user->setSurname($array['surname']);
                        $return["status"] = 'success';
                        $return["code"] = '200';
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                        ];
                        $return["messages"][] = 'The format surname is incorrect';
                    }
                }

                if (!empty($array['description'])) {
                    $user->setDescription($array['description']);
                    $return["status"] = 'success';
                    $return["code"] = '200';
                }

                if (!empty($array['email'])) {
                    if ($user->getEmail() != $array['email']) {
                        if ($this->userRepository->findOneBy(['email' => $array['email']]) == null) {
                            $user->setEmail($array['email']);
                            $return["status"] = 'success';
                            $return["code"] = '200';
                        } else {
                            $return = [
                                "status" => 'error',
                                "code" => '400',
                            ];
                            $return['messages'][] = 'User with this email exists';
                        }
                    }
                }

                if (!empty($array['password']) && !empty($array['confirmPassword'])) {
                    if ($array['password'] == $array['confirmPassword']) {
                        $user->setPassword(password_hash($array['password'], PASSWORD_BCRYPT));
                        $return["status"] = 'success';
                        $return["code"] = '200';
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                        ];
                        $return['messages'][] = 'The password confirm is different';
                    }
                }

                if ($return['code'] == '200') {
                    $this->userRepository->save($user, true);
                    $return['messages'][] = 'The user has been updated successfully';
                    $return['user'] = $user->getDataInArray();
                }
            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                ];
                $return['messages'][] = 'User not found';
            }
        } else {
            $return = [
                "status" => 'error',
                "code" => '400',
            ];
            $return['messages'][] = 'Data not found';
        }

        return new JsonResponse($return);
    }

    #[Route('/user/upload/{id}', name: 'user.opload', methods: ['POST'])]
    public function upload($id, Request $request)
    {
        $user = $this->userRepository->find($id);
        $return = [];

        if ($user != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory') . '/user', $fileName);
                    $user->setImgPath($fileName);

                    $this->userRepository->save($user, true);
                    $return = [
                        'code' => '200',
                        'status' => 'success',
                        'messages' => ['Image saved successfully']
                    ];
                } catch (FileException $e) {
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Image not saved', $e]
                    ];
                }
            }else{
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['File not found']
                ];
            }
        }
        return new JsonResponse($return);
    }

    #[Route('/user/image/{id}', name: 'user.getImage', methods: ['GET'])]

    public function getImage($id, Request $request)
    {

        $user = $this->userRepository->find($id);
        $path = $this->getParameter('images_directory') . '/user/' . $user->getImgPath();
        $response = new BinaryFileResponse($path);

        return $response;
    }

    #[Route('/about_us', name: 'about_us', methods: ['GET'])]
    public function aboutUs()
    {
        $role = $this->roleRepository->findOneBy(['key_value' => 'admin']);
        $users = $role->getUsers()->toArray();
        $return = [];
        foreach ($users as $user) {
            $return[] = $user->getDataInArray();
        }
        return new JsonResponse($return);
    }

    #[Route('/contact', name: 'contact', methods: ['POST'])]
    public function contact(Request $request)
    {
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $contact = new CustomerSupport();
            //seteamos los atributos con cada campo del 
            $contact->setEmail($array['email']);
            $contact->setName($array['name']);
            $contact->setDescription($array['message']);

            //guardamos en la bbdd $this->contactRepository->save($contact, true);
            $contact = $this->customerSupportRepository->save($contact, true);
            $return["status"] = 'success';
            $return["code"] = '200';
            $return["message"][] = 'En breve será atendido, gracias por el feedback';
        } else {
            $return["status"] = 'error';
            $return["code"] = '400';
            $return["message"][] = 'No hay datos';
            //retornamos mensaje de error con su codigo en el array de $return
        }

        return new JsonResponse($return);
    }
}
