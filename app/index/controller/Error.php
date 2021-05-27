<?php
namespace app\index\controller;
use app\BaseController;
use think\facade\View;

class Error{
    public function miss(){
        header("status: 404 Not Found");
        return view("miss",401);
    }
}