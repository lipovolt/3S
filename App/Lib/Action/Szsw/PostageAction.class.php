<?php

class PostageAction extends CommonAction{

	public function eub($insert=false){
		if(!$insert){
			$this->postage=M(C('DB_SZ_POSTAGE_EUB'))->select();
			$this->display();
		}else{
			$eub = M(C('DB_SZ_POSTAGE_EUB'));
			$postage=$eub->select();
			$count = $eub->count();
			$data[C('DB_SZ_POSTAGE_EUB_ID')] = $count+1;
			$data[C('DB_SZ_POSTAGE_EUB_COUNTRY')] = null;
			$data[C('DB_SZ_POSTAGE_EUB_REGISTER')] = null;
			$data[C('DB_SZ_POSTAGE_EUB_FEE')] = null;
			$postage[$count+1]=$data;
			$this->assign('postage',$postage);
			$this->display();
		}
		
	}

	public function saveEub(){
		if(IS_POST){
			$eub = M(C('DB_SZ_POSTAGE_EUB'));
			$count = $eub->count();
			$eub->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_SZ_POSTAGE_EUB_ID')]=I('post.'.'id'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_EUB_COUNTRY')]=I('post.'.'country'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_EUB_REGISTER')]=I('post.'.'register'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_EUB_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$eub->save($data);
			}
			$count = $count+1;
			if(I('post.'.'country'.$count,'','htmlspecialchars')!=null && I('post.'.'country'.$count,'','htmlspecialchars')!=''){
				$new[C('DB_SZ_POSTAGE_EUB_COUNTRY')]=I('post.'.'country'.$count,'','htmlspecialchars');
				$new[C('DB_SZ_POSTAGE_EUB_REGISTER')]=I('post.'.'register'.$count,'','htmlspecialchars');
				$new[C('DB_SZ_POSTAGE_EUB_FEE')]=I('post.'.'fee'.$count,'','htmlspecialchars');
				$eub->add($new);
			}
			$eub->commit();
			$this->success('保存成功');	
		}
	}

	public function cpc($insert=false){
		if(!$insert){
			$this->postage=M(C('DB_SZ_POSTAGE_CPC'))->select();
			$this->display();
		}else{
			$cpc = M(C('DB_SZ_POSTAGE_CPC'));
			$postage=$cpc->select();
			$count = $cpc->count();
			$data[C('DB_SZ_POSTAGE_CPC_ID')] = $count+1;
			$data[C('DB_SZ_POSTAGE_CPC_COUNTRY')] = null;
			$data[C('DB_SZ_POSTAGE_CPC_CLASS')] = null;
			$postage[$count+1]=$data;
			$this->assign('postage',$postage);
			$this->display();
		}
		
	}

	public function saveCpc(){
		if(IS_POST){
			$cpc = M(C('DB_SZ_POSTAGE_CPC'));
			$count = $cpc->count();
			$cpc->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_SZ_POSTAGE_CPC_ID')]=I('post.'.'id'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_CPC_COUNTRY')]=I('post.'.'country'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_CPC_CLASS')]=I('post.'.'class'.$i,'','htmlspecialchars');
				$cpc->save($data);
			}
			$count = $count+1;
			if(I('post.'.'country'.$count,'','htmlspecialchars')!=null && I('post.'.'country'.$count,'','htmlspecialchars')!=''){
				$new[C('DB_SZ_POSTAGE_CPC_COUNTRY')]=I('post.'.'country'.$count,'','htmlspecialchars');
				$new[C('DB_SZ_POSTAGE_CPC_CLASS')]=I('post.'.'class'.$count,'','htmlspecialchars');
				$cpc->add($new);
			}
			$cpc->commit();
			$this->success('保存成功');	
		}
	}

	public function cpf($insert=false){
		if(!$insert){
			$this->postage=M(C('DB_SZ_POSTAGE_CPF'))->select();
			$this->display();
		}else{
			$cpf = M(C('DB_SZ_POSTAGE_CPF'));
			$postage=$cpf->select();
			$count = $cpf->count();
			$data[C('DB_SZ_POSTAGE_CPF_ID')] = $count+1;
			$data[C('DB_SZ_POSTAGE_CPF_REGISTER')] = null;
			$data[C('DB_SZ_POSTAGE_CPF_CLASS')] = null;
			$data[C('DB_SZ_POSTAGE_CPF_FEE')] = null;
			$postage[$count+1]=$data;
			$this->assign('postage',$postage);
			$this->display();
		}
		
	}

	public function saveCpf(){
		if(IS_POST){
			$cpf = M(C('DB_SZ_POSTAGE_CPF'));
			$count = $cpf->count();
			$cpf->startTrans();
			for($i=1;$i<=$count;$i++){
				$data[C('DB_SZ_POSTAGE_CPF_ID')]=I('post.'.'id'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_CPF_REGISTER')]=I('post.'.'register'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_CPF_CLASS_ID')]=I('post.'.'class'.$i,'','htmlspecialchars');
				$data[C('DB_SZ_POSTAGE_CPF_CLASS_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$cpf->save($data);
			}
			$count = $count+1;
			if(I('post.'.'class'.$count,'','htmlspecialchars')!=null && I('post.'.'class'.$count,'','htmlspecialchars')!=''){
				$new[C('DB_SZ_POSTAGE_CPF_REGISTER')]=I('post.'.'register'.$i,'','htmlspecialchars');
				$new[C('DB_SZ_POSTAGE_CPF_CLASS_ID')]=I('post.'.'class'.$i,'','htmlspecialchars');
				$new[C('DB_SZ_POSTAGE_CPF_CLASS_FEE')]=I('post.'.'fee'.$i,'','htmlspecialchars');
				$cpf->add($new);
			}
			$cpf->commit();
			$this->success('保存成功');	
		}
	}
}

?>