<?php




class InboundAction extends CommonAction{

	public function index(){
		$usswInOrders = M(C('DB_USSW_INBOUND'))->select();
        import('ORG.Util.Page');
        foreach ($usswInOrders as $key => $value) {
            $Data[$key]=array(
                C('DB_USSW_INBOUND_ID')=>$value[C('DB_USSW_INBOUND_ID')],
                C('DB_USSW_INBOUND_DATE')=>$value[C('DB_USSW_INBOUND_DATE')],
                C('DB_USSW_INBOUND_SHIPPING_WAY')=>$value[C('DB_USSW_INBOUND_SHIPPING_WAY')],
                C('DB_USSW_INBOUND_STATUS')=>$value[C('DB_USSW_INBOUND_STATUS')],
                'declare-package-quantity'=>$this->getInboundOrderPackageQuantity($value[C('DB_USSW_INBOUND_ID')]),
                'weight'=>$this->getInboundOrderPackageWeight($value[C('DB_USSW_INBOUND_ID')]),
                $volume = $this->getInboundOrderPackageVolume($value[C('DB_USSW_INBOUND_ID')]),
                'volume'=>$volume/1000000,
                'volumeWeight'=>$this->getInboundOrderPackageVolumeWeight($value[C('DB_USSW_INBOUND_ID')]),
                'declare-item-quantity'=>$this->getInboundOrderItemQuantity($value[C('DB_USSW_INBOUND_ID')],C('DB_USSW_INBOUND_ITEM_DQUANTITY')),
                'confirmed-item-quantity'=>$this->getInboundOrderItemQuantity($value[C('DB_USSW_INBOUND_ID')],C('DB_USSW_INBOUND_ITEM_CQUANTITY')),
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
        $quantityArray = M(C('DB_USSW_INBOUND_ITEM'))->where(array(C('DB_USSW_INBOUND_ITEM_IOID')=>$orderID))->getField($column,true);
        $quantity=0;
        foreach ($quantityArray as $key => $value) {
            $quantity = $quantity + $value;
        }
        return $quantity;
    }

    private function getInboundOrderPackageQuantity($orderID){
        $resultArray = M(C('DB_USSW_INBOUND_PACKAGE'))->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        return count($resultArray);
    }

    private function getInboundOrderPackageWeight($orderID){
        $weightArray = M(C('DB_USSW_INBOUND_PACKAGE'))->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->getField(C('DB_USSW_INBOUND_PACKAGE_WEIGHT'),true);
        $total=0;
        foreach ($weightArray as $key => $value) {
            $total = $total + $value;
        }
        return $total;
    }

    private function getInboundOrderPackageVolume($orderID){
        $resultArray = M(C('DB_USSW_INBOUND_PACKAGE'))->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $total=0;
        foreach ($resultArray as $key => $value) {
            $total = $total + $value[C('DB_USSW_INBOUND_PACKAGE_LENGTH')]*$value[C('DB_USSW_INBOUND_PACKAGE_WIDTH')]*$value[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')];
        }
        return $total;
    }

    private function getInboundOrderPackageVolumeWeight($orderID){
        $resultArray = M(C('DB_USSW_INBOUND_PACKAGE'))->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $total=0;
        foreach ($resultArray as $key => $value) {
            $total = $total + $value[C('DB_USSW_INBOUND_PACKAGE_LENGTH')]*$value[C('DB_USSW_INBOUND_PACKAGE_WIDTH')]*$value[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')]/5000;
        }
        return round($total,2);
    }

	public function fileImport(){
		$this->display();
	}

    public function addInbound(){
    	$data[C('DB_USSW_INBOUND_DATE')] = date('Y-m-d');
        $data[C('DB_USSW_INBOUND_SHIPPING_WAY')] = I('post.'.C('DB_USSW_INBOUND_SHIPPING_WAY'),'','htmlspecialchars');
        $usswInbound = M(C('DB_USSW_INBOUND'));
        $usswInbound->startTrans();
        $result =  $usswInbound->add($data);
        $usswInbound->commit();
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

    Public function addItem($orderID){
        $status = M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$orderID))->getField('status');
        if($status != '已入库' and $status != '产品已导入' and $status != '待入库'){
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
                $highestRow = $sheet->getHighestDataRow(); // 取得总行数
                $highestColumn = $sheet->getHighestDataColumn(); // 取得总列数

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

                if($this->verifyImportedInboundItemTemplateColumnName($firstRow)){
                    $product = M(C('DB_PRODUCT'));                
                    $product->startTrans();
                    $errorInFile = null;
                    $data = null;
                    for($i=2;$i<=$highestRow;$i++){
                        if($product->where(array(C('DB_PRODUCT_SKU')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find() == null){
                            $errorInFile[$i]='产品编码不存在或未注册';
                        }

                        $data[$i][C('DB_USSW_INBOUND_ITEM_IOID')] = $orderID;
                        $data[$i][C('DB_USSW_INBOUND_ITEM_SKU')]= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                        $data[$i][C('DB_USSW_INBOUND_ITEM_DQUANTITY')]= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();                                        
                    }
                    $product->commit();
                    
                    if($errorInFile != null){
                        $this->assign('errorInFile',$errorInFile);
                        $this->display('importInboundError');
                    }else{
                        $usswInboundItem = M(C('DB_USSW_INBOUND_ITEM'));               
                        $usswInboundItem->startTrans();
                        $result = 0;
                        foreach ($data as $key => $value){
                            if(false !== $result){
                                $result = $usswInboundItem->add($value);
                            }else{
                                $errorDuringInsert[$key] = '未能添加';
                            }
                            
                        }
                        $usswInboundItem->commit();

                        if(null == $errorDuringInsert){  
                            if($status=='装箱已导入')
                                $status='待入库';
                            elseif($status == null or $status=='')
                                $status='产品已导入';                        
                            $updateInboundOrder=array(C('DB_USSW_INBOUND_STATUS')=>$status);                
                            M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$orderID))->save($updateInboundOrder);
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

    Public function addPackage($orderID){
        $status = M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$orderID))->getField('status');
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
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_IOID')] = $orderID;
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_WEIGHT')]= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();  
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_LENGTH')]= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_WIDTH')]= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                        $data[$i][C('DB_USSW_INBOUND_PACKAGE_HEIGHT')]= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        if($this->verifyPackage($data[$i]) != null){
                            $errorInFile[$i]= $this->verifyPackage($data[$i]);
                        }                                       
                    }

                    if($errorInFile!=null){
                        $this->assign('errorInFile',$errorInFile);
                        $this->display('importInboundError');
                    }else{
                        $usswInboundPackage = M(C('DB_USSW_INBOUND_PACKAGE'));               
                        $usswInboundPackage->startTrans();
                        $result = 0;
                        foreach ($data as $key => $value){
                            if(false !== $result){
                                $result = $usswInboundPackage->add($value);
                            }else{
                                $errorDuringInsert[$key] = '未能添加';
                            }
                            
                        }
                        $usswInboundPackage->commit();

                        if(null == $errorDuringInsert){ 
                            if($status=='产品已导入')
                                $status='待入库';
                            elseif($status == null or $status=='')
                                $status='装箱已导入';                       
                            $updateInboundOrder=array(C('DB_USSW_INBOUND_STATUS')=>$status);                
                            M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$orderID))->save($updateInboundOrder);
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
        if($packageToVerify[C('DB_USSW_INBOUND_PACKAGE_WEIGHT')] == null or $packageToVerify[C('DB_USSW_INBOUND_PACKAGE_WEIGHT')] == '')
            return '重量是必填项！';
        elseif($packageToVerify[C('DB_USSW_INBOUND_PACKAGE_LENGTH')] == null or $packageToVerify[C('DB_USSW_INBOUND_PACKAGE_LENGTH')] == '')
            return '长度是必填项！';
        elseif($packageToVerify[C('DB_USSW_INBOUND_PACKAGE_WIDTH')] == null or $packageToVerify[C('DB_USSW_INBOUND_PACKAGE_WIDTH')] == '')
            return '宽度是必填项！';
        elseif($packageToVerify[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')] == null or $packageToVerify[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')] == '')
            return '高度是必填项！';
        elseif($packageToVerify[C('DB_USSW_INBOUND_PACKAGE_NUMBER')] == null or $packageToVerify[C('DB_USSW_INBOUND_PACKAGE_NUMBER')] == '')
            return '编号是必填项！';
        else
            return null;
    }

    public function updateInboundPackage(){
        if(IS_POST){
            $data[C('DB_USSW_INBOUND_PACKAGE_ID')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_ID'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_PACKAGE_NUMBER')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_NUMBER'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_PACKAGE_WEIGHT')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_WEIGHT'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_PACKAGE_LENGTH')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_LENGTH'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_PACKAGE_WIDTH')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_WIDTH'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_PACKAGE_HEIGHT')] = I('post.'.C('DB_USSW_INBOUND_PACKAGE_HEIGHT'),'','htmlspecialchars');
            if($this->verifyPackage($data) == null){
                M(C('DB_USSW_INBOUND_PACKAGE'))->save($data);
                $this->success('保存成功');
            }else{
                $this->error($this->verifyPackage($data));
            }
        }
    }

    public function inboundOrderItems($orderID){
        $Data = M(C('DB_USSW_INBOUND_ITEM'));
        $where=array(C('DB_USSW_INBOUND_ITEM_IOID')=>$orderID);
        import('ORG.Util.Page');
        $count = $Data->where(array(C('DB_USSW_INBOUND_ITEM_IOID')=>$orderID))->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->where(array(C('DB_USSW_INBOUND_ITEM_IOID')=>$orderID))->select();
        $this->assign('items',$items);
        $this->assign('page',$show);
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function inboundOrderPackage($orderID){
        $Data = M(C('DB_USSW_INBOUND_PACKAGE'));
        $where=array(C('DB_USSW_INBOUND_PACAKGE_IOID')=>$orderID);
        import('ORG.Util.Page');
        $count = $Data->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->where(array(C('DB_USSW_INBOUND_PACKAGE_IOID')=>$orderID))->select();
        $this->assign('items',$items);
        $this->assign('page',$show);
        $this->assign('orderID',$orderID);
        $this->display();
    }

    public function confirmQuantity($orderID){
        $Data = M('ussw_inbound_'.$orderID);
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

    public function updateConfirmedQuantity(){
        if($this->skuVerify(I('post.'.C('DB_USSW_INBOUND_ITEM_SKU'),'','htmlspecialchars'))){
            $data[C('DB_USSW_INBOUND_ITEM_SKU')] = I('post.'.C('DB_USSW_INBOUND_ITEM_SKU'),'','htmlspecialchars');
            $data[C('DB_USSW_INBOUND_ITEM_CQUANTITY')] = I('post.'.C('DB_USSW_INBOUND_ITEM_CQUANTITY'),'','htmlspecialchars');
            $usswInboundOrder = M(C('DB_USSW_INBOUND_ITEM'));
            $where = array(C('DB_USSW_INBOUND_ITEM_ID')=>I('post.'.C('DB_USSW_INBOUND_ITEM_ID'),'','htmlspecialchars'));
            $result =  $usswInboundOrder->where($where)->save($data);
            if(false !== $result || 0 !== $result) {
                $this->success('操作成功！');
            }else{
                $this->error('写入错误！');
            }
         }
         else{
            $this->error('产品编码不存在');
         }
    }

    public function updateStorage($ioid){
        $status = M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$ioid))->getField('status');
        if($status != "已入库"){
            $items = M(C('DB_USSW_INBOUND_ITEM'))->where(array(C('DB_USSW_INBOUND_ITEM_IOID')=>$ioid))->select();
            $storage = M(C('DB_USSTORAGE'));
            $storage->startTrans();
            foreach ($items as $value) {
               $a = $storage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSW_INBOUND_ITEM_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
               $c = $storage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSW_INBOUND_ITEM_SKU')]))->getField(C('DB_USSTORAGE_CINVENTORY'));
               $q[C('DB_USSTORAGE_SKU')] = $value[C('DB_USSW_INBOUND_ITEM_SKU')];
               $q[C('DB_USSTORAGE_AINVENTORY')] = $a+$value[C('DB_USSW_INBOUND_ITEM_CQUANTITY')];
               $q[C('DB_USSTORAGE_CINVENTORY')] = $c+$value[C('DB_USSW_INBOUND_ITEM_CQUANTITY')];
               if($this->isInStorage($value[C('DB_USSW_INBOUND_ITEM_SKU')])!=0){
                    $r = $storage->where(array(C('DB_USSTORAGE_ID')=>$this->isInStorage($value[C('DB_USSW_INBOUND_ITEM_SKU')])))->save($q);
               }
               else{

                    $r = $storage->add($q);
               }
               
            }
            $storage->commit();
            $data['status'] = '已入库';
            M(C('DB_USSW_INBOUND'))->where(array(C('DB_USSW_INBOUND_ID')=>$ioid))->save($data);
            $this->success('入库成功！');
        }
        else{
            $this->error('该单已入库！');
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

}

?>