<?php

class PurchaseAction extends CommonAction{

	public function index(){
        $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->select());
		$this->display();
	}

	public function importPurchase(){
		$this->display();
	}

	public function import(){
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
                if(!$this->verifyImportedPurchaseTemplateColumnName($firstRow)){
                    $this->error("模板错误，请检查模板！");                   
                }

                $ppo = null;
                $ppoi = null;
                $supplier = null;
                $indexOfPpo = 0;
                $indexOfPpoi =0;
                for($i=2;$i<=$highestRow;$i++){   
                    $data=null;
                    $data['tmpNo']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $data[C('DB_PURCHASE_ITEM_SKU')]= $this->verifySku($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue());
                    $data[C('DB_PURCHASE_ITEM_PRICE')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')]= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    $data[C('DB_PURCHASE_SHIPPING_FEE')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue(); 
                    $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue(),"utf-8","auto"); 
                    $data[C('DB_PURCHASE_MANAGER')] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_SUPPLIER_COMPANY')] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_SUPPLIER_PERSON')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_SUPPLIER_WANGWANG')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_SUPPLIER_QQ')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_SUPPLIER_TEL')]= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                    $data[C('DB_SUPPLIER_WEBSITE')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_PURCHASE_ORDER_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    $data[C('DB_PURCHASE_TRACKING_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                    $data[C('DB_PURCHASE_REMARK')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue(),"utf-8","auto"); 
                    $verifyError = $this->verifyPurchaseOrder($data);
                    if($verifyError != null)
                        $this->error($verifyError);

                    if($this->inPpo($data['tmpNo'],$ppo)){
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PURCHASE_ID')] = $data['tmpNo'];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_SKU')] = $data[C('DB_PURCHASE_ITEM_SKU')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PRICE')] = $data[C('DB_PURCHASE_ITEM_PRICE')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] = $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_WAREHOUSE')] = $data[C('DB_PURCHASE_ITEM_WAREHOUSE')];
                        $indexOfPpoi = $indexOfPpoi+1;

                    }else{
                        if($data[C('DB_SUPPLIER_COMPANY')]!=null or $data[C('DB_SUPPLIER_COMPANY')]!=''){
                            if($this->getSupplierID($data[C('DB_SUPPLIER_COMPANY')])==null){
                                $supp[C('DB_SUPPLIER_COMPANY')] = $data[C('DB_SUPPLIER_COMPANY')];
                                $supp[C('DB_SUPPLIER_PERSON')] = $data[C('DB_SUPPLIER_PERSON')];
                                $supp[C('DB_SUPPLIER_WANGWANG')] = $data[C('DB_SUPPLIER_WANGWANG')];
                                $supp[C('DB_SUPPLIER_QQ')] = $data[C('DB_SUPPLIER_QQ')];
                                $supp[C('DB_SUPPLIER_WEBSITE')] = $data[C('DB_SUPPLIER_WEBSITE')];
                                $supp[C('DB_SUPPLIER_TEL')] = $data[C('DB_SUPPLIER_TEL')];
                                $supp[C('DB_SUPPLIER_ADDRESS')] = $data[C('DB_SUPPLIER_ADDRESS')];
                                $data[C('DB_PURCHASE_SUPPLIER_ID')] = $this->insertSupplier($supp);
                            }
                            else{
                                $data[C('DB_PURCHASE_SUPPLIER_ID')] = $this->getSupplierID($data[C('DB_SUPPLIER_COMPANY')]);
                            }
                        }                      
                        $ppo[$indexOfPpo][C('DB_PURCHASE_ID')] = $data['tmpNo'];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_MANAGER')] = $data[C('DB_PURCHASE_MANAGER')];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_CREATE_DATE')] = date("Y-m-d H:i:s" ,time());
                        $ppo[$indexOfPpo][C('DB_PURCHASE_PURCHASED_DATE')] = null;
                        $ppo[$indexOfPpo][C('DB_PURCHASE_SHIPPING_FEE')] = $data[C('DB_PURCHASE_SHIPPING_FEE')];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_STATUS')] = '待确认';
                        $ppo[$indexOfPpo][C('DB_PURCHASE_CANCEL')] = 0;
                        $ppo[$indexOfPpo][C('DB_PURCHASE_ORDER_NUMBER')] = $data[C('DB_PURCHASE_ORDER_NUMBER')];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_TRACKING_NUMBER')] = $data[C('DB_PURCHASE_TRACKING_NUMBER')];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_SUPPLIER_ID')] = $data[C('DB_PURCHASE_SUPPLIER_ID')];
                        $ppo[$indexOfPpo][C('DB_PURCHASE_REMARK')] = $data[C('DB_PURCHASE_REMARK')];
                        $indexOfPpo = $indexOfPpo+1;
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PURCHASE_ID')] = $data['tmpNo'];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_SKU')] = $data[C('DB_PURCHASE_ITEM_SKU')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PRICE')] = $data[C('DB_PURCHASE_ITEM_PRICE')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] = $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')];
                        $ppoi[$indexOfPpoi][C('DB_PURCHASE_ITEM_WAREHOUSE')] = $data[C('DB_PURCHASE_ITEM_WAREHOUSE')];
                        $indexOfPpoi = $indexOfPpoi+1;
                    }
                    
                }

                $purchase = M(C('DB_PURCHASE'));
                $purchase->startTrans();
                foreach ($ppo as $ppokey => $ppovalue) {
                    $tmpNo = $ppovalue[C('DB_PURCHASE_ID')];
                    $ppovalue[C('DB_PURCHASE_ID')] = null;
                    $purchaseID = $purchase->add($ppovalue);
                    foreach ($ppoi as $ppoikey => $ppoivalue) {
                        if($ppoivalue[C('DB_PURCHASE_ITEM_PURCHASE_ID')] == $tmpNo)
                            $ppoi[$ppoikey][C('DB_PURCHASE_ITEM_PURCHASE_ID')] = $purchaseID;
                    }
                }
                $purchase->commit();

                $purchaseItem = M(C('DB_PURCHASE_ITEM'));
                $purchaseItem->startTrans();
                $product = M(C('DB_PRODUCT'));
                $product->startTrans();
                foreach ($ppoi as $key => $value) {
                    $purchaseItem->add($value);
                    $price = array(C('DB_PRODUCT_PRICE') => $value[C('DB_PURCHASE_ITEM_PRICE')]);
                    $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_PURCHASE_ITEM_SKU')]))->setField($price);
                }
                $product->commit();
                $purchaseItem->commit();

                $this->success('导入成功！');
                
        }else{
            $this->error("请选择上传的文件");
        }   
	}

	private function verifyImportedPurchaseTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_PURCHASE')));$c++){
            if($firstRow[$c] != C('IMPORT_PURCHASE')[$c])
                return false;
        }
        return true;
    }

    private function verifyPurchaseOrder($purchaseOrderToVerify){
        if($purchaseOrderToVerify[C('DB_PURCHASE_ITEM_SKU')] == null or $purchaseOrderToVerify[C('DB_PURCHASE_ITEM_SKU')] == '')
            return '产品编码是必填项！';
        elseif($purchaseOrderToVerify['tmpNo'] == null or $purchaseOrderToVerify['tmpNo'] == '')
            return '临时编码是必填项！';
        elseif($purchaseOrderToVerify[C('DB_PURCHASE_ITEM_PRICE')] == null or $purchaseOrderToVerify[C('DB_PURCHASE_ITEM_PRICE')] == '')
            return '单价是必填项！';
        elseif($purchaseOrderToVerify[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] == null or $purchaseOrderToVerify[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] == '')
            return '数量是必填项！';
        elseif($purchaseOrderToVerify[C('DB_PURCHASE_MANAGER')] == null or $purchaseOrderToVerify[C('DB_PURCHASE_MANAGER')] == '')
            return '产品经理是必填项！';
        else
            return null;
    }

    private function inPpo($tmpNo,$ppo){
        foreach ($ppo as $key => $value) {
            if($tmpNo==$value[C('DB_PURCHASE_ID')])
                return true;
        }
        return false;
    }

    private function getSupplierID($company){
       return M(C('DB_SUPPLIER'))->where(array(C('DB_SUPPLIER_COMPANY')=>$company))->getField(C('DB_SUPPLIER_ID'));
    }

    private function insertSupplier($supp){
        return M(C('DB_SUPPLIER'))->add($supp);
    }

    private function verifySku($sku){
        $result = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
        if($result == null or $result == '')
            $this->error($sku.' 不在产品列表');
        else
            return $sku;
    }

    public function editPurchaseOrder($purchaseID){
        $purchaseOrder = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->select();
        $supplierID =  M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_SUPPLIER_ID'));
        $supplier = M(C('DB_SUPPLIER'))->where(array(C('DB_SUPPLIER_ID')=>$supplierID))->select();
       
        $purchaseItem = M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_PURCHASE_ID')=>$purchaseID))->select();
        $total = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_SHIPPING_FEE'));
        foreach ($purchaseItem as $key => $value) {
            $total = $total+$value[C('DB_PURCHASE_ITEM_PRICE')]*$value[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')];            
        }
        $this->assign('purchaseOrder',$purchaseOrder);
        $this->assign('total',$total);
        $this->assign('purchaseItem',$purchaseItem);
        $this->assign('supplier',$supplier);
        $this->display();
    }

    public function updatePurchaseOrder(){
        if(IS_POST){
            $data[C('DB_PURCHASE_ID')] = I('post.'.C('DB_PURCHASE_ID'),'','htmlspecialchars');
            $data[C('DB_PURCHASE_MANAGER')] = mb_convert_encoding(I('post.'.C('DB_PURCHASE_MANAGER'),'','htmlspecialchars'),"utf-8","auto");
            $data[C('DB_PURCHASE_SHIPPING_FEE')] = I('post.'.C('DB_PURCHASE_SHIPPING_FEE'),'','htmlspecialchars');
            $data[C('DB_PURCHASE_ORDER_NUMBER')] = I('post.'.C('DB_PURCHASE_ORDER_NUMBER'),'','htmlspecialchars');
            $data[C('DB_PURCHASE_TRACKING_NUMBER')] = I('post.'.C('DB_PURCHASE_TRACKING_NUMBER'),'','htmlspecialchars');
            $data[C('DB_PURCHASE_REMARK')] = I('post.'.C('DB_PURCHASE_REMARK'),'','htmlspecialchars');
            M(C('DB_PURCHASE'))->save($data);
            $this->success('保存成功');
        }
    }

    public function updatePurchaseItem(){
        if(IS_POST){
            $purchaseID = M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_ID')=>I('post.'.C('DB_PURCHASE_ITEM_ID'),'','htmlspecialchars')))->getField(C('DB_PURCHASE_ITEM_PURCHASE_ID'));
            $purchaseOrderStatus = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
            if($purchaseOrderStatus=="待确认" or $purchaseOrderStatus=="已确认" or $purchaseOrderStatus=="待付款"){
                $data[C('DB_PURCHASE_ITEM_ID')] = I('post.'.C('DB_PURCHASE_ITEM_ID'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_SKU')] = I('post.'.C('DB_PURCHASE_ITEM_SKU'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_PRICE')] = I('post.'.C('DB_PURCHASE_ITEM_PRICE'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] = I('post.'.C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY')] = I('post.'.C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = I('post.'.C('DB_PURCHASE_ITEM_WAREHOUSE'),'','htmlspecialchars');
                M(C('DB_PURCHASE_ITEM'))->save($data);
                $this->success('保存成功');
            }elseif($purchaseOrderStatus=="已付款" or $purchaseOrderStatus=="待发货" or $purchaseOrderStatus=="部分到货"){
                $data[C('DB_PURCHASE_ITEM_ID')] = I('post.'.C('DB_PURCHASE_ITEM_ID'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY')] = I('post.'.C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY'),'','htmlspecialchars');
                $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = I('post.'.C('DB_PURCHASE_ITEM_WAREHOUSE'),'','htmlspecialchars');
                M(C('DB_PURCHASE_ITEM'))->save($data);
                $this->success('保存成功');
            }else{
                $this->error('已完成的采购单，无法修改');
            }
        }
    }

    public function deletePurchaseItem($id){
        if(M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->delete() == false)
            $this->success('删除失败');
        else
            $this->success('删除成功');
    }

    public function addPurchaseItem($id){
        $data[C('DB_PURCHASE_ITEM_PURCHASE_ID')] = $id;
        $data[C('DB_PURCHASE_ITEM_SKU')] = null;
        $data[C('DB_PURCHASE_ITEM_PRICE')] = null;
        $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] = null;
        $data[C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY')] = null;
        $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = null;
        $purchaseItem = M(C('DB_PURCHASE_ITEM'));
        $purchaseItem->startTrans();
        $purchaseItem->add($data);
        $items = $purchaseItem->where(array(C('DB_PURCHASE_ITEM_PURCHASE_ID')=>$id))->select();
        $this->assign('purchaseItem',$items);
        $this->success();
    }

    public function confirmPurchaseOrder($purchaseID){
        $status = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
        if($status == '待确认'){
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->setField(C('DB_PURCHASE_STATUS'),'已确认');
            $this->success("采购单已确认");
        }else{
            $this->error("已确认过的采购单，无法再次确认");
        }
    }

    public function payPurchaseOrder($purchaseID){
        $status = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
        if($status == '待确认'){
            $this->error("请先确认采购单");
        }elseif($status == '已确认'){
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->setField(C('DB_PURCHASE_STATUS'),'已付款');
            $this->success("已修改状态");
        }else{
            $this->error("状态无法更新");
        }
    }

}

?>