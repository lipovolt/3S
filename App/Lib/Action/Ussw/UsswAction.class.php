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
            $this->usstorage = M(C('DB_USSTORAGE'))->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
    }

    Public function usswEdit($sku){
        $this->usstorage = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->select();
        $this->display();

    }

    public function update(){
        $data[C('DB_USSTORAGE_ID')] = I('post.'.C('DB_USSTORAGE_ID'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_POSITION')] = I('post.'.C('DB_USSTORAGE_POSITION'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_SKU')] = I('post.'.C('DB_USSTORAGE_SKU'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CNAME')] = I('post.'.C('DB_USSTORAGE_CNAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ENAME')] = I('post.'.C('DB_USSTORAGE_ENAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ATTRIBUTE')] = I('post.'.C('DB_USSTORAGE_ATTRIBUTE'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.'.C('DB_USSTORAGE_CINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.'.C('DB_USSTORAGE_AINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_OINVENTORY')] = I('post.'.C('DB_USSTORAGE_OINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_IINVENTORY')] = I('post.'.C('DB_USSTORAGE_IINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CSALES')] = I('post.'.C('DB_USSTORAGE_CSALES'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_REMARK')] = I('post.'.C('DB_USSTORAGE_REMARK'),'','htmlspecialchars');
        $usstorage = M(C('DB_USSTORAGE'));
    	$result =  $usstorage->save($data);
	    if($result) {
	    	$this->success('操作成功！');}
	    else{
	        $this->error('写入错误！');
	     }
    }
}

?>