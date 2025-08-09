<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = new Category();
            $categories = $categories->index()->get();

            return $this->successResponse(CategoryResource::collection($categories), 'Categories fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function create(CategoryRequest $request){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $category = new Category();
            $category->name = $body['name'];
            $category->save();
            DB::commit();

            return $this->successResponse(new CategoryResource($category), 'Category created successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
