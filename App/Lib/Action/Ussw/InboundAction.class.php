<?php

class InboundAction extends CommonAction{

	public function index(){
		$Data = M('ussw_inbound');
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $inbounds = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('inbounds',$inbounds);
        $this->assign('page',$show);
		$this->display();
	}

	public function fileImport(){
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

    public function addInbound(){
    	$data['date'] = date('Y-m-d');
        $data['way'] = I('post.wayValue','','htmlspecialchars');
        $data['pQuantity'] = I('post.pQuantityValue','','htmlspecialchars');
        $data['weight'] = I('post.weightValue','','htmlspecialchars');
        $data['volume'] = I('post.volumeValue','','htmlspecialchars');
        $data['volumeWeight'] = (I('post.volumeValue','','htmlspecialchars')/5000)>I('post.weightValue','','htmlspecialchars')? I('post.volumeValue','','htmlspecialchars')/5000:I('post.weightValue','','htmlspecialchars');
        $data['status'] = I('post.statusValue','','htmlspecialchars');
        $usswInbound = M('ussw_inbound');
        $result =  $usswInbound->add($data);
		 if($result) {
		     $this->success('操作成功！');
		 }else{
		     $this->error('写入错误！');
		 }

    }
}

?>