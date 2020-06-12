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
use tree\Tree;

class PortalGradeModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];


    /**
     * 获取某导航下所有菜单树形结构数组
     * @param int $navId 导航id
     * @param int $maxLevel 最大获取层级,默认不限制
     * @return array
     */
    public function gradeTreeArray($maxLevel = 0)
    {        
        $grades     = $this->where('status', 1)->order('list_order ASC')->select()->toArray();
        $gradesTree = [];
        if (!empty($grades)) {
            $tree = new Tree();           
            $tree->init($grades);
            $gradesTree = $tree->getTreeArray(0, $maxLevel);
        }
        return $gradesTree;
    }

    /**
     * 生成年级 select树形结构
     * @param int $selectId 需要选中的年级 id
     * @param int $currentCid 需要隐藏的年级 id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminGradeTree($selectId = 0, $currentCid = 0)
    {
        $where =['parent_id'=>0];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }
        $grades = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $newGrades = [];
        foreach ($grades as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";
            array_push($newGrades, $item);
        }

        $tree->init($newGrades);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);

        return $treeStr;
    }

    /**
     * 年级树形结构
     * @param int $currentIds
     * @param string $tpl
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminGradeTableTree($currentIds = 0, $tpl = '')
    {
        
//        if (!empty($currentCid)) {
//            $where['id'] = ['neq', $currentCid];
//        }
        $grades = $this->order("list_order ASC")->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }

        $newGrades = [];
        foreach ($grades as $item) {
            $item['parent_id_node'] = ($item['parent_id']) ? ' class="child-of-node-' . $item['parent_id'] . '"' : '';
            $item['style']          = empty($item['parent_id']) ? '' : 'display:none;';
            $item['status_text']    = empty($item['status'])?'隐藏':'显示';
            $item['checked']        = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['url']            = cmf_url('portal/List/index', ['id' => $item['id']]);
            $item['str_action']     = '<a href="' . url("AdminGrade/add", ["parent" => $item['id']]) . '">添加子项</a>  <a href="' . url("AdminGrade/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("AdminGrade/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            if($item['parent_id']!=0)
            {
                $item['str_action']     = '<a href="' . url("AdminGrade/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("AdminGrade/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            }              
            
            if ($item['status']) {
                $item['str_action'] .= '<a class="js-ajax-dialog-btn" data-msg="您确定隐藏此项吗" href="' . url('AdminGrade/toggle', ['ids' => $item['id'], 'hide' => 1]) . '">隐藏</a>';
            } else {
                $item['str_action'] .= '<a class="js-ajax-dialog-btn" data-msg="您确定显示此项吗" href="' . url('AdminGrade/toggle', ['ids' => $item['id'], 'display' => 1]) . '">显示</a>';
            }
            array_push($newGrades, $item);
        }

        $tree->init($newGrades);

        if (empty($tpl)) {
            $tpl = " <tr id='node-\$id' \$parent_id_node style='\$style' data-parent_id='\$parent_id' data-id='\$id'>
                        <td style='padding-left:20px;'><input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]' value='\$id' data-parent_id='\$parent_id' data-id='\$id'></td>
                        <td><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
                        <td>\$id</td>
                        <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>                        
                        <td>\$status_text</td>
                        <td>\$str_action</td>
                    </tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);
        return $treeStr;
    }


    

    /**
     * 添加年级
     * @param $data
     * @return bool
     */
    public function addGrade($data)
    {
        $result = true;      
        $this->allowField(true)->save($data);        
        return $result;
    }

    public function editGrade($data)
    {
        $result = true;
        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldCategory = $this->where('id', $id)->find();

        if (empty($oldCategory)) {
            $result = false;
        } else {                      
            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
        }
        return $result;
    }


}