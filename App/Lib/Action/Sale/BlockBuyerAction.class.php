<?php

class BlockBuyerAction extends CommonAction{

	public function index(){
		$Data=M(C('DB_BLOCK_BUYER'));
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $blockedBuyers = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('blockedBuyers',$blockedBuyers);
        $this->assign('page',$show);
        $this->display();
	}

	public function addBlockBuyer(){
		$this->assign('platforms',C('DB_BLOCK_BUYER_PLATFORM_CONSTANT'));
		$this->display();
	}

	public function addBlockBuyerHandle(){
		$map[C('DB_BLOCK_BUYER_BID')] = array('eq',$_POST[C('DB_BLOCK_BUYER_BID')]);
		$map[C('DB_BLOCK_BUYER_PLATFORM')] = array('eq',$_POST[C('DB_BLOCK_BUYER_PLATFORM')]);
		$exist = M(C('DB_BLOCK_BUYER'))->where($map)->find();
		if($exist!==false && $exist!==null){
			$this->error('该买家已存在！');
		}else{
			if(M(C('DB_BLOCK_BUYER'))->add($_POST)){
				$this->redirect('Sale/BlockBuyer/index','添加成功');
			}else{
				$this->error('添加失败，请重新添加');
			}
		}
	}

	public function delete($id){
		if(M(C('DB_BLOCK_BUYER'))->where(array(C('DB_BLOCK_BUYER_ID')=>$id))->delete()!==false){
			$this->redirect('Sale/BlockBuyer/index','删除成功');
		}else{
			$this->error('删除失败，请重新删除');
		}
	}

	public function textView(){
		$buyers = M(C('DB_BLOCK_BUYER'))->getField(C('DB_BLOCK_BUYER_BID'),true);
		foreach ($buyers as $key => $value) {
			if($blockedBuyer!=null){
				$blockedBuyer = $blockedBuyer.','.$value;
			}else{
				$blockedBuyer = $value;
			}
			
		}
		$this->assign('blockedBuyer',$blockedBuyer);
		$this->display();
	}
}

?>