<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
class PublicController extends Controller {
    public function index(){
		//var_dump(C('DB_TYPE'));die;
        // $this->show();          
        //$model = D("Col");    
        // $model->user='cgcgcg';
        // $model->password='123456';
        // $model->add();
        //$a = $model->select();
        //var_dump($a);
        // $this->display();
		$this->display();
    }
	
 }