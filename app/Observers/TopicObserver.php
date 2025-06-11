<?php

namespace App\Observers;

use App\Jobs\GenerateSlug;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TopicObserver
{
    /**
     * 当创建或更新主题时，从正文中生成摘录
     *
     * @param Topic $topic
     * @return void
     */
    public function saving(Topic $topic): void
    {
        // 过滤话题内容的特殊标签
        $topic->body = clean($topic->body, 'user_topic_body');

        // 生成摘要
        $topic->excerpt = make_excerpt($topic->body);
    }

    public function saved(Topic $topic): void
    {
        // 如果没有 slug，则使用标题生成 slug
        // 我们按照查得的日本公司最常见的方式来处理 slug
        // 生成 slug 对 URL 友好，一般是百分比符号数字字母横杠下划线组成，防止URL出现特殊符号导致网页无法识别
        if (!$topic->slug) {
            $topic->slug = rawurlencode(Str::replace(' ', '-', $topic->title));
        }
    }

    /**
     * When a topic is deleted, remove all replies associated with it.
     * @param Topic $topic
     * @return void
     */
    public function deleted(Topic $topic): void
    {
        DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
