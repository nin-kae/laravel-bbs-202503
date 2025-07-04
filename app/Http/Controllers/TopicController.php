<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Only authenticated users can create or edit and destroy topics.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Topic $topic
     * @param User $user
     * @param Link $link
     * @return View
     */
    public function index(Request $request, Topic $topic, User $user, Link $link): View
    {
        $topics = $topic->withOrder($request->order)
            ->with(['user', 'category'])
            ->paginate($this->perPage);

        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();

        return view('topics.index', compact('topics', 'active_users', 'links'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Topic $topic
     * @return View
     */
    public function create(Topic $topic): View
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTopicRequest $request
     * @param Topic $topic
     * @return RedirectResponse
     */
    public function store(StoreTopicRequest $request, Topic $topic): RedirectResponse
    {
        $topic->fill($request->validated());
        $topic->user()->associate($request->user());
        $topic->save();

        return redirect()->to($topic->link())->with('success', 'トピックが正常に作成されました。');
    }

    /**
     * Display the specified resource.
     * 如果主题有 slug，并且它与请求 slug 不匹配，则重定向到主题的链接。
     *
     * @param Topic $topic
     * @param ?null $slug
     * @return View|RedirectResponse
     */
    public function show(Topic $topic, $slug = null): View|RedirectResponse
    {
        if (!empty($topic->slug) && $topic->slug != rawurlencode($slug)) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Topic $topic
     * @return View
     * @throws AuthorizationException
     */
    public function edit(Topic $topic): View
    {
        $this->authorize('update', $topic);
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateTopicRequest $request, Topic $topic): RedirectResponse
    {
        $this->authorize('update', $topic);

        $topic->fill($request->validated());
        $topic->save();
        return redirect()->to($topic->link())->with('success', 'トピックは正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Topic $topic
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Topic $topic): RedirectResponse
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('success', 'トピックは正常に削除されました。');
    }

    /**
     * 话题上传图片
     *
     * @param Request $request
     * @param ImageUploadHandler $uploader
     * @return JsonResponse
     */
    public function uploadImage(Request $request, ImageUploadHandler $uploader): JsonResponse
    {
        // 初始化返回数据，默认上传失败
        $data = [
            'success' => false,
            'message' => 'Upload failed!',
            'file_path' => ''
        ];

        // 判断是否有上传文件, 并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'topics', auth()->user()->id, 1024);

            // 如果上传成功
            if ($result) {
                $data['success'] = true;
                $data['message'] = 'Upload successful!';
                $data['file_path'] = $result['path']; // 返回图片的存储路径
            } else {
                $data['message'] = 'Invalid image format or upload failed.';
            }
        }
        return response()->json($data);
    }
}
