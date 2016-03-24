<?php

class ManageAction extends CommonAction{

	public function index(){		
		$this->display();
	}

	public function productInfo(){
		$this->display();
	}

	public function productBatchAdd(){
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
                    /*data['account']= $data['truename'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();  
                     $sex = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    // $data['res_id']    = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                     $data['class'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                     $data['year'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                     $data['city']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                     $data['company']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                     $data['zhicheng']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                     $data['zhiwu']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                     $data['jibie']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                     $data['honor']= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                     $data['tel']= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                     $data['qq']= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                     $data['email']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                     $data['remark']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                     $data['sex']=$sex=='男'?1:0;
                     $data['res_id'] =1;
                     
                     $data['last_login_time']=0;
                     $data['create_time']=$data['last_login_ip']=$_SERVER['REMOTE_ADDR'];
                     $data['login_count']=0;
                     $data['join']=0;
                     $data['avatar']='';
                     $data['password']=md5('123456');              
                     M('Member')->add($data);*/
                     $data['sku']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                     $data['title-cn']= $data['title-en'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                     $data['price'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();  
                     $data['weigth'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                     $data['length'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                     $data['width']= $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                     $data['height']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                     $data['battery']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                     $data['de']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                     $data['way-to-de']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                     $data['us']= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                     $data['way-to-us']= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                     $data['de-declare']= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                     $data['us-declare']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                     $data['manager']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                     $data['supplier']=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();   
                     M('products')->add($data);
                 } 

                  $this->success('导入成功！');
         }else
             {
                 $this->error("请选择上传的文件");
             }    
          

     }
}

?>