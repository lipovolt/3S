<?php

class UsswAction extends CommonAction{

	public function ussw(){
		$this->display();
	}

    /*创建usswInbound表

    CREATE TABLE IF NOT EXISTS `3s_ussw_inbound`(
    `id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT,
    `date` date default null,
    `way` varchar(10) default null,
    `pQuantity` smallint(6) default 0,
    `weight` decimal(5,2) default 0,
    `volume` decimal(8,5) default 0,
    `volumeWeight` decimal(5,2) default 0,
    `iQuantity` smallint(6) default 0,
    `status` varchar(10) default null
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        
    */

    public function usswInbound(){
        $this->display();
    }

	public function itemInbound(){
		$where['sku'] = I('post.sku','','htmlspecialchars');
		$where['position'] = I('post.position','','htmlspecialchars');
		$row = M('usstorage')->where($where)->find();
		$data['cinventory'] = $row['cinventory']+1;
		$data['ainventory'] = $row['ainventory']+1;

		$result = M('usstorage')->where($where)->save($data);
		
		if($result){
			$this->success('入库成功！');
		}
		else{
			$this->error("入库失败！");
		}
	}

	public function storageFileBatchAdd(){
        $this->display();
     }

    public function usswManage(){
    	if($_POST['keyword']==""){
            $Data = M('usstorage');
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $this->usstorage = M('usstorage')->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
    }

    Public function usswEdit($sku){
        $this->usstorage = M('usstorage')->where(array('sku'=>$sku))->select();
        $this->display();

    }

    public function update(){
        $data['id'] = I('post.idValue','','htmlspecialchars');
        $data['position'] = I('post.positionValue','','htmlspecialchars');
        $data['sku'] = I('post.skuValue','','htmlspecialchars');
        $data['cname'] = I('post.cnameValue','','htmlspecialchars');
        $data['ename'] = I('post.enameValue','','htmlspecialchars');
        $data['attribute'] = I('post.attributeValue','','htmlspecialchars');
        $data['cinventory'] = I('post.cinventoryValue','','htmlspecialchars');
        $data['ainventory'] = I('post.ainventoryValue','','htmlspecialchars');
        $data['oinventory'] = I('post.oinventoryValue','','htmlspecialchars');
        $data['iinventory'] = I('post.iinventoryValue','','htmlspecialchars');
        $data['csales'] = I('post.csalesValue','','htmlspecialchars');
        $data['remark'] = I('post.remarkValue','','htmlspecialchars');
        $usstorage = M('usstorage');
    	$result =  $usstorage->save($data);
	    if($result) {
	    	$this->success('操作成功！');}
	    else{
	        $this->error('写入错误！');
	     }
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