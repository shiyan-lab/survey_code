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
namespace app\portal\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\portal\model\PortalSubjectModel;
use think\Db;
use app\admin\model\ThemeModel;


class AdminSubjectController extends AdminBaseController
{
       
    public function index(){

        $postid = $this->request->param('postid');

        $portalSubjectModel = new PortalSubjectModel();
       
        $subject = $portalSubjectModel->order(['list_order'=>'asc'])->where('post_id', $postid)->select();
        $typename =array('1' => '单行文本题','2'=>'多行文本题','3'=>'单选题','4'=>'多选题','5'=>'矩阵单选题','6'=>'段落说明题');

        if (empty($subject)) {
            abort(404, '问卷尚未添加题目!');
        }

        $this->assign('postid', $postid);
        $this->assign('subject', $subject);
        $this->assign('typename', $typename);
        return $this->fetch();
    }
  
    public function add()
    {
        $content = hook_one('portal_admin_subject_add_view');

        if (!empty($content)) {
            return $content;
        }
       
        $postId = $this->request->param('postid', 0, 'intval');       
        $this->assign('postid', $postId);
        return $this->fetch();
    }

  
    public function addPost()
    {   
        if ($this->request->isPost()) {        
            $data = $this->request->param();             
            $post   = $data['post'];                
            $result = $this->validate($post, 'AdminSubject');
               
            if ($result !== true) {
                $this->error($result);
            }
            
            $ismust=0;           
            if(!empty($data['post']['is_must'])){        
                $ismust=1;
            }
            $data['post']['is_must']=$ismust;


            switch ($data['post']['subject_type'])
            {        
            case 3:            
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                    
                }           
            break;
            case 4:
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                    
                }
            break;
            case 5:
                if (!empty($data['optionask'])) {
                    $data['post']['subject_content']['ask'] = $data['optionask'];                    
                }       
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                    
                }
            break;
            case 6: 
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                  
                }            
            break;
            default:
                $data['post']['subject_content']="";
            }       
            $data['post']['subject_content']=json_encode($data['post']['subject_content']);

            //abort(404, '异常消息', [参数]);
            $portalSubjectModel = new PortalSubjectModel();        
            
            $result = $portalSubjectModel->adminAddSubject($data['post']); 

            if ($result === false) {
                $this->error('添加失败!');
            } 

            $this->success('添加成功!');
        }
    }

   
    public function edit()
    {
        
        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $subject = PortalSubjectModel::get($id)->toArray();
            $subject['subject_content']=json_decode($subject['subject_content'],true);         
            if (!empty($subject['subject_content']['option'])) {
                $option = $subject['subject_content']['option'];               
                $this->assign('option',$option);              
            }
            if (!empty($subject['subject_content']['ask'])) {
                $ask = $subject['subject_content']['ask'];
                $this->assign('ask',$ask);               
            } 
            $this->assign('id', $id);
            $this->assign($subject);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }


    public function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();                      
            $post   = $data['post'];                
            $result = $this->validate($post, 'AdminSubject');
               
            if ($result !== true) {
                $this->error($result);
            }
            
            $ismust=0;           
            if(!empty($data['post']['is_must'])){        
                $ismust=1;
            }
            $data['post']['is_must']=$ismust;
            
            switch ($data['post']['subject_type'])
            {        
            case 3:            
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];
                }           
            break;
            case 4:
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];      
                }
            break;
            case 5:
                if (!empty($data['optionask'])) {
                    $data['post']['subject_content']['ask'] = $data['optionask'];                    
                }       
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                  
                }
            break;
            case 6: 
                if (!empty($data['optionname'])) {
                    $data['post']['subject_content']['option'] = $data['optionname'];                  
                }            
            break;
            default:
                $data['post']['subject_content']="";
            }       
            $data['post']['subject_content']=json_encode($data['post']['subject_content']);           
            
            $postalSubjectModel = new PortalSubjectModel();
            $postalSubjectModel->adminEditSubject($data['post']);          

            $this->success('保存成功!');
        }
    }
   
    public function listOrder()
    {
        parent::listOrders(Db::name('portal_subject'));
        $this->success("排序更新成功！", '');
    }
 

    public function delete()
    {
        $portalSubjectModel = new PortalSubjectModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findSubject = $portalSubjectModel->where('id', $id)->find();

        if (empty($findSubject)) {
            $this->error('题目不存在!');
        }
       
        $result = $portalSubjectModel
            ->where('id', $id)
            ->delete();
        if ($result) {          
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
