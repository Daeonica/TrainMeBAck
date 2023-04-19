<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class ImageController extends AbstractController
{

    public function __construct(private UserRepository $userRepository, private CourseRepository $courseRepository, private CategoryRepository $categoryRepository){

    }

    #[Route('/user/upload/{id}', methods: ['POST'])]
    public function uploadImageUser($id, Request $request)
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
            } else {
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['File not found']
                ];
            }
        }
        return new JsonResponse($return);
    }

    #[Route('/user/image/{id}', methods: ['GET'])]

    public function getUserImage($id, Request $request)
    {

        $user = $this->userRepository->find($id);
        $path = $this->getParameter('images_directory') . '/user/' . $user->getImgPath();
        $response = new BinaryFileResponse($path);

        return $response;
    }

    #[Route('/course/upload/image/{id}', methods: ['POST'])]
    public function uploadImageCourse($id, Request $request)
    {
        $course = $this->courseRepository->find($id);
        $return = [];

        if ($course != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory') . '/course', $fileName);
                    $course->setImgPath($fileName);

                    $this->courseRepository->save($course, true);
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
            } else {
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['File not found']
                ];
            }
        }
        return new JsonResponse($return);
    }

    #[Route('/course/image/{id}', methods: ['GET'])]

    public function getCourseImage($id, Request $request)
    {

        $course = $this->courseRepository->find($id);
        $path = $this->getParameter('images_directory') . '/course/' . $course->getImgPath();
        $response = new BinaryFileResponse($path);

        return $response;
    }

    #[Route('/course/upload/document/{id}', methods: ['POST'])]
    public function uploadDocumentCourse($id, Request $request)
    {
        $course = $this->courseRepository->find($id);
        $return = [];

        if ($course != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory') . '/course/document/', $fileName);
                    $course->setDocumentRoot($fileName);

                    $this->courseRepository->save($course, true);
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
            } else {
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['File not found']
                ];
            }
        }
        return new JsonResponse($return);
    }

    #[Route('/course/document/{id}', methods: ['GET'])]

    public function getCourseDocument($id)
    {

        $course = $this->courseRepository->find($id);
        $path = $this->getParameter('images_directory') . '/course/document/' . $course->getDocumentRoot();
        $response = new BinaryFileResponse($path);

        return $response;
    }


    #[Route('/category/image/{id}', methods: ['GET'])]

    public function getCategoryImage($id, Request $request)
    {

        $category = $this->categoryRepository->find($id);
        $path = $this->getParameter('images_directory') . '/category/' . $category->getImgPath();
        $response = new BinaryFileResponse($path);

        return $response;
    }


    #[Route('/category/upload/image/{id}', methods: ['POST'])]
    public function uploadImageCategory($id, Request $request)
    {
        $category = $this->categoryRepository->find($id);
        $return = [];

        if ($category != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory') . '/category', $fileName);
                    $category->setImgPath($fileName);

                    $this->categoryRepository->save($category, true);
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
            } else {
                $return = [
                    'code' => '400',
                    'status' => 'error',
                    'messages' => ['File not found']
                ];
            }
        }
        return new JsonResponse($return);
    }
}
