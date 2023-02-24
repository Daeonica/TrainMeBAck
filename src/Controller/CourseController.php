<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CourseRepository;
use App\Entity\Course;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;

class CourseController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository, private CourseRepository $courseRepository, private UserRepository $userRepository)
    {
    }

    #[Route('/course/search/{query}', name: 'course.search', methods: ['POST'])]
    public function search($query)
    {
        $return['courses'] = [];
        $categories = $this->categoryRepository->findBy(['name' => $query]);

        foreach ($categories as $category) {
            //esto es un array de cursos en el que la categoria coincide con lo que pasamos por query
            $return['courses'] = $category->getCourses();
        };

        $courses = $this->courseRepository->findBetween($query);
        foreach ($courses as $course) {
            //recogemos todos los cursos en que el nombre coincide con la query
            if (empty($return['courses'])) {
                $data = $course->getDataInArray();
                unset($data['buy_user_courses']);
                unset($data['user']);
                unset($data['category']);
                $return['courses'][] =  $data;
            } else {
                foreach ($return['courses'] as $return_course) {
                    //recorremos el array de cursos(categoria) y lo comparamos con el array de cursos(nombre)
                    //si no coinciden, añadimos el curso(nombre) al array final 
                    if ($course->getId() != $return_course->getId()) {
                        $return['courses'][] = $course;
                    }
                }
            }
        };

        //creamos un array con todos los users de la bbdd que tienen el nombre lo de la query
        $users = $this->userRepository->findBetween($query);
        //recorremos los usuarios
        foreach ($users as $user) {
            //miramos los cursos que tienen los users
            if (empty($return['courses'])) {
                foreach ($user->getCourses() as $user_course) {
                    $data = $user_course->getDataInArray();
                    unset($data['buy_user_courses']);
                    unset($data['user']);
                    unset($data['category']);
                    $return['courses'][] =  $data;
                }
            } else {
                foreach ($user->getCourses() as $userCourse) {

                    //recorremos el array que hemos creado inicialmente con los cursos con categoria igual a la query.
                    foreach ($return['courses'] as $return_course) {
                        //hacemos la comparativa de los cursos del user con los del array finakl. Si no coinciden se me meten
                        if ($userCourse->getId() != $return_course->getId()) {
                            $return['courses'][] = $userCourse;
                        }
                    }
                }
            }
        }
        return new JsonResponse($return);
    }


    #[Route('/course/get-by-id/{id}', name: 'course.get-by-id', methods: ['POST'])]
    public function getCourseById($id)
    {
        $course = $this->courseRepository->find($id)->getDataInArray();

        return new JsonResponse($course);
    }




    #[Route('/course/create', name: 'course.create', methods: ['POST'])]
    public function createCourse(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];

        if ($json != null) {
            $array = json_decode($json, true);

            if (!empty($array['name']) && !empty($array['description']) && !empty($array['price'] && !empty($array['user']))) {
                $user       = $this->userRepository->find($array['user']['id']);
                $category   = $this->categoryRepository->find($array['category']['id']);

                if ($user != null && $category != null) {
                    $course = new Course();
                    $course->setName($array['name']);
                    $course->setDescription($array['description']);
                    $course->setPrice($array['price']);
                    $course->setImgPath('null');
                    $course->setDocumentRoot('null');
                    $course->setUser($user);
                    $course->setCategory($category);
                    $this->courseRepository->save($course, true);
                    $return = [
                        'status' => 'success',
                        'code' => 200,
                        'messages' => ['El curso ha sido creado con éxito']
                    ];
                } else {
                    if ($user == null) {
                        $return['messages'][] = ['User not exists'];
                    }

                    if ($category == null) {
                        $return['messages'][] = ['Category not exists'];
                    }
                }
            } else {
                $return = [
                    'status' => 'error',
                    'code' => 400,
                    'messages' => ['Hay algún campo vacio']
                ];
            }
        } else {
            $return = [
                'status' => 'error',
                'code' => 400,
                'messages' => ['json vacío']
            ];
        }
        return new JsonResponse($return);
    }

    #[Route('/course/delete', name: 'course.delete', methods: ['DELETE'])]
    public function deleteCourse(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];

        if ($json != null) {
            $array = json_decode($json, true);
            $course = $this->courseRepository->find($array['id']);

            if ($course != null) {
                $this->courseRepository->remove($course, true);
                $return = [
                    'code' => '200',
                    'status' => 'success',
                    'messages' => ['El curso ha sido eliminado con éxito']
                ];
            } else {
                $return = [
                    "code" => '400',
                    "status" => 'error',
                    'messages' => ['El curso no existe']
                ];
            }
        } else {
            $return = [
                'code' => '400',
                'status' => 'error',
                'messages' => ['Campos vacios']
            ];
        }

        return new JsonResponse($return);
    }

    #[Route('/course/update', name: 'course.update', methods: ['PUT'])]
    public function updateCourse(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            $array = json_decode($json, true);
            $course = $this->courseRepository->find($array['id']);

            if ($course != null) {
                if (!empty($array['name'])) {
                    $course->setName($array['name']);
                    $return["status"] = 'success';
                    $return["code"] = '200';
                }

                if (!empty($array['description'])) {
                    $course->setDescription($array['description']);
                    $return["status"] = 'success';
                    $return["code"] = '200';
                }

                if (!empty($array['price'])) {
                    $course->setPrice($array['price']);
                    $return["status"] = 'success';
                    $return["code"] = '200';
                }

                if (!empty($array['category'])) {
                    $category = $this->categoryRepository->find($array['category']['id']);
                    if ($category) {
                        $course->setCategory($category);
                        $return["status"] = 'success';
                        $return["code"] = '200';
                    } else {
                        $return["status"] = 'error';
                        $return["code"] = '400';
                        $return['messages'][] = 'Category not found';
                    }
                }

                if ($return['code'] == '200') {
                    $this->courseRepository->save($course, true);
                    $return = [
                        'status' => 'success',
                        'code' => '200',
                        'messages' => ['curso actualizado con éxito']
                    ];
                }
            } else {
                $return = [
                    "status" => 'error',
                    "code" => '400',
                    'messages' => ['No hay datos']
                ];
            }
        }
        return new JsonResponse($return);
    }

    #[Route('/course/get', name: 'course.getCourses', methods: ['GET'])]
    public function getCourses(Request $request)
    {
        $response = [];
        $courses = $this->courseRepository->findAll();

        foreach ($courses as $course) {
            $response[] = $course->getDataInArray();
        }
        return new JsonResponse($response);
    }

    #[Route('/course/trainer/{id}', name: 'course.getCoursesUser', methods: ['GET'])]
    public function getCoursesUser($id, Request $request)
    {
        $user = $this->userRepository->find($id);
        $courses = $user->getCourses();
        $response = [];

        foreach ($courses as $course) {
            $response[] = $course->getDataInArray();
        }

        return new JsonResponse($response);
    }
}
