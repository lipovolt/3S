<?php

class ShenzhenAction extends CommonAction{

	public function shenzhen(){
    $this->display();
	}

  public function importUsswInbound(){
    $this->display();
  }

  public function updateUsswInbound(){
    $this->display();
  }

/*
  创建美国自建仓入库明细表
  CREATE TABLE IF NOT EXISTS `usswYYYYMMDD` (
  `sku` varchar(10) primary key NOT NULL,
  `quantity` smallint(6) DEFAULT 0,
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

*/

  public function creatUsswInbound(){
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
                $tableName = '3s_ussw'.date(Ymd,time()); 
                $createTable="CREATE TABLE IF NOT EXISTS ".$tableName." (`sku` varchar(10) primary key NOT NULL,`quantity` smallint(6) DEFAULT 0) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                M()->execute($createTable,true);
                for($i=2;$i<=$highestRow;$i++)
                 {
                    $result = M()->execute("insert into ".$tableName." (sku, quantity) values(".$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue().", ".$data['quantity']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue().");");                                      
                 } 

                if($result){
                  $this->success('导入成功！');
                }
                else{
                  $this->error("导入失败！");
                }
         }else
             {
                 $this->error("请选择上传的文件");
             }
  }

  public function update(){

    /*先清空旧表

    */

    if(!empty(I('post.tableNameValue','','htmlspecialchars'))){
        M(I('post.tableNameValue','','htmlspecialchars'))->delete();
    }

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
                $tableName = '3s_ussw'.date(Ymd,time()); 
                $createTable="CREATE TABLE IF NOT EXISTS ".$tableName." (`sku` varchar(10) primary key NOT NULL,`quantity` smallint(6) DEFAULT 0) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
                M()->execute($createTable,true);
                for($i=2;$i<=$highestRow;$i++)
                 {
                    $result = M()->execute("insert into ".$tableName." (sku, quantity) values(".$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue().", ".$data['quantity']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue().");");                                      
                 } 

                if($result){
                  $this->success('导入成功！');
                }
                else{
                  $this->error("导入失败！");
                }
         }else
             {
                 $this->error("请选择上传的文件");
             }
         }
}

?>