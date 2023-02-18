<?php

namespace App\Controller;

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

    #[Route('/course/delete', name: 'course.delete', methods: ['POST'])]
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

    #[Route('/course/update', name: 'course.update', methods: ['POST'])]
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

    #[Route('/course/all/{id}', name: 'course.getCoursesUser', methods: ['GET'])]
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
