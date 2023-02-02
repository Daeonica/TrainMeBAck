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

    #[Route('/category/get', name: 'get_category', methods: ['POST'])]
    public function getCategories(): JsonResponse
    {
        $categoriesJson = $this->categoryRepository->findAll();
        $categories = [];
        $return = [];

        foreach ($categoriesJson as $category) {
            $categories[] = $category->getDataInArray();
        }

        if (!empty($categories)) {
            $return = [
                'code' => 200,
                'status' => 'success',
                'categories' => $categories
            ];
        } else {
            $return = [
                'code' => 400,
                'status' => 'error'
            ];

            $return['messages'][] = 'No hay categorías existentes';
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
