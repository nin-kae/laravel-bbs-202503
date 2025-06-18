<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /*
     * 每页分页的项目数
     *
     * @var int
     */
    protected int $perPage = 15;

}
