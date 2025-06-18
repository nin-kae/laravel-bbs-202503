<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reply;
use Illuminate\View\View;

class ReplyController extends Controller
{
    /*
     * 显示回复列表
     *
     * @return View
     */
    public function index(): View
    {
        // 获取所有回复
        $replies = Reply::with('user', 'topic')
            ->latest()
            ->paginate($this->perPage);

        return view('admin.replies.index', compact('replies'));
    }
}
