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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\File;


class ImageController extends AbstractController
{

    
    public function __construct(private UserRepository $userRepository, private CourseRepository $courseRepository, private CategoryRepository $categoryRepository)
    {
    }

    #[Route('/user/upload/{id}', methods: ['POST'])]
    public function uploadImageUser($id, Request $request)
    {
        $user = $this->userRepository->find($id);
        $return = [];

        if ($user != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $imageConstraint = new Image();

                $validator = Validation::createValidator();
                $violations = $validator->validate($file, $imageConstraint);

                if (0 === count($violations)) {
                    $fileName = date('Y-m-d') . time() . '.' . $file->guessExtension();
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
                        'messages' => ['Invalid image file']
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
        $response = [];
        $user = $this->userRepository->find($id);

        if ($user->getImgPath()) {
            $path = $this->getParameter('images_directory') . '/user/' . $user->getImgPath();
            $response = new BinaryFileResponse($path);
            return $response;
        } else {
            $path = $this->getParameter('images_directory') . '/user/user-icon.png';
            $response = new BinaryFileResponse($path);
            return $response;
        }
    }

    #[Route('/course/upload/image/{id}', methods: ['POST'])]
    public function uploadImageCourse($id, Request $request)
    {
        $course = $this->courseRepository->find($id);
        $return = [];

        if ($course != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $imageConstraint = new Image();

                $validator = Validation::createValidator();
                $violations = $validator->validate($file, $imageConstraint);

                if (0 === count($violations)) {
                    $fileName = date('Y-m-d') . time() . '.' . $file->guessExtension();
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
                        'messages' => ['Invalid image file']
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
        $response = [];

        if ($course->getImgPath()) {
            $path = $this->getParameter('images_directory') . '/course/' . $course->getImgPath();
            $response = new BinaryFileResponse($path);
            return $response;
        } else {
            $path = $this->getParameter('images_directory') . '/course/course-icon.png';
            $response = new BinaryFileResponse($path);
            return $response;
        }
    }

    #[Route('/course/upload/document/{id}', methods: ['POST'])]
    public function uploadDocumentCourse($id, Request $request)
    {
        $course = $this->courseRepository->find($id);
        $return = [];

        if ($course != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $documentConstraint = new File([
                    'mimeTypes' => [
                        'application/pdf',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ],
                    'mimeTypesMessage' => 'Please upload a valid PDF or Excel document',
                ]);

                $validator = Validation::createValidator();
                $violations = $validator->validate($file, $documentConstraint);

                if (0 === count($violations)) {
                    $fileName = date('Y-m-d') . time() . '.' . $file->guessExtension();
                    try {
                        $file->move($this->getParameter('images_directory') . '/course/document/', $fileName);
                        $course->setDocumentRoot($fileName);

                        $this->courseRepository->save($course, true);
                        $return = [
                            'code' => '200',
                            'status' => 'success',
                            'messages' => ['Document saved successfully']
                        ];
                    } catch (FileException $e) {
                        $return = [
                            'code' => '400',
                            'status' => 'error',
                            'messages' => ['Document not saved', $e]
                        ];
                    }
                } else {
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Invalid file format. Please upload a valid PDF or Excel document']
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

        if ($category->getImgPath()) {
            $path = $this->getParameter('images_directory') . '/category/' . $category->getImgPath();
            $response = new BinaryFileResponse($path);
            return $response;
        } else {
            $path = $this->getParameter('images_directory') . '/category/category-icon.png';
            $response = new BinaryFileResponse($path);
            return $response;
        }
    }

    #[Route('/category/upload/image/{id}', methods: ['POST'])]
    public function uploadImageCategory($id, Request $request)
    {
        $category = $this->categoryRepository->find($id);
        $return = [];

        if ($category != null) {
            $file = $request->files->get('file', null);
            if ($file) {
                $imageConstraint = new Image();

                $validator = Validation::createValidator();
                $violations = $validator->validate($file, $imageConstraint);

                if (0 === count($violations)) {
                    $fileName = date('Y-m-d') . time() . '.' . $file->guessExtension();
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
                        'messages' => ['Invalid image file']
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

    #[Route('/course/upload/video/{id}', methods: ['POST'])]
    public function uploadVideoCourse($id, Request $request, ValidatorInterface $validator)
    {
        $course = $this->courseRepository->find($id);
        $return = [];

        if ($course != null) {
            $file = $request->files->get('file', null);
            if ($file instanceof UploadedFile) {
                // Agregar restricción de validación de archivo
                $fileConstraint = new FileConstraint([
                    'mimeTypes' => [
                        'video/mp4',
                        'video/quicktime', // Para archivos .mov
                        // Agregar más tipos MIME de video si es necesario
                    ],
                    'mimeTypesMessage' => 'Por favor, carga un archivo de video válido.',
                ]);

                // Validar el archivo
                $errors = $validator->validate($file, $fileConstraint);

                if (count($errors) > 0) {
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Uploaded file is not a valid video file (MP4 extension).']
                    ];
                } else {
                    $fileName = date('YYYY-mm-dd') . time() . '.' . $file->guessExtension();
                    try {
                        $file->move($this->getParameter('images_directory') . '/course/video/', $fileName);
                        $course->setVideoPath($fileName);

                        $this->courseRepository->save($course, true);
                        $return = [
                            'code' => '200',
                            'status' => 'success',
                            'messages' => ['Video saved successfully'],
                            'course'    => $course->getDataInArray()
                        ];
                    } catch (FileException $e) {
                        $return = [
                            'code' => '400',
                            'status' => 'error',
                            'messages' => ['Video not saved', $e]
                        ];
                    }
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


    #[Route('/course/video/{id}', methods: ['GET'])]
    public function getCourseVideo($id)
    {

        $course = $this->courseRepository->find($id);
        $path = $this->getParameter('images_directory') . '/course/video/' . $course->getVideoPath();
        $response = new BinaryFileResponse($path);

        return $response;
    }
   
}
