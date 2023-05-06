<?php

namespace App\Controller;

use App\Entity\BuyUserCourse;
use App\Entity\Course;
use App\Entity\Category;
use App\Entity\Review;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\BuyUserCourseRepository;
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

    public function __construct(private BuyUserCourseRepository $buyUserCourseRepository, private ReviewRepository $reviewRepository, private CourseRepository $courseRepository, private  UserRepository $userRepository, private  CategoryRepository $categoryRepository, private RoleRepository $roleRepository)
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

        $sergioTrainer    = $this->userRepository->findOneBy(['email' => 'trainer@user.com']);
        $davidTrainer    = $this->userRepository->findOneBy(['email' => 'trainer1@user.com']);
        $yerayTrainer    = $this->userRepository->findOneBy(['email' => 'trainer2@user.com']);

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

        if (!$sergioTrainer) {
            $sergioTrainer = new User;
            $sergioTrainer->setName('Sergio');
            $sergioTrainer->setSurname('Peinado');
            $sergioTrainer->setDescription('Entrenador licenciado en ciencias del deporte. Especialista en pérdida de grasa');
            $sergioTrainer->setEmail('sergio@trainer.com');
            $sergioTrainer->setPassword($password);
            $sergioTrainer->setRole($trainer);
            $sergioTrainer->setPassword($password);
            $sergioTrainer->setImgPath('sergio-peinado.jpg');
            $sergioTrainer->setRegisterDate(new \DateTime);
            $this->userRepository->save($sergioTrainer, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }

        if (!$davidTrainer) {
            $davidTrainer = new User;
            $davidTrainer->setName('David');
            $davidTrainer->setSurname('Marchante');
            $davidTrainer->setDescription('Master en entrenamiento de fuerza y powerlifting. Record del mundo de la dominada más pesada del mundo');
            $davidTrainer->setEmail('david@trainer.com');
            $davidTrainer->setPassword($password);
            $davidTrainer->setRole($trainer);
            $davidTrainer->setPassword($password);
            $davidTrainer->setImgPath('david-marchante.jpg');
            $davidTrainer->setRegisterDate(new \DateTime);
            $this->userRepository->save($davidTrainer, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }

        if (!$yerayTrainer) {
            $yerayTrainer = new User;
            $yerayTrainer->setName('Yerai');
            $yerayTrainer->setSurname('Alonso');
            $yerayTrainer->setDescription('Entrenador personal certificado por la NCSA y especialista en calistenia');
            $yerayTrainer->setEmail('yerai@trainer.com');
            $yerayTrainer->setPassword($password);
            $yerayTrainer->setRole($trainer);
            $yerayTrainer->setPassword($password);
            $yerayTrainer->setImgPath('yerai-alonso.jpg');
            $yerayTrainer->setRegisterDate(new \DateTime);
            $this->userRepository->save($yerayTrainer, true);
            $return['messages']['users'][] = 'Trainer user created';
        } else {
            $return['messages']['users'][] = 'Trainer user already exists';
        }




        $lifeStyleCategory      = $this->categoryRepository->findOneBy(['name' => 'Vida saludable']);
        $runningCategory       = $this->categoryRepository->findOneBy(['name' => 'Running']);
        $sportCategory   = $this->categoryRepository->findOneBy(['name' => 'Sport']);

        if (!$lifeStyleCategory) {
            $lifeStyleCategory = new Category;
            $lifeStyleCategory->setName('Vida saludable');
            $lifeStyleCategory->setDescription('Life style category for healthy life style');
            $lifeStyleCategory->setImgPath('vida-saludable.jpg');
            $this->categoryRepository->save($lifeStyleCategory, true);
            $return['messages']['categories'][] = 'Life style category created';
        } else {
            $return['messages']['categories'][] = 'Life style category already exists';
        }

        if (!$runningCategory) {
            $runningCategory = new Category;
            $runningCategory->setName('Running');
            $runningCategory->setDescription('Running sport is a good way to keep fit, and it is also a way to socialize with other people. ');
            $runningCategory->setImgPath('running.png');
            $this->categoryRepository->save($runningCategory, true);
            $return['messages']['categories'][] = 'Running category created';
        } else {
            $return['messages']['categories'][] = 'Running category already exists';
        }

        if (!$sportCategory) {
            $sportCategory = new Category;
            $sportCategory->setName('Sport');
            $sportCategory->setDescription('Go to the gym and do some sport is a good way to keep fit, and it is also a way to socialize with other people. There are many different sports, and each sport has its own benefits and risks. Some sports are individual sports, such as running, swimming, cycling, and others are group sports, such as football, basketball, and rugby.');
            $sportCategory->setImgPath('deporte.png');
            $this->categoryRepository->save($sportCategory, true);
            $return['messages']['categories'][] = 'Sport category created';
        } else {
            $return['messages']['categories'][] = 'Sport category already exists';
        }

        $adidasCourse               = $this->courseRepository->findOneBy(['name' => 'Start to run']);
        $correrParaPararCourse      = $this->courseRepository->findOneBy(['name' => 'Improve your running']);
        $gimnasioCourse             = $this->courseRepository->findOneBy(['name' => 'Improve your gym routine with this exercises']);
        $herbalifeCourse            = $this->courseRepository->findOneBy(['name' => 'Basics of nutrition']);
        $nikeCourse                 = $this->courseRepository->findOneBy(['name' => 'Maraton preparation']);
        $runningCourse              = $this->courseRepository->findOneBy(['name' => 'Your first 5k']);
        $personalObjectivesCourse   = $this->courseRepository->findOneBy(['name' => 'Give it all in the weights and gain strength']);
        $lifeStyleCourse            = $this->courseRepository->findOneBy(['name' => 'Basics for a healthy life style']);

        if (!$adidasCourse) {
            $adidasCourse = new Course;
            $adidasCourse->setName('Start to run');
            $adidasCourse->setDescription('Start to run with this course, you will learn the basics of running. You will learn how to run, how to breath, how to warm up, how to stretch, and how to avoid injuries.');
            $adidasCourse->setImgPath('adidas.jpg');
            $adidasCourse->setPrice('20');
            $adidasCourse->setVideoPath('adidas.mp4');
            $adidasCourse->setDocumentRoot('adidas.pdf');
            $adidasCourse->setCategory($runningCategory);
            $adidasCourse->setUser($sergioTrainer);
            $this->courseRepository->save($adidasCourse, true);
            $return['messages']['courses'][] = 'Adidas course created';
        } else {
            $return['messages']['courses'][] = 'Adidas course already exists';
        }

        if (!$correrParaPararCourse) {
            $correrParaPararCourse = new Course;
            $correrParaPararCourse->setName('Improve your running');
            $correrParaPararCourse->setDescription('Improve your running with this course, you will learn how to run faster, how to run longer, how to breath, how to warm up, how to stretch, and how to avoid injuries.');
            $correrParaPararCourse->setImgPath('correr-para-parar.jpg');
            $correrParaPararCourse->setVideoPath('correr-para-parar.mp4');
            $correrParaPararCourse->setPrice('35');
            $correrParaPararCourse->setDocumentRoot('correr-para-parar.pdf');
            $correrParaPararCourse->setCategory($runningCategory);
            $correrParaPararCourse->setUser($sergioTrainer);
            $this->courseRepository->save($correrParaPararCourse, true);
            $return['messages']['courses'][] = 'Correr para parar course created';
        } else {
            $return['messages']['courses'][] = 'Correr para parar course already exists';
        }

        if (!$gimnasioCourse) {
            $gimnasioCourse = new Course;
            $gimnasioCourse->setName('Improve your gym routine with this exercises');
            $gimnasioCourse->setDescription('Improve your gym routine with this exercises, you will learn how to do the most common exercises in the gym, how to do them correctly, and how to avoid injuries.');
            $gimnasioCourse->setImgPath('gimnasio.jpg');
            $gimnasioCourse->setVideoPath('gimnasio.mp4');
            $gimnasioCourse->setPrice('39');
            $gimnasioCourse->setDocumentRoot('gimnasio.pdf');
            $gimnasioCourse->setCategory($sportCategory);
            $gimnasioCourse->setUser($davidTrainer);
            $this->courseRepository->save($gimnasioCourse, true);
            $return['messages']['courses'][] = 'Gimnasio course created';
        } else {
            $return['messages']['courses'][] = 'Gimnasio course already exists';
        }

        if (!$herbalifeCourse) {
            $herbalifeCourse = new Course;
            $herbalifeCourse->setName('Basics of nutrition');
            $herbalifeCourse->setDescription('Basics of nutrition, you will learn the basics of nutrition, how to eat healthy, how to eat to gain muscle, how to eat to lose weight, and how to eat to maintain your weight.');
            $herbalifeCourse->setImgPath('herbalife.jpg');
            $herbalifeCourse->setVideoPath('herbalife.mp4');
            $herbalifeCourse->setDocumentRoot('herbalife.pdf');
            $herbalifeCourse->setPrice('15');
            $herbalifeCourse->setCategory($lifeStyleCategory);
            $herbalifeCourse->setUser($davidTrainer);
            $this->courseRepository->save($herbalifeCourse, true);
            $return['messages']['courses'][] = 'Herbalife course created';
        } else {
            $return['messages']['courses'][] = 'Herbalife course already exists';
        }

        if (!$nikeCourse) {
            $nikeCourse = new Course;
            $nikeCourse->setName('Maraton preparation');
            $nikeCourse->setDescription('Maraton preparation, you will learn how to prepare a maraton, how to train for a maraton, how to eat, how to rest, and how to avoid injuries.');
            $nikeCourse->setImgPath('nike.png');
            $nikeCourse->setVideoPath('nike.mp4');
            $nikeCourse->setDocumentRoot('nike.pdf');
            $nikeCourse->setCategory($runningCategory);
            $nikeCourse->setPrice('18');
            $nikeCourse->setUser($yerayTrainer);
            $this->courseRepository->save($nikeCourse, true);
            $return['messages']['courses'][] = 'Nike course created';
        } else {
            $return['messages']['courses'][] = 'Nike course already exists';
        }

        if (!$runningCourse) {
            $runningCourse = new Course;
            $runningCourse->setName('Your first 5k');
            $runningCourse->setDescription('Your first 5k, you will learn how to prepare your first 5k, how to train for a 5k, how to eat, how to rest, and how to avoid injuries.');
            $runningCourse->setImgPath('running.jpg');
            $runningCourse->setVideoPath('running.mp4');
            $runningCourse->setPrice('32');
            $runningCourse->setDocumentRoot('running.pdf');
            $runningCourse->setCategory($runningCategory);
            $runningCourse->setUser($yerayTrainer);
            $this->courseRepository->save($runningCourse, true);
            $return['messages']['courses'][] = 'Running course created';
        } else {
            $return['messages']['courses'][] = 'Running course already exists';
        }

        if (!$personalObjectivesCourse) {
            $personalObjectivesCourse = new Course;
            $personalObjectivesCourse->setName('Give it all in the weights and gain strength');
            $personalObjectivesCourse->setDescription('Give it all in the weights and gain strength, you will learn how to train with weights, how to train to gain strength, how to eat, how to rest, and how to avoid injuries.');
            $personalObjectivesCourse->setVideoPath('tomalo-personal.mp4');
            $personalObjectivesCourse->setDocumentRoot('tomalo-personal.pdf');
            $personalObjectivesCourse->setImgPath('tomalo-personal.jpg');
            $personalObjectivesCourse->setPrice('55');
            $personalObjectivesCourse->setCategory($sportCategory);
            $personalObjectivesCourse->setUser($davidTrainer);
            $this->courseRepository->save($personalObjectivesCourse, true);
            $return['messages']['courses'][] = 'Personal objectives course created';
        } else {
            $return['messages']['courses'][] = 'Personal objectives course already exists';
        }

        if (!$lifeStyleCourse) {
            $lifeStyleCourse = new Course;
            $lifeStyleCourse->setName('Basics for a healthy life style');
            $lifeStyleCourse->setDescription('Basics for a healthy life style, you will learn the basics for a healthy life style, how to eat, how to rest, and how to avoid injuries.');
            $lifeStyleCourse->setImgPath('vida-saludable.jpg');
            $lifeStyleCourse->setVideoPath('vida-saludable.mp4');
            $lifeStyleCourse->setPrice('45');
            $lifeStyleCourse->setDocumentRoot('vida-saludable.pdf');
            $lifeStyleCourse->setCategory($lifeStyleCategory);
            $lifeStyleCourse->setUser($davidTrainer);
            $this->courseRepository->save($lifeStyleCourse, true);
            $return['messages']['courses'][] = 'Life style course created';
        } else {
            $return['messages']['courses'][] = 'Life style course already exists';
        }


        return new JsonResponse($return);
    }
}
