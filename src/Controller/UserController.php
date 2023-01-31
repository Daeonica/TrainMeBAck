<?php

namespace App\Controller;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTime;
use DateTimeInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private RoleRepository $roleRepository)
    {
    }



    #[Route('/user/login', name: 'user.login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $email = $array['email'];
            $password = $array['password'];
            $userByEmail = $this->userRepository->findOneBy(['email' => $email]);
            if ($userByEmail != null) {
                if (password_verify($password, $userByEmail->getPassword())) {
                    $return = [
                        'status' => 'success',
                        'code' => 200,
                        'messages' => 'Usuario logueado correctamente',
                        'user' => $userByEmail->getDataInArray()
                    ];
                } else {
                    $return = [
                        'status' => 'error',
                        'code' => 400,
                        'messages' => 'La contraseña no coincide'
                    ];
                }
            } else {
                $return = [
                    'status' => 'error',
                    'code' => 400,
                    'messages' => 'No existe usuario con este email'
                ];
            }
        } else {
            $return = [
                'status' => 'error',
                'code' => 400,
                'messages' => 'No has añadido ningún campo'
            ];
        }
        return new JsonResponse($return);
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
                                $user->setRegisterDate(new \DateTime);


                                if (isset($array['role_id'])) {
                                    $role = $this->roleRepository->findOneBy(['key_value' => $array['role_id']]);
                                } else {
                                    $role = $this->roleRepository->findOneBy(['key_value' => 'super_admin']);
                                    if ($role == null) {
                                        $role = new Role();
                                        $role->setName('super_admin');
                                        $role->setKeyValue('super_admin');
                                        $this->roleRepository->save($role, true);
                                    }
                                }
                                $user->setRole($role);
                                $this->userRepository->save($user, true);
                                unset($array);

                                $return = [
                                    "user" => $user->getDataInArray(),
                                    "status" => 'success',
                                    "code" => '200',
                                ];
                                $return['messages'][] = 'Usuario registrado correctamente';
                            } else {
                                $return = [
                                    "status" => 'error',
                                    "code" => '400',
                                ];
                                $return['messages'][] = 'La contraseña no coincide';
                            }
                        } else {
                            $return = [
                                "status" => 'error',
                                "code" => '400',
                            ];
                            $return['messages'][] = 'La contraseña no es válida';
                        }
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                            "messages" => []
                        ];
                        if ($this->hasNumber($array['name'])) {
                            $return["messages"][] = "El nombre no es válido";
                        };
                        if ($this->hasNumber($array['surname'])) {
                            $return["messages"][] = "El apellido no es válido";
                        };
                    };
                } else {
                    $return = [
                        "status" => 'error',
                        "code" => '400',
                    ];
                    $return["messages"][] = "El email ya existe";
                }
            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                    "messages" => []
                ];
                if (empty($array['name'])) {
                    $return["messages"][] = "El nombre no puede estar vacío";
                };
                if (empty($array['surname'])) {
                    $return["messages"][] = "El apellido no puede estar vacío";
                };
                if (empty($array['email'])) {
                    $return["messages"][] = "El email no puede estar vacío";
                };
            }
        } else {
            $return = [
                "status" => 'error',
                "code" => '400',
            ];
            $return['messages'] = 'Campos vacíos';
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
                ];
                $return['messages'][] = 'El usuario ha sido eliminado correctamente';
            } else {
                $return = [
                    "code" => '400',
                    "status" => 'error',
                ];
                $return['messages'][] = 'El usuario no se encuentra';
            }
        } else {
            $return['code'] = '400';
            $return['status'] = 'error';
            $return['messages'][] = 'Campos vacíos';
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
                        $return["messages"][] = 'El formato del nombre no es correcto';
                    }
                }

                if (!empty($array['surname'])) {
                    if (!$this->hasNumber($array['surname'])) {
                        $user->setName($array['surname']);
                        $return["status"] = 'success';
                        $return["code"] = '200';
                    } else {
                        $return = [
                            "status" => 'error',
                            "code" => '400',
                        ];
                        $return["messages"][] = 'El formato del apellido no es correcto';
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
                            $return['messages'][] = 'Ya existe usuario con este email';
                        }
                    }
                }




                if ($return['code'] == '200') {
                    $this->userRepository->save($user, true);
                    $return['messages'][] = 'El usuario ha sido actualizado correctamente';
                }

            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                ];
                $return['messages'][] = 'No se ha podido identificar al usuario';
            }
        } else {
            $return = [
                "status" => 'error',
                "code" => '400',
            ];
            $return['messages'][] = 'No hay datos';
        }

        return new JsonResponse($return);
    }

    #[Route('/user/upload/{id}', name: 'user.opload', methods: ['POST'])]
    public function upload($id, Request $request)
    {
        $user = $this->userRepository->find($id);


        if ($user != null) {
            $file = $request->files->get('file');
            if ($file) {
                $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory') . '/user', $fileName);
                    $user->setImgPath($fileName);

                    $this->userRepository->save($user, true);
                } catch (FileException $e) {
                    dd($e);
                }
            }
        }
        return new JsonResponse('Imagen subida correctamente');
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
        $role = $this->roleRepository->findOneBy(['key_value' => 'super_Admin']);
        $users = $role->getUsers()->toArray();
        $return = [];
        foreach ($users as $user) {
            $return[] = $user->getDataInArray();
        }
        return new JsonResponse($return);
    }
}
