<?php

class PostageAction extends CommonAction{

	public function firstclass(){
		$this->postage=M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->select();
		$this->display();
	}

	public function updateFirstClass(){
		if(IS_POST){
			$firstclass = M(C('DB_USSW_POSTAGE_FIRSTCLASS'));
			$count = $firstclass->count();
			$firstclass->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_USSW_POSTAGE_FIRSTCLASS_OZ')]=I('post.'.'oz'.$i,'','htmlspecialchars');
				$data[C('DB_USSW_POSTAGE_FIRSTCLASS_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$firstclass->save($data);
			}
			$firstclass->commit();
			$this->success('保存成功');	
		}

	}

}

?>