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
            $products = $Data->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('products',$products);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->products = M(C('db_product'))->where($where)->select();
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
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
                    $szStorage = M(C('DB_SZSTORAGE'));
                    $szStorage-> startTrans();
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
                            $data[C('db_product_purchase_link')]=$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                            $data[C('db_product_ggs_ussw_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                            $data[C('db_product_rc_winit_us_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
                            $data[C('db_product_rc_winit_de_sale_price')]=$objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
                            $data[C('db_product_ebay_com_best_match')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                            $data[C('db_product_ebay_com_price_lowest')]=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
                            $data[C('db_product_ebay_de_best_match')]=$objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue();
                            $data[C('db_product_ebay_de_price_lowest')]=$objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue();
                            
                            $verifyError = $this->verifyProduct($data);
                            if($verifyError != null){
                                $this->error($verifyError);
                            }else{
                                $p=$products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->find();
                                if($p!=null && $p!==false){
                                    $result = $products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->save($data);
                                }else{
                                    $result = $products->add($data);
                                    $this->addToSzStorage( $data[C('DB_PRODUCT_SKU')]);
                                }
                            }
                        }
                         
                    } 
                    $products->commit();
                    $szStorage->commit();
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

    private function addToSzStorage($sku){
        $szstorage = M(C('DB_SZSTORAGE'));
        $szs = $szstorage->where(array(C('DB_SZSTORAGE_SKU')=>$sku))->find();
        if($szs==null){
            $szs[C('DB_SZSTORAGE_SKU')] = $sku;
            $szs[C('DB_SZSTORAGE_CINVENTORY')] = 0;
            $szs[C('DB_SZSTORAGE_AINVENTORY')] = 0;
            $szstorage->add($szs);
        }
    }

    Public function productEdit($sku){
        $product = M(C('db_product'))->where(array(C('db_product_sku')=>$sku))->select();
        $product[0][C('DB_METADATA_USED_UPC')] = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USED_UPC'));
        $this->assign('product',$product);
        $this->display();

    }

    public function barcode($sku){
        Vendor('tcpdf.tcpdf_barcodes_1d');
        Vendor('tcpdf.tcpdf');
        $barcodeobj = new TCPDFBarcode('P', 'mmm',array(400,300));
        $barcode = $barcodeobj->getBarcodeHTML(1, 20, 'black');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        // set font
        $pdf->SetFont('helvetica', '', 11);
        // define barcode style
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );
        $pdf->AddPage('L');

        // CODE 128 AUTO 
        $style['position'] = 'C';       
        $pdf->write1DBarcode('1001.01', 'C128A', '', '', '', 18, 0.4, $style, 'N');
        $pdf->Cell(0, 0, '  MADE IN CHINA', 0, 1, 'C');

        $pdf->Ln();
        $pdf->Output('sku', 'I');
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
         $data[C('db_product_pweight')] = I('post.'.C('db_product_pweight'),'','htmlspecialchars');
        $data[C('db_product_plength')] = I('post.'.C('db_product_plength'),'','htmlspecialchars');
        $data[C('db_product_pwidth')] = I('post.'.C('db_product_pwidth'),'','htmlspecialchars');
        $data[C('db_product_pheight')] = I('post.'.C('db_product_pheight'),'','htmlspecialchars');
        $data[C('db_product_premark')] = I('post.'.C('db_product_premark'),'','htmlspecialchars');
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
        $data[C('DB_PRODUCT_SZ_US_SALE_PRICE')] = I('post.'.C('DB_PRODUCT_SZ_US_SALE_PRICE'),'','htmlspecialchars');
        $data[C('DB_PRODUCT_SZ_DE_SALE_PRICE')] = I('post.'.C('DB_PRODUCT_SZ_DE_SALE_PRICE'),'','htmlspecialchars');
        $data[C('db_product_ebay_com_best_match')] = I('post.'.C('db_product_ebay_com_best_match'),'','htmlspecialchars');
        $data[C('db_product_ebay_com_price_lowest')] = I('post.'.C('db_product_ebay_com_price_lowest'),'','htmlspecialchars');
        $data[C('db_product_ebay_de_best_match')] = I('post.'.C('db_product_ebay_de_best_match'),'','htmlspecialchars');
        $data[C('db_product_ebay_de_price_lowest')] = I('post.'.C('db_product_ebay_de_price_lowest'),'','htmlspecialchars');
        $data[C('db_product_purchase_link')] = I('post.'.C('db_product_purchase_link'),'','htmlspecialchars');
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

    /*public function saveUPC($upc,$id){
        $data[C('db_product_id')] = $id;
        $data[C('db_product_upc')] = $upc;
        M(C('DB_PRODUCT'))->save($data);
        $mdata[C('DB_METADATA_USED_UPC')]=$upc;
        $mdata[C('DB_METADATA_ID')]=1;
        M(C('DB_METADATA'))->save($mdata);
        $this->success("UPC码已保存");
    }*/

    public function allocatUpc($id){
        $product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_ID')=>$id))->find();
        $product[C('DB_PRODUCT_UPC')] = $this->generateUPC();
        M(C('DB_PRODUCT'))->save($product);
        $this->success("UPC码已保存");
    }

    private function generateUPC(){
        $usedUpc = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USED_UPC'));
        $num=str_split($usedUpc);
        if((int)$num[10]==9){
            $oddSum = (int)$num[0]+(int)$num[2]+(int)$num[4]+(int)$num[6]+(int)$num[8];
            $evenSum = (int)$num[1]+(int)$num[3]+(int)$num[5]+(int)$num[7]+(int)$num[9]+1;
        }else{
            $oddSum = (int)$num[0]+(int)$num[2]+(int)$num[4]+(int)$num[6]+(int)$num[8]+(int)$num[10]+1;
            $evenSum = (int)$num[1]+(int)$num[3]+(int)$num[5]+(int)$num[7]+(int)$num[9];
        }        
        $sum = $oddSum*3+$evenSum;
        $verifyNumber = (10-round($sum%10))==10?0:(10-round($sum%10));
        if($verifyNumber==0){;
            $upc = (substr($usedUpc, 0,11)+1)*10;
        }else{
            $upc = (substr($usedUpc, 0,11)+1)*10+$verifyNumber;
        }
        M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->setField(C('DB_METADATA_USED_UPC'),$upc);
        return $upc;
    }

    private function verifyImportedProductTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_PRODUCT')));$c++){
            if($firstRow[$c] != C('IMPORT_PRODUCT')[$c]){
                return false;
            }                
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
        elseif($productToVerify[C('db_product_battery')] == null or $productToVerify[C('db_product_battery')] == '')
            return '电池属性是必填项！';
        elseif($productToVerify[C('db_product_tode')] == null or $productToVerify[C('db_product_tode')] == '')
            return '德国头程是必填项！';
        elseif($productToVerify[C('db_product_tous')] == null or $productToVerify[C('db_product_tous')] == '')
            return '美国头程是必填项！';
        elseif($productToVerify[C('db_product_manager')] == null or $productToVerify[C('db_product_manager')] == '')
            return '产品经理是必填项！';
        elseif($productToVerify[C('db_product_supplier')] == null or $productToVerify[C('db_product_supplier')] == '')
            return '供货商编号是必填项';
        elseif($productToVerify[C('db_product_purchase_link')] == null or $productToVerify[C('db_product_purchase_link')] == '')
            return '采购链接是必填项';
        else
            return null;
    }

    public function updateWinitProductList(){
        $this->display();
    }

    public function updateWinitProductInfo(){
        if (!empty($_FILES) && I('post.country','','htmlspecialchars') != "unSelected") {
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
                for($c='A';$c!=$highestColumn;$c++){
                    $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();                    
                }
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($highestColumn.'1')->getValue();   

                if($this->verifyImportedWinitProductTemplateColumnName($firstRow)){    
                    $products = M(C('db_product'));
                    $products-> startTrans();
                    for($i=2;$i<=$highestRow;$i++)
                    {   
                        if($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()==''){
                            break;
                        }else{
                            $data=null;
                            
                            if(I('post.country','','htmlspecialchars')=="de"){
                                $data[C('db_product_sku')]= $this->toTextSku(mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue(),"utf-8","auto")); 
                                $data[C('db_product_pweight')] = $objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue()*1000;
                                $data[C('db_product_plength')] = $objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue();
                                $data[C('db_product_pwidth')]= $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue();
                                $data[C('db_product_pheight')]= $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getValue();
                                $data[C('db_product_detariff')]= $objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue();

                            }elseif(I('post.country','','htmlspecialchars')=="us"){
                                $data[C('db_product_sku')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue(),"utf-8","auto"); 
                                $data[C('db_product_ustariff')]= $objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue()==0 ?5:$objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue();
                            }                           
                            $tmp = $products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->find()>;
                            if($tmp != null && ($tmp[C('db_product_pweight')]==null || $tmp[C('db_product_pweight')]==0)){
                                $result = $products->where(array(C('db_product_sku')=>$data[C('db_product_sku')]))->save($data);
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
                 $this->error("请选择上传的文件和国家");
             } 
    }

    private function verifyImportedWinitProductTemplateColumnName($firstRow){
        for($c='A';$c!=end(array_keys(C('IMPORT_WINIT_PRODUCT')));$c++){
            if($firstRow[$c] != C('IMPORT_WINIT_PRODUCT')[$c]){ 
                return false;
            }      
        }
        if($firstRow[end(array_keys(C('IMPORT_WINIT_PRODUCT')))] != C('IMPORT_WINIT_PRODUCT')[end(array_keys(C('IMPORT_WINIT_PRODUCT')))]){
            return false;
        }else{
            return true;
        }        
    }

    public function productPackRequirement(){
        $this->assign('products', D('ProductPackRequirementView')->select());
        $this->display();
    }

    public function pprSearch(){
         if($_POST['keywordValue']==""){
            $Data = D('ProductPackRequirementView');
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $products = $Data->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('products',$products);
            $this->assign('page',$show);
        }
        else{
            $where[C('DB_PRODUCT_SKU')] = array('eq',I('post.keywordValue','','htmlspecialchars'));
            $pId=M(C('DB_PRODUCT'))->where($where)->getField(C('DB_PRODUCT_ID'));
            $products = D('ProductPackRequirementView')->where(array(C('DB_PRODUCT_PACK_REQUIREMENT_PRODUCT_ID')=>$pId))->select();
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('products',$products);
        }
        $this->display("productPackRequirement");
    }

    public function newPackRequirement(){
        $this->display();
    }

    public function newPackRequirementHandle(){
        $product_id = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$_POST[C('DB_PRODUCT_SKU')]))->getField(C('DB_PRODUCT_ID'));
        $map[C('DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE')] = array('eq', $_POST[C('DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE')]);
        $map[C('DB_PRODUCT_PACK_REQUIREMENT_PRODUCT_ID')] = array('eq', $product_id);
        $existSku = M(C('DB_PRODUCT_PACK_REQUIREMENT'))->where($map)->find();
        if($product_id!=null && $existSku==null){
            $data[C('DB_PRODUCT_PACK_REQUIREMENT_PRODUCT_ID')] = $product_id;
            $data[C('DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE')] = $_POST[C('DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE')];
            $data[C('DB_PRODUCT_PACK_REQUIREMENT_REQUIREMENT')] = $_POST[C('DB_PRODUCT_PACK_REQUIREMENT_REQUIREMENT')];
            if(M(C('DB_PRODUCT_PACK_REQUIREMENT'))->add($data)){
                $this->redirect('productPackRequirement','',1,'已保存');
            }else{
                $this->error('保存失败，请重新保存');
            }
            
        }else{
            $this->error($_POST[C('DB_PRODUCT_SKU')].' 该产品不存在 或者该产品已经在包装要求表里');
        }
    }

    public function pprEdit($id){
        $this->assign('requirement',D('ProductPackRequirementView')->where(array(C('DB_PRODUCT_PACK_REQUIREMENT_ID')=>$id))->find());
        $this->assign('warehouse',C('WAREHOUSE'));
        $this->display();
    }

    public function editPackRequirementHandle(){
        if(M(C('DB_PRODUCT_PACK_REQUIREMENT'))->save($_POST)){
            $this->redirect('productPackRequirement','',1,'已保存');
        }else{
            $this->error('保存失败，请重新保存');
        }
    }

    public function pprDelete($id){
        if(M(C('DB_PRODUCT_PACK_REQUIREMENT'))->where(array(C('DB_PRODUCT_PACK_REQUIREMENT_ID')=>$id))->delete()){
            $this->success('已删除');
        }else{
            $this->error('删除失败，请重新删除');
        }
    }
}

?>