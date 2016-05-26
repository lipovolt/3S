<?php

class SupplierAction extends CommonAction{
	public function index(){
		if($_POST['keyword']==""){
            $Data = M(C('DB_SUPPLIER'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $supplier = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('supplier',$supplier);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->supplier = M(C('DB_SUPPLIER'))->where($where)->select();
        }
        $this->display();
	}

	public function add(){
		if(IS_POST){
			$data[C('DB_SUPPLIER_COMPANY')] = I('post.'.C('DB_SUPPLIER_COMPANY'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_PERSON')] = I('post.'.C('DB_SUPPLIER_PERSON'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_WANGWANG')] = I('post.'.C('DB_SUPPLIER_WANGWANG'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_QQ')] = I('post.'.C('DB_SUPPLIER_QQ'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_WEBSITE')] = I('post.'.C('DB_SUPPLIER_WEBSITE'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_TEL')] = I('post.'.C('DB_SUPPLIER_TEL'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_ADDRESS')] = I('post.'.C('DB_SUPPLIER_ADDRESS'),'','htmlspecialchars');

			M(C('DB_SUPPLIER'))->add($data);
			$this->redirect('Purchase/Supplier/index');
		}
	}

	public function editSupplier($id){
		$supplier = M(C('DB_SUPPLIER'))->where(array(C('DB_SUPPLIER_ID')=>$id))->select();
		$this->assign('supplier',$supplier);
		$this->display();
	}

	public function edit(){
		if(IS_POST){
			$data[C('DB_SUPPLIER_ID')] = I('post.'.C('DB_SUPPLIER_ID'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_COMPANY')] = I('post.'.C('DB_SUPPLIER_COMPANY'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_PERSON')] = I('post.'.C('DB_SUPPLIER_PERSON'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_WANGWANG')] = I('post.'.C('DB_SUPPLIER_WANGWANG'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_QQ')] = I('post.'.C('DB_SUPPLIER_QQ'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_WEBSITE')] = I('post.'.C('DB_SUPPLIER_WEBSITE'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_TEL')] = I('post.'.C('DB_SUPPLIER_TEL'),'','htmlspecialchars');
			$data[C('DB_SUPPLIER_ADDRESS')] = I('post.'.C('DB_SUPPLIER_ADDRESS'),'','htmlspecialchars');

			M(C('DB_SUPPLIER'))->save($data);
			$this->redirect('Purchase/Supplier/index');
		}
	}
}

?>