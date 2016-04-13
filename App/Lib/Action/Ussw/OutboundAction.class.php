<?php

class OutboundAction extends CommonAction{

	public function index(){
        if($_POST['keyword']==""){
            $Data = M(C('DB_USSW_OUTBOUND'));
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
            $this->outboundOrders = M(C('DB_USSW_OUTBOUND'))->where($where)->select();
        }
        $this->display();
	}

	public function itemOutbound(){
        if(IS_POST){
            if(I('post.sku','','htmlspecialchars')!=''){
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = M(C('DB_USSTORAGE'))->where($where)->find();
                    $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+1;
                    $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]-1;

                    $result = M(C('DB_USSTORAGE'))->where($where)->save($data);
                    if(false !== $result and 0!== $result){
                        $this->success('出库成功！');
                    }
                    else{
                        $this->error('出库失败！');
                    }
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = M(C('DB_USSTORAGE'))->where($where)->find();
                    $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+1;
                    $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]-1;

                    $result = M(C('DB_USSTORAGE'))->where($where)->save($data);
                    if(false !== $result and 0!== $result){
                        $this->success('出库成功！');
                    }
                    else{
                        $this->error('出库失败！');
                    }
                }
            }
            
        }
    }

    public function itemBatchOutbound(){
        if(IS_POST){
            if(I('post.sku','','htmlspecialchars')!=''){
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = M(C('DB_USSTORAGE'))->where($where)->find();
                    $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+I('post.quantity','','htmlspecialchars');
                    $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]-I('post.quantity','','htmlspecialchars');
                    $result = M(C('DB_USSTORAGE'))->where($where)->save($data);
                    
                    if(false !== $result and 0!== $result){
                        $this->success('出库成功！');
                    }
                    else{
                        $this->error("出库失败！");
                    }
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = M(C('DB_USSTORAGE'))->where($where)->find();
                    $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+I('post.quantity','','htmlspecialchars');
                    $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]-I('post.quantity','','htmlspecialchars');
                    $result = M(C('DB_USSTORAGE'))->where($where)->save($data);
                    
                    if(false !== $result and 0!== $result){
                        $this->success('出库成功！');
                    }
                    else{
                        $this->error("出库失败！");
                    }
                }
            }
        }
    }

    public function importEbaySaleRecordFile(){
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
                    $buyerID =  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    //判断ebay订单号是否已存在
                    if($this->duplicateSaleNo($saleNo)){ 
                    //ebay订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorOfFile]['error'] = '该ebay订单号已存在';
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }else{ 
                    //ebay订单号不在出库表中。
                        if($saleNo!=$objPHPExcel->getActiveSheet()->getCell("A".($i-1))->getValue()){ 
                        //判断是否跟上一行订单号相同，不相同的话，需要创建新出库单
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET_NO')] = $saleNo;
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_STATUS')] = '待出库';
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date('Y-m-d H:i:s');
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET')] = 'ebay.com';
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_SELLER_ID')] = I('post.sellerID','','htmlspecialchars');
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ID')] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_TEL')] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_EMAIL')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
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
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                                        $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    }else{
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                        $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue()*$skuQuantityDepart[1];
                                        $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    }
                                }
                                foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                    if(M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                        //检查产品编码是否在usstorage中,不存在添加错误信息
                                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                        $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                        $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                                    }else{
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$saleNo;
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AG".$i)->getValue();
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
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }else{
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue()*$skuQuantityDepart[1];
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                    
                                }
                            }
                            foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                if(M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                    //检查产品编码是否在usstorage中,不存在添加错误信息
                                    $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                    $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                    $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                    $indexForErrorOfFile = $indexForErrorOfFile+1;
                                }else{
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$saleNo;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AG".$i)->getValue();
                                    $k=$k+1;
                                }
                            }                        
                        }

                    }
                                                        
                }
                //验证可用库存数量是否大于需要的数量
                foreach ($outboundOrderItems as $key => $outbounditem) {
                    $rows = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')], $map[C('DB_USSTORAGE_AINVENTORY')]=>array('neq',0)))->select();
                    $totalAvailableQuantity = 0;
                    $totalNeedQuantity = 0;
                    $positions = null;
                    foreach ($rows as $key => $row) {
                        //查看该SKU总库存。收集该SKU的货位
                        $totalAvailableQuantity = $row[C('DB_USSTORAGE_AINVENTORY')] + $totalAvailableQuantity;
                        $positions = $positions==null?$row[C('DB_USSTORAGE_POSITION')]:$positions.'|'.$row[C('DB_USSTORAGE_POSITION')];
                    }
                    
                    foreach ($outboundOrderItems as $key => $value) {
                        if($value[C('DB_USSW_OUTBOUND_ITEM_SKU')] == $outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')])
                            $totalNeedQuantity = $totalNeedQuantity+$value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                    }

                    if($totalAvailableQuantity < $totalNeedQuantity){
                        //总库存小于订单数量，添加错误信息
                        $errorInFile[$indexForErrorOfFile]['saleno']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_OOID')];
                        $errorInFile[$indexForErrorOfFile]['sku']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                        $errorInFile[$indexForErrorOfFile]['error']='库存不足,可用库存'.$totalAvailableQuantity.'小于总出库数量'.$totalNeedQuantity;
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }
                }
                if($errorInFile != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$errorInFile);             
                    $this->display('importOutboundOrderError');
                }else{
                    //无错误信息
                    foreach ($outboundOrder as $key => $order) {
                        //循环第一次整理的ebay订单。查找是否有相同buyerid和地址信息的订单
                        if ($this->buyerExists($order,$filteredOutboundOrder)==-1){
                            //如果buyerid和第一栏地址信息不相同，添加到已过滤的数组。
                            $filteredOutboundOrder[$key]=$order;

                        }else{
                            //如果buyerid和第一栏地址重复，不添加该单到已过滤的数组。与该单相对应的产品列表的订单号更新为已存在的订单号。
                            $changedToSaleNo = $this->buyerExists($order,$filteredOutboundOrder);
                            foreach ($outboundOrderItems as $key => $item) {
                                if($item[C('DB_USSW_OUTBOUND_ITEM_OOID')]==$order[C('DB_USSW_OUTBOUND_MARKET_NO')]){
                                    $outboundOrderItems[$key][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$changedToSaleNo;
                                }
                            }
                        }
                    }
                    //更新已过滤的订单数组到ussw_outbound
                    foreach ($filteredOutboundOrder as $key => $value) {
                        M(C('DB_USSW_OUTBOUND'))->add($value);
                    }
                    //更新订单产品数组到ussw_outbound_item
                    foreach ($outboundOrderItems as $key => $value) {
                        $oid=M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_MARKET_NO')=>$value[C('DB_USSW_OUTBOUND_ITEM_OOID')]))->getField('id');
                        $value[C('DB_USSW_OUTBOUND_ITEM_OOID')]=$oid;
                        M(C('DB_USSW_OUTBOUND_ITEM'))->add($value);
                        $usstorage = M(C('DB_USSTORAGE'));
                        $rows = $usstorage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSW_OUTBOUND_ITEM_SKU')],$map[C('DB_USSTORAGE_AINVENTORY')]=>array('neq',0)))->select();
                        $difference = $value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                        //更新库存信息
                        foreach ($rows as $key => $row) {
                            if($row[C('DB_USSTORAGE_AINVENTORY')]>=$difference){
                                $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')] - $difference;
                                $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')] + $difference;
                                $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                                break;
                            }else{
                                $data[C('DB_USSTORAGE_OINVENTORY')] = $difference- $row[C('DB_USSTORAGE_AINVENTORY')];
                                $data[C('DB_USSTORAGE_AINVENTORY')] = 0;                            
                                $difference = $difference- $row[C('DB_USSTORAGE_AINVENTORY')];
                                $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                            }

                        }

                    }
                    $this->success('导入成功！');
                }
            }else{
                $this->error("模板不正确，请检查");
            }

        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function verifyImportedEbayEnOrderColumnName($secondRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_EBAY_EN_ORDER')));$c++){
            if(trim($secondRow[$c]) != C('IMPORT_EBAY_EN_ORDER')[$c]){
                return false;
            }
                
        }
        return true;
    }

    private function buyerExists($order, $filteredOutboundOrder){
        if($filteredOutboundOrder==null){
            return -1;
        }else{
            foreach ($filteredOutboundOrder as $key => $value) {
                if($order['buyerid'] == $value['buyerid'] and $order['buyeraddress1'] == $value['buyeraddress1']){
                    return $value['saleno'];
                }
            }
            return -1;
        }
    }

    private function duplicateSaleNo($saleNo){
        if(M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_MARKET_NO')=>$saleNo))->find()!=null)
            return true;
        else
            return false;
        
    }

    private function existsSaleNo($saleNo){
    	$result = M('ussw_outbound')->where('saleno='.$saleNo)->find();
    	if($result==null or $result==false){
    		return false;
    	}
    	else{return true;}
    }

    public function outboundOrderDetails($id){
        $this->order=M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_ID')=>$id))->select();
        $outboundOrderItems=M(C('DB_USSW_OUTBOUND_ITEM'))->where(array(C('DB_USSW_OUTBOUND_ITEM_OOID')=>$id))->select();
        $this->assign('outboundOrderItems',$outboundOrderItems);
        $this->display();
    }

    public function confirmOutboundOrder($id){
        if(M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_ID')=>$id))->getField(C('DB_USSW_OUTBOUND_STATUS'))=='已出库'){
            $this->error('该订单已经出库，请勿重复操作！');
        }else{
            $items = M(C('DB_USSW_OUTBOUND_ITEM'))->where(array(C('DB_USSW_OUTBOUND_ITEM_OOID')=>$id))->select();
            $usstorage = M(C('DB_USSTORAGE'));
            foreach ($items as $key => $item) {
                $positions = explode("|",$item[C('DB_USSW_OUTBOUND_ITEM_POSITION')]);
                $quantity = $item[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                if($position==''){
                    $row=$usstorage->where(array(C('DB_USSTORAGE_SKU')=>$item[C('DB_USSW_OUTBOUND_ITEM_SKU')]))->find();
                    if($row[C('DB_USSTORAGE_OINVENTORY')]>=$quantity){
                        $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')]-$quantity;
                        $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+$quantity;
                        $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        break;
                    }else{
                        $data[C('DB_USSTORAGE_OINVENTORY')] = 0;
                        $data[C('DB_USSTORAGE_CSALES')] = $row['csales']+$row[C('DB_USSTORAGE_OINVENTORY')];
                        $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        $quantity = $quantity - $row[C('DB_USSTORAGE_OINVENTORY')];
                    }
                }else{
                    foreach ($positions as $key => $position) {
                        $row=$usstorage->where(array(C('DB_USSTORAGE_SKU')=>$item[C('DB_USSW_OUTBOUND_ITEM_SKU')],C('DB_USSTORAGE_POSITION')=>$position))->find();
                        if($row[C('DB_USSTORAGE_OINVENTORY')]>=$quantity){
                            $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')]-$quantity;
                            $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+$quantity;
                            $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                            break;
                        }else{
                            $data[C('DB_USSTORAGE_OINVENTORY')] = 0;
                            $data[C('DB_USSTORAGE_CSALES')] = $row['csales']+$row[C('DB_USSTORAGE_OINVENTORY')];
                            $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                            $quantity = $quantity - $row[C('DB_USSTORAGE_OINVENTORY')];
                        }                
                    }
                }                
            }
            M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_ID')=>$id))->setField(array(C('DB_USSW_OUTBOUND_STATUS')=>'已出库'));
            $this->success('出库成功！');
        }
    }
}

?>