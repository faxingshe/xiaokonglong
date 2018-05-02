<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Handlers\ImageUploadHandler;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
//        try {
//            $this->authorize('update', $user);
//        } catch (AuthorizationException $e) {
//            return redirect()->route('login');
//        }
        if (Auth::user()->cant('update',$user)){
            return redirect()->route('login');
        }
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        if (Auth::user()->cant('update',$user)){
            return redirect()->route('login');
        }
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }

}
