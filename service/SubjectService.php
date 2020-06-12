<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.21xiao.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: sy 
// +----------------------------------------------------------------------
namespace app\portal\service;

use app\portal\model\PortalSubjectModel;
class SubjectService
{

    public function publishedSubject($postId)
    {      

        $portalSubjectModel = new PortalSubjectModel();
        if (!empty($postId)) {
            $where = [               
                'subject.post_id'     => $postId
            ];
            $subject = $portalSubjectModel->order('list_order asc')->alias('subject')->field('subject.*')
                ->where($where)
                ->select();
        } 
        return $subject;
    }

}


?>