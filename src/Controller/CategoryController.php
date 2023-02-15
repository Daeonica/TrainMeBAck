<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    #[Route('/category/get-by-id/{id}', name: 'category.get_by_id', methods: ['GET'])]
    public function getCategoryById($id, Request $request)
    {
        $category = $this->categoryRepository->find($id);

        return new JsonResponse($category->getDataInArray());
    }


    #[Route('/category/get', name: 'get_category', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $categoriesJson = $this->categoryRepository->findAll();
        $categories = [];

        foreach ($categoriesJson as $category) {
            $categories[] = $category->getDataInArray();
        }



        return new JsonResponse($categories);
    }

    #[Route('/category/update', name: 'set_category', methods: ['PUT'])]
    public function setCategory(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $data = json_decode($json, true);
        $return = [];

        if ($data != null) {
            if (!empty($data['name']) && !empty($data['description']) && !empty($data['id'])) {
                $category = $this->categoryRepository->find($data['id']);

                if ($category != null) {
                    $category->setName($data['name']);
                    $category->setDescription($data['description']);
                    $this->categoryRepository->save($category, true);
                    $return = [
                        'code' => '200',
                        'status' => 'success',
                        'category' => $category->getDataInArray(),
                        'messages' => ['Category updated successfully']
                    ];
                }else{
                    $return = [
                        'code' => '400',
                        'status' => 'error',
                        'messages' => ['Category not found']
                    ];
                }
            } else {
                $return['code'] = '400';
                $return['status'] = 'error';

                if (empty($data['id'])) {
                    $return['messages'][] = "Id not received";
                }

                if (empty($data['name'])) {
                    $return['messages'][] = "Name not received";
                }

                if (empty($data['description'])) {
                    $return['messages'][] = "Description not received";
                }
            }
        }

        return new JsonResponse($return);
    }



    #[Route('/category/add', name: 'add_category', methods: ['POST'])]

    public function setCategories(Request $request): JsonResponse
    {
        $json = $request->get('data', null);
        $return = [];

        if ($json != null) {
            $array = json_decode($json, true);
            $name = $array['name'];
            $description = $array['description'];

            $category = new Category;

            if (!empty($name)) {
                $category->setName($name);
                $return = [
                    'code' => 200,
                    'status' => 'success'
                ];
            } else {
                $return['messages'][] = 'El nombre no es válido';
            }

            if (!empty($description)) {
                $category->setDescription($description);
                $return = [
                    'code' => 200,
                    'status' => 'success'
                ];
            }

            if ($return['code'] == 200) {
                $this->categoryRepository->save($category, true);
                $return['category'] = $category->getDataInArray();
            } else {
                $return['messages'][] = 'El usuario no se ha guardado';
            }
        }

        return new JsonResponse($return);
    }
}