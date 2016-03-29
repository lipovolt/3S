<?php

class UsswAction extends CommonAction{

	public function ussw(){
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

	public function batchAdd(){
    if (!empty($_FILES)) {
        import('ORG.Net.UploadFile');
         $config=array(
             'allowExts'=>array('xlsx','xls'),
             'savePath'=>'./Public/upload/',
             'saveRule'=>'time',
         );
         $upload = new UploadFile($config);
         if (!$upload->upload()) {
             $this->error($upload->getErrorMsg());
         } else {
             $info = $upload->getUploadFileInfo();
             
         }
     
         vendor("PHPExcel.PHPExcel");
             $file_name=$info[0]['savepath'].$info[0]['savename'];
             $objReader = PHPExcel_IOFactory::createReader('Excel5');
             $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
             $sheet = $objPHPExcel->getSheet(0);
             $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            for($i=2;$i<=$highestRow;$i++)
             {   
                 $data['sku']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                 $data['position']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                 $data['cname'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();  
                 $data['ename'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                 $data['attribute'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                 $data['cinventory']= $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                 $data['ainventory']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                 $data['oinventory']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                 $data['iinventory']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                 $data['csales']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                 $data['remark']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue(); 
                 M('usstorage')->add($data);
             } 

              $this->success('导入成功！');
     }else
         {
             $this->error("请选择上传的文件");
         }    
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