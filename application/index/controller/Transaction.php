<?php
namespace app\index\controller;

use app\common\Base;


class Transaction extends Base
{
    public function index()
    {
//        $api = new Api();
//        $result =$api->getUsageHistory();
//        dump($result);
        return view('/tranhistory');
    }


}
