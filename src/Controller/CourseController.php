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

    /**
     * EXPLICACIÓN PARA DESARROLADORES:
     * 
     * He cambiado la estructura de las consultas.
     * Cuando queremos buscar un curso, suele ser algo jerárquico.
     * Es decir, tu objetivo es buscar cursos a través de una palabra.
     * La idea es buscar primero en la tabla curso, y luego en tablas que tengan relación con la tabla cursos (usuario, categoría).
     * ········································
     * ········································
     * ····                                ····
     * ····    ADJUNTO EXPLICACIÓN         ····
     * ····    PASO POR PASO DE LO         ····
     * ····    QUE SE HA HECHO PARA        ····
     * ····    QUE LA FUNCIONALIDAD        ····
     * ····    DE BÚSQUEDA SEA LA ÓPTIMA   ····
     * ····                                ····
     * ········································
     * ········································
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * PRIMER PASO:                                                                                    -------------------------------------------------------------
     *                                                                                                 -------------------------------------------------------------
     * Primero buscas si existe un curso en la tabla curso con el nombre, si hay cursos se guardan.    -------------------------------------------------------------
     *                                                                                                 -------------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * SEGUNDO PASO:                                                                                                                           ---------------------
     *                                                                                                                                         ---------------------
     * Después la siguiente tabla a buscar debería ser categoría con el nombre que se recibe a través de la query (también podría ser usuario) ---------------------
     * y ver cursos de esa categoría. Ahora debemos de validar si en el array principal donde hemos guardados los cursos en el primer paso     --------------------- 
     * ya está guardado algún curso de los que recibimos a través de las categorías.                                                           ---------------------
     * Si no lo está, se guardará.                                                                                                             ---------------------
     *                                                                                                                                         ---------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * TERCER PASO:                                                                                                         ----------------------------------------
     *                                                                                                                      ----------------------------------------
     * Lo último nos quedaría por buscar usuarios a través del nombre que se recibe a través de la query,                   ----------------------------------------
     * se cogen los ussarios y se recorren sus cursos, y volvemos a hacer los mismo,                                        ----------------------------------------
     * si el curso que se recibe a través del usuario ya esá guardado en el array principal de cursos, no se añadirá.       ----------------------------------------
     * En caso contrario, lo guardará en el array principal.                                                                ----------------------------------------
     *                                                                                                                      ----------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------------------------------------------
     */

    #[Route('/course/search/{query}', name: 'course.search', methods: ['POST'])]
    public function search($query)
    {
        $all_courses    = [];
        $return         = [];
        $courses        = $this->courseRepository->findBetween($query);
        $categories     = $this->categoryRepository->findBy(['name' => $query]);
        $users          = $this->userRepository->findBetween($query);

        // Recogemos y guardamos todo lo que encuentre a través de la tabla cursos:
        foreach ($courses as $course) {
            $all_courses[] = $course;
        };

        // Recogemos y guardamos categorías con el nombre que reciba en la query:
        foreach ($categories as $category) {
            // De cada categoría, recorremos sus cursos:
            foreach ($category->getCourses() as $course) {
                // Si en el array de cursos principal está vacío, que guarde directamente el curso
                if (empty($all_courses)) {
                    $all_courses[] = $course;
                } else {
                    // Sino, estamos recorriendo los cursos guardados en el array principal.
                    // Comprobamos si los cursos guardados en el array principal y los cursos recibidos a través de las categorías son las mismas
                    // Únicamente, se guardará el curso recibido por categorías si cuando acaba de recorrer todo el array, se valida que ya no exista en el array general
                    $saved = false;
                    foreach ($all_courses as $all_course) {
                        if ($all_course->getId() == $course->getId()) {
                            $saved = true;
                        }
                    }

                    // Aquí cuando acaba de recorrer todos los cursos guardados en el array principal ve que sigue siendo falso, se guardará en el array

                    if ($saved == false) {
                        $all_courses[] = $course;
                    }
                }
            }
        };

        // Cogemos usuarios que tengan en común con el nombre que recibimos en la query
        foreach ($users as $user) {
            // De cada usuario cogemos array de cursos y la recorremos
            foreach ($user->getCourses() as $course) {
                // Si está vacío que las añada
                if (empty($all_courses)) {
                    $all_courses[] = $course;
                } else {
                    // Sino tenemos que comprobar si el curso recibido del usuario ya está guardado en el array general de cursos
                    $saved = false;
                    foreach ($all_courses as $all_course) {
                        if ($all_course->getId() == $course->getId()) {
                            $saved = true;
                        }
                    }

                    // Si no está guardado en el array general de cursos, se guardará
                    if ($saved == false) {
                        $all_courses[] = $course;
                    }
                }
            }
        }

        foreach ($all_courses as $all_course) {
            // Aquí simpremente estoy limpiando de datos innecesarios para que la respuesta sea más limpia
            $data = $all_course->getDataInArray();
            unset($data['buy_user_courses']);
            $return[] = $data;
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
                    if ($user->getRole()->getKeyValue() == 'trainer') {
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
                            'messages' => ['Course created successfully']
                        ];
                    }else{
                        $return = [
                            'status' => 'error',
                            'code' => 400,
                            'messages' => ['User not have permission to create course']
                        ];
                    }
                } else {
                    $return['code'] = '400';
                    $return['status'] = 'error';
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
                    'messages' => ['Data empty']
                ];
            }
        } else {
            $return = [
                'status' => 'error',
                'code' => 400,
                'messages' => ['Json empty']
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
