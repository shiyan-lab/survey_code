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


class PortalSubjectModel extends Model
{

   /*  protected $type = [
        'subject_content' => 'array',
    ];
 */
    /**
     * 后台管理添问卷题目
     * @param array $data 问卷数据     * 
     * @return $this
     */
    public function adminAddSubject($data)
    {      
                       
        $this->allowField(true)->isUpdate(false)->save($data);

        return $this;

    }

    /**
     * 后台管理编辑题目
     * @param array $data 题目数据     * 
     * @return $this
     */
    public function adminEditSubject($data)
    {
        $result = true;
        $id          = intval($data['id']);
        $result=$this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
        return $result;
    }


    

}