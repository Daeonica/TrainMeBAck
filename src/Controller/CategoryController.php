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

    #[Route('/category/get-by-id/{id}', methods: ['GET'])]
    public function getCategoryById($id, Request $request)
    {
        $category = $this->categoryRepository->find($id);

        return new JsonResponse($category->getDataInArray());
    }


    #[Route('/category/get', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $categoriesJson = $this->categoryRepository->findAll();
        $categories = [];

        foreach ($categoriesJson as $category) {
            $categories[] = $category->getDataInArray();
        }



        return new JsonResponse($categories);
    }

    #[Route('/category/update', methods: ['PUT'])]
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



    #[Route('/category/add', methods: ['POST'])]

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
                $return['messages'][] = 'The name is not valid';
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
                $return['messages'][] = 'Category saved successfully';
            } else {
                $return['messages'][] = 'The category is not saved';
            }
        }

        return new JsonResponse($return);
    }

    #[Route('/category/delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        //recibimos los datos en un json
        $json = $request->get('data', null);
        $return = [];
        if ($json != null) {
            //transformamos los datos a array
            $array = json_decode($json, true);
            $category = $this->categoryRepository->find($array['id']);
            if ($category != null) {
                $this->categoryRepository->remove($category, true);
                $return = [
                    "code" => '200',
                    "status" => 'success',
                ];
                $return['messages'][] = 'The category has been deleted successfully';
            } else {
                $return = [
                    "code" => '400',
                    "status" => 'error',
                ];
                $return['messages'][] = 'The category not found';
            }
        } else {
            //si los datos recibidos estan vacios
            $return['code'] = '400';
            $return['status'] = 'error';
            $return['messages'][] = 'Data is empty';
        }
        return new JsonResponse($return);
    }
}
