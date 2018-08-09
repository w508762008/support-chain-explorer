<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
class IndexController extends Controller {
    public function index(){
		//var_dump(C('DB_TYPE'));die;
        // $this->show();          
        $model = D('Accounts');   
        $model = D('Blocks');   
          /* $model->user='cgcgcg';
          $model->password='123456';
          $model->add(); */
        $a = $model->select();
		$a = $model->where(array('block_num'=>'28'))->select();
        var_dump($a);
        // $this->display();
		$this->display();
    }
	public function explorer(){
		$block = I('get.block');
		if($block){
			$model = D('Blocks');
			$a = $model->where(array('block_num'=>$block))->order('block_num asc')->select();
			foreach($a as $k=>$v){
				$data = $v;
				$data['timestamps'] = date('Y-m-d H:i:s',$data['timestamp']->sec);
			}
			$this->assign('data',$data);
			$this->assign('keys',$block);
		}
		$this->display();
	}
	public function query(){
		$data = I('post.data');
		
		
		$model = D('Blocks');
		//var_dump(strlen($data));die;
		if(strlen($data)>=64){
			$where['block_id'] = $data;
			//$where['block_id'] = $this->Hex2String($data);
		}elseif(strlen($data)<64 &&strlen($data)>0 ){
			$where['block_num'] = $data;	
		}
		$a = $model->where($where)->order('block_num asc')->select();//var_dump($model->getlastsql());
		
		foreach($a as $k=>$v){
			$data = $v;
			//$data['block_id'] = $this->String2Hex($data['block_id']);
			//$data['prev_block_id'] = $this->String2Hex($data['prev_block_id']);
			
			$data['timestamps'] = date('Y-m-d H:i:s',$data['timestamp']->sec);
			
			//$data['prev_block_id'] = dechex($data['prev_block_id']);
		}
		echo json_encode($data);die;
		
        
	}
	public function fylist(){		
		$model = D('Blocks');
		$p = I('get.p');
		
		if($p){
			$limit = ($p-1)*10;
		}else{
			$limit = 0;
		}
		$list = $model->order('block_num desc')->limit($limit,10)->select();
		//$list = $model->query("XMAX2.blocks.find({}).sort({'block_num':1}).skip(0).limit(10)");
		
		foreach($list as $k=>$v){
			$v['timestamps'] = date('Y-m-d H:i:s',$v['timestamp']->sec);
			$newlist[] = $v;
		}
		$this->assign('list',$newlist);
		$count = count($model->select());
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$this->assign('page',$show);
		//var_dump($show);
		//var_dump($list);die;
		//var_dump($model->getlastsql());die;
		$this->display();
		
	}
	function String2Hex($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++){
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}

	function Hex2String($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
 }