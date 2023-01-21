<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    #[Route('/user/login', name: 'user.login')]
    public function login(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $email = $array['email'];
            $password = $array['password'];
            $userByEmail = $this->userRepository->findOneByEmailField($email);

            if ($userByEmail != null) {
                if (password_verify($password, $userByEmail->getPassword())) {
                    $return = [
                        'status' => 'success',
                        'error' => 200,
                        'message' => 'Usuario logueado correctamente',
                        'user' => $userByEmail
                    ];
                    
                }else{
                    $return = [
                        'status' => 'error',
                        'error' => 404,
                        'message' => 'La contraseña no coincide'
                    ];
                }
            }else{
                $return = [
                    'status' => 'error',
                    'error' => 404,
                    'message' => 'No existe usuario con este email'
                ];
            }
        }else{
            $data = [
                'status' => 'error',
                'error' => 404,
                'message' => 'No has añadido ningún campo'
            ];
        }
        return new JsonResponse ($return);
    }

    #[Route('/user/register', name: 'user.register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        if ($json != null) {
            $array = json_decode($json, true);
            if (!empty($array['name']) && !empty($array['surname']) && !empty($array['email'])) {
                if ($this->userRepository->findOneBy(['email' => $array['email']]) == null) {
                    if (!$this->hasNumber($array['name']) && !$this->hasNumber($array['surname'])) {
                        if ($array['password'] != '') {
                            if ($array['password'] == $array['confirmPassword']) {
                                $user = new User();
                                $user->setName($array['name']);
                                $user->setSurname($array['surname']);
                                $user->setEmail($array['email']);
                                $user->setPassword(password_hash($array['password'], PASSWORD_BCRYPT));
                                $this->userRepository->save($user, true);
                                unset($array['confirmPassword']);
                                $array['encriptedPassword'] = $user->getPassword();
                                $return = [
                                    "user" => $array,
                                    "status" => 'success',
                                    "code" => '200',
                                    "message" => ['Usuario registrado correctamente']
                                ];
                            } else {
                                $return = [
                                    "status" => 'error',
                                    "code" => '400',
                                    "message" => ['La contraseña no coincide']
                                ];
                            }
                        } else {
                            $return = [
                                "status" => 'error',
                                "code" => '400',
                                "message" => ['La contraseña no es válida']
                            ];
                        }
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                            "message" => []
                        ];
                        if ($this->hasNumber($array['name'])) {
                            $return["message"][] = "El nombre no es válido";
                        };
                        if ($this->hasNumber($array['surname'])) {
                            $return["message"][] = "El apellido no es válido";
                        };
                    };
                } else {
                    $return = [
                        "status" => 'error',
                        "code" => '400',
                        "message" => ['El email ya existe']
                    ];
                }
            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                    "message" => []
                ];
                if (empty($array['name'])) {
                    $return["message"][] = "El nombre no puede estar vacío";
                };
                if (empty($array['surname'])) {
                    $return["message"][] = "El apellido no puede estar vacío";
                };
                if (empty($array['email'])) {
                    $return["message"][] = "El email no puede estar vacío";
                };
            }
        } else {
            $return = ['Campos vacíos'];
        }


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
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $user = $this->userRepository->find($array['id']);
            if ($user != null) {
                $this->userRepository->remove($user, true);
                $return = [
                    "code" => '200',
                    "status" => 'success',
                    "message" => 'El usuario ha sido eliminado correctamente'
                ];
            } else {
                $return = [
                    "code" => '404',
                    "status" => 'error',
                    "message" => 'El usuario no se encuentra'
                ];
            }
        } else {
            $return['code'] = '400';
            $return['status'] = 'error';
            $return['message'] = 'Campos vacíos';
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
                if (!empty($array['name']) && !empty($array['surname'])) {
                    if (!$this->hasNumber($array['name']) && !$this->hasNumber($array['surname'])) {
                        $user->setName($array['name']);
                        $user->setSurname($array['surname']);
                        $return = [
                            "user" => $array,
                            "status" => 'success',
                            "code" => '200',
                            "message" => []
                        ];
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                            "message" => []
                        ];
                        if ($this->hasNumber($array['name'])) {
                            $return["message"][] = "El nombre no es válido";
                        };
                        if ($this->hasNumber($array['surname'])) {
                            $return["message"][] = "El apellido no es válido";
                        };
                    };
                }else{
                    $return = [
                        "status" => 'error',
                        "code" => '400',
                        "message" => []
                    ];
                }

                if (!empty($array['email'])) {
                    if ($user->getEmail() != $array['email']) {
                        if ($this->userRepository->findOneBy(['email' => $array['email']]) == null) {
                            $user->setEmail($array['email']);
                            $return = [
                                "user" => $array,
                                "status" => 'success',
                                "code" => '200',
                            ];
                        }else{
                            $return = [
                                "status" => 'error',
                                "code" => '400',
                            ];
                            $return["message"][] = 'Email ya existe';
                        }
                    }
                }

                if ($return['code'] == '200') {
                    $this->userRepository->save($user, true);
                    $return['message'][] = 'El usuario ha sido actualizado correctamente';
                }

            } else {
                $return = [
                    "status" => 'error',
                    "code" => '404',
                    "message" => ['No se ha podido identificar al usuario']
                ];
            }
        } else {
            $return = ['Campos vacíos'];
        }


        return new JsonResponse($return);
    }
}
