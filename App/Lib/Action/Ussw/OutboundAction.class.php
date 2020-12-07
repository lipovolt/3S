<?php

class OutboundAction extends CommonOutboundAction{

	public function index(){
        if($_POST['keyword']==""){
            $Data = M(C('DB_USSW_OUTBOUND'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $outboundOrders = $Data->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('outboundOrders',$outboundOrders);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('eq',I('post.keywordValue','','htmlspecialchars'));
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

    public function importUOOHandel(){
        switch ($_POST["market"]) {
            case 'amazon':
                $this->importAmazonSaleRecordFile("amazon",$_POST["sellerID"]);
                break;
            case 'ebay':
                $this->importEbaySaleRecordFile("ebay",$_POST["sellerID"]);
                break;
            case 'groupon':
                $this->importGrouponSaleRecordFile("groupon",$_POST["sellerID"]);
                break;
            case 'amazon_fba':
                $this->importAmazonFBASaleRecordFile("amazon_fba",$_POST["sellerID"]);
                break;
            default:
                $this->error("请选择平台和账号！");
                break;
        }
    }

    private function importAmazonSaleRecordFile($market,$sellerID){
        if(!empty($_FILES)){
             import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/usswOutbound/',
                 'saveRule'=>I('post.market').'_'.I('post.sellerID').'_'.time(),
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

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }
            if($this->verifyImportedAmazonOrderColumnName($firstRow)){
                $j = 0; //索引：辅助数组，首先合并相同ebay订单号的订单
                $k = 0; //索引： 产品明细辅助数组
                $indexForErrorFile = 0; //索引：错误信息数组

                for($i=2;$i<=$highestRow;$i++){
                    $saleNo = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                    //判断amazon订单号是否已存在
                    if($this->duplicateSaleNo($market,$sellerID,$saleNo)){
                        //ebay订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorFile]['error'] = '该amazon'.$sellerID.'订单号已存在';
                        $indexForErrorFile = $indexForErrorFile+1;
                    }else{
                        //ebay订单号不在出库表中
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET_NO')] = $saleNo;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_STATUS')] = '已出库';
                        if(I('post.order_date')==null || I('post.order_date')==''){
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date('Y-m-d H:i:s');
                        }else{
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date(I('post.order_date'));
                        }
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET')] = 'amazon';
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_SELLER_ID')] = $sellerID;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_TEL')] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_EMAIL')] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue()." ".$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue();
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
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }else{
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue()*$skuQuantityDepart[1];
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }
                            }
                            foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                if(M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                    //检查产品编码是否在usstorage中,不存在添加错误信息
                                    $errorInFile[$indexForErrorFile]['saleno'] = $saleNo;
                                    $errorInFile[$indexForErrorFile]['sku'] = $departedSkuQuantityValue['sku'];
                                    $errorInFile[$indexForErrorFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                    $indexForErrorFile = $indexForErrorFile+1;
                                }else{
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$saleNo;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
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
                    $outboundOrderItems[$key-1][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                    foreach ($outboundOrderItems as $key => $value) {
                        if($value[C('DB_USSW_OUTBOUND_ITEM_SKU')] == $outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')])
                            $totalNeedQuantity = $totalNeedQuantity+$value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                    }

                    if($totalAvailableQuantity < $totalNeedQuantity){
                        //总库存小于订单数量，添加错误信息
                        $errorInFile[$indexForErrorFile]['saleno']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_OOID')];
                        $errorInFile[$indexForErrorFile]['sku']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                        $errorInFile[$indexForErrorFile]['error']='库存不足,可用库存'.$totalAvailableQuantity.'小于总出库数量'.$totalNeedQuantity;
                        $indexForErrorFile = $indexForErrorFile+1;
                    }
                }

                if($errorInFile != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$errorInFile);             
                    $this->display('importOutboundOrderError');
                }else{
                    //无错误信息
                    foreach ($outboundOrder as $key => $order) {
                        //循环第一次整理的amazon订单。查找是否有相同buyer和地址信息的订单
                        if ($this->buyerExists($order,$filteredOutboundOrder)==-1){
                            //如果buyer和地址信息不相同，添加到已过滤的数组。
                            $filteredOutboundOrder[$key]=$order;
                        }else{
                            //如果buyer和地址重复，不添加该单到已过滤的数组。与该单相对应的产品列表的订单号更新为已存在的订单号。
                            $changedToSaleNo = $this->buyerExists($order,$filteredOutboundOrder);
                            foreach ($outboundOrderItems as $key => $item) {
                                if($item[C('DB_USSW_OUTBOUND_ITEM_OOID')]==$order[C('DB_USSW_OUTBOUND_MARKET_NO')]){
                                    $outboundOrderItems[$key][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$changedToSaleNo;
                                }
                            }
                        }
                    }
                    //更新ussw_outbound和ussw_outbound_item
                    $this->addUsswOutboundOrder($filteredOutboundOrder,$outboundOrderItems,$sellerID);
                    $this->outboundItemsPriceUp($sellerID,$outboundOrderItems);
                    $this->exportPackingList($filteredOutboundOrder);
                }
            }else{
                $this->error("不是amazon订单模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function importAmazonFBASaleRecordFile($market,$sellerID){
        if(!empty($_FILES)){
             import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/amazonFbaOutbound/',
                 'saveRule'=>I('post.market').'_'.I('post.sellerID').'_'.time(),
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

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }
            if($this->verifyImportedAmazonFBAOrderColumnName($firstRow)){
                $j = 0; //索引：辅助数组，首先合并相同amazon订单号的订单
                $k = 0; //索引： 产品明细辅助数组
                $indexForErrorFile = 0; //索引：错误信息数组

                for($i=2;$i<=$highestRow;$i++){
                    $saleNo = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    //判断amazon订单号是否已存在
                    if($this->duplicateAmazonUsFbaSaleNo($market,$sellerID,$saleNo)){
                        //amazon订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorFile]['error'] = '该amazon'.$sellerID.'订单号已存在';
                        $indexForErrorFile = $indexForErrorFile+1;
                    }else{
                        //amazon订单号不在出库表中
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET_NO')] = $saleNo;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_STATUS')] = '已出库';
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= substr($objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue(), 0,10).' '.substr($objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue(), 11,8);
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET')] = $market;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_SELLER_ID')] = $sellerID;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_TEL')] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_EMAIL')] = null;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("AA".$i)->getValue()." ".$objPHPExcel->getActiveSheet()->getCell("AB".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("AC".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("AE".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_SHIPPING_COMPANY')] = $objPHPExcel->getActiveSheet()->getCell("AQ".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_TRACKING_NUMBER')] = $objPHPExcel->getActiveSheet()->getCell("AR".$i)->getValue();
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
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }else{
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue()*$skuQuantityDepart[1];
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }
                            }

                            foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$saleNo;
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                                $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                                $k=$k+1; 
                            }                         
                        }
                    }
                }

                if($errorInFile != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$errorInFile);             
                    $this->display('importOutboundOrderError');
                }else{
                    $this->addAmazonUsFBAOrder($outboundOrder,$outboundOrderItems,$sellerID);
                    $this->outboundItemsPriceUp($sellerID,$outboundOrderItems);
                    $this->success("导入成功");
                }
            }else{
                $this->error("不是amazon fba订单模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }


    private function importEbaySaleRecordFile($market,$sellerID){
    	if (!empty($_FILES)) {
            import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/usswOutbound/',
                 'saveRule'=>I('post.market').'_'.I('post.sellerID').'_'.time(),
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
                    $sku = $this->skuDecode($objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue());

                    //判断sku格式是否与账号要求的SKU格式一致
                    if(strcasecmp($sellerID, 'greatgoodshop')==0 && !$this->isStandardSkuFormat($objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue())){
                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorOfFile]['error'] = '该ebay'.$sellerID.'订单号的SKU不是 greatgoodshop sku 格式';
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }
                    if(strcasecmp($sellerID, 'ali-retail')==0 && !$this->isARSKUFormat($objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue())){
                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorOfFile]['error'] = '该ebay'.$sellerID.'订单号的SKU不是 ali-retail sku 格式';
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }
                    
                    //判断ebay订单号是否已存在
                    if($this->duplicateSaleNo($market,$sellerID,$saleNo)){ 
                    //ebay订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorOfFile]['error'] = '该ebay'.$sellerID.'订单号已存在';
                        $indexForErrorOfFile = $indexForErrorOfFile+1;
                    }else{ 
                    //ebay订单号不在出库表中。
                        if($saleNo!=$objPHPExcel->getActiveSheet()->getCell("A".($i-1))->getValue()){ 
                        //判断是否跟上一行订单号相同，不相同的话，需要创建新出库单
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET_NO')] = $saleNo;
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_STATUS')] = '已出库';
                            if(I('post.order_date')==null || I('post.order_date')==''){
                                $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date('Y-m-d H:i:s');
                            }else{
                                $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date(I('post.order_date'));
                            }
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET')] = 'ebay';
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_SELLER_ID')] = $sellerID;
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ID')] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_TEL')] = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_EMAIL')] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
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
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                                        $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AV".$i)->getValue();
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
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("AV".$i)->getValue();
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
                    $outboundOrderItems[$key-1][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
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
                    //更新ussw_outbound和ussw_outbound_item
                    $this->addUsswOutboundOrder($filteredOutboundOrder,$outboundOrderItems,$sellerID);
                    $this->outboundItemsPriceUp($sellerID,$outboundOrderItems);
                    $this->exportPackingList($filteredOutboundOrder);
                }
            }else{
                $this->error("不是ebay订单模板，请检查");
            }

        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function importGrouponSaleRecordFile($market,$sellerID){
        if(!empty($_FILES)){
             import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/usswOutbound/',
                 'saveRule'=>$market.'_'.$sellerID.'_'.time(),
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

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }
            if($this->verifyImportedGrouponOrderColumnName($firstRow)){
                $j = 0; //索引：辅助数组，首先合并相同ebay订单号的订单
                $k = 0; //索引： 产品明细辅助数组
                $indexForErrorFile = 0; //索引：错误信息数组

                for($i=2;$i<=$highestRow;$i++){
                    $saleNo = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $sku = $objPHPExcel->getActiveSheet()->getCell("BC".$i)->getValue();
                    //判断amazon订单号是否已存在
                    if($this->duplicateSaleNo($market,$sellerID,$saleNo)){
                        //ebay订单号在出库表中，添加错误信息
                        $errorInFile[$indexForErrorFile]['saleno'] = $saleNo;
                        $errorInFile[$indexForErrorFile]['error'] = '该groupon'.$sellerID.'订单号已存在';
                        $indexForErrorFile = $indexForErrorFile+1;
                    }else{
                        //ebay订单号不在出库表中
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET_NO')] = $saleNo;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_STATUS')] = '已出库';
                        if(I('post.order_date')==null || I('post.order_date')==''){
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date('Y-m-d H:i:s');
                        }else{
                            $outboundOrder[$j][C('DB_USSW_OUTBOUND_CREATE_TIME')]= Date(I('post.order_date'));
                        }
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_MARKET')] = 'groupon';
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_SELLER_ID')] = $sellerID;
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_NAME')] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_CITY')] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_STATE')] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_ZIP')] = $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                        $outboundOrder[$j][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')] = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
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
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }else{
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['sku'] = $skuQuantityDepart[0];
                                    $departedSkuQuantity[$indexForDepartedSkuQuantity]['quantity'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue()*$skuQuantityDepart[1];
                                    $indexForDepartedSkuQuantity = $indexForDepartedSkuQuantity+1;
                                }
                            }
                            foreach ($departedSkuQuantity as $key => $departedSkuQuantityValue) {
                                if(M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$departedSkuQuantityValue['sku']))->find() == null){
                                    //检查产品编码是否在usstorage中,不存在添加错误信息
                                    $errorInFile[$indexForErrorFile]['saleno'] = $saleNo;
                                    $errorInFile[$indexForErrorFile]['sku'] = $departedSkuQuantityValue['sku'];
                                    $errorInFile[$indexForErrorFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                    $indexForErrorFile = $indexForErrorFile+1;
                                }else{
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$saleNo;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$departedSkuQuantityValue['sku'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$departedSkuQuantityValue['quantity'];
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("BD".$i)->getValue();
                                    $outboundOrderItems[$k][C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
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
                    $outboundOrderItems[$key-1][C('DB_USSW_OUTBOUND_ITEM_POSITION')] = $positions;
                    foreach ($outboundOrderItems as $key => $value) {
                        if($value[C('DB_USSW_OUTBOUND_ITEM_SKU')] == $outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')])
                            $totalNeedQuantity = $totalNeedQuantity+$value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                    }

                    if($totalAvailableQuantity < $totalNeedQuantity){
                        //总库存小于订单数量，添加错误信息
                        $errorInFile[$indexForErrorFile]['saleno']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_OOID')];
                        $errorInFile[$indexForErrorFile]['sku']=$outbounditem[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                        $errorInFile[$indexForErrorFile]['error']='库存不足,可用库存'.$totalAvailableQuantity.'小于总出库数量'.$totalNeedQuantity;
                        $indexForErrorFile = $indexForErrorFile+1;
                    }
                }

                if($errorInFile != null){
                    //有错误信息，输出错误信息，不做数据库操作。
                    $this->assign('errorInFile',$errorInFile);             
                    $this->display('importOutboundOrderError');
                }else{
                    //无错误信息
                    foreach ($outboundOrder as $key => $order) {
                        //循环第一次整理的amazon订单。查找是否有相同buyer和地址信息的订单
                        if ($this->buyerExists($order,$filteredOutboundOrder)==-1){
                            //如果buyer和地址信息不相同，添加到已过滤的数组。
                            $filteredOutboundOrder[$key]=$order;
                        }else{
                            //如果buyer和地址重复，不添加该单到已过滤的数组。与该单相对应的产品列表的订单号更新为已存在的订单号。
                            $changedToSaleNo = $this->buyerExists($order,$filteredOutboundOrder);
                            foreach ($outboundOrderItems as $key => $item) {
                                if($item[C('DB_USSW_OUTBOUND_ITEM_OOID')]==$order[C('DB_USSW_OUTBOUND_MARKET_NO')]){
                                    $outboundOrderItems[$key][C('DB_USSW_OUTBOUND_ITEM_OOID')]=$changedToSaleNo;
                                }
                            }
                        }
                    }
                    //更新ussw_outbound和ussw_outbound_item
                    $this->addUsswOutboundOrder($filteredOutboundOrder,$outboundOrderItems,$sellerID);
                    $this->outboundItemsPriceUp($sellerID,$outboundOrderItems);
                    $this->exportPackingList($filteredOutboundOrder);
                }
            }else{
                $this->error("不是groupon订单模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function addUsswOutboundOrder($outboundOrders,$outboundOrderItems,$sellerID){
        //添加出库单到ussw_outbound
        $usswOutbound = M(C('DB_USSW_OUTBOUND'));
        $usswOutbound->startTrans();
        foreach ($outboundOrders as $key => $value) {
            $usswOutbound->add($value);
        }
        //添加出库单到ussw_outbound_item
        $usswOutboundItem = M(C('DB_USSW_OUTBOUND_ITEM'));
        $usstorage = M(C('DB_USSTORAGE'));
        $usswOutboundItem->startTrans();
        $usstorage->startTrans();
        $kpiSaleRecord = M(C('DB_KPI_SALE_RECORD'));
        $kpiSaleRecord->startTrans();
        foreach ($outboundOrderItems as $key => $value) {
            $where[C('DB_USSW_OUTBOUND_MARKET_NO')] = array('eq',$value[C('DB_USSW_OUTBOUND_ITEM_OOID')]);
            $where[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('eq',$sellerID);
            $oid=$usswOutbound->where($where)->getField('id');
            $value[C('DB_USSW_OUTBOUND_ITEM_OOID')]=$oid;
            $value[C('DB_SZ_OUTBOUND_ITEM_POSITION')]=$usstorage->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSW_OUTBOUND_ITEM_SKU')]))->getField(C('DB_USSTORAGE_POSITION'));
            $usswOutboundItem->add($value);
            $map[C('DB_USSTORAGE_SKU')] = array('eq',$value[C('DB_USSW_OUTBOUND_ITEM_SKU')]); 
            $map[C('DB_USSTORAGE_AINVENTORY')]=array('neq',0);                   
            $rows = $usstorage->where($map)->select();
            $difference = $value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
            //更新库存信息
            /*更新出库信息到待出库，然后需要手动点击确认出库
            foreach ($rows as $key => $row) {
                if($row[C('DB_USSTORAGE_AINVENTORY')]>=$difference){
                    $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')] - $difference;
                    $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')] + $difference;
                    $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                    break;
                }else{
                    $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')]+$row[C('DB_USSTORAGE_AINVENTORY')];
                    $data[C('DB_USSTORAGE_AINVENTORY')] = 0;                            
                    $difference = $difference- $row[C('DB_USSTORAGE_AINVENTORY')];
                    $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                }

            }*/
            //更新库存信息到已出库，不需手动点击确认出库
            foreach ($rows as $key => $row) {
                $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')] - $difference;
                $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')] + $difference;
                $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
            }
            //统计销售绩效考核的sku
            $kpiMap[C('DB_KPI_SALE_SKU')] = array('eq', $value[C('DB_USSW_OUTBOUND_ITEM_SKU')]);
            $kpiMap[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', C('USSW'));
            $kpiSaleId = M(C('DB_KPI_SALE'))->where($map)->getField(C('DB_KPI_SALE_ID'));
            $repeatOrder = M(C('DB_KPI_SALE_RECORD'))->where(array(C('DB_KPI_SALE_RECORD')=>$value[C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]))->find();
            if($kpiSaleId!=null && $repeatOrder==null){
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSaleId;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SKU')] = $value[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = C('USSW');
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = time();
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_QUANTITY')] = $value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET')] = I('post.market');
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SELLER_ID')] = $sellerID;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET_NO')] = $value[C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = $value[C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')];
                $kpiSaleRecord->add($kpiSaleRecordData);
            }
        }
        $usswOutbound->commit();
        $usswOutboundItem->commit();
        $usstorage->commit();
        $kpiSaleRecord->commit();
    }

    private function addAmazonUsFBAOrder($outboundOrders,$outboundOrderItems,$sellerID){
        //添加出库单到amazon_us_fba_outbound
        $fbaOutbound = M(C('DB_AMAZON_US_FBA_OUTBOUND'));
        $fbaOutbound->startTrans();
        $usFbaStorage = M(C('DB_AMAZON_US_STORAGE'));
        $usFbaStorage->startTrans();
        foreach ($outboundOrders as $key => $value) {
            $fbaOutbound->add($value);
        }
        //添加出库单到ussw_outbound_item
        $fbaOutboundItem = M(C('DB_AMAZON_US_FBA_OUTBOUND_ITEM'));
        $fbaOutboundItem->startTrans();
        $kpiSaleRecord = M(C('DB_KPI_SALE_RECORD'));
        $kpiSaleRecord->startTrans();
        foreach ($outboundOrderItems as $key => $value) {
            $oid=$fbaOutbound->where(array(C('DB_USSW_OUTBOUND_MARKET_NO')=>$value[C('DB_USSW_OUTBOUND_ITEM_OOID')]))->getField('id');
            $value[C('DB_USSW_OUTBOUND_ITEM_OOID')]=$oid;
            $fbaOutboundItem->add($value);
            $map[C('DB_USSTORAGE_SKU')] = array('eq',$value[C('DB_USSW_OUTBOUND_ITEM_SKU')]); 
            $map[C('DB_USSTORAGE_AINVENTORY')]=array('neq',0); 
            $difference = $value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];

            //统计累计销量
            $where[C('DB_USSTORAGE_SKU')] = array('eq',$value[C('DB_USSW_OUTBOUND_ITEM_SKU')]);                   
            $row = $usFbaStorage->where($where)->find();
            $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')] + $difference;
            $usFbaStorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
            
            //统计销售绩效考核的sku
            $kpiMap[C('DB_KPI_SALE_SKU')] = array('eq', $value[C('DB_USSW_OUTBOUND_ITEM_SKU')]);
            $kpiMap[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', C('USSW'));
            $kpiSaleId = M(C('DB_KPI_SALE'))->where($map)->getField(C('DB_KPI_SALE_ID'));
            $repeatOrder = M(C('DB_KPI_SALE_RECORD'))->where(array(C('DB_KPI_SALE_RECORD')=>$value[C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')]))->find();
            if($kpiSaleId!=null && $repeatOrder==null){
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSaleId;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SKU')] = $value[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = C('USSW');
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = time();
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_QUANTITY')] = $value[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET')] = I('post.market');
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SELLER_ID')] = $sellerID;
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET_NO')] = $value[C('DB_USSW_OUTBOUND_ITEM_MARKET_NO')];
                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = $value[C('DB_USSW_OUTBOUND_ITEM_TRANSACTION_NO')];
                $kpiSaleRecord->add($kpiSaleRecordData);
            }
        }
        $fbaOutbound->commit();
        $fbaOutboundItem->commit();
        $kpiSaleRecord->commit();
        $usFbaStorage->commit();
    }

    private function verifyImportedAmazonOrderColumnName($firstRow){
        for($c='A';$c<='Y';$c++){
            if(trim($firstRow[$c]) != C('IMPORT_AMAZON_UNSHIPPED_ORDER')[$c]){
                return false;
            }                
        }
        return true;
    }

    private function verifyImportedAmazonFBAOrderColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_AMAZON_FBA_ORDER')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_AMAZON_FBA_ORDER')[$c]){
                return false;
            }                
        }
        return true;
    }

    private function verifyImportedEbayEnOrderColumnName($secondRow){
        foreach (array_keys(C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')) as $key => $value) {
            if(trim($secondRow[$value]) != C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')[$value]){
                return false;
            }
        }
        return true;          
    }

    private function verifyImportedGrouponOrderColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_GROUPON_UNSHIPPED_ORDER')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_GROUPON_UNSHIPPED_ORDER')[$c]){
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
                if(strcasecmp($order[C('DB_USSW_OUTBOUND_BUYER_ID')], $value[C('DB_USSW_OUTBOUND_BUYER_ID')])==0
                     and strcasecmp($order[C('DB_USSW_OUTBOUND_BUYER_NAME')], $value[C('DB_USSW_OUTBOUND_BUYER_NAME')])==0 and strcasecmp($order[C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')], $value[C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')])==0){
                    return $value[C('DB_USSW_OUTBOUND_MARKET_NO')];
                }
            }
            return -1;
        }
    }

    private function duplicateSaleNo($market,$sellerID,$saleNo){
        $map[C('DB_USSW_OUTBOUND_MARKET')] = array('eq',$market);
        $map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('eq',$sellerID);
        $map[C('DB_USSW_OUTBOUND_MARKET_NO')] = array('eq',$saleNo);
        if(M(C('DB_USSW_OUTBOUND'))->where($map)->find()!=null)
            return true;
        else
            return false;        
    }

    private function duplicateAmazonUsFbaSaleNo($market,$sellerID,$saleNo){
        $map[C('DB_USSW_OUTBOUND_MARKET')] = array('eq',$market);
        $map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('eq',$sellerID);
        $map[C('DB_USSW_OUTBOUND_MARKET_NO')] = array('eq',$saleNo);
        if(M(C('DB_AMAZON_US_FBA_OUTBOUND'))->where($map)->find()!=null)
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
                //$positions = explode("|",$item[C('DB_USSW_OUTBOUND_ITEM_POSITION')]);
                $quantity = $item[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                $map[C('DB_USSTORAGE_SKU')] = array('eq',$item[C('DB_USSW_OUTBOUND_ITEM_SKU')]);
                $map[C('DB_USSTORAGE_OINVENTORY')]=array('neq',0);
                $rows=$usstorage->where($map)->select();
                foreach ($rows as $key => $row) {
                    if($row[C('DB_USSTORAGE_OINVENTORY')]>=$quantity){
                        $data[C('DB_USSTORAGE_OINVENTORY')] = $row[C('DB_USSTORAGE_OINVENTORY')]-$quantity;
                        $data[C('DB_USSTORAGE_CSALES')] = $row[C('DB_USSTORAGE_CSALES')]+$quantity;
                        $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                        break;
                    }else{
                        $data[C('DB_USSTORAGE_OINVENTORY')] = 0;
                        $data[C('DB_USSTORAGE_CSALES')] = $row['csales']+$row[C('DB_USSTORAGE_OINVENTORY')];
                        $quantity = $quantity - $row[C('DB_USSTORAGE_OINVENTORY')];
                        $usstorage->where(array(C('DB_USSTORAGE_ID')=>$row[C('DB_USSTORAGE_ID')]))->save($data);
                    }   
                }              
            }
            M(C('DB_USSW_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_ID')=>$id))->setField(array(C('DB_USSW_OUTBOUND_STATUS')=>'已出库'));
            $this->success('出库成功！');
        }
    }

    private function isInStock($sku){
        $result = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku,C('DB_USSTORAGE_AINVENTORY')>0))->select();
        if($result == null || $result == false){
            return false;
        }
        else{
            return true;
        }
    }

    private function exportPackingList($outboundOrder){
        $xlsName  = "Packinglist";
        $xlsCell  = array(
            array(C('DB_USSW_OUTBOUND_ITEM_MARKET_NO'),'平台订单号'),
            array(C('DB_USSW_OUTBOUND_BUYER_NAME'),'收货人'),
            array(C('DB_USSW_OUTBOUND_BUYER_ADDRESS1'),'收货地址1'),
            array(C('DB_USSW_OUTBOUND_BUYER_ADDRESS2'),'收货地址2'),
            array(C('DB_USSW_OUTBOUND_BUYER_CITY'),'城市'),
            array(C('DB_USSW_OUTBOUND_BUYER_STATE'),'州'),
            array(C('DB_USSW_OUTBOUND_BUYER_COUNTRY'),'国家'),
            array(C('DB_USSW_OUTBOUND_BUYER_ZIP'),'邮编'),
            array(C('DB_USSW_OUTBOUND_ITEM_POSITION'),'货位'),
            array(C('DB_USSW_OUTBOUND_ITEM_SKU'),'产品编码'),
            array(C('DB_PRODUCT_CNAME'),'产品名称'),
            array(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'),'数量') 
            );
        $this->exportExcel($xlsName,$xlsCell,$this->getPackingList($outboundOrder));
    }

    private function getPackingList($outboundOrder){
        $i=0;
        $obo=M(C('DB_USSW_OUTBOUND'));
        $oboi=M(C('DB_USSW_OUTBOUND_ITEM'));
        $products=M(C('DB_PRODUCT'));
        foreach ($outboundOrder as $key => $value) {
            $order=$obo->where(array(C('DB_USSW_OUTBOUND_MARKET_NO')=>$value[C('DB_USSW_OUTBOUND_MARKET_NO')]))->find();
            $items=$oboi->where(array(C('DB_USSW_OUTBOUND_ITEM_OOID')=>$order[C('DB_USSW_OUTBOUND_ID')]))->select();
            $data[$i][C('DB_USSW_OUTBOUND_MARKET_NO')]=$value[C('DB_USSW_OUTBOUND_MARKET_NO')];
            $data[$i][C('DB_USSW_OUTBOUND_SELLER_ID')]=$value[C('DB_USSW_OUTBOUND_SELLER_ID')];
            $data[$i][C('DB_USSW_OUTBOUND_MARKET')]=$value[C('DB_USSW_OUTBOUND_MARKET')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_NAME')]=$order[C('DB_USSW_OUTBOUND_BUYER_NAME')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')]=$order[C('DB_USSW_OUTBOUND_BUYER_ADDRESS1')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')]=$order[C('DB_USSW_OUTBOUND_BUYER_ADDRESS2')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_CITY')]=$order[C('DB_USSW_OUTBOUND_BUYER_CITY')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_STATE')]=$order[C('DB_USSW_OUTBOUND_BUYER_STATE')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_COUNTRY')]=$order[C('DB_USSW_OUTBOUND_BUYER_COUNTRY')];
            $data[$i][C('DB_USSW_OUTBOUND_BUYER_ZIP')]=$order[C('DB_USSW_OUTBOUND_BUYER_ZIP')];
            foreach ($items as $keyItem => $valueItem) {
                $data[$i][C('DB_USSW_OUTBOUND_ITEM_POSITION')]=$valueItem[C('DB_USSW_OUTBOUND_ITEM_POSITION')];
                $data[$i][C('DB_USSW_OUTBOUND_ITEM_SKU')]=$valueItem[C('DB_USSW_OUTBOUND_ITEM_SKU')];
                $data[$i][C('DB_PRODUCT_CNAME')]=$products->where(array(C('DB_PRODUCT_SKU')=>$valueItem[C('DB_USSW_OUTBOUND_ITEM_SKU')]))->getField(C('DB_PRODUCT_CNAME'));
                $data[$i][C('DB_USSW_OUTBOUND_ITEM_QUANTITY')]=$valueItem[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                $i=$i+1;
            }
        }
        return $data;
    }

    public function packing($sid){
        $map[C('DB_USSW_OUTBOUND_ID')]=array('egt',$sid);
        $orders = M(C('DB_USSW_OUTBOUND'))->where($map)->select();
        $this->exportPackingList($orders);
    }
}

?>