<?php

namespace App\Controllers;

use App\Models\ProductModel;

class ProductController
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }


    public function getProducts($user)
    {
        return $this->model->getProducts($user);
    }

    public function getProduct($id)
    {
        return $this->model->getProduct($id);
    }

    public function newProduct() {
        $this->model->newProduct();
    }

    public function updateProduct() {
        $this->model->updateProduct();
    }

}