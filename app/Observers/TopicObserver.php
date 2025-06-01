<?php

namespace App\Observers;

use App\Models\Topic;

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
        $topic->excerpt = make_excerpt($topic->body);
    }
}
