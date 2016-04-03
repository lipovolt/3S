<?php

class ProductAction extends CommonAction{

    public function productInfo(){
        if($_POST['keyword']==""){
            $Data = M('products');
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $products = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('products',$products);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->products = M('products')->where($where)->select();
        }
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
                     $data['sku']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                     $data['title-cn']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                     $data['price'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();  
                     $data['weight'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                     $data['length'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                     $data['width']= $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                     $data['height']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                     $data['battery']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                     $data['de']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue()=='None' ?0:1;
                     $data['way-to-de']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                     $data['us']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue()=='None' ?0:1;
                     $data['way-to-us']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                     $data['de-rate']= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                     $data['us-rate']= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                     $data['manager']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                     $data['supplier']=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                     
                     M('products')->add($data);
                 } 

                  $this->success('导入成功！');
         }else
             {
                 $this->error("请选择上传的文件");
             }    
    }

    Public function productEdit($sku){
        $this->product = M('products')->where(array('sku'=>$sku))->select();
        $this->display();

    }

    public function update(){
        $data['id'] = I('post.ProductCode','','htmlspecialchars');
        $data['sku'] = I('post.skuValue','','htmlspecialchars');
        $data['title-cn'] = I('post.Name','','htmlspecialchars');
        $data['title-en'] = I('post.EName','','htmlspecialchars');
        $data['price'] = I('post.price','','htmlspecialchars');
        $data['weight'] = I('post.weightValue','','htmlspecialchars');
        $data['length'] = I('post.lengthValue','','htmlspecialchars');
        $data['width'] = I('post.widthValue','','htmlspecialchars');
        $data['height'] = I('post.heightValue','','htmlspecialchars');
        if (I('post.battery','','htmlspecialchars')=='on'){
            $data['battery'] = 1;
        }
        else{
           $data['battery'] = 0; 
        }
        if (I('post.de','','htmlspecialchars')=='on'){
            $data['de'] = 1;
        }
        else{
           $data['de'] = 0; 
        }
        $data['way-to-de'] = I('post.way-to-de-value','','htmlspecialchars');
        if (I('post.us','','htmlspecialchars')=='on'){
            $data['us'] = 1;
        }
        else{
           $data['us'] = 0; 
        }
        $data['way-to-us'] = I('post.way-to-us-value','','htmlspecialchars');
        $data['de-rate'] = I('post.de-rate-value','','htmlspecialchars');
        $data['us-rate'] = I('post.us-rate-value','','htmlspecialchars');
        $data['manager'] = I('post.managerValue','','htmlspecialchars');
        $data['supplier'] = I('post.supplierValue','','htmlspecialchars');
        $data['ggs-ussw-sp'] = I('post.ggs-ussw-sp','','htmlspecialchars');
        $data['rc-winit-us-sp'] = I('post.rc-winit-us-sp','','htmlspecialchars');
        $data['rc-winit-de-sp'] = I('post.rc-winit-de-sp','','htmlspecialchars');
        $data['ebaycombest'] = I('post.ebaycombest','','htmlspecialchars');
        $data['ebaycomcheapest'] = I('post.ebaycomcheapest','','htmlspecialchars');
        $data['ebaydebest'] = I('post.ebaydebest','','htmlspecialchars');
        $data['ebaydecheapest'] = I('post.ebaydecheapest','','htmlspecialchars');
        $product = M('products');
        $result =  $product->save($data);
             if($result) {
                 $this->success('操作成功！');
             }else{
                 $this->error('写入错误！');
             }

    }
}

?>