<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
class IndexController extends Controller {
	public $td = '';
    public function index(){
		$this->explorer();
		//var_dump(C('DB_TYPE'));die;
        // $this->show();          
        $model = D('Accounts');   
        $model = D('Blocks');   
          /* $model->user='cgcgcg';
          $model->password='123456';
          $model->add(); */
        $a = $model->select();
		$a = $model->where(array('block_num'=>'28'))->select();
       // var_dump($a);
        // $this->display();
		$this->display('explorer');
    }
	public function explorer(){ //块数据
		$block = I('get.block');
		$p = I('get.p')?I('get.p'):1;
		if($block || $p){
			$model = D('Blocks');
			//$model = new \MongoModel('Blocks');
			if(strlen(trim($block))>=64){
				$where['block_id'] = $block;
				$this->assign('keys',$block);
			}elseif(strlen(trim($block))<64 &&strlen(trim($block))>0 ){
				$where['block_num'] = $block;
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
			$count = count($model->select());
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
		$count = count($model->select());
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
		
		$count = count($model->select());
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
					if($i>0 && $i!=2){
						
						$this->td .= '<div class="row r'.$i.'"><div class="col-sm-3">'.ucfirst($k).': </div> <div class="col-sm-9">'.$v.'</div></div>';	
					}else{
					//$this->td .= '<div class="row"><div class="col-sm-3">'.$k.': </div> <div class="col-sm-9">'.$v.'</div></div>';
					$this->td .= '';
					}
				}
			}
		}
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