<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
class IndexController extends Controller {
	public $td = '';
    public function index(){
		$this->explorer();
		
		$this->display('explorer');
    }
	public function explorer(){ //块数据
		$block = I('get.block');
		$p = I('get.p')?I('get.p'):1;
		if($block || $p){
			$model = D('Blocks');
			//$all = array("distinct"=>"Blocks");
			
			//$model = new \MongoModel('Blocks');
			if(strlen(trim($block))>=64){
				$where['block_id'] = $block;
				$this->assign('keys',$block);
			}elseif(strlen(trim($block))<64 &&strlen(trim($block))>0 ){
				$where['block_num'] = intval($block);
				$this->assign('keys',$block);				
			}
			
			if($p){
				$limit = ($p-1)*10;
			}else{
				$limit = 0;
			}
			$list = $model->where($where)->order('block_num desc')->limit($limit,10)->select();
			foreach($list as $k=>$v){
				$v['timestamps'] = date('Y-m-d H:i:s',$v['timestamp']->sec);
				$v['kb'] = substr(sprintf("%.3f",floatval($v['block_size']/1024)),0,-1);;
				$newlist[] = $v;
			}
			$this->assign('list',$newlist);
			$count = ($model->total(array('where'=>$where)));
			//$sss = $model->total(array('where'=>$where));var_dump($sss);var_dump($model->getlastsql());die;
			$page = new \Think\Page($count,10);
			$show = $page->show();
			$this->assign('page',$show);
			// $a = $model->where($where)->order('block_num asc')->select();
			// foreach($a as $k=>$v){
				// $data = $v;
				// $data['timestamps'] = date('Y-m-d H:i:s',$data['timestamp']->sec);
			// }
			//$this->assign('data',$data);
			
		}
		$this->display();
	}
	public function query(){//查询块数据
		$data = I('post.data');
		if($data){
		$a = D("Accounts")->where(array('name'=>$data))->select(); //先查询用户是否存在
		if(count($a)){
			foreach($a as $k=>$v){
				$data = $v;
				$data['createdTimes'] = date('Y-m-d H:i:s',$data['createdTime']->sec);
				$data['url'] = U('index/address',array('name'=>$v['name']));
			}
			echo json_encode(array('type'=>1,'list'=>$data));die;
		}
		
		$a = D('Transactions')->where(array('transaction_id'=>$data))->select();//第二查询记录id
		if(count($a)){
			foreach($a as $k=>$v){
				$data = $v;
				$data['expirations'] = date('Y-m-d H:i:s',$data['expiration']->sec);
				$data['url'] = U('index/paylist',array('transaction'=>$v['transaction_id']));
			}
			echo json_encode(array('type'=>2,'list'=>$data));die;
		}
		}
		$model = D('Blocks');
		//var_dump(strlen($data));die;
		if(strlen($data)>=64){
			$where['block_id'] = $data;
		}elseif(strlen($data)<64 &&strlen($data)>0 ){
			$where['block_num'] = $data;	
		}
		$a = $model->where($where)->order('block_num asc')->select();//var_dump($model->getlastsql());
		
		foreach($a as $k=>$v){
			$data = $v;
			$data['timestamps'] = date('Y-m-d H:i:s',$data['timestamp']->sec);
		}
		echo json_encode(array('type'=>3,'list'=>$data));die;
		
        
	}
	public function fylist(){		//数据列表
		$model = D('Messages');
		$p = I('get.p');
		
		if($p){
			$limit = ($p-1)*10;
		}else{
			$limit = 0;
		}
		$list = $model->order('createdTime desc')->limit($limit,10)->select();
		//$list = $model->query("XMAX2.blocks.find({}).sort({'block_num':1}).skip(0).limit(10)");
		
		foreach($list as $k=>$v){
			$v['createdTimes'] = date('Y-m-d H:i:s',$v['createdTime']->sec);
			$v['ids'] = $v['_id']->__toString();
			$newlist[] = $v;
		}
		$this->assign('list',$newlist);
		$count = ($model->total());
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$this->assign('page',$show);
		//var_dump($show);
		//var_dump($list);die;
		//var_dump($model->getlastsql());die;
		$this->display();
		
	}
	public function address(){//矿工信息  交易记录
		$name = I('get.name');
		$model = D('Accounts');
		if($name){
			$where['name'] = $name; 
			
			$a = $model->where($where)->select();
			//var_dump($a);die;
			foreach($a as $k=>$v){
				$data = $v;
				$data['createdTimes'] = date('Y-m-d H:i:s',$data['createdTime']->sec);
			}
			$this->assign('data',$data);
			$this->assign('keys',$block);
		}
		$this->display();
	}
	public function block(){
		$model = D('Blocks');
		//var_dump(strlen($data));die;
		$data = I('get.data');
		if(strlen($data)>=64){
			$where['block_id'] = $data;
		}elseif(strlen($data)<64 &&strlen($data)>0 ){
			$where['block_num'] = $data;	
		}
		$a = $model->where($where)->order('block_num asc')->select();//var_dump($model->getlastsql());
		
		foreach($a as $k=>$v){
			$data = $v;
			$data['timestamps'] = date('Y-m-d H:i:s',$data['timestamp']->sec);
		}
		$this->assign('data',$data);
		$this->assign('keys',$block);
		$this->display();
	}
	public function account(){
		$model = D('Accounts');
		$p = I('get.p');
		
		if($p){
			$limit = ($p-1)*10;
		}else{
			$limit = 0;
		}
		if($name){
			$where['name'] = new \MongoRegex("/$name/"); 
		}
		$list = $model->where($where)->limit($limit,10)->select();
		//var_dump($model->getlastsql());die;
		
		foreach($list as $k=>$v){
			$v['createdTimes'] = date('Y-m-d H:i:s',$v['createdTime']->sec);
			$newlist[] = $v;
		}
		$this->assign('list',$newlist);
		$count = ($model->total(array('where'=>$where)));
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$this->assign('page',$show);
		$this->display();
		
	}
	
	public function paylist(){//交易记录
		$keys = I('get.transaction');
		if($keys){
			$where['transaction_id'] = $keys;
		}
		$model = D('Transactions');
		
		$count = ($model->total(array('where'=>$where)));
		$page = new \Think\Page($count,10);
		$show = $page->show();
		$this->assign('page',$show);
		$p = I('get.p');
		
		if($p){
			$limit = ($p-1)*10;
		}else{
			$limit = 0;
		}
		$list = $model->where($where)->order('expiration desc')->limit($limit,10)->select();
		foreach($list as $k=>$v){
			$v['expirations'] = date('Y-m-d H:i:s',$v['expiration']->sec);
			$v['messagess'] = array_values(get_object_vars($v['messages'][0]));
			$newlist[] = $v;
		}
		//var_dump($newlist);die;
		$this->assign('list',$newlist);
		
		$this->display();
	}
	public function transaction(){
		$data = I('get.data');
		$a = D('Transactions')->where(array('transaction_id'=>$data))->select();//第二查询记录id
		if(count($a)){
			foreach($a as $k=>$v){
				$data = $v;
				$data['expirations'] = date('Y-m-d H:i:s',$data['expiration']->sec);
				$data['messagess'] = array_values(get_object_vars($data['messages'][0]));
				$data['url'] = U('index/paylist',array('transaction'=>$v['transaction_id']));
			}
			
		}
		$this->assign('data',$data);
		$this->assign('keys',$block);
		$this->display();
	}
	public function message(){
		$id = I("get.id");
		$model = D('Messages');
		//$id = "ObjectId('$id')";
		
		$a = $model->where(array('_id'=>new \MongoId($id)))->order('createdTime asc')->select();
		//var_dump($model->getlastsql());
		//var_dump($a);die;
		$a[$id]['createdTimes'] = date('Y-m-d H:i:s',$a[$id]['createdTime']->sec);
		
		$data = $a[$id]['data'];
		$this->td = '';
		$this->gettb($data,0);
		
		$this->assign('datas',$this->td);
		
		$this->assign('data',$a[$id]);
		$this->display();
	}
	
	public function gettb($data,$i){$i++;
		if(is_array($data)){
			foreach($data as $k=>$v){
				if(is_array($v)){
					$this->gettb($v,$i);
				}else{
					if($i>0 && $i!=2  && $i!=4){
						
						$this->td .= '<div class="row r'.$i.'"><div class="col-sm-3">'.ucfirst($k).': </div> <div class="col-sm-9">'.$v.'</div></div>';	
					}else{
					//$this->td .= '<div class="row"><div class="col-sm-3">'.$k.': </div> <div class="col-sm-9">'.$v.'</div></div>';
					$this->td .= '';
					}
				}
			}
		}
	}
	
	//搜索处理 页面
	public function search(){
		$keys = I('post.keys')?I('post.keys'):'';//
		$type = I('post.type')?I('post.type'):'';//搜索类型
		$model = D('Blocks');
		$model1 = D('Messages');
		$model2 = D('Transactions');
		$model3 = D('Accounts');
		$p = I('post.p')?I('post.p'):1;
		$_GET['p'] = $p;
		if($p){
			$limit = ($p-1)*10;
		}else{
			$limit = 0;
		}
		if(strlen($keys)>=64){
			$where['block_id'] = $keys;
		}elseif(strlen($keys)<64 &&strlen($keys)>0 ){
			$where['block_num'] = intval($keys);			
		}
		if($keys){
			if(is_numeric($keys))$where1['message_id'] = intval($keys);
			$where2['transaction_id'] = $keys;
			$where3['name'] = new \MongoRegex("/$keys/"); 
		}
		
		$model->find($where);
		$blocks_num = ($model->total(array('where'=>$where)));//blocks 数量
		//var_dump($blocks_num);die;
		$this->assign('blocks_num',($blocks_num));
		if($blocks_num>0 && $type=='' ){$type = 'Blocks';}
		
		$model1->find($where);
		if(is_array($where1))$messages_num = ($model1->total(array('where'=>$where1)));//Messages 数量
		$this->assign('messages_num',($messages_num));
		if($messages_num>0 && $type==''){$type = 'Messages';}
		
		$model2->find($where);
		$transactions_num = ($model2->total(array('where'=>$where2)));//Transactions 数量
		$this->assign('transactions_num',($transactions_num));
		if($transactions_num>0 && $type==''){$type = 'Transactions';}
		
		$model3->find($where);
		$accounts_num = ($model3->total(array('where'=>$where3)));//Accounts 数量
		$this->assign('accounts_num',($accounts_num));
		//var_dump($type);
		if($accounts_num>0 && $type==''){$type = 'Accounts';}
		if($type==''){
			$type = 'Blocks';
		}
		//var_dump($where3);
		if($type == 'Blocks'){
			$list = $model->where($where)->order('block_num desc')->limit($limit,10)->select();
			foreach($list as $k=>$v){
				$v['timestamps'] = date('Y-m-d H:i:s',$v['timestamp']->sec);
				$v['kb'] = substr(sprintf("%.3f",floatval($v['block_size']/1024)),0,-1);;
				$newlist[] = $v;
			}
			$this->assign('list',$newlist);
			$count = ($model->total(array('where'=>$where)));
			$page = new \Think\Page($count,10);
			$show = $page->showj();
			$this->assign('page',$show);
			
		}elseif($type == 'Messages'){
			$list = $model1->where($where1)->order('createdTime desc')->limit($limit,10)->select();
			foreach($list as $k=>$v){
				$v['createdTimes'] = date('Y-m-d H:i:s',$v['createdTime']->sec);
				$newlist[] = $v;
			}
			$this->assign('list',$newlist);
			$count = ($model1->total(array('where'=>$where1)));
			$page = new \Think\Page($count,10);
			$show = $page->showj();
			$this->assign('page',$show);
			
		}elseif($type == 'Transactions'){
			$count = ($model2->total(array('where'=>$where2)));
			$page = new \Think\Page($count,10);
			$show = $page->showj();
			$this->assign('page',$show);
			$list = $model2->where($where2)->order('expiration desc')->limit($limit,10)->select();
			foreach($list as $k=>$v){
				$v['expirations'] = date('Y-m-d H:i:s',$v['expiration']->sec);
				$v['messagess'] = array_values(get_object_vars($v['messages'][0]));
				$newlist[] = $v;
			}
			
			$this->assign('list',$newlist);
		}elseif($type == 'Accounts'){
			$list = $model3->where($where3)->limit($limit,10)->select();
			//var_dump($model3->getlastsql());die;
			
			foreach($list as $k=>$v){
				$v['createdTimes'] = date('Y-m-d H:i:s',$v['createdTime']->sec);
				$newlist[] = $v;
			}
			$this->assign('list',$newlist);
			$count = ($model3->total(array('where'=>$where3)));
			$page = new \Think\Page($count,10);
			$show = $page->showj();
			$this->assign('page',$show);
		}
		
		
		
		$this->assign('type',$type);
		$this->assign('keys',$keys);
		
		$this->display();
	}
	
	
	
 }