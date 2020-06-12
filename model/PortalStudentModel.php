<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\model;

use app\admin\model\RouteModel;
use think\Model;


class PortalStudentModel extends Model
{

    /**
     * add_time 自动完成
     * @param $value
     * @return false|int
     */
    public function setAddTimeAttr($value)
    {
        return strtotime($value);
    }   
     
        /**
     * 后台管理添问学生信息
     * @param array $data 学生数据     * 
     * @return $this
     */
    public function adminAddStudent($data)
    {      
                       
        $this->allowField(true)->isUpdate(false)->save($data);

        return $this;

    }


    /**
     * 后台管理编辑学生信息
     * @param array $data 学生数据     * 
     * @return $this
     */
    public function adminEditStudent($data)
    {
        $result = true;
        $id = intval($data['id']);
        $result=$this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
        return $result;
    }

}