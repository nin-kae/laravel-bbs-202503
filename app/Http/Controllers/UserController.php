<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Except for the 'show' method, all other methods in this controller require authentication.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * 显示用户个人中心页面
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * 显示用户编辑页面
     *
     * @param User $user
     * @return View
     * @throws AuthorizationException
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户个人中心页面
     *
     * @param UserRequest $request
     * @param ImageUploadHandler $uploader
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user): RedirectResponse
    {
//        $this->authorize($request, [
//            'name' => 'required|max:255',
//            'introduction' => 'nullable|max:200',
//            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
//        ]);
//
//        $user->update($request->only(['name', 'introduction', 'avatar']));
//
//        if ($request->hasFile('avatar')) {
//            $user->updateAvatar(request()->file('avatar'));
//        }
        $this->authorize('update', $user);
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);
            if ($result === false) {
                return redirect()->back()->withErrors('Image upload failed. Please try again.');
            }
            $data['avatar'] = $result['path'];
        }

        $user->update($data);
        return redirect()->route('users.show', $user)->with('success', 'Profile updated successfully.');
    }

    public function impersonateUser(int $id, Request $request): RedirectResponse
    {
        if (!auth()->user() || !app()->isLocal())  {
            abort(403, 'Unauthorized action.');
        }

        $user = User::find($id);
        if ($user) {
            auth()->user()->impersonate($user);
            // 获取传递过来的重定向 URL，如果没有则默认重定向到首页
            $redirectTo = $request->input('redirect_to', '/');
            return redirect($redirectTo);
        }

        return redirect()->back()->with('error', 'User not found.');
    }
}
