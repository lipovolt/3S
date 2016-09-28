<?php

class PurchaseAction extends CommonAction{

	public function index($status='',$cancel=''){
        if($_POST['keyword']==""){
            if($status !=''){
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_STATUS')=>$status))->select());
                $this->display();
            }
            elseif( $cancel != ''){
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_CANCEL')=>$cancel))->select());
                $this->display();
            }else{
                $map[C('DB_PURCHASE_STATUS')] = array('neq','全部到货');
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where($map)->select());
                $this->display();
            }
        }else{
            if($_POST['keyword']==C('DB_PURCHASE_ID')){
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>I('post.keywordValue','','htmlspecialchars')))->select());
            }
            if($_POST['keyword']==C('DB_PURCHASE_MANAGER')){
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_MANAGER')=>I('post.keywordValue','','htmlspecialchars')))->select());
            }
            if($_POST['keyword']==C('DB_PURCHASE_ITEM_SKU')){
                $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
                $purchaseOrders = M(C('DB_PURCHASE_ITEM'))->distinct(true)->where($where)->getField(C('DB_PURCHASE_ITEM_PURCHASE_ID'),true);
                $map[C('DB_PURCHASE_ID')] = array('in',$purchaseOrders);  
                $this->assign('purchaseOrder',M(C('DB_PURCHASE'))->where($map)->select());
            }
            $this->assign('keyword',I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue',I('post.keywordValue','','htmlspecialchars'));
            $this->display();
        }        
        
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
                $indexOfPpo = 0;
                $indexOfPpoi =0;
                for($i=2;$i<=$highestRow;$i++){   
                    $data=null;
                    $data['tmpNo']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $data[C('DB_PURCHASE_ITEM_SKU')]= $this->verifySku(mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue(),"utf-8","auto"));
                    $data[C('DB_PURCHASE_ITEM_PRICE')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')]= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    $data[C('DB_PURCHASE_SHIPPING_FEE')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue(); 
                    $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue(),"utf-8","auto"); 
                    $data[C('DB_PURCHASE_MANAGER')] = mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue(),"utf-8","auto");
                    $data[C('DB_PURCHASE_SUPPLIER_ID')] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                    $data[C('DB_PURCHASE_ORDER_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                    $data[C('DB_PURCHASE_TRACKING_NUMBER')]= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                    $data[C('DB_PURCHASE_REMARK')]= mb_convert_encoding($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue(),"utf-8","auto"); 
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
            $purchaseItem[$key][C('DB_PRODUCT_CNAME')] = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_PURCHASE_ITEM_SKU')]))->getField(C('DB_PRODUCT_CNAME'));         
        }
        $this->assign('purchaseOrder',$purchaseOrder);
        $this->assign('total',$total);
        $this->assign('purchaseItem',$purchaseItem);
        $this->assign('supplier',$supplier);
        $this->display();
    }

    public function deletePurchaseOrder($purchaseID){
        $purchaseOrderStatus = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
        if($purchaseOrderStatus=="待确认" or $purchaseOrderStatus=="待付款"){
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->delete();
            $this->success("采购单已删除");
        }else{
            $this->error("已付款的采购单不能删除");
        }
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

    public function updatePurchaseItem($id){
        if(IS_POST){
            $purchaseOrderStatus = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$id))->getField(C('DB_PURCHASE_STATUS'));
            $count = count(I('post.'.C('DB_PURCHASE_ITEM_ID')));
            
            if($purchaseOrderStatus=="待确认" or $purchaseOrderStatus=="待付款"){
                $purchaseItem = M(C('DB_PURCHASE_ITEM'));
                $purchaseItem->startTrans();
                for($i=0;$i<$count;$i++){
                    $data[C('DB_PURCHASE_ITEM_ID')] = I('post.'.C('DB_PURCHASE_ITEM_ID'),'','htmlspecialchars')[$i];
                    $data[C('DB_PURCHASE_ITEM_SKU')] = I('post.'.C('DB_PURCHASE_ITEM_SKU'),'','htmlspecialchars')[$i];
                    $data[C('DB_PURCHASE_ITEM_PRICE')] = I('post.'.C('DB_PURCHASE_ITEM_PRICE'),'','htmlspecialchars')[$i];
                    $data[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')] = I('post.'.C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY'),'','htmlspecialchars')[$i];
                    $data[C('DB_PURCHASE_ITEM_WAREHOUSE')] = I('post.'.C('DB_PURCHASE_ITEM_WAREHOUSE'),'','htmlspecialchars')[$i];
                    $purchaseItem->save($data);
                }
                $purchaseItem->commit();
                $this->success('保存成功');
            }elseif($purchaseOrderStatus=="待发货" or $purchaseOrderStatus=="部分到货"){
                for ($i=0; $i < $count; $i++) { 
                    if(I('post.new_received_quantity','','htmlspecialchars')[$i]>0){
                        $this->receivePurchasedItem(I('post.'.C('DB_PURCHASE_ITEM_ID'),'','htmlspecialchars')[$i],I('post.new_received_quantity','','htmlspecialchars')[$i]);
                    }
                    
                }
                $this->changeItemReceiveStatus($id);
                $this->success('保存成功');
            }else{
                $this->error('已完成的采购单，无法修改');
            }
        }
    }

    public function deletePurchaseItem($purchase_item_id){
        if(M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_ID')=>$purchase_item_id))->delete() == false)
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
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->setField(C('DB_PURCHASE_STATUS'),'待付款');
            $this->success("采购单已确认");
        }else{
            $this->error("已确认过的采购单，无法再次确认");
        }
    }

    public function payPurchaseOrder($purchaseID){
        $status = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
        if($status == '待确认'){
            $this->error("请先确认采购单");
        }elseif($status == '待付款'){
            $data[C('DB_PURCHASE_PURCHASED_DATE')] = date("Y-m-d H:i:s" ,time());
            $data[C('DB_PURCHASE_STATUS')] = '待发货';
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->save($data);
            $items = M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_PURCHASE_ID')=>$purchaseID))->select();
            $product = M(C('DB_PRODUCT'));
            foreach ($items as $key => $value) {
                $p = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_PURCHASE_ITEM_SKU')]))->select();
                if($value[C('DB_PURCHASE_ITEM_PRICE')] != $p[C('DB_PRODUCT_PRICE')]){
                    $p[C('DB_PRODUCT_PRICE')] = $value[C('DB_PURCHASE_ITEM_PRICE')];
                    $product->save($p);
                }
            }
            
            $this->success("已修改状态");
        }else{
            $this->error("状态无法更新");
        }
    }

    public function confirmAndPayPurchaseOrder($purchaseID){
        $status = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->getField(C('DB_PURCHASE_STATUS'));
        if($status == '待确认'){
            $data[C('DB_PURCHASE_PURCHASED_DATE')] = date("Y-m-d H:i:s" ,time());
            $data[C('DB_PURCHASE_STATUS')] = '待发货';
            M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->save($data);
            $items = M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_PURCHASE_ID')=>$purchaseID))->select();
            $product = M(C('DB_PRODUCT'));
            foreach ($items as $key => $value) {
                $p = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_PURCHASE_ITEM_SKU')]))->select();
                if($value[C('DB_PURCHASE_ITEM_PRICE')] != $p[C('DB_PRODUCT_PRICE')]){
                    $p[C('DB_PRODUCT_PRICE')] = $value[C('DB_PURCHASE_ITEM_PRICE')];
                    $product->save($p);
                }
            }
            
            $this->redirect('index',array('status'=>'待确认'));
        }else{
            $this->error("状态无法更新");
        }
    }

    public function receivePurchasedItem($id,$newReceived){
        $purchaseItem = M(C('DB_PURCHASE_ITEM'));        
        $purchaseItem->startTrans();
        $receivedQuantity = $purchaseItem->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->getField(C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY'));
        $receivedQuantity = $receivedQuantity + $newReceived;
        $purchaseItem->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->setField(C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY'),$receivedQuantity);
        $purchaseID = $purchaseItem->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->getField(C('DB_PURCHASE_ITEM_PURCHASE_ID'));
        $purchaseItem->commit();
                
        $data[C('DB_RESTOCK_CREATE_DATE')] = date("Y-m-d H:i:s" ,time());
        $data[C('DB_RESTOCK_SKU')] = $purchaseItem->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->getField(C('DB_PURCHASE_ITEM_SKU'));
        $data[C('DB_RESTOCK_MANAGER')] = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$data[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_MANAGER'));
        $data[C('DB_RESTOCK_WAREHOUSE')] = $purchaseItem->where(array(C('DB_PURCHASE_ITEM_ID')=>$id))->getField(C('DB_PURCHASE_ITEM_WAREHOUSE'));
        if($data[C('DB_RESTOCK_WAREHOUSE')]=='美自建仓' || $data[C('DB_RESTOCK_WAREHOUSE')]=='万邑通美西'){
            $data[C('DB_RESTOCK_TRANSPORT')] = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$data[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_TOUS'));
            $data[C('DB_RESTOCK_STATUS')] = '待发货';
            $this->updateRestock($data,$newReceived);
        }
        if($data[C('DB_RESTOCK_WAREHOUSE')]=='万邑通德国'){
            $data[C('DB_RESTOCK_TRANSPORT')] = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$data[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_TODE'));
            $data[C('DB_RESTOCK_STATUS')] = '待发货';
            $this->updateRestock($data,$newReceived);
        }
        if($data[C('DB_RESTOCK_WAREHOUSE')]=='深圳仓'){
            $this->updataSzStorage($data[C('DB_RESTOCK_SKU')],$newReceived);
        }
    }

    private function updataSzStorage($sku,$newReceived){
        $szstorage = M(C('DB_SZSTORAGE'));
        $szs = $szstorage->where(array(C('DB_SZSTORAGE_SKU')=>$sku))->find();
        if($szs !==null && $szs !==false){
            $szs[C('DB_SZSTORAGE_CINVENTORY')] = $szs[C('DB_SZSTORAGE_CINVENTORY')]+$newReceived;
            $szs[C('DB_SZSTORAGE_AINVENTORY')] = $szs[C('DB_SZSTORAGE_AINVENTORY')]+$newReceived;
            $szstorage->save($szs);
        }else{
            $szs[C('DB_SZSTORAGE_SKU')] = $sku;
            $szs[C('DB_SZSTORAGE_CINVENTORY')] = $newReceived;
            $szs[C('DB_SZSTORAGE_AINVENTORY')] = $newReceived;
            $szstorage->add($szs);
        }
    }

    private function updateRestock($data,$newReceived){

        $restock = M(C('DB_RESTOCK'));
        $restock->startTrans();
        $isInRestock = $restock->where(array(C('DB_RESTOCK_SKU')=>$data[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>$data[C('DB_RESTOCK_WAREHOUSE')],C('DB_RESTOCK_STATUS')=>$data[C('DB_RESTOCK_STATUS')]))->find();
        if($isInRestock !=null && $isInRestock!=false){
            $data[C('DB_RESTOCK_QUANTITY')] = $isInRestock[C('DB_RESTOCK_QUANTITY')]+$newReceived;
            $restock->where(array(C('DB_RESTOCK_ID')=>$isInRestock[C('DB_RESTOCK_ID')]))->save($data);
        }else{
            $data[C('DB_RESTOCK_QUANTITY')] = $newReceived;
            $restock->add($data);
        }     
        $restock->commit();
    }

    private function changeItemReceiveStatus($purchaseID){
        $status = '全部到货';
        $items = M(C('DB_PURCHASE_ITEM'))->where(array(C('DB_PURCHASE_ITEM_PURCHASE_ID')=>$purchaseID))->select();
        foreach ($items as $key => $value) {
            if($value[C('DB_PURCHASE_ITEM_RECEIVED_QUANTITY')]<$value[C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY')]){
                $status = '部分到货';
            }
        }
        M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$purchaseID))->setField(C('DB_PURCHASE_STATUS'),$status);
    }
}

?>