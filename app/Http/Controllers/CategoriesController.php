<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::pluck('name', 'id')->toArray();
        $data = [];
        foreach ($categories as $key => $category) {
            $data[] = ['id' => $key, 'name' => $category];
        }
        return $this->responseSuccess('OK', $data);
    }
}
