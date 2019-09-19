<?php

class InboundAction extends CommonAction{

	public function index(){
		$winitdeInOrders = M(C('DB_WINITDE_INBOUND'))->order('id desc')->select();
        import('ORG.Util.Page');
        foreach ($winitdeInOrders as $key => $value) {
            $Data[$key]=array(
                C('DB_WINITDE_INBOUND_ID')=>$value[C('DB_WINITDE_INBOUND_ID')],
                C('DB_WINITDE_INBOUND_DATE')=>$value[C('DB_WINITDE_INBOUND_DATE')],
                C('DB_WINITDE_INBOUND_RECEIVE_DATE')=>$value[C('DB_WINITDE_INBOUND_RECEIVE_DATE')],
                C('DB_WINITDE_INBOUND_SHIPPING_WAY')=>$value[C('DB_WINITDE_INBOUND_SHIPPING_WAY')],
                C('DB_WINITDE_INBOUND_STATUS')=>$value[C('DB_WINITDE_INBOUND_STATUS')],
                'declare-package-quantity'=>$this->getInboundOrderPackageQuantity($value[C('DB_WINITDE_INBOUND_ID')]),
                'weight'=>$this->getInboundOrderPackageWeight($value[C('DB_WINITDE_INBOUND_ID')]),
                $volume = $this->getInboundOrderPackageVolume($value[C('DB_WINITDE_INBOUND_ID')]),
                'volume'=>$volume/1000000,
                'volumeWeight'=>$this->getInboundOrderPackageVolumeWeight($value[C('DB_WINITDE_INBOUND_ID')]),
                'declare-item-quantity'=>$this->getInboundOrderItemQuantity($value[C('DB_WINITDE_INBOUND_ID')],C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')),
                'confirmed-item-quantity'=>$this->getInboundOrderItemQuantity($value[C('DB_WINITDE_INBOUND_ID')],C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')),
                );
        }
        $count = count($Data);
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $inbounds = array_slice($Data, $Page->firstRow,$Page->listRows);
        $this->assign('inbounds',$inbounds);
        $this->assign('page',$show);
        $this->display();
	}

    private function getInboundOrderItemQuantity($orderID,$column){
        $quantityArray = M(C('DB_WINITDE_INBOUND_ITEM'))->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$orderID))->getField($column,true);
        $quantity=0;
        foreach ($quantityArray as $key => $value) {
            $quantity = $quantity + $value;
        }
        return $quantity;
    }

    private function getInboundOrderPackageQuantity($orderID){
        $resultArray = M(C('DB_WINITDE_INBOUND_PACKAGE'))->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        return count($resultArray);
    }

    private function getInboundOrderPackageWeight($orderID){
        $weightArray = M(C('DB_WINITDE_INBOUND_PACKAGE'))->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->getField(C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT'),true);
        $total=0;
        foreach ($weightArray as $key => $value) {
            $total = $total + $value;
        }
        return $total;
    }

    private function getInboundOrderPackageVolume($orderID){
        $resultArray = M(C('DB_WINITDE_INBOUND_PACKAGE'))->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $total=0;
        foreach ($resultArray as $key => $value) {
            $total = $total + $value[C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')]*$value[C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')]*$value[C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')];
        }
        return $total;
    }

    private function getInboundOrderPackageVolumeWeight($orderID){
        $resultArray = M(C('DB_WINITDE_INBOUND_PACKAGE'))->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $total=0;
        foreach ($resultArray as $key => $value) {
            $total = $total + $value[C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')]*$value[C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')]*$value[C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')]/5000;
        }
        return round($total,2);
    }

	public function fileImport(){
		$this->display();
	}

    public function addInbound(){
    	$data[C('DB_WINITDE_INBOUND_DATE')] = date('Y-m-d');
        $data[C('DB_WINITDE_INBOUND_SHIPPING_WAY')] = I('post.'.C('DB_WINITDE_INBOUND_SHIPPING_WAY'),'','htmlspecialchars');
        $winitdeInbound = M(C('DB_WINITDE_INBOUND'));
        $winitdeInbound->startTrans();
        $result =  $winitdeInbound->add($data);
        $winitdeInbound->commit();
		if($result !== false) {
		  $this->redirect('index');
		}else{
		  $this->error('写入错误！');
		}

    }

    public function importItem($orderID){
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function importPackage($orderID){
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function addItem($orderID){
        $status = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->getField('status');
        if($status != '已入库' and $status != '产品已导入' and $status != '待入库'){
            if (!empty($_FILES)) {
                $splitname = explode('.',$file['name']);
                $filename = $splitname[0].'_'.time();
                import('ORG.Net.UploadFile');
                 $config=array(
                     'allowExts'=>array('xlsx','xls'),
                     'savePath'=>'./Public/upload/winitdeInbound/',
                     'saveRule'=>$filename,
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
                $highestRow = $sheet->getHighestDataRow(); // 取得总行数
                $highestColumn = $sheet->getHighestDataColumn(); // 取得总列数

                for ($i=$highestRow; $i >0 ; $i--) { 
                    if($sheet->getCell("B".$i) == null or $sheet->getCell("B".$i) =='')
                        $highestRow = $i;
                    else{
                        $highestRow = $i;
                        break;
                    }      
                }
                
                for($c='A';$c<=$highestColumn;$c++){
                    $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
                }

                if($this->verifyImportedInboundItemTemplateColumnName($firstRow)){
                    $product = M(C('DB_PRODUCT'));                
                    $product->startTrans();
                    $errorInFile = null;
                    $data = null;
                    for($i=2;$i<=$highestRow;$i++){
                        $map[C('DB_PRODUCT_SKU')] = array('eq',strval($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue()));
                        if($product->where($map)->find() == null){
                            $errorInFile[$i]='产品编码不存在或未注册';
                        }

                        $data[$i][C('DB_WINITDE_INBOUND_ITEM_IOID')] = $orderID;
                        $data[$i][C('DB_WINITDE_INBOUND_ITEM_RESTOCK_ID')]= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue(); 
                        $data[$i][C('DB_WINITDE_INBOUND_ITEM_SKU')]= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();  
                        $data[$i][C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')]= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();                                        
                    }
                    $product->commit();

                    $restock = M(C('DB_RESTOCK'));
                    $restock->startTrans();
                    for($i=2;$i<=$highestRow;$i++){
                        $restockId = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                        $sku = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();

                        $restockRow = $restock->where(array(C('DB_RESTOCK_ID')=>$restockId))->find();
                        if($restockRow != null && $restockRow[C('DB_RESTOCK_SKU')] != $sku){
                            $errorInFile[$i]='产品编码与补货表不一致';
                        }                                     
                    }
                    $restock->commit();
                    
                    if($errorInFile != null){
                        $this->assign('errorInFile',$errorInFile);
                        $this->display('importInboundError');
                    }else{
                        $winitdeInboundItem = M(C('DB_WINITDE_INBOUND_ITEM'));               
                        $winitdeInboundItem->startTrans();
                        $result = 0;
                        foreach ($data as $key => $value){
                            $checkExist = $winitdeInboundItem->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$value[C('DB_WINITDE_INBOUND_ITEM_IOID')], C('DB_WINITDE_INBOUND_ITEM_SKU')=>$value[C('DB_WINITDE_INBOUND_ITEM_SKU')]))->find();
                            if($checkExist !== false && $checkExist!==null){
                                $checkExist[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')] = $checkExist[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')]+$value[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')];
                                $winitdeInboundItem->save($checkExist);
                            }else{
                                if(false !== $result){
                                    $result = $winitdeInboundItem->add($value);
                                }else{
                                    $errorDuringInsert[$key] = '未能添加';
                                }  
                            }                                                      
                        }
                        $winitdeInboundItem->commit();

                        if(null == $errorDuringInsert){  
                            if($status=='装箱已导入')
                                $status='待入库';
                            elseif($status == null or $status=='')
                                $status='产品已导入';                        
                            $updateInboundOrder=array(C('DB_WINITDE_INBOUND_STATUS')=>$status);                
                            M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->save($updateInboundOrder);
                            $this->success('导入成功！');
                        }else{
                            $this->assign('errorInFile',$errorDuringInsert);
                            $this->display('importInboundError');
                        }

                        //更新restock表格状态
                        $restock = M(C('DB_RESTOCK'));
                        $restock->startTrans(); 
                        foreach ($data as $key => $value){
                            $r = $restock->where(array(C('DB_RESTOCK_ID')=>$value[C('DB_WINITDE_INBOUND_ITEM_RESTOCK_ID')]))->find();
                            if($r[C('DB_RESTOCK_QUANTITY')] <= $value[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')]){
                                $r[C('DB_RESTOCK_STATUS')] = '已发货';
                                $r[C('DB_RESTOCK_SHIPPING_DATE')] = date("Y-m-d H:i:s" ,time());
                                $restock->save($r);
                            }else{
                                $tmp[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')];
                                $tmp[C('DB_RESTOCK_STATUS')] = '已发货';
                                $tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_WINITDE_INBOUND_ITEM_SKU')];
                                $tmp[C('DB_RESTOCK_CREATE_DATE')] = date("Y-m-d H:i:s" ,time());
                                $tmp[C('DB_RESTOCK_SHIPPING_DATE')] = date("Y-m-d H:i:s" ,time());
                                $tmp[C('DB_RESTOCK_MANAGER')] = $r[C('DB_RESTOCK_MANAGER')];
                                $tmp[C('DB_RESTOCK_WAREHOUSE')] = $r[C('DB_RESTOCK_WAREHOUSE')];
                                $tmp[C('DB_RESTOCK_TRANSPORT')] = $r[C('DB_RESTOCK_TRANSPORT')];
                                $restock->add($tmp);
                                $r[C('DB_RESTOCK_QUANTITY')]= $r[C('DB_RESTOCK_QUANTITY')]-$value[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')];
                                $r[C('DB_RESTOCK_STATUS')] = '延迟发货';
                                $restock->save($r);
                            }
                            
                        } 
                        $restock->commit();                
                    }
                }else{
                    $this->error('模板错误，请检查！');
                }

            }else{
                $this->error("请选择上传的文件");
            }
        }else{
            $this->error("无法上传！错误原因： 该单已入库或产品已导入！");
        } 
        
    }

    private function verifyImportedInboundItemTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_INBOUND_ITEM')));$c++){
            if($firstRow[$c] != C('IMPORT_INBOUND_ITEM')[$c])
                return false;
        }
        return true;
    }

    public function addPackage($orderID){
        $status = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->getField('status');
        if($status != '已入库' and $status != '装箱已导入' and $status != '待入库'){
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

                for($c='A';$c<=$highestColumn;$c++){
                    $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
                }

                if($this->verifyImportedInboundPackageTemplateColumnName($firstRow)){

                    for($i=2;$i<=$highestRow;$i++){
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_IOID')] = $orderID;
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT')]= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();  
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')]= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')]= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                        $data[$i][C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')]= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        if($this->verifyPackage($data[$i]) != null){
                            $errorInFile[$i]= $this->verifyPackage($data[$i]);
                        }                                       
                    }

                    if($errorInFile!=null){
                        $this->assign('errorInFile',$errorInFile);
                        $this->display('importInboundError');
                    }else{
                        $winitdeInboundPackage = M(C('DB_WINITDE_INBOUND_PACKAGE'));               
                        $winitdeInboundPackage->startTrans();
                        $result = 0;
                        foreach ($data as $key => $value){
                            if(false !== $result){
                                $result = $winitdeInboundPackage->add($value);
                            }else{
                                $errorDuringInsert[$key] = '未能添加';
                            }
                            
                        }
                        $winitdeInboundPackage->commit();

                        if(null == $errorDuringInsert){ 
                            if($status=='产品已导入')
                                $status='待入库';
                            elseif($status == null or $status=='')
                                $status='装箱已导入';                       
                            $updateInboundOrder=array(C('DB_WINITDE_INBOUND_STATUS')=>$status);                
                            M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->save($updateInboundOrder);
                            $this->success('导入成功！');
                        }else{
                            $this->assign('errorInFile',$errorDuringInsert);
                            $this->display('importInboundError');
                        }      
                    }  
                    
                }else{
                    $this->error('模板错误，请检查！');
                }

            }else{
                $this->error("请选择上传的文件");
            }
        }
        else{
            $this->error("无法上传！错误原因： 该单已入库或装箱已导入！");
        } 
        
    }

    private function verifyImportedInboundPackageTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('DB_USSW_INBOUND_PACKAGE')));$c++){
            if($firstRow[$c] != C('DB_USSW_INBOUND_PACKAGE')[$c])
                return false;
        }
        return true;
    }

    private function verifyPackage($packageToVerify){
        if($packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT')] == null or $packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT')] == '')
            return '重量是必填项！';
        elseif($packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')] == null or $packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')] == '')
            return '长度是必填项！';
        elseif($packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')] == null or $packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')] == '')
            return '宽度是必填项！';
        elseif($packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')] == null or $packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')] == '')
            return '高度是必填项！';
        elseif($packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_NUMBER')] == null or $packageToVerify[C('DB_WINITDE_INBOUND_PACKAGE_NUMBER')] == '')
            return '编号是必填项！';
        else
            return null;
    }

    public function updateInboundPackage(){
        if(IS_POST){
            $data[C('DB_WINITDE_INBOUND_PACKAGE_ID')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_ID'),'','htmlspecialchars');
            $data[C('DB_WINITDE_INBOUND_PACKAGE_NUMBER')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_NUMBER'),'','htmlspecialchars');
            $data[C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_WEIGHT'),'','htmlspecialchars');
            $data[C('DB_WINITDE_INBOUND_PACKAGE_LENGTH')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_LENGTH'),'','htmlspecialchars');
            $data[C('DB_WINITDE_INBOUND_PACKAGE_WIDTH')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_WIDTH'),'','htmlspecialchars');
            $data[C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT')] = I('post.'.C('DB_WINITDE_INBOUND_PACKAGE_HEIGHT'),'','htmlspecialchars');
            if($this->verifyPackage($data) == null){
                M(C('DB_WINITDE_INBOUND_PACKAGE'))->save($data);
                $this->success('保存成功');
            }else{
                $this->error($this->verifyPackage($data));
            }
        }
    }

    public function inboundOrderItems($orderID){
        $Data = M(C('DB_WINITDE_INBOUND_ITEM'));
        $where=array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$orderID);
        import('ORG.Util.Page');
        $count = $Data->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$orderID))->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$orderID))->select();
        $this->assign('items',$items);
        $this->assign('page',$show);
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function inboundOrderPackage($orderID){
        $Data = M(C('DB_WINITDE_INBOUND_PACKAGE'));
        $where=array(C('DB_WINITDE_INBOUND_PACAKGE_IOID')=>$orderID);
        import('ORG.Util.Page');
        $count = $Data->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->where(array(C('DB_WINITDE_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $this->assign('items',$items);
        $this->assign('page',$show);
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function confirmQuantity($orderID){
        $Data = M('winitde_inbound_'.$orderID);
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('items',$items);
        $this->assign('page',$show);
        $this->assign('orderID',$orderID);
        $this->display();
    }

    private function skuVerify($skuToVerify){
        $isInProductTable = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$skuToVerify))->find();
        if($isInProductTable != null){
            return true;
        }
        else{
            return false;
        }
    }

    public function bUpdateConfirmedQuantity($orderID){
        $inStatus = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->getField(C('DB_WINITDE_INBOUND_STATUS'));
        if($inStatus=='已入库'){
            $this->error('该单已完成入库，无法更新！');
        }
        $winitdeInboundOrder = M(C('DB_WINITDE_INBOUND_ITEM'));
        $winitdeInboundOrder->startTrans();
        $restock = M(C('DB_RESTOCK'));
        $restock->startTrans();
        foreach ($_POST['id'] as $key => $value) {
            $data[C('DB_WINITDE_INBOUND_ITEM_ID')] = $value;
            $data[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')] = $_POST['cQuantity'][$key];
            $winitdeInboundOrder->save($data);
            $where = array(C('DB_WINITDE_INBOUND_ITEM_ID')=>$value);
            $restockId = $winitdeInboundOrder->where($where)->getField(C('DB_WINITDE_INBOUND_ITEM_RESTOCK_ID'));
            $restockQuantity = $restock->where(array(C('DB_RESTOCK_ID')=>$restockId))->getField(C('DB_RESTOCK_QUANTITY'));
            if($restockQuantity>$data[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')]){
                $tmp[C('DB_RESTOCK_ID')] = $restockId;
                $tmp[C('DB_RESTOCK_QUANTITY')] = $restockQuantity-$data[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')];
                $tmp[C('DB_RESTOCK_STATUS')] = '部分发货';
                $restock->save($tmp);
            }
        }
        $restock->commit();
        $winitdeInboundOrder->commit();
        $this->success('保存成功');
    }

    public function scanConfirmedQuantity($orderID,$scannedSku,$quantity){
        $inStatus = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$orderID))->getField(C('DB_WINITDE_INBOUND_STATUS'));
        if($inStatus=='已入库'){
            $this->error('该单已完成入库，无法更新！');
        }
        $map[C('DB_WINITDE_INBOUND_ITEM_IOID')] = array('eq',$orderID);
        $map[C('DB_WINITDE_INBOUND_ITEM_SKU')] = array('eq',$scannedSku);
        $inboundItem = M(C('DB_WINITDE_INBOUND_ITEM'))->where($map)->find();
        if($inboundItem==false || $inboundItem==null){
            $this->redirect('inboundOrderItems', array('orderID'=>$orderID), 3,$scannedSku.' Does not exist in this Inbound Order.');
        }else{
            $inboundItem[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')]=$inboundItem[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')]+$quantity;
            M(C('DB_WINITDE_INBOUND_ITEM'))->save($inboundItem);
        }
        $this->redirect("inboundOrderItems",array('orderID'=>$orderID));
    }

    /*public function updateStorage($ioid){
        $status = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$ioid))->getField('status');
        if($status != "已入库"){
            $items = M(C('DB_WINITDE_INBOUND_ITEM'))->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$ioid))->select();
            $storage = M(C('DB_USSTORAGE'));
            $storage->startTrans();
            $product = M(C('DB_PRODUCT'));
            foreach ($items as $value) {
               $a = $storage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_WINITDE_INBOUND_ITEM_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
               $c = $storage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_WINITDE_INBOUND_ITEM_SKU')]))->getField(C('DB_USSTORAGE_CINVENTORY'));
               $q[C('DB_USSTORAGE_SKU')] = $value[C('DB_WINITDE_INBOUND_ITEM_SKU')];
               $q[C('DB_USSTORAGE_AINVENTORY')] = $a+$value[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')];
               $q[C('DB_USSTORAGE_CINVENTORY')] = $c+$value[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')];
               if($this->isInStorage($value[C('DB_WINITDE_INBOUND_ITEM_SKU')])!=0){
                    $q[C('DB_PRODUCT_CNAME')] = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_WINITDE_INBOUND_ITEM_SKU')]))->getField(C('DB_PRODUCT_CNAME'));
                    $r = $storage->where(array(C('DB_USSTORAGE_ID')=>$this->isInStorage($value[C('DB_WINITDE_INBOUND_ITEM_SKU')])))->save($q);
               }
               else{
                    $q[C('DB_PRODUCT_CNAME')] = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_WINITDE_INBOUND_ITEM_SKU')]))->getField(C('DB_PRODUCT_CNAME'));
                    $r = $storage->add($q);
               }
               
            }
            $storage->commit();
            $data['status'] = '已入库';
            $data['receive_date'] = date('Y-m-d');
            M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$ioid))->save($data);
            $this->success('入库成功！');
        }
        else{
            $this->error('该单已入库！');
        }
    }*/

    public function directInbound($ioid){
        $status = M(C('DB_WINITDE_INBOUND'))->where(array(C('DB_WINITDE_INBOUND_ID')=>$ioid))->getField('status');
        if($status != "已入库"){
            $winitdeInboundItemTable = M(C('DB_WINITDE_INBOUND_ITEM'));
            $winitdeInboundItemTable->starttrans();
            $items = $winitdeInboundItemTable->where(array(C('DB_WINITDE_INBOUND_ITEM_IOID')=>$ioid))->select();
            foreach ($items as $value) {
               $value[C('DB_WINITDE_INBOUND_ITEM_CQUANTITY')] = $value[C('DB_WINITDE_INBOUND_ITEM_DQUANTITY')];
               $winitdeInboundItemTable->save($value);
            }
            $winitdeInboundItemTable->commit();
            //$this->updateStorage($ioid);
        }
        else{
            $this->error('该单已经入库！');
        }
    }

    private function isInStorage($sku){
        $row = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->find();
        if( $row == null){
            return 0;
        } 
        else{
            return $row[C('DB_USSTORAGE_ID')];
        }
    }

    public function deleteInboundOrder($orderIDToDelete){
        $this->error('该功能尚未完善');       
    }

    public function insertSingleItem(){
        if(IS_POST){
            if($this->checkSku(I('post.sku','','htmlspecialchars'))){
                $usstorage = M(C('DB_USSTORAGE'));
                $usstorage->starttrans();
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+1;

                        $result = $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = 1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = 1;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+1;
                        $result = $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = 1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = 1;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }
            }else{
                $this->error('产品编码不在产品列表，请检查');
            }
            
        } 
    }

    public function insertMultiItem(){
        if(IS_POST){
            if($this->checkSku(I('post.sku','','htmlspecialchars'))){
                $usstorage = M(C('DB_USSTORAGE'));
                $usstorage->starttrans();
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+I('post.quantity','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');

                        $result = $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.quantity','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                        $result = $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }
            }else{
                $this->error('产品编码不在产品列表，请检查');
            }
            
        } 
    }

    private function checkSku($sku){
        $result = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
        if($result != ''){
            return true;
        }
        return false;
    }

}

?>