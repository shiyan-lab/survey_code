<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 
// +----------------------------------------------------------------------
namespace app\portal\validate;

use think\Validate;

class PortalStudentValidate extends Validate
{
    protected $rule = [
        'id'  => 'require|integer',
        'name'  => 'require|chs',        
        'grade' => 'integer',
        'class' => 'integer',        
    ];
    protected $message = [
        'id.require' => '学号不能为空',
        'id.integer' => '学号必须由数字组成',
        'name.require'=>'姓名不能为空', 
        'name.chs'=>'姓名必须为汉字',       
        'grade.integer' => '年级传入数据错误',
        'class.integer' => '班级传入数据错误',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];

   
}