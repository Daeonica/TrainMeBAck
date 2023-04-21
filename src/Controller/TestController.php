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

        $daniUser   = $this->userRepository->findOneBy(['email' => 'dani@admin.com']);
        $afnanUser   = $this->userRepository->findOneBy(['email' => 'afnan@admin.com']);
        $pauUser   = $this->userRepository->findOneBy(['email' => 'pau@admin.com']);

        $trainerUser    = $this->userRepository->findOneBy(['email' => 'trainer@user.com']);
        $trainerUser1    = $this->userRepository->findOneBy(['email' => 'trainer1@user.com']);
        $trainerUser2    = $this->userRepository->findOneBy(['email' => 'trainer2@user.com']);

        $customerUser   = $this->userRepository->findOneBy(['email' => 'customer@user.com']);

        $password = password_hash('admin123', PASSWORD_BCRYPT);


        if (!$customerUser) {
            $customerUser = new User;
            $customerUser->setName('customer');
            $customerUser->setSurname('customer');
            $customerUser->setDescription('customer');
            $customerUser->setEmail('customer@user.com');
            $customerUser->setImgPath('chad.jpg');
            $customerUser->setPassword($password);
            $customerUser->setRole($customer);
            $customerUser->setRegisterDate(new \DateTime);

            $this->userRepository->save($customerUser, true);
            $return['messages']['users'][] = 'Customer user created';
        } else {
            $return['messages']['users'][] = 'Customer user already exists';
        }


        if (!$daniUser) {
            $daniUser = new User;
            $daniUser->setName('dani');
            $daniUser->setSurname('urbano');
            $daniUser->setDescription('Front-End developer Back-End helper, and Documentation manager');
            $daniUser->setEmail('dani@admin.com');
            $daniUser->setPassword($password);
            $daniUser->setRole($admin);
            $daniUser->setImgPath('dani.jpg');
            $daniUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($daniUser, true);
            $return['messages']['users'][] = 'Dani user created';
        } else {
            $return['messages']['users'][] = 'Dani user already exists';
        }

        if (!$afnanUser) {
            $afnanUser = new User;
            $afnanUser->setName('afnan');
            $afnanUser->setSurname('amin');
            $afnanUser->setDescription('Back-End PHP Specialist and Front-End helper.');
            $afnanUser->setEmail('afnan@admin.com');
            $afnanUser->setPassword($password);
            $afnanUser->setRole($admin);
            $afnanUser->setImgPath('afnan.jpg');
            $afnanUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($afnanUser, true);
            $return['messages']['users'][] = 'Afnan user created';
        } else {
            $return['messages']['users'][] = 'Afnan user already exists';
        }

        if (!$pauUser) {
            $pauUser = new User;
            $pauUser->setName('pau');
            $pauUser->setSurname('exposito');
            $pauUser->setDescription('UX/UI Designer and Project Manager.');
            $pauUser->setEmail('pau@admin.com');
            $pauUser->setPassword($password);
            $pauUser->setRole($admin);
            $pauUser->setImgPath('pau.jpg');
            $pauUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($pauUser, true);
            $return['messages']['users'][] = 'Admin user created';
        } else {
            $return['messages']['users'][] = 'Admin user already exists';
        }

        if (!$trainerUser) {
            $trainerUser = new User;
            $trainerUser->setName('Sergio');
            $trainerUser->setSurname('Peinado');
            $trainerUser->setDescription('Entrenador licenciado en ciencias del deporte. Especialista en pérdida de grasa');
            $trainerUser->setEmail('trainer@user.com');
            $trainerUser->setPassword($password);
            $trainerUser->setRole($trainer);
            $trainerUser->setPassword($password);
            $trainerUser->setImgPath('sergioPeinado.jpeg');
            $trainerUser->setRegisterDate(new \DateTime);
            $this->userRepository->save($trainerUser, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }

        if (!$trainerUser1) {
            $trainerUser1 = new User;
            $trainerUser1->setName('David');
            $trainerUser1->setSurname('Marchante');
            $trainerUser1->setDescription('Master en entrenamiento de fuerza y powerlifting. Record del mundo de la dominada más pesada del mundo');
            $trainerUser1->setEmail('trainer1@user.com');
            $trainerUser1->setPassword($password);
            $trainerUser1->setRole($trainer);
            $trainerUser1->setPassword($password);
            $trainerUser1->setImgPath('davidMarchante.jpeg');
            $trainerUser1->setRegisterDate(new \DateTime);
            $this->userRepository->save($trainerUser1, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }

        if (!$trainerUser2) {
            $trainerUser2 = new User;
            $trainerUser2->setName('Yerai');
            $trainerUser2->setSurname('Alonso');
            $trainerUser2->setDescription('Entrenador personal certificado por la NCSA y especialista en calistenia');
            $trainerUser2->setEmail('trainer2@user.com');
            $trainerUser2->setPassword($password);
            $trainerUser2->setRole($trainer);
            $trainerUser2->setPassword($password);
            $trainerUser2->setImgPath('yeraiAlonso.jpg');
            $trainerUser2->setRegisterDate(new \DateTime);
            $this->userRepository->save($trainerUser2, true);
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
            $nutritionCategory->setImgPath('nutricion.jpg');
            $this->categoryRepository->save($nutritionCategory, true);
            $return['messages']['categories'][] = 'Nutrition category created';
        } else {
            $return['messages']['categories'][] = 'Nutrition category already exists';
        }

        if (!$crossFitCategory) {
            $crossFitCategory = new Category;
            $crossFitCategory->setName('crossfit');
            $crossFitCategory->setDescription('crossfit');
            $crossFitCategory->setImgPath('crossfit.jpg');
            $this->categoryRepository->save($crossFitCategory, true);
            $return['messages']['categories'][] = 'Crossfit category created';
        } else {
            $return['messages']['categories'][] = 'Crossfit category already exists';
        }

        if (!$powerliftingCategory) {
            $powerliftingCategory = new Category;
            $powerliftingCategory->setName('powerlifting');
            $powerliftingCategory->setDescription('powerlifting');
            $powerliftingCategory->setImgPath('powerlifting.jpg');
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
            $nutritionCourse->setVideoPath('culturista-1.mp4');
            $nutritionCourse->setImgPath('nutritionCourse.jpg');
            $nutritionCourse->setDocumentRoot('nutricion.pdf');
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
        $review->setUser($daniUser);
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
            $crossFitCourse->setImgPath('crossfitCourse.jpg');
            $crossFitCourse->setVideoPath('culturista-2.mp4');
            $crossFitCourse->setDocumentRoot('crossfit.pdf');
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
            $powerliftingCourse->setImgPath('powerliftingCourse.jpg');
            $powerliftingCourse->setVideoPath('esguince.mp4');
            $powerliftingCourse->setDocumentRoot('powerlifting.pdf');
            $this->courseRepository->save($powerliftingCourse, true);
            $return['messages']['courses'][] = 'Powerlifitng course created';
        } else {
            $return['messages']['courses'][] = 'Powerlifitng course already exists';
        }

        return new JsonResponse($return);
    }
}
