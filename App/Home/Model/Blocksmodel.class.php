<?php
namespace Home\Model;
 use Think\Model\MongoModel;
 Class BlocksModel extends MongoModel 
 { 
	public function ss(){
		return 'ssssssssss';
	}
	public function counts(){
		return $this->db->count();
	} 
 }
?>