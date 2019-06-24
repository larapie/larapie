<?php

namespace App\Modules\User\Http\Controllers\Api;

use App\Modules\User\Models\User;
use App\Modules\User\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapie\Core\Base\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index()
    {
        return UserTransformer::collection(User::all());
    }

    /**
     * Show the specified resource.
     *
     * @return UserTransformer
     */
    public function show()
    {
        return UserTransformer::resource(Auth::user());
    }

    /**
     * Update a user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $success = User::update($id, $request->all());

        if ($success)
            return response()->json('success');

        return response()->json('failed')->setStatusCode(500);
    }

    /**
     * delete a user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(Request $request, $id)
    {
        $success = User::destroy($id);

        if ($success)
            return response()->json('success');

        return response()->json('failed')->setStatusCode(500);
    }
}
