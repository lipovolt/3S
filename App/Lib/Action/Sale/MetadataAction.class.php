<?php

class MetadataAction extends CommonAction{
	public function index(){
		$data = M(C('DB_METADATA'))->select();
		$pw = array('ebay greatgoodshop'=>'04052016','ebay blackfive'=>'04052016','amazon 498307481@qq.com'=>'09092014','groupon 498307481@qq.com'=>'02072017','ebay rc-helicar'=>'09092014','ebay vtkg5755'=>'09092014','ebay yzhan-816'=>'04052016','阿里云 112.74.39.0'=>'administrator/Shangsi2018/shangs','阿里云 120.79.171.131'=>'administrator/Shangsi2018/674584');
		$this->assign('data',$data);
		$this->assign('pw',$pw);
		$this->display();
	}

	public function update(){
		if($this->isPost()){
			$data[C('DB_METADATA_ID')] = I('post.'.C('DB_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTOUSD')] = I('post.'.C('DB_METADATA_EURTOUSD'),'','htmlspecialchars');
			$data[C('DB_METADATA_USDTORMB')] = I('post.'.C('DB_METADATA_USDTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTORMB')] = I('post.'.C('DB_METADATA_EURTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_DEMWST')] = I('post.'.C('DB_METADATA_DEMWST'),'','htmlspecialchars');
			$data[C('DB_METADATA_USED_UPC')] = I('post.'.C('DB_METADATA_USED_UPC'),'','htmlspecialchars');
			$metadata = M(C('DB_METADATA'));
			$metadata->startTrans();
			$result = $metadata->save($data);
			$metadata->commit();
            if(false !== $result || 0 !== $result){
                $this->success('保存成功！');
            }else{
                $this->error("保存不成功！");
            }

		}
	}

	public function usSalePlanMetadata(){
		$this->data=M(C('DB_USSW_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function updateUsSaleMetaDate(){
		if($this->isPost()){
			$data[C('DB_USSW_SALE_PLAN_METADATA_ID')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR1')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR1'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR2')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR2'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR3')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR3'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR4')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR4'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR5')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR5'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_PCR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_PCR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SQNR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SQNR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_DENOMINATOR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_DENOMINATOR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_GRFR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_GRFR'),'','htmlspecialchars');
			$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'));
			$metadata->startTrans();
			$result = $metadata->save($data);

			$metadata->commit();
            if(false !== $result || 0 !== $result){
                $this->success('保存成功！');
            }else{
                $this->error("保存不成功！");
            }

		}
	}

	public function szSalePlanMetadata(){
		$this->data=M(C('DB_SZ_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function updateSzSaleMetaDate(){
		if($this->isPost()){
			$data[C('DB_SZ_SALE_PLAN_METADATA_ID')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR1')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR1'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR2')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR2'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR3')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR3'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR4')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR4'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR5')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR5'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_PCR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_PCR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SQNR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SQNR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_GRFR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_GRFR'),'','htmlspecialchars');
			$metadata = M(C('DB_SZ_SALE_PLAN_METADATA'));
			$metadata->startTrans();
			$result = $metadata->save($data);

			$metadata->commit();
            if(false !== $result || 0 !== $result){
                $this->success('保存成功！');
            }else{
                $this->error("保存不成功！");
            }

		}
	}
}

?>