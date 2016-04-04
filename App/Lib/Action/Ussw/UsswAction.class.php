<?php

class UsswAction extends CommonAction{

	public function ussw(){
		$this->display();
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
}

?>