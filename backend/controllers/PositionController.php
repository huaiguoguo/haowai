<?php

namespace backend\controllers;
use backend\extensions\BackendController;
use Yii;
use yii\filters\AccessControl;
use common\models\LoginForm;
use app\models\category;
use yii\filters\VerbFilter;


class PositionController extends BackendController
{

    public $pagetitle = "FireCMS欢迎你";
    public $actionName = 'WebInfo';


    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
        'access' => [
        'class' => AccessControl::className(),
        'rules' => [
        [
        'actions' => ['login','logout', 'error','index','webinfo','category','editcategory'],
        'allow' => true,
        'roles' => ['@'],
        ],
        [
        'actions' => ['logout','login'],
        'allow' => true,
        'roles' => ['?'],
        ],
        ],
        ],
        'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
        'logout' => ['post'],
        ],
        ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
        'error' => [
        'class' => 'yii\web\ErrorAction',
        ],
        'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
        ],
        ];
    }



    public function actionIndex()
    {
        $this->pageTitle = 'FireCMS欢迎你';
        $this->render('index');
    }

    public function actionWebInfo(){
        $data = array();
        $dosubmit = $this->getInput('dosubmit');
        if($dosubmit){
            $adminusers = Adminusers::model()->findByPk($this->adminuserinfo['id']);
            $adminusers->phone =$this->getInput('mobile');
            $adminusers->QQ =$this->getInput('qq');
            $adminusers->email =$this->getInput('email');
            $adminusers->save(false);
        }else{
            $data['adminuserinfo'] = $this->adminuserinfo;
            $this->display('webinfo2',$data);
        }
    }




// //分类
//     public function actionCategory()
//     {
//         global $categorylists;
//         $data=array();
//         $categorylists = ProCategory::model()->getAllCategory();
//         $cate = $this->get_cat_array();
//         $Trlist = $this->showTreeTr($cate);

//         $data['Trlist'] = $Trlist;

//         $this->display('category',$data);
//     }


    public function actionCategory()
    {
        global $categorylists;
        $data=array();
        //$categorylists = Category::model()->getAllCategory();
        $array = array();
        $Category = new Category;
        $categorylists = $Category->getAllCategory();

        $cate = $this->get_cat_array();
        $Trlist = $this->showTreeTr($cate);

        $data['Trlist'] = $Trlist;

        return $this->render('category',$data);
    }



    public function actionEditcategory(){
        $Category  = new Category();
        global $categorylists;
        $data = array();
        $dosubmit = Yii::$app->request->get('dosubmit');
        $cid = Yii::$app->request->get('cid');
        $CateInfo = $Category->findOne($cid);

        if($dosubmit){
            $CateInfo->category_name = $this->getInput('cname');
            $CateInfo->parent_id = $this->getInput('parent_id');
            $CateInfo->type = $this->getInput('type');
            if($CateInfo->save(false)) echo "1";
            else                    echo "-1";
        }else{
            $categorylists = $Category->getAllCategory();
            $cate = $this->get_cat_array();
            $string = $this->showTree($cate,$CateInfo->parent_id);
            $data['string'] = $string;
            $data['CateInfo'] = $CateInfo;

            return $this->render('editcate',$data);
        }
    }



    public function actionAddCategory()
    {
        global $categorylists;
        $category = array();

        $dosubmit = $this->getInput('dosubmit');
        if($dosubmit){
            $cname = $this->getInput('cname');
            $parent_id = $this->getInput('parent_id');
            $type = $this->getInput('type');
            $category = new ProCategory;
            $category->category_name = $cname;
            $category->parent_id = $parent_id;
            $category->type = $type;
            if($parent_id){
               $parent_info = ProCategory::model()->findByPk($parent_id);
               $category->path = $parent_id.",".$parent_info->path;
           }
           if($category->save(false)) echo "1";
           else                    echo "-1";
       }else{
        $categorylists = ProCategory::model()->getAllCategory();
        $cate = $this->get_cat_array();
        $string = $this->showTree($cate);
        $data['string'] = $string;
        $this->display('addcategory',$data);
    }
}



public function actionDelCate(){
    $id = $this->getInput('id');

            //删除文章
    $this->deleteSitelist($id);

            //删除分类
    $CDbCriteria = new CDbCriteria;
    $CDbCriteria->condition = "FIND_IN_SET($id,path)";
    ProCategory::model()->deleteAll($CDbCriteria);
    ProCategory::model()->deleteByPK($id);

    $this->redirect('/backed/product/category');
}


function showTree($tree,$id='') {
    global $string;
    $icon ='&nbsp;&nbsp;&nbsp;&nbsp;';
    $str ='';

    foreach ($tree as $k => $v) {
      $selected = '';
      if(count(explode(',', $v['path']))>1) $str = str_repeat($icon, count(explode(',', $v['path']))-1);
      if($id == $v['id']){ $selected = 'selected';}
      $string .= '<option value="'.$v['id'].'" '.$selected.'>'.$str.$v['category_name'].'</option>';
      if (count($v['child']) > 0) {
        $this->showTree($v['child'],$id);
    }
}
return $string;
}




function showTreeTr($tree) {
    global $tr;
    $icon ="<i class='board_z' style='padding-left:33px;'></i>";
    $board = '<i class="board"></i>';
    $controllerid = Yii::$app->controller->id;
    $str ='';
    foreach ($tree as $k => $v) {
        $tr .= "<tr><td>";
        if(count(explode(',', $v['path']))>1) $str = str_repeat($icon, count(explode(',', $v['path']))-1).$board;
        $tr .= $str.$v['category_name'];
        $tr .="</td>";
        $tr .= "<td><a href='/$controllerid/editcategory?cid=$v[id]' class='btn'>编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;";
        $tr .='<a href=';
        $tr .="javascript:G.ui.tips.confirm('此操作会删除子分类和子分类下面的文章,确定要删除吗?','/$controllerid/delcate/id/$v[id]');";
        $tr .=" class='btn'>";
        $tr .= '删除</a>';
        $tr .= "</td></tr>";
        if (count($v['child']) > 0) {
            $this->showTreeTr($v['child']);
        }
    }
    return $tr;
}


    //无限分类递归数组
public function get_cat_array($pid = 0)
{
    global $categorylists;
    $arr = array();

    foreach($categorylists as $index => $row){
                //对每个分类进行循环。
                if($categorylists[$index]['parent_id'] == $pid){ //如果有子类
                    $row['child'] = $this->get_cat_array($categorylists[$index]['id']); //调用函数，传入参数，继续查询下级
                    $arr[] = $row; //组合数组
                }
            }
            return $arr;
        }


    }
