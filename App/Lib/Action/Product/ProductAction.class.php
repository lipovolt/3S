<?php

class ProductAction extends CommonAction{

    public function productInfo(){
        if($_POST['keyword']==""){
            $Data = M(C('db_product'));
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
            $this->products = M(C('db_product'))->where($where)->select();
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

                for ($i=$highestRow; $i >0 ; $i--) { 
                    if($sheet->getCell("A".$i) == null or $sheet->getCell("A".$i) =='')
                        $highestRow = $i;
                    else{
                        $highestRow = $i;
                        break;
                    }      
                }

                //excel firt column name verify
                for($c='A';$c<=$highestColumn;$c++){
                    $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
                }
                if($this->verifyImportedProductTemplateColumnName($firstRow)){    
                    $products = M(C('db_product'));
                    $products-> startTrans();
                    for($i=2;$i<=$highestRow;$i++)
                    {   
                        if($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()==''){
                            break;
                        }else{
                            $data=null;
                            $data[C('db_product_sku')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue(),"utf-8","auto");
                            $data[C('db_product_cname')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue(),"utf-8","auto");
                            $data[C('db_product_ename')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue(),"utf-8","auto");
                            $data[C('db_product_price')] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();  
                            $data[C('db_product_weight')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                            $data[C('db_product_length')] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                            $data[C('db_product_width')]= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                            $data[C('db_product_height')]= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                            $data[C('db_product_battery')]= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                            $data[C('db_product_tode')]= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                            $data[C('db_product_tous')]= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                            $data[C('db_product_ustariff')]= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                            $data[C('db_product_detariff')]= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();                             
                            $data[C('db_product_incoming_day')]= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                            $data[C('db_product_manager')]= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                            $data[C('db_product_supplier')]=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                            $data[C('db_product_ggs_ussw_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                            $data[C('db_product_rc_winit_us_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                            $data[C('db_product_rc_winit_de_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
                            $data[C('db_product_ebay_com_best_match')]=$objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
                            $data[C('db_product_ebay_com_price_lowest')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                            $data[C('db_product_ebay_de_best_match')]=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
                            $data[C('db_product_ebay_de_price_lowest')]=$objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue();
                            
                            $verifyError = $this->verifyProduct($data);
                            if($verifyError != null){
                                $this->error($verifyError);
                            }else{
                                if($products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->find() != null){
                                    $result = $products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->save($data);
                                }else{
                                    $result = $products->add($data);
                                }
                            }
                        }
                         
                    } 
                    $products->commit();
                    if(false !== $result || 0 !== $result){
                        $this->success('导入成功！');
                    }else{
                        $this->error("导入不成功！请重新上传。");
                    }
                }else{
                    $this->error("模板错误，请检查模板！");
                }
                
                
         }else
             {
                 $this->error("请选择上传的文件");
             }    
    }

    Public function productEdit($sku){
        $this->product = M(C('db_product'))->where(array(C('db_product_sku')=>$sku))->select();
        $this->display();

    }

    public function update(){
        $data = null;
        $data[C('db_product_id')] = I('post.'.C('db_product_id'),'','htmlspecialchars');
        $data[C('db_product_sku')] = I('post.'.C('db_product_sku'),'','htmlspecialchars');
        $data[C('db_product_upc')] = I('post.'.C('db_product_upc'),'','htmlspecialchars');
        $data[C('db_product_cname')] = I('post.'.C('db_product_cname'),'','htmlspecialchars');
        $data[C('db_product_ename')] = I('post.'.C('db_product_ename'),'','htmlspecialchars');
        $data[C('db_product_price')] = I('post.'.C('db_product_price'),'','htmlspecialchars');
        $data[C('db_product_weight')] = I('post.'.C('db_product_weight'),'','htmlspecialchars');
        $data[C('db_product_length')] = I('post.'.C('db_product_length'),'','htmlspecialchars');
        $data[C('db_product_width')] = I('post.'.C('db_product_width'),'','htmlspecialchars');
        $data[C('db_product_height')] = I('post.'.C('db_product_height'),'','htmlspecialchars');
        $data[C('db_product_battery')] = I('post.'.C('db_product_battery'),'','htmlspecialchars');
        $data[C('db_product_tode')] = I('post.'.C('db_product_tode'),'','htmlspecialchars');
        $data[C('db_product_tous')] = I('post.'.C('db_product_tous'),'','htmlspecialchars');
        $data[C('db_product_detariff')] = I('post.'.C('db_product_detariff'),'','htmlspecialchars');
        $data[C('db_product_ustariff')] = I('post.'.C('db_product_ustariff'),'','htmlspecialchars');
        $data[C('db_product_manager')] = I('post.'.C('db_product_manager'),'','htmlspecialchars');
        $data[C('db_product_supplier')] = I('post.'.C('db_product_supplier'),'','htmlspecialchars');
        $data[C('db_product_ggs_ussw_sale_price')] = I('post.'.C('db_product_ggs_ussw_sale_price'),'','htmlspecialchars');
        $data[C('db_product_rc_winit_us_sale_price')] = I('post.'.C('db_product_rc_winit_us_sale_price'),'','htmlspecialchars');
        $data[C('db_product_rc_winit_de_sale_price')] = I('post.'.C('db_product_rc_winit_de_sale_price'),'','htmlspecialchars');
        $data[C('DB_PRODUCT_AMAZON_USSW_SALE_PRICE')] = I('post.'.C('DB_PRODUCT_AMAZON_USSW_SALE_PRICE'),'','htmlspecialchars');
        $data[C('db_product_ebay_com_best_match')] = I('post.'.C('db_product_ebay_com_best_match'),'','htmlspecialchars');
        $data[C('db_product_ebay_com_price_lowest')] = I('post.'.C('db_product_ebay_com_price_lowest'),'','htmlspecialchars');
        $data[C('db_product_ebay_de_best_match')] = I('post.'.C('db_product_ebay_de_best_match'),'','htmlspecialchars');
        $data[C('db_product_ebay_de_price_lowest')] = I('post.'.C('db_product_ebay_de_price_lowest'),'','htmlspecialchars');
        
        $verifyError = $this->verifyProduct($data);
        if($verifyError != null){
            $this->error($verifyError);
        }else{
            $product = M(C('db_product'));
            $product-> startTrans();
            $result =  $product->save($data);
            $product->commit();
            if(false !== $result || 0 !== $result) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }

        }        
    }

    private function verifyImportedProductTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_PRODUCT')));$c++){
            if($firstRow[$c] != C('IMPORT_PRODUCT')[$c])
                return false;
        }
        return true;
    }

    private function verifyProduct($productToVerify){
        if($productToVerify[C('db_product_sku')] == null or $productToVerify[C('db_product_sku')] == '')
            return '产品编码是必填项！';
        elseif($productToVerify[C('db_product_cname')] == null or $productToVerify[C('db_product_cname')] == '')
            return '中文名称是必填项！';
        elseif($productToVerify[C('db_product_ename')] == null or $productToVerify[C('db_product_ename')] == '')
            return '英文名称是必填项！';
        elseif($productToVerify[C('db_product_price')] == null or $productToVerify[C('db_product_price')] == '')
            return '采购价是必填项！';
        elseif($productToVerify[C('db_product_weight')] == null or $productToVerify[C('db_product_weight')] == '')
            return '重量是必填项！';
        elseif($productToVerify[C('db_product_length')] == null or $productToVerify[C('db_product_length')] == '')
            return '长度是必填项！';
        elseif($productToVerify[C('db_product_width')] == null or $productToVerify[C('db_product_width')] == '')
            return '宽度是必填项！';
        elseif($productToVerify[C('db_product_height')] == null or $productToVerify[C('db_product_height')] == '')
            return '高度是必填项！';
        elseif($productToVerify[C('db_product_battery')] == null or $productToVerify[C('db_product_battery')] == '')
            return '电池属性是必填项！';
        elseif($productToVerify[C('db_product_tode')] == null or $productToVerify[C('db_product_tode')] == '')
            return '德国头程是必填项！';
        elseif($productToVerify[C('db_product_tous')] == null or $productToVerify[C('db_product_tous')] == '')
            return '美国头程是必填项！';
        elseif($productToVerify[C('db_product_manager')] == null or $productToVerify[C('db_product_manager')] == '')
            return '产品经理是必填项！';
        else
            return null;
    }
}

?>