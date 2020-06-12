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

use cmf\controller\AdminBaseController;
use app\portal\model\PortalGradeModel;
use think\Db;



class AdminGradeController extends AdminBaseController
{
    /**
     * 年级列表
     * @adminMenu(
     *     'name'   => '年级管理',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '年级列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $portalGradeModel = new PortalGradeModel();
        $gradeTree = $portalGradeModel->adminGradeTableTree();
        $this->assign('grade_tree', $gradeTree);
        return $this->fetch();
    }

    /**
     * 添加年级
     * @adminMenu(
     *     'name'   => '添加年级',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加年级',
     *     'param'  => ''
     * )
     */
    public function add()
    {        
        $parentId            = $this->request->param('parent', 0, 'intval');
        $portalGradeModel = new PortalGradeModel();
        $gradesTree      = $portalGradeModel->adminGradeTree($parentId);
        $this->assign('grades_tree', $gradesTree);
        return $this->fetch();
    }

    /**
     * 添加年级提交
     * @adminMenu(
     *     'name'   => '添加年级提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加年级提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $portalGradeModel = new PortalGradeModel();
        $data = $this->request->param();
        $result = $this->validate($data, 'PortalGrade');
        if ($result !== true) {
            $this->error($result);
        }
        $result = $portalGradeModel->addGrade($data);
        if ($result === false) {
            $this->error('添加失败!');
        }
        $this->success('添加成功!', url('AdminGrade/index'));
    }

    /**
     * 编辑年级
     * @adminMenu(
     *     'name'   => '编辑年级',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑年级',
     *     'param'  => ''
     * )
     */
    public function edit()
    {     
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $grade = PortalGradeModel::get($id)->toArray();
            $portalGradeModel = new PortalGradeModel();
            $gradesTree      = $portalGradeModel->adminGradeTree($grade['parent_id'], $id);

            $this->assign($grade);           
            $this->assign('grades_tree', $gradesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑年级提交
     * @adminMenu(
     *     'name'   => '编辑年级提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑年级提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'PortalGrade');

        if ($result !== true) {
            $this->error($result);
        }

        $portalGradeModel = new PortalGradeModel();

        $result = $portalGradeModel->editGrade($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 年级选择对话框
     * @adminMenu(
     *     'name'   => '年级选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '年级选择对话框',
     *     'param'  => ''
     * )
     */
    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $portalCategoryModel = new PortalGradeModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $categoryTree = $portalCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $where      = ['delete_time' => 0];
        $categories = $portalCategoryModel->where($where)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 年级排序
     * @adminMenu(
     *     'name'   => '年级排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '年级排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders(Db::name('portal_grade'));
        $this->success("排序更新成功！", '');
    }

    /**
     * 年级显示隐藏
     * @adminMenu(
     *     'name'   => '年级显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '年级显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data                = $this->request->param();
        $portalGradeModel = new PortalGradeModel();

        if (isset($data['ids']) && !empty($data["display"])) {
            $ids = $this->request->param('ids/a');
            $portalGradeModel->where(['id' => ['in', $ids]])->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $ids = $this->request->param('ids/a');
            $portalGradeModel->where(['id' => ['in', $ids]])->update(['status' => 0]);
            $this->success("更新成功！");
        }

    }

    /**
     * 删除年级
     * @adminMenu(
     *     'name'   => '删除年级',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除年级',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $portalGradeModel = new PortalGradeModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findGrade = $portalGradeModel->where('id', $id)->find();

        if (empty($findGrade)) {
            $this->error('此项不存在!');
        }
        //判断此年级有无子年级（不算被删除的子年级）
        $gradeChildrenCount = $portalGradeModel->where(['parent_id' => $id])->count();

        if ($gradeChildrenCount > 0) {
            $this->error('此年级下有班级无法删除!');
        }

        $gradeStudentCount = 0;
        if($findGrade['parent_id']!=0){
            $parentGrade = $portalGradeModel->where('id', $findGrade['parent_id'])->find();
            $gradeStudentCount = Db::name('portal_student')->where(['grade'=>$parentGrade['href'],'class'=>$findGrade['href']])->count();
        }
        
        if ($gradeStudentCount > 0) {
            $this->error('此班级下有学生，无法删除!');
        }
        
        $result = $portalGradeModel
            ->where('id', $id)
            ->delete();
        if ($result) {           
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }

    //excel导出
    public function  excelexport()
    {
        $portalGradeModel = new PortalGradeModel();
        $gradeTree = $portalGradeModel->gradeTreeArray();       
        foreach ($gradeTree as $key => $value) {
            foreach ($value['children'] as $key2 => $value2) {
                $gradedata[$value['href'].$value2['href']]['gradename']=$value['name'];
                $gradedata[$value['href'].$value2['href']]['gradeid']=$value['href'];
                $gradedata[$value['href'].$value2['href']]['classname']=$value2['name'];
                $gradedata[$value['href'].$value2['href']]['classid']=$value2['href'];
            }        

        }
        $cellname= array('0' => 'gradename','1'=>'classname','2' => 'gradeid','3'=>'classid');        
        $excelName = \cmf_exportExcel('年级数据',$cellname,$gradedata,'grade');       
    }

    //excel导出
    public function  excelimport()
    {
        $action = $this->request->param('action', 0, 'intval');
        if($action==0)
        {
            return $this->fetch();
        }
        if($action==1)
        {
            $filename = $this->request->param('filename');            
            $exceldata=\cmf_importexecl(\str_replace('/','\\',$_SERVER['DOCUMENT_ROOT']).'\upload\\'.\str_replace('/','\\',$filename));
            if($exceldata['error']==0)
            {
                return $this->error($exceldata['message']);
            }
            if($exceldata['error']==1)
            {
                // file_put_contents('test.txt',json_encode($exceldata['data'])."\n\r",8);
                foreach ($exceldata['data'] as $key => $value) {
                    $thisData['gradename']=$value[0];
                    $thisData['classname']=$value[1];
                    $thisData['gradeid']=$value[2];
                    $thisData['classid']=$value[3];

                    $portalGradeModel = new PortalGradeModel();                    
                    $result = $portalGradeModel->where(['parent_id'=>0,'name'=>$thisData['gradename']])->find();
                    // file_put_contents('test.txt',json_encode($result)."\n\r",8);          
                    if($result)
                    {
                        $result2 = $portalGradeModel->where(['parent_id'=>$result['id'],'name'=>$thisData['classname']])->find();
                        if($result2){
                            return $this->error('数据导入失败，存在名字为‘'.$thisData['gradename'].$thisData['classname'].'’的班级。');
                        }
                        else {
                            $data['parent_id']=$result['id'];
                            $data['name']=$thisData['classname'];
                            $data['href']=$thisData['classid'];
                            $portalGradeModel->isUpdate(false)->save($data);
                        }
                    }
                    else
                    {
                        return $this->error('数据导入失败，找不到名字为‘'.$thisData['gradename'].'’的年级。');
                    }
                    
                }
                return $this->success('数据导入成功！');
            }
            
        }
    }
    

}
