<?php

/*
创建美国仓出库表
create table if not exists `3s_ussw_outbound`(
`id` smallint(6) unsigned primary key not null auto_increment,
`ebaysaleno` smallint(6),
`status` varchar(10) default null,
`shippingway` varchar(30) default null,
`trackingnumber` varchar(30) default null,
`time` datetime
) engine=myisam default charset=utf8;

*/

class OutboundAction extends CommonAction{

	public function index(){
		$this->display();
	}

	public function itemOutbound(){
    	$where['sku'] = I('post.sku','','htmlspecialchars');
		$where['position'] = I('post.position','','htmlspecialchars');
		$row = M('usstorage')->where($where)->find();
		$data['csales'] = $row['csales']+1;
		$data['ainventory'] = $row['ainventory']-1;

		$result = M('usstorage')->where($where)->save($data);
		
		if($result){
			$this->success('出库成功！');
		}
		else{
			$this->error("出库失败！");
		}
    }

    public function itemBatchOutbound(){
    	$where['sku'] = I('post.sku','','htmlspecialchars');
		$where['position'] = I('post.position','','htmlspecialchars');
		$row = M('usstorage')->where($where)->find();
		$data['csales'] = $row['csales']+I('post.quantity','','htmlspecialchars');
		$data['ainventory'] = $row['ainventory']-I('post.quantity','','htmlspecialchars');

		$result = M('usstorage')->where($where)->save($data);
		
		if($result){
			$this->success('出库成功！');
		}
		else{
			$this->error("出库失败！");
		}
    }
}

?>