<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * 专门用来处理“用户是否有权限执行某个动作”的逻辑
 */
class UserPolicy
{
    /**
     * 确定用户是否可以查看任何模型。
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * 用于控制用户是否能“查看某个指定资源”
     * 所有用户都不允许查看其他人的资料
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * 当前登录用户 $user 只有在 修改自己的资料页（即 $model 是他自己）时，才有权限进行更新
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * 用于控制“是否允许恢复软删除的数据”
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * 判断用户是否有权限永久删除某个模型对象
     * 禁止任何人永久删除用户记录（跳过软删除，直接从数据库物理删除）
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
