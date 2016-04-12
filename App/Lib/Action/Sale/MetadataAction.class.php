<?php

class MetadataAction extends CommonAction{
	public function index(){
		$data = M(C('DB_METADATA'))->select();
		$this->assign('data',$data);
		$this->display();
	}

	public function update(){
		if($this->isPost()){
			$data[C('DB_METADATA_ID')] = I('post.'.C('DB_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTOUSD')] = I('post.'.C('DB_METADATA_EURTOUSD'),'','htmlspecialchars');
			$data[C('DB_METADATA_USDTORMB')] = I('post.'.C('DB_METADATA_USDTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTORMB')] = I('post.'.C('DB_METADATA_EURTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_DEMWST')] = I('post.'.C('DB_METADATA_DEMWST'),'','htmlspecialchars');
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
}

?>