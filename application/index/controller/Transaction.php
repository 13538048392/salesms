<?php
namespace app\index\controller;

use app\common\Base;


class Transaction extends Base
{
    public function index()
    {
        return view('/tranhistory');
    }


}
