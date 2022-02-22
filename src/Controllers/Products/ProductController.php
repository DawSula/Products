<?php

declare(strict_types=1);

namespace App\src\Controllers\Products;

use App\src\Controllers\Controller;
use App\src\Model\Product\CategoryModel;
use App\src\Model\Product\ProductModel;


class ProductController extends Controller
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;


    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function list()
    {
        $products = $this->productModel->getAll();

        $this->twig->addGlobal('_get', $_GET);
        echo $this->twig->render('Products/list.html', ['products' => $products]);
    }

    public function add()
    {
        $categories = $this->categoryModel->list();

        $this->twig->addGlobal('_get', $_GET);
        echo $this->twig->render('Products/add.html', ['categories' => $categories]);
    }

    public function show(int $id)
    {
        $product = $this->productModel->get($id);

        echo $this->twig->render('Products/show.html', ['product' => $product]);
    }

    public function edit(int $id)
    {
        $product = $this->productModel->get($id);

        $categories = $this->categoryModel->list();

        $this->twig->addGlobal('_get', $_GET);
        echo $this->twig->render('Products/edit.html', ['product' => $product, 'categories' => $categories]);
    }

    public function delete()
    {
        $productId = (int)$this->request->getParams()['id'];

        $this->productModel->deleteProduct($productId);

        self::redirect('/', ['before' => 'deleted']);
    }

    public function create()
    {
        $params = $this->request->getParams();

        $path = '/addProduct';

        if (!$this->validator->addProductValidate($params, $path)) {
            self::redirect($path, ['before' => 'dataError']);
        }

        $this->productModel->addProduct($params);

        self::redirect('/', ['before' => 'created']);
    }

    public function update()
    {
        $params = $this->request->getParams();

        $path = "/edit/{$params['productId']}";

        if (!$this->validator->addProductValidate($params, $path)) {
            self::redirect($path, ['before' => 'dataError']);
        }

        $this->productModel->updateProduct($params);

        self::redirect('/', ['before' => 'updated']);
    }
}