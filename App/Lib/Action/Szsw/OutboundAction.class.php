<?php

class OutboundAction extends CommonOutboundAction{

    public function index(){
        if($_POST['keyword']==""){
            $Data = M(C('DB_SZ_OUTBOUND'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $outboundOrders = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('outboundOrders',$outboundOrders);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = I('post.keywordValue','','htmlspecialchars');
            $this->outboundOrders = M(C('DB_SZ_OUTBOUND'))->where($where)->select();
        }
        $this->display();
    }

    public function simpleOutbound(){
      $this->display();
    }

    public function simpleOut(){
        if(IS_POST){
            if(I('post.sku','','htmlspecialchars')!=''){
                
                $where[C('DB_SZSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                if(I('post.position','','htmlspecialchars') != ''){
                    $where[C('DB_SZSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                }                
                $row = M(C('DB_SZSTORAGE'))->where($where)->find();
                $data[C('DB_SZSTORAGE_CSALES')] = $row[C('DB_SZSTORAGE_CSALES')]+I('post.quantity','','htmlspecialchars');
                $data[C('DB_SZSTORAGE_AINVENTORY')] = $row[C('DB_SZSTORAGE_AINVENTORY')]-I('post.quantity','','htmlspecialchars');

                $result = M(C('DB_SZSTORAGE'))->where($where)->save($data);
                if(false !== $result and 0!== $result){
                    $this->success('出库成功！');
                }
                else{
                    $this->error('出库失败！');
                }
            }
            
        }
    }

    public function importEbaySaleRecordFile(){
        if (!empty($_FILES)) {
            import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/szEbayOrders/',
                 'saveRule'=>$_POST['sellerID'].'_'.time(),
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
            
            //excel second column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $secondRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'2')->getValue();
                
            }

            if($this->verifyImportedEbayEnOrderColumnName($secondRow)){
                $j = 0; //索引：辅助数组，首先合并相同ebay订单号的订单
                $k = 0; //索引： 产品明细辅助数组
                $indexForErrorOfFile = 0; //索引：错误信息数组
                for($i=4;$i<=$highestRow-3;$i++){
                    $saleNo =  $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $buyerID =  $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue();
                    //判断ebay订单号是否已存在
                    if($this->duplicateSaleNo($this->getMarket($objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue()),$_POST['sellerID'],$saleNo)){ 
                        //ebay订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorOfFile]['error'] = '该ebay订单号已存在';
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }else{ 
                    //ebay订单号不在出库表中。
                        if($saleNo!=$objPHPExcel->getActiveSheet()->getCell("A".($i-1))->getValue()){ 
                        //判断是否跟上一行订单号相同，不相同的话，需要创建新出库单
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_MARKET_NO')] = $saleNo;
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_STATUS')] = '已出库';
                            if(I('post.order_date')==null || I('post.order_date')==''){
                                $outboundOrder[$j][C('DB_SZ_OUTBOUND_CREATE_TIME')]= Date('Y-m-d H:i:s');
                            }else{
                                $outboundOrder[$j][C('DB_SZ_OUTBOUND_CREATE_TIME')]= Date(I('post.order_date'));
                            }
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_MARKET')] = $this->getMarket($objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue());
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_SELLER_ID')] = $_POST['sellerID'];
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_ID')] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_TEL')] = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_EMAIL')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
                            $outboundOrder[$j][C('DB_SZ_OUTBOUND_SHIPPING_WAY')] = $objPHPExcel->getActiveSheet()->getCell("AP".$i)->getValue();
                            $j=$j+1;

                            if($sku!=''){
                                //如果sku不为空，首先按照|拆分sku,然后按照*拆分sku和quantity.
                                $skuDepart = null;
                                $skuQuantityDepart = null;
                                $departedSkuQuantity = null;                            
                                $indexForDepartedSkuQuantity = 0;
                                $skuDepart = explode("|",$sku);
                                foreach ($skuDepart as $key => $departedSku) {
                                    $skuQuantityDepart = explode("*",$departedSku);
                                    if(count($skuQuantityDepart)==1){
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue();
                                        $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    }else{
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue()*$skuQuantityDepart[1];
                                        $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    }
                                }
                                foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                    
                                    if(M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                        //检查产品编码是否在szstorage中,不存在添加错误信息
                                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                        $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                        $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入深圳仓';
                                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                                    }else{
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_OOID')]=$saleNo;
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_POSITION')] = $positions;
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                                        $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AR".$i)->getValue();
                                        $k=$k+1; 
                                    }
                                }                         
                            }
                        }elseif($saleNo==$objPHPExcel->getActiveSheet()->getCell("A".($i-1))->getValue() and $sku!=''){
                            //订单号与上一行的订单号相同并且sku列不为空，把当前行的产品分配到上一行的出库单里
                            $skuDepart = null;
                            $skuQuantityDepart = null;
                            $departedSkuQuantity = null;
                            $indexForDepartedSkuQuantity = 0;
                            $skuDepart = explode("|",$sku);                        
                            foreach ($skuDepart as $key => $departedSku) {
                                $skuQuantityDepart = explode("*",$departedSku);
                                if(count($skuQuantityDepart)==1){
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue();
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }else{
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue()*$skuQuantityDepart[1];
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    
                                }
                            }
                            foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                if(M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                    //检查产品编码是否在usstorage中,不存在添加错误信息
                                    $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                    $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                    $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入深圳仓';
                                    $indexForErrorOfFile = $indexForErrorOfFile+1;
                                }else{
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_OOID')]=$saleNo;
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_POSITION')] = $positions;
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                                    $outboundOrderItems[$k][C('DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AR".$i)->getValue();
                                    $k=$k+1;
                                }
                            }                        
                        }

                    }
                                                        
                }
                /*//验证可用库存数量是否大于需要的数量
                $error = $this->mergerArray($errorInFile,$this->checkAinventory($outboundOrderItems));
                if($error != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$error);             
                    $this->display('importOutboundOrderError');
                }else{
                    //无错误信息
                    //更新已合并的订单数组到szsw_outbound 
                    $this->addOutboundOrder($this->mergeOrder($outboundOrder));                    
                    //更新订单产品数组到szsw_outbound_item 和 szstorage
                    $this->addOutboundItem($outboundOrderItems,$_POST['sellerID']);                    
                    $this->success('导入成功！');
                }*/

                //不需要验证库存数量和发货数量
                if($errorInFile != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$errorInFile);             
                    $this->display('importOutboundOrderError');
                }else{
                    //无错误信息
                    //更新已合并的订单数组到szsw_outbound 
                    $mergeOrder = $this->mergeOrder($outboundOrder,$outboundOrderItems);
                    $this->addOutboundOrder($mergeOrder[0]);                    
                    //更新订单产品数组到szsw_outbound_item 和 szstorage
                    $this->addOutboundItem($mergeOrder[1],$_POST['sellerID']);  
                    $this->outboundItemsPriceUp($_POST['sellerID'],$outboundOrderItems);                  
                    $this->success('导入成功！');
                }
            }else{
                $this->error("模板不正确，请检查");
            }

        }else{
            $this->error("请选择上传的文件");
        }
    }
   
    private function checkAinventory($outboundItems){
        $index = 0;
        
        foreach ($outboundItems as $key => $outbounditem) {
            $ainventory = $this->getAinvenroty($outbounditem[C('DB_SZ_OUTBOUND_ITEM_SKU')]);
            $totalNeedQuantity=0;
            foreach ($outboundItems as $key => $value) {
                if($value[C('DB_SZ_OUTBOUND_ITEM_SKU')] == $outbounditem[C('DB_SZ_OUTBOUND_ITEM_SKU')])
                    $totalNeedQuantity = $totalNeedQuantity+$value[C('DB_SZ_OUTBOUND_ITEM_QUANTITY')];
            }

            if($ainventory < $totalNeedQuantity){
                //总库存小于订单数量，添加错误信息
                $error[$index]['saleno']=$outbounditem[C('DB_SZ_OUTBOUND_ITEM_OOID')];
                $error[$index]['sku']=$outbounditem[C('DB_SZ_OUTBOUND_ITEM_SKU')];
                $error[$index]['error']='库存不足,可用库存 '.$ainventory.' 小于总出库数量 '.$totalNeedQuantity;
                $index = $index+1;
            }
        }
        return $error;
    }

    private function mergerArray($arr1,$arr2){

        if($arr1!=null && $arr2!=null){
            return array_merge($arr1,$arr2);
        }elseif($arr1==null && $arr2!=null){
            return $arr2;
        }elseif($arr1!=null && $arr2==null){
            return $arr1;
        }else{
            return null;
        }
    }

    /*
    Merge the orders from the same buyer
    */
    private function mergeOrder($orders,$outboundOrderItems){
        foreach ($orders as $key => $order) {
            $exist = $this->buyerExists($order,$filteredOutboundOrder);
            if ($exist==-1){
                $filteredOutboundOrder[$key]=$order;
            }else{
                foreach ($outboundOrderItems as $key => $item) {
                    if($item[C('DB_SZ_OUTBOUND_ITEM_OOID')]==$order[C('DB_SZ_OUTBOUND_MARKET_NO')]){
                        $outboundOrderItems[$key][C('DB_SZ_OUTBOUND_ITEM_OOID')]=$exist;
                    }
                }
            }
        }
        return array($filteredOutboundOrder,$outboundOrderItems);
    }

    private function addOutboundItem($outboundItems,$sellerId){
        $szstorage = M(C('DB_SZSTORAGE'));
        $szOutbound = M(C('DB_SZ_OUTBOUND'));
        $szOutboundItem = M(C('DB_SZ_OUTBOUND_ITEM'));
        $szOutboundItem->startTrans();        
        $kpiSaleRecord = M(C('DB_KPI_SALE_RECORD'));
        $kpiSaleRecord->startTrans();
        foreach ($outboundItems as $key => $value) {
            //add to sz_outbound_item
            $oid = $szOutbound->where(array(C('DB_SZ_OUTBOUND_MARKET_NO')=>$value[C('DB_SZ_OUTBOUND_ITEM_OOID')]))->getField('id');
            $value[C('DB_SZ_OUTBOUND_ITEM_OOID')]= $oid;
            $value[C('DB_SZ_OUTBOUND_ITEM_POSITION')] = $this->getPosition($value[C('DB_SZ_OUTBOUND_ITEM_SKU')]);
            $szOutboundItem->add($value);
            
            //update sz usstorage
            $where = array(C('DB_SZSTORAGE_SKU')=>$value[C('DB_SZ_OUTBOUND_ITEM_SKU')]);
            $szstorage->where($where)->setDec(C('DB_SZSTORAGE_AINVENTORY'),$value[C('DB_SZ_OUTBOUND_ITEM_QUANTITY')]);
            $szstorage->where($where)->setInc(C('DB_SZSTORAGE_CSALES'),$value[C('DB_SZ_OUTBOUND_ITEM_QUANTITY')]);

            //统计销售绩效考核的sku
            $kpiMap[C('DB_KPI_SALE_SKU')] = array('eq', $value[C('DB_SZ_OUTBOUND_ITEM_SKU')]);
            $kpiMap[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', C('SZSW'));
            $kpiSaleId = M(C('DB_KPI_SALE'))->where($kpiMap)->getField(C('DB_KPI_SALE_ID'));
            $repeatOrder = $kpiSaleRecord->where(array(C('DB_KPI_SALE_RECORD_TRANSACTION_NO')=>$value[C('DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO')]))->find();
            if($kpiSaleId!=null && $repeatOrder==null){
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSaleId;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SKU')] = $value[C('DB_SZ_OUTBOUND_ITEM_SKU')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = C('SZSW');
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = time();
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_QUANTITY')] = $value[C('DB_SZ_OUTBOUND_ITEM_QUANTITY')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET')] = $szOutbound->where(array(C('DB_SZ_OUTBOUND_ID')=>$oid))->getField(C('DB_SZ_OUTBOUND_MARKET'));
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SELLER_ID')] = $sellerId;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET_NO')] = $value[C('DB_SZ_OUTBOUND_ITEM_MARKET_NO')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = $value[C('DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO')]; 
                $kpiSaleRecord->add($kpiSaleRecordData);
            }
        }
        $szOutboundItem->commit();
        $szstorage->commit();
        $kpiSaleRecord->commit();
    }

    private function addOutboundOrder($orders){
        $szOutbound = M(C('DB_SZ_OUTBOUND'));
        $szOutbound->startTrans();
        foreach ($orders as $key => $value) {
            $szOutbound->add($value);
        }
        $szOutbound->commit();
    }

    private function getAinvenroty($sku){
        return M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_SKU')=>$sku))->getField(C('DB_SZSTORAGE_AINVENTORY'));
    }

    private function getPosition($sku){
        return M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_SKU')=>$sku))->getField(C('DB_SZSTORAGE_POSITION'));
    }

    private function verifyImportedEbayEnOrderColumnName($secondRow){
        foreach (array_keys(C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')) as $key => $value) {
            if(trim($secondRow[$value]) != C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')[$value]){
                return false;
            }
        }
        return true;          
    }

    private function buyerExists($order, $filteredOrder){
        if($filteredOrder==null){
            return -1;
        }else{
            foreach ($filteredOrder as $key => $value) {
                if($order[C('DB_SZ_OUTBOUND_BUYER_ID')] == $value[C('DB_SZ_OUTBOUND_BUYER_ID')] && $order[C('DB_SZ_OUTBOUND_BUYER_ADDRESS1')] == $value[C('DB_SZ_OUTBOUND_BUYER_ADDRESS1')]){
                    return $value[C('DB_SZ_OUTBOUND_MARKET_NO')];
                }
            }
            return -1;
        }
    }

    private function duplicateSaleNo($market,$sellerId,$saleNo){
        $map[C('DB_SZ_OUTBOUND_MARKET')]=array('eq',$market);
        $map[C('DB_SZ_OUTBOUND_MARKET_NO')]=array('eq',$saleNo);
        $map[C('DB_SZ_OUTBOUND_SELLER_ID')]=array('eq',$sellerId);
        if(M(C('DB_SZ_OUTBOUND'))->where($map)->find()!==null && M(C('DB_SZ_OUTBOUND'))->where($map)->find()!==false)
            return true;
        else
            return false;
        
    }

    public function outboundOrderDetails($id){
        $this->order=M(C('DB_SZ_OUTBOUND'))->where(array(C('DB_SZ_OUTBOUND_ID')=>$id))->select();
        $outboundOrderItems=M(C('DB_SZ_OUTBOUND_ITEM'))->where(array(C('DB_SZ_OUTBOUND_ITEM_OOID')=>$id))->select();
        $this->assign('outboundOrderItems',$outboundOrderItems);
        $this->display();
    }

    public function confirmOutboundOrder($id){
        if(M(C('DB_SZ_OUTBOUND'))->where(array(C('DB_SZ_OUTBOUND_ID')=>$id))->getField(C('DB_SZ_OUTBOUND_STATUS'))=='已出库'){
            $this->error('该订单已经出库，请勿重复操作！');
        }else{
            $items = M(C('DB_SZ_OUTBOUND_ITEM'))->where(array(C('DB_SZ_OUTBOUND_ITEM_OOID')=>$id))->select();
            $usstorage = M(C('DB_SZSTORAGE'));
            foreach ($items as $key => $item) {
                //$positions = explode("|",$item[C('DB_SZ_OUTBOUND_ITEM_POSITION')]);
                $quantity = $item[C('DB_SZ_OUTBOUND_ITEM_QUANTITY')];
                $map[C('DB_SZSTORAGE_SKU')] = array('eq',$item[C('DB_SZ_OUTBOUND_ITEM_SKU')]);
                $map[C('DB_SZSTORAGE_OINVENTORY')]=array('neq',0);
                $rows=$usstorage->where($map)->select();
                foreach ($rows as $key => $row) {
                    if($row[C('DB_SZSTORAGE_OINVENTORY')]>=$quantity){
                        $data[C('DB_SZSTORAGE_OINVENTORY')] = $row[C('DB_SZSTORAGE_OINVENTORY')]-$quantity;
                        $data[C('DB_SZSTORAGE_CSALES')] = $row[C('DB_SZSTORAGE_CSALES')]+$quantity;
                        $usstorage->where(array(C('DB_SZSTORAGE_ID')=>$row[C('DB_SZSTORAGE_ID')]))->save($data);
                        break;
                    }else{
                        $data[C('DB_SZSTORAGE_OINVENTORY')] = 0;
                        $data[C('DB_SZSTORAGE_CSALES')] = $row['csales']+$row[C('DB_SZSTORAGE_OINVENTORY')];
                        $quantity = $quantity - $row[C('DB_SZSTORAGE_OINVENTORY')];
                        $usstorage->where(array(C('DB_SZSTORAGE_ID')=>$row[C('DB_SZSTORAGE_ID')]))->save($data);
                    }   
                }              
            }
            M(C('DB_SZ_OUTBOUND'))->where(array(C('DB_SZ_OUTBOUND_ID')=>$id))->setField(array(C('DB_SZ_OUTBOUND_STATUS')=>'已出库'));
            $this->success('出库成功！');
        }
    }

    private function getMarket($price){
        if(substr($price,0,4)=='EUR '){
            return 'ebay.de';
        }else{
            return 'ebay.com';
        }
    }
}

?>