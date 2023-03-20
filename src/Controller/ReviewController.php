<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Review;
use App\Repository\BuyUserCourseRepository;
use App\Repository\CourseRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class ReviewController extends AbstractController
{

    public function __construct(private CourseRepository $courseRepository, private UserRepository $userRepository, private ReviewRepository $reviewRepository, private BuyUserCourseRepository $buyUserCourseRepository)
    {
    }

    #[Route('/course/reviews/{id}', methods: ['GET'])]
    public function courseReviews($id, Request $request): JsonResponse
    {
        $reviews = [];

        foreach ($this->reviewRepository->findBy(['course' => $id]) as $review) {
            $reviews[] = $review->getDataInArray();
        }

        return new JsonResponse($reviews);
    }

    #[Route('/course/create/review/{user_id}/{course_id}', methods: ['POST'])]
    public function createReview($user_id, $course_id, Request $request): JsonResponse
    {

        $json           = $request->get('data', null);
        $array          = json_decode($json, true);

        if ($json) {
            $course         = $this->courseRepository->find($course_id);
            $user           = $this->userRepository->find($user_id);
            if ($course && $user) {
                $purchased     = $this->buyUserCourseRepository->findOneBy(['user' => $user->getId(), 'course' => $course->getId()]);
                if ($purchased) {
                    $comment = $array['comment'];
                    if (!empty($array['comment'])) {
                        $review = new Review;
                        $review->setComment($comment);
                        $review->setUser($user);
                        $review->setCourse($course);
                        $review->setReviewDate(new \DateTime);
                        $this->reviewRepository->save($review, true);
                        $return = [
                            'status' => 'success',
                            'code' => 200,
                            'messages' => ['Review created successfully'],
                            'review' => $review->getDataInArray()
                        ];
                    } else {
                        $return = [
                            'status' => 'error',
                            'code' => 400,
                            'messages' => ['Comment is empty']
                        ];
                    }
                } else {
                    $return = [
                        'status' => 'error',
                        'code' => 400,
                        'messages' => ['Have to buy this course for upload your review']
                    ];
                }
            }else{
                $return = [
                    'status' => 'error',
                    'code' => 400,
                ];
                if (!$user) {
                    $return['messages'][] = 'User not exists';
                }
                if (!$course) {
                    $return['messages'][] = 'Course not exists';
                }
             
            }
        } else {
            $return = [
                'status' => 'error',
                'code' => 400,
                'messages' => ['Data not received']
            ];
        }



        return new JsonResponse($return);
    }


    #[Route('/course/review/delete/{id}', methods: ['DELETE'])]
    public function deleteReview($id, Request $request): JsonResponse
    {
        $this->reviewRepository->remove($this->reviewRepository->find($id),true);
        $return = [
            'status' => 'success',
            'code' => 200,
            'messages' => ['Review deleted successfully']
        ];
        return new JsonResponse($return);
    }
}
