<?php

namespace App\Controller;

use App\Repository\BuyUserCourseRepository;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountController extends AbstractController
{

    public function __construct(private CourseRepository $courseRepository, private UserRepository $userRepository, private CategoryRepository $categoryRepository, private BuyUserCourseRepository $buyUserCourseRepository)
    {
    }

    #[Route('/count/trainer-courses/{trainer_id}')]

    public function countTrainerCourses($trainer_id)
    {
        $courses = $this->courseRepository->findBy(['trainer' => $trainer_id]);
        return $this->json(count($courses));
    }

    #[Route('/count/course-students/{course_id}')]
    public function countCourseStudents($course_id)
    {
        $students = $this->buyUserCourseRepository->findBy(['course' => $course_id]);
        return $this->json(count($students));
    }

    #[Route('/count/category-courses/{category_id}')]
    public function countCategoryCourses($category_id)
    {
        $courses = $this->courseRepository->findBy(['category' => $category_id]);
        return $this->json(count($courses));
    }

}
