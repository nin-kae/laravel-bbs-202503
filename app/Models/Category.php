<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * 当前表没有 created_at 和 updated_at 字段（不自动更新时间字段）
     * 因此设置 $timestamps 为 false
     * 在 Laravel 中，默认情况下，Eloquent 模型会自动维护 created_at 和 updated_at 字段
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 允许通过批量赋值操作时，只能写入 name 和 description 这两个字段
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];
}
