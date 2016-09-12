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

	public function priorityFlatRate(){
		$this->postage=M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->select();
		$this->display();
	}

	public function updatepriorityFlatRate(){
		if(IS_POST){
			$pfr = M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'));
			$count = $pfr->count();
			$pfr->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')]=I('post.'.'id'.$i,'','htmlspecialchars');
				$data[C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$pfr->save($data);
			}
			$pfr->commit();
			$this->success('保存成功');	
		}

	}

	public function priority(){
		$this->postage=M(C('DB_USSW_POSTAGE_PRIORITY'))->select();
		$this->display();
	}

	public function updatePriority(){
		if(IS_POST){
			$priority = M(C('DB_USSW_POSTAGE_PRIORITY'));
			$count = $priority->count();
			$priority->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_USSW_POSTAGE_PRIORITY_LBS')]=I('post.'.'lbs'.$i,'','htmlspecialchars');
				$data[C('DB_USSW_POSTAGE_PRIORITY_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$priority->save($data);
			}
			$priority->commit();
			$this->success('保存成功');	
		}

	}

	public function fedexSmartPost(){
		$this->postage=M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->select();
		$this->display();
	}

	public function updateFedexSmart(){
		if(IS_POST){
			$priority = M(C('DB_USSW_POSTAGE_FEDEX_SMART'));
			$count = $priority->count();
			$priority->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_USSW_POSTAGE_FEDEX_SMART_LBS')]=I('post.'.'lbs'.$i,'','htmlspecialchars');
				$data[C('DB_USSW_POSTAGE_FEDEX_SMART_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$priority->save($data);
			}
			$priority->commit();
			$this->success('保存成功');	
		}

	}

	public function fedexHomeDelivery(){
		$this->postage=M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->select();
		$this->display();
	}

	public function updateFedexHome(){
		if(IS_POST){
			$priority = M(C('DB_USSW_POSTAGE_FEDEX_HOME'));
			$count = $priority->count();
			$priority->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_USSW_POSTAGE_FEDEX_HOME_LBS')]=I('post.'.'lbs'.$i,'','htmlspecialchars');
				$data[C('DB_USSW_POSTAGE_FEDEX_HOME_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$priority->save($data);
			}
			$priority->commit();
			$this->success('保存成功');	
		}

	}

}

?>