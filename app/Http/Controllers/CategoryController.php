<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-category'))
        {
            $categories = Category::get();

            return view('admin.category.index', compact('categories'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();
        if($user->can('create-category'))
        {
            return view('admin.category.create');
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::user();
        if($user->can('create-category'))
        {
            $validation = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'color' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ];
            $request->validate($validation);

            $post = [
                'name' => $request->name,
                'color' => $request->color,
                // 'color' => '#' . $request->color,
            ];

            Category::create($post);

            return redirect()->route('admin.category')->with('success', __('Category created successfully'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userObj = \Auth::user();
        if($userObj->can('edit-category'))
        {
            $category = Category::find($id);

            return view('admin.category.edit', compact('category'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userObj = \Auth::user();
        if($userObj->can('edit-category'))
        {
            $category        = Category::find($id);
            $category->name  = $request->name;
            $category->color = $request->color;
            // $category->color = '#' . $request->color;
            $category->save();

            return redirect()->route('admin.category')->with('success', __('Category updated successfully'));
        }
        else
        {
            return view('403');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \Auth::user();
        if($user->can('delete-category'))
        {
            $category = Category::find($id);
            $category->delete();

            return redirect()->route('admin.category')->with('success', __('Category deleted successfully'));
        }
        else
        {
            return view('403');
        }
    }
}
