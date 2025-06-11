<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReplyPolicy
{
    /**
     * 只有回复的作者或主题的作者可以删除回复
     *
     * @param User $user
     * @param Reply $reply
     * @return bool
     */
    public function destroy(User $user, Reply $reply): bool
    {
        return $user->isAuthorOf($reply) || $user->isAuthorOf($reply->topic);
    }
}
