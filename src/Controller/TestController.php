<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Category;
use App\Entity\Review;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\ReviewRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{

    public function __construct(private ReviewRepository $reviewRepository, private CourseRepository $courseRepository, private  UserRepository $userRepository, private  CategoryRepository $categoryRepository, private RoleRepository $roleRepository)
    {
    }

    /**
     * Endpoint para crear datos de testeo de aplicación.
     * 
     * F
     */

    #[Route('/test-data', methods: ['POST'])]
    public function addTestingData()
    {
        $admin      = $this->roleRepository->findOneBy(['key_value' => 'admin']);
        $trainer    = $this->roleRepository->findOneBy(['key_value' => 'trainer']);
        $customer   = $this->roleRepository->findOneBy(['key_value' => 'customer']);
        $return     = [];

        if (!$admin) {
            $admin = new Role;
            $admin->setName('admin');
            $admin->setKeyValue('admin');
            $this->roleRepository->save($admin, true);
            $return['messages']['roles'][] = 'Admin role created';
        } else {
            $return['messages']['roles'][] = 'Admin role already exists';
        }

        if (!$trainer) {
            $trainer = new Role;
            $trainer->setName('trainer');
            $trainer->setKeyValue('trainer');
            $this->roleRepository->save($trainer, true);
            $return['messages']['roles'][] = 'Trainer role created';
        } else {
            $return['messages']['roles'][] = 'Trainer role already exists';
        }

        if (!$customer) {
            $customer = new Role;
            $customer->setName('customer');
            $customer->setKeyValue('customer');
            $this->roleRepository->save($customer, true);
            $return['messages']['roles'][] = 'Customer role created';
        } else {
            $return['messages']['roles'][] = 'Customer role already exists';
        }

        $adminUser   = $this->userRepository->findOneBy(['email' => 'admin@user.com']);
        $trainerUser    = $this->userRepository->findOneBy(['email' => 'trainer@user.com']);
        $customerUser   = $this->userRepository->findOneBy(['email' => 'customer@user.com']);

        $password = password_hash('admin123', PASSWORD_BCRYPT);


        if (!$customerUser) {
            $customerUser = new User;
            $customerUser->setName('customer');
            $customerUser->setSurname('customer');
            $customerUser->setDescription('customer');
            $customerUser->setEmail('customer@user.com');
            $customerUser->setPassword($password);
            $customerUser->setRole($customer);
            $customerUser->setRegisterDate(new \DateTime);

            $this->userRepository->save($customerUser, true);
            $return['messages']['users'][] = 'Customer user created';
        } else {
            $return['messages']['users'][] = 'Customer user already exists';
        }

        if (!$adminUser) {
            $adminUser = new User;
            $adminUser->setName('admin');
            $adminUser->setSurname('admin');
            $adminUser->setDescription('admin');
            $adminUser->setEmail('admin@user.com');
            $adminUser->setPassword($password);
            $adminUser->setRole($admin);
            $adminUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($adminUser, true);
            $return['messages']['users'][] = 'Admin user created';
        } else {
            $return['messages']['users'][] = 'Admin user already exists';
        }

        if (!$trainerUser) {
            $trainerUser = new User;
            $trainerUser->setName('trainer');
            $trainerUser->setSurname('trainer');
            $trainerUser->setDescription('trainer');
            $trainerUser->setEmail('trainer@user.com');
            $trainerUser->setPassword($password);
            $trainerUser->setRole($trainer);
            $trainerUser->setPassword($password);
            $trainerUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($trainerUser, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }


        $nutritionCategory      = $this->categoryRepository->findOneBy(['name' => 'nutrition']);
        $crossFitCategory       = $this->categoryRepository->findOneBy(['name' => 'crossfit']);
        $powerliftingCategory   = $this->categoryRepository->findOneBy(['name' => 'powerlifting']);

        if (!$nutritionCategory) {
            $nutritionCategory = new Category;
            $nutritionCategory->setName('nutrition');
            $nutritionCategory->setDescription('nutrition');
            $this->categoryRepository->save($nutritionCategory, true);
            $return['messages']['categories'][] = 'Nutrition category created';
        } else {
            $return['messages']['categories'][] = 'Nutrition category already exists';
        }

        if (!$crossFitCategory) {
            $crossFitCategory = new Category;
            $crossFitCategory->setName('crossfit');
            $crossFitCategory->setDescription('crossfit');
            $this->categoryRepository->save($crossFitCategory, true);
            $return['messages']['categories'][] = 'Crossfit category created';
        } else {
            $return['messages']['categories'][] = 'Crossfit category already exists';
        }

        if (!$powerliftingCategory) {
            $powerliftingCategory = new Category;
            $powerliftingCategory->setName('powerlifting');
            $powerliftingCategory->setDescription('powerlifting');
            $this->categoryRepository->save($powerliftingCategory, true);
            $return['messages']['categories'][] = 'Powerlifitng category created';
        } else {
            $return['messages']['categories'][] = 'Powerlifitng category already exists';
        }

        $nutritionCourse        = $this->courseRepository->findOneBy(['name' => 'nutrition']);
        $crossFitCourse         = $this->courseRepository->findOneBy(['name' => 'crossfit']);
        $powerliftingCourse     = $this->courseRepository->findOneBy(['name' => 'powerlifting']);


        if (!$nutritionCourse) {
            $nutritionCourse = new Course;
            $nutritionCourse->setName('nutrition');
            $nutritionCourse->setDescription('nutrition');
            $nutritionCourse->setPrice(45);
            $nutritionCourse->setUser($trainerUser);
            $nutritionCourse->setCategory($nutritionCategory);
            $this->courseRepository->save($nutritionCourse, true);
            $return['messages']['courses'][] = 'Nutrition course created';
        } else {
            $return['messages']['courses'][] = 'Nutrition course already exists';
        }

        $review = new Review;
        $review->setUser($customerUser);
        $review->setCourse($nutritionCourse);
        $review->setComment('El curso está bien');
        $review->setReviewDate(new \DateTime);
        $this->reviewRepository->save($review, true);

        $review = new Review;
        $review->setUser($adminUser);
        $review->setCourse($nutritionCourse);
        $review->setComment('El curso está bien');
        $review->setReviewDate(new \DateTime);
        $this->reviewRepository->save($review, true);



        if (!$crossFitCourse) {
            $crossFitCourse = new Course;
            $crossFitCourse->setName('crossfit');
            $crossFitCourse->setDescription('crossfit');
            $crossFitCourse->setPrice(45);
            $crossFitCourse->setUser($trainerUser);
            $crossFitCourse->setCategory($crossFitCategory);
            $this->courseRepository->save($crossFitCourse, true);
            $return['messages']['courses'][] = 'Crossfit course created';
        } else {
            $return['messages']['courses'][] = 'Crossfit course already exists';
        }

        if (!$powerliftingCourse) {
            $powerliftingCourse = new Course;
            $powerliftingCourse->setName('powerlifting');
            $powerliftingCourse->setDescription('powerlifting');
            $powerliftingCourse->setPrice(45);
            $powerliftingCourse->setUser($trainerUser);
            $powerliftingCourse->setCategory($powerliftingCategory);
            $this->courseRepository->save($powerliftingCourse, true);
            $return['messages']['courses'][] = 'Powerlifitng course created';
        } else {
            $return['messages']['courses'][] = 'Powerlifitng course already exists';
        }

        return new JsonResponse($return);
    }
}
