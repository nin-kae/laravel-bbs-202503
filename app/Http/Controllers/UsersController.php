<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
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
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户个人中心页面
     *
     * @param UserRequest $request
     * @param User $user
     * @return RedirectResponse
     * @throws ValidationException
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
        $data = $request->all();

        if ($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);
            if ($result === false) {
                return redirect()->back()->withErrors('Image upload failed. Please try again.');
            }
            $data['avatar'] = $result['path'];
        }

        $user->update($data);
        return redirect()->route('users.show', $user)->with('success', 'Profile updated successfully.');
    }
}
