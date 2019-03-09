<?php

class OutboundAction extends CommonAction{

    public function index(){
        if($_POST['keyword']==""){
            $Data = M(C('DB_WINIT_OUTBOUND'));
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
            $where[I('post.keyword','','htmlspecialchars')] = I('post.keywordValue','','htmlspecialchars');
            $this->outboundOrders = M(C('DB_WINIT_OUTBOUND'))->where($where)->select();
        }
        $this->display();
    }

    public function importWinitOutboundOrder(){
        $this->display();
    }

    public function outboundOrderDetails($id){
        $this->order=M(C('DB_WINIT_OUTBOUND'))->where(array(C('DB_USSW_OUTBOUND_ID')=>$id))->select();
        $outboundOrderItems=M(C('DB_WINIT_OUTBOUND_ITEM'))->where(array(C('DB_WINIT_OUTBOUND_ITEM_OOID')=>$id))->select();
        $this->assign('outboundOrderItems',$outboundOrderItems);
        $this->display();
    }

    public function importWOOHandel(){
        if(!empty($_FILES)){
             import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xlsx','xls'),
                 'savePath'=>'./Public/upload/winitOutbound/',
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

            //get row number
            for($maxRow=$highestRow; $maxRow>1; $maxRow--) {
                if($objPHPExcel->getActiveSheet()->getCell('A'.$maxRow)->getValue()!=''){
                    $highestRow=$maxRow;
                    break;
                }
            }

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }
            if($this->verifyWinitOutboundColumnName($firstRow)){
                $winitOutboundOrder=M(C('DB_WINIT_OUTBOUND'));
                $winitOutboundOrderItem=M(C('DB_WINIT_OUTBOUND_ITEM'));
                $winitOutboundOrder->startTrans();
                $winitOutboundOrderItem->startTrans();        
                $kpiSaleRecord = M(C('DB_KPI_SALE_RECORD'));
                $kpiSaleRecord->startTrans();
                for($i=2;$i<=$highestRow;$i++){
                    $woo[C('DB_WINIT_OUTBOUND_MARKET')]=I('post.market');
                    $woo[C('DB_WINIT_OUTBOUND_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_SHIPPING_COMPANY')]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_SHIPPING_WAY')]=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_STATUS')]='已出库';
                    if(I('post.order_date')==null || I('post.order_date')==''){
                        $woo[C('DB_WINIT_OUTBOUND_CREATE_TIME')]=Date('Y-m-d H:i:s');
                    }else{
                        $woo[C('DB_WINIT_OUTBOUND_CREATE_TIME')]=Date(I('post.order_date'));
                    }
                    $woo[C('DB_WINIT_OUTBOUND_SELLER_ID')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ID')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_NAME')]=$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_TEL')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_EMAIL')]=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ADDRESS1')]=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ADDRESS2')]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_CITY')]=$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_STATE')]=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')]=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ZIP')]=$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                    $index=$winitOutboundOrder->add($woo);
                    for($j='U'; $j!=$highestColumn;$j++) { 
                        if($objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue()!=null && $objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue()!='' && $index!= false){
                            $wooi[C('DB_WINIT_OUTBOUND_ITEM_OOID')]=$index;
                            $wooi[C('DB_WINIT_OUTBOUND_ITEM_SKU')]=$objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue();
                            $j++;
                            $j++;
                            $wooi[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')]=$objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue();
                            $j++;
                            $wooi[C('DB_WINIT_OUTBOUND_ITEM_MARKET_NO')]=$objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue();
                            $j++;
                            $wooi[C('DB_WINIT_OUTBOUND_ITEM_TRANSACTION_NO')]=$objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue();
                            $winitOutboundOrderItem->add($wooi);
                            

                            //统计销售绩效考核的sku
                            $kpiMap[C('DB_KPI_SALE_SKU')] = array('eq', $wooi[C('DB_WINIT_OUTBOUND_ITEM_SKU')]);
                            if($woo[C('DB_WINIT_OUTBOUND_SHIPPING_COMPANY')]=='DE Warehouse'){
                                $kpiMap[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', C('winit_de_warehouse'));
                            }elseif($woo[C('DB_WINIT_OUTBOUND_SHIPPING_COMPANY')]=='USWC Warehouse'){
                                $kpiMap[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', C('winit_uswc_warehouse'));
                            }
                            $kpiSaleId = M(C('DB_KPI_SALE'))->where($kpiMap)->getField(C('DB_KPI_SALE_ID'));
                            $repeatOrder = M(C('DB_KPI_SALE_RECORD'))->where(array(C('DB_KPI_SALE_RECORD_TRANSACTION_NO')=>$wooi[C('DB_SZ_OUTBOUND_ITEM_TRANSACTION_NO')]))->find();
                            if($kpiSaleId!=null && $repeatOrder==null){
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSaleId;
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SKU')] = $wooi[C('DB_WINIT_OUTBOUND_ITEM_SKU')];
                                if($woo[C('DB_WINIT_OUTBOUND_SHIPPING_COMPANY')]=='DE Warehouse'){
                                    $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = C('winit_de_warehouse');
                                    $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET')] = 'ebay.de';
                                }elseif($woo[C('DB_WINIT_OUTBOUND_SHIPPING_COMPANY')]=='USWC Warehouse'){
                                    $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = C('winit_uswc_warehouse');
                                    $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET')] = 'ebay.com';
                                }
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = time();
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_QUANTITY')] = $wooi[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')];
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_SELLER_ID')] = I('post.sellerID');
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_MARKET_NO')] = $wooi[C('DB_WINIT_OUTBOUND_ITEM_MARKET_NO')];
                                $kpiSaleRecordData[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = $wooi[C('DB_WINIT_OUTBOUND_ITEM_TRANSACTION_NO')];
                                $kpiSaleRecord->add($kpiSaleRecordData);
                            }
                            $wooi=null;
                        }else{
                            break;
                        }
                    }
                    $woo=null;
                }
                $winitOutboundOrder->commit();
                $winitOutboundOrderItem->commit();
                $kpiSaleRecord->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是万邑通出库单模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function addUsswOutboundOrder($outboundOrders,$outboundOrderItems){
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
        foreach ($outboundOrderItems as $key => $value) {
            $oid=$usswOutbound->where(array(C('DB_USSW_OUTBOUND_MARKET_NO')=>$value[C('DB_USSW_OUTBOUND_ITEM_OOID')]))->getField('id');
            $value[C('DB_USSW_OUTBOUND_ITEM_OOID')]=$oid;
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
        }
        $usswOutbound->commit();
        $usswOutboundItem->commit();
        $usstorage->commit();
    }


    private function verifyWinitOutboundColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_WINIT_OUTBOUND')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_WINIT_OUTBOUND')[$c]){
                return false;
            }                
        }
        return true;
    }

    public function convertToWinitOutboundFile(){
        $this->display();
    }

    public function convertToWinitOutboundFileHandle(){
        if($_POST['sellerID']!=null){
             import('ORG.Net.UploadFile');
             $config=array(
                 'allowExts'=>array('xls'),
                 'savePath'=>'./Public/upload/winitOutbound/',
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
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'2')->getValue(); 
            }

            $winitOutOrder=array();
            if($this->verifyEWSOColumnName($firstRow)){
                for ($i=4; $i < $highestRow-2; $i++) {
                    $tmpdata=null;
                    $tmpdata['Sales Record Number'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                    $tmpdata['Shipping Service'] = $objPHPExcel->getActiveSheet()->getCell("AJ".$i)->getValue();
                    $tmpdata['User Id'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                    $tmpdata['Buyer Fullname'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                    $tmpdata['Buyer Phone Number'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                    $tmpdata['Buyer Email'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                    $tmpdata['Buyer Address 1'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                    $tmpdata['Buyer Address 2'] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                    $tmpdata['Buyer City'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                    $tmpdata['Buyer State'] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                    $tmpdata['Buyer Zip'] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                    $tmpdata['Buyer Country'] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                    $tmpdata['Custom Label'] = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    $tmpdata['Quantity'] = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                    $tmpdata['Item Number'] = $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                    $tmpdata['Transaction ID'] = $objPHPExcel->getActiveSheet()->getCell("AL".$i)->getValue();
                    $tmpdata['Sale Price'] = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
                    $tmpdata['Shipping and Handling'] = $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                    $tmpdata['Total Price'] = $objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();
                    $winitOutOrder = $this->getWinitOutOrder($winitOutOrder,$tmpdata); 
                }
                $this->preTypeExportWinitOutOrder($winitOutOrder);
            }
        }else{
            $this->error('请给出卖家账号');
        }
    }

    private function preTypeExportWinitOutOrder($winitOutOrder){
        $exportFile = array();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $expCellName = array('Seller Order NO.','Warehouse','Shipping Service','Value-added service 1','Value-added service 2','Insured amount','eBaySellerID','eBayBuyerID','Buyer Fullname','Buyer Phone Number','Buyer Email','Buyer Address 1','Buyer Address 2','Buyer City','Buyer State','Buyer Postcode','Buyer Country','House No.','Duplicate order','SKU No.','Attribute','Quantity','Item Number','Transaction ID','SKU No.','Attribute','Quantity','Item Number','Transaction ID','SKU No.','Attribute','Quantity','Item Number','Transaction ID');
        foreach ($winitOutOrder as $key => $value) {
            $tmpdata['A'] = $value['Sales Record Number'];
            $tmpdata['B'] = $value['Warehouse'];
            $tmpdata['C'] = $value['Shipping Service'];
            $tmpdata['G'] = $_POST['sellerID'];
            $tmpdata['H'] = $value['User Id'];
            $tmpdata['I'] = $value['Buyer Fullname'];
            $tmpdata['J'] = $value['Buyer Phone Number']==null?0:$value['Buyer Phone Number'];
            $tmpdata['K'] = $value['Buyer Email'];
            if(strstr($value['Shipping Service'], 'DHL')==false){
                if($this->isPackstationAddress($value['Buyer Address 1'],$value['Buyer Address 2'])){
                    $tmpdata['L'] = $value['Buyer Address 1'].' '.$value['Buyer Address 2'];                
                    $tmpdata['M'] = null;
                }else{
                    $tmpdata['L'] = $value['Buyer Address 1'];
                    $tmpdata['M'] = $value['Buyer Address 2'];
                }                
            }else{
                $tmpAddress = $this->getAddressHouseNo($value['Buyer Address 1'],$value['Buyer Address 2']);
                $tmpdata['L'] = $tmpAddress[0];
                $tmpdata['M'] = $tmpAddress[1];
                $tmpdata['R'] = $tmpAddress[2];
            }            

            if(count($this->explodeBuyerCity($value['Buyer City']))==2){
                $tmpdata['N'] = $this->explodeBuyerCity($value['Buyer City'])[0];
                if($tmpdata['M']==null || $tmpdata['M']==''){
                    $tmpdata['M'] = $this->explodeBuyerCity($value['Buyer City'])[1];
                }
            }else{
                $tmpdata['N'] = $value['Buyer City'];
            }
            
            $tmpdata['O'] = $value['Buyer State'];

            if(strlen($value['Buyer Zip'])==4 && $this->is5DigitsZipCountry($value['Buyer Country'])){
                $tmpdata['P'] =  strval(' 0'.$value['Buyer Zip']);
            }else{
                $tmpdata['P'] = $value['Buyer Zip'];
            }
            
            $tmpdata['Q'] = $value['Buyer Country'];
            if((count($expCellName)-19)<(count($value['items'])*5)){
                for($index=0;$index<(count($value['items'])*5-(count($expCellName)-19))/5;$index++){
                    array_push($expCellName,'SKU No.');
                    array_push($expCellName,'Attribute');
                    array_push($expCellName,'Quantity');
                    array_push($expCellName,'Item Number');
                    array_push($expCellName,'Transaction ID');
                }
            }
            $index=19;
            foreach ($value['items'] as $ikey => $ivalue) {                
                $tmpdata[$cellName[$index++]] = $ivalue['item_sku'];
                $index++;
                $tmpdata[$cellName[$index++]] = $ivalue['item_qty'];
                $tmpdata[$cellName[$index++]] = $this->scieNumToStr($ivalue['item_id']);
                $tmpdata[$cellName[$index++]] = $this->scieNumToStr($ivalue['item_tran_id']);
            }
            array_push($exportFile,$tmpdata);
            $tmpdata=null;
        }
        $this->exportWinitOutboundExcel("winitOutbound",$expCellName,$exportFile);
    }

    private function getAddressHouseNo($add1,$add2){
        if($this->isPackstationAddress($add1,$add2)){
            return array($add1.' '.$add2,null,0);
        }else{
            $explodedAdd1 = explode(' ', $add1);
            if(count($explodedAdd1)>1 && $this->isHouseNo($explodedAdd1[count($explodedAdd1)-1])){
                for ($i=0; $i < count($explodedAdd1)-1; $i++) { 
                    $tmpadd1 = $tmpadd1.' '.$explodedAdd1[$i];
                }
                return array($tmpadd1,$add2,$explodedAdd1[count($explodedAdd1)-1]);
            }else{
                $explodedAdd2 = explode(' ',$add2);
                if(count($explodedAdd2)>1 && $this->isHouseNo($explodedAdd2[count($explodedAdd2)-1])){
                    for ($i=0; $i < count($explodedAdd2)-1; $i++) { 
                        $tmpadd1 = $tmpadd1.' '.$explodedAdd2[$i];
                    }
                    return array($tmpadd1,$add1,$explodedAdd2[count($explodedAdd2)-1]);
                }elseif(count($explodedAdd2)==1 && $this->isHouseNo($add2)){
                    return array($add1,null,$add2);
                }else{
                    return array($add1,$add2,null);
                }
            }
        }
    }

    private function isHouseNo($str){
        $numArr = explode('-', $str);
        if(count($numArr)==1){
            if(is_numeric($str) || is_numeric(substr($str, 0,strlen($str)-1))){
                return true;
            }else{
                return false;
            }
        }else{
            if(count($numArr)==2 && $this->isHouseNo($numArr[0]) && $this->isHouseNo($numArr[1])){
                return true;
            }else{
                return false;
            }
        }        
    }

    private function isPackstationAddress($address1, $address2){
        $address = $address1.' '.$address2;
        if(strstr($address,'Packstation')==false && strstr($address,'packstation')==false){
            return false;
        }else{
            return true;
        }
    }

    private function explodeBuyerCity($city){
        $delimiters = array('/', 'OT ', 'ot ', 'Ot ','OT-');
        foreach ($delimiters as $key => $value) {
            $ot = explode($value, $city);
            if(count($ot)==2){
                $ot[1] = 'OT '.$ot[1];
                return $ot;
            }
        }
        return $city;
    }

    private function is5DigitsZipCountry($country){
        $countrys = array('Germany','France','United States','Finland');
        if(array_search($country, $countrys)===false){
            return false;
        }else{
            return true;
        }
    }

    private function exportWinitOutboundExcel($expTitle,$expCellName,$expTableData){
        $fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i]); 
        } 
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$cellName[$j]]);
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$j].($i+2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            
          }
        } 
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }

    private function scieNumToStr($num) {
        if (stripos($num, 'e') === false)
            return $num;
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0) {
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return $result;
    }

    private function getWinitOutOrder($winitOutOrder, $newRow){
        if($newRow!=null){
            if($newRow['Sales Record Number']!=null && $newRow['Sales Record Number']!='' && $newRow['User Id']!=null && $newRow['User Id']!='' && $newRow['Buyer Fullname']!=null && $newRow['Buyer Fullname']!='' && $newRow['Custom Label']!=null && $newRow['Custom Label']!='' ){
                //simple order with address and item information
                if($this->existedAddress($winitOutOrder,$newRow)==false){
                    $newRow['Warehouse'] = $this->getWarehouseByCountry($newRow['Buyer Country']);
                    $newRow['Shipping Service'] = $this->getWinitShippingService($newRow);
                    $itemArray = $this->getWinitOutItemArray($newRow);
                    $newRow['items']=array();
                    foreach ($itemArray as $iakey => $iavalue) {
                        array_push($newRow['items'],$iavalue);
                    }
                    array_push($winitOutOrder, $newRow);
                    return $winitOutOrder;
                }else{
                    $winitOutOrder[$this->existedAddress($winitOutOrder,$newRow)]['Sales Record Number'] = $newRow['Sales Record Number'];
                    $itemArray = $this->getWinitOutItemArray($newRow);
                    foreach ($itemArray as $iakey => $iavalue) {
                        array_push($winitOutOrder[$this->existedAddress($winitOutOrder,$newRow)]['items'],$iavalue);
                    }
                    $winitOutOrder[$this->existedAddress($winitOutOrder,$newRow)]['Shipping Service'] = $this->getMergedOrderWinitShippingService($winitOutOrder[$this->existedAddress($winitOutOrder,$newRow)]);
                    return $winitOutOrder;
                }
            }elseif($newRow['Sales Record Number']!=null && $newRow['Sales Record Number']!='' && $newRow['User Id']!=null && $newRow['User Id']!='' && $newRow['Buyer Fullname']!=null && $newRow['Buyer Fullname']!='' && ($newRow['Custom Label']==null || $newRow['Custom Label']=='')){
                //combi order address information row
                if($this->existedAddress($winitOutOrder,$newRow)==false){
                    $newRow['Warehouse'] = $this->getWarehouseByCountry($newRow['Buyer Country']);
                    $newRow['Shipping Service'] = $this->getWinitShippingService($newRow);
                    $newRow['items']=array();
                    array_push($winitOutOrder,$newRow);
                    return $winitOutOrder;
                }else{
                    $winitOutOrder[$this->existedAddress($winitOutOrder,$newRow)]['Sales Record Number'] = $newRow['Sales Record Number'];
                    return $winitOutOrder;
                }
            }elseif($newRow['Sales Record Number']!=null && $newRow['Sales Record Number']!='' && $newRow['User Id']!=null && $newRow['User Id']!='' && ($newRow['Buyer Fullname']==null || $newRow['Buyer Fullname']=='') && $newRow['Custom Label']!=null && $newRow['Custom Label']!=''){
                //Combi order item row
                $itemArray = $this->getWinitOutItemArray($newRow);
                foreach ($itemArray as $iakey => $iavalue) {
                    array_push($winitOutOrder[$this->getParentOrder($winitOutOrder,$newRow)]['items'],$iavalue);
                }
                return $winitOutOrder;
            }else{
                $this->error($newRow['Sales Record Number'].' 无法处理');
            }
        }
    }

    private function getWinitOutItemArray($newRow){
        $splittSku = explode('|', $newRow['Custom Label']);
        $itemArray = array();
        if(count($splittSku)==1){
            if(count(explode('*', $newRow['Custom Label']))==1){
                array_push($itemArray, array('item_sku'=>$newRow['Custom Label'],'item_att'=>$newRow['Attribute'],'item_qty'=>$newRow['Quantity'],'item_id'=>$newRow['Item Number'],'item_tran_id'=>$newRow['Transaction ID']));
            }else{
                array_push($itemArray,array('item_sku'=>explode('*', $newRow['Custom Label'])[0],'item_att'=>$newRow['Attribute'],'item_qty'=>$newRow['Quantity']*explode('*', $newRow['Custom Label'])[1],'item_id'=>$newRow['Item Number'],'item_tran_id'=>$newRow['Transaction ID']));
            }
        }else{
            foreach ($splittSku as $key => $value) {
                if(count(explode('*', $value))==1){
                    array_push($itemArray,array('item_sku'=>$value,'item_att'=>$newRow['Attribute'],'item_qty'=>$newRow['Quantity'],'item_id'=>$newRow['Item Number'],'item_tran_id'=>$newRow['Transaction ID']));
                }else{
                    array_push($itemArray,array('item_sku'=>explode('*', $value)[0],'item_att'=>$newRow['Attribute'],'item_qty'=>$newRow['Quantity']*explode('*', $value)[1],'item_id'=>$newRow['Item Number'],'item_tran_id'=>$newRow['Transaction ID']));
                }
            }            
        }
        return $itemArray;
    }

    private function getWinitShippingService($row){ 
        if($row['Buyer Country']=='United States'){
            return $this->shippingServiceMapEbayWinitUswc($row['Shipping Service']);
        }elseif($row['Buyer Country']=='Germany'){
            $accountingAction = A('Accounting/Accounting');
            $tmpPrice = null;
            $tmpPrice = $accountingAction->getCurrencyAmount($row['Total Price'])['amount'];
            if($row['Quantity']>1 && $tmpPrice>13){
                return "OSF810556|DHL - Domestic Paket (Standard 1-3 Business Days)";
            }elseif($row['Shipping Service']=='eBayPlus'){
                return $this->shippingServiceMapEbayWinitDe($this->getGermanyEbayPlusShippingService($row['Custom Label']));
            }else{
                return $this->shippingServiceMapEbayWinitDe($row['Shipping Service']);
            }
        }else{
            return "OSF810553|DE Post - International Parcel (Economy 2-8 Business Days)";
        }
    }

    private function getMergedOrderWinitShippingService($winitOutOrder){
        $winitDeSale = M(C('DB_RC_DE_SALE_PLAN'));
        $tmpPrice=0;
        foreach ($winitOutOrder['items'] as $key => $value) {
            $tmpPrice = $tmpPrice+($winitDeSale->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$value['item_sku']))->getField(C('DB_RC_DE_SALE_PLAN_PRICE')))*$value['item_qty'];           
        }
        if($tmpPrice>13){
            return $this->shippingServiceMapEbayWinitDe('DHL Paket');
        }else{
            return $this->shippingServiceMapEbayWinitDe('Deutsche Post Bücher-/Warensendung');
        }
    }

    private function shippingServiceMapEbayWinitUswc($ebayShippingService){
        switch($ebayShippingService){
            case 'UPS Surepost':
              return "OSF810613|UPS - Surepost (Economy 1-6 Business Days)-USWC";
              break;
            case 'UPS First Class Mail':
              return "OSF810790|USPS - First Class Package (Standard 3-5 Business Days)-USWC";
              break;
            default:
              return null;
              break;
        }
    }

    private function shippingServiceMapEbayWinitDe($ebayShippingService){
        switch($ebayShippingService){
            case 'Deutsche Post Bücher-/Warensendung':
              return "OSF810544|DE Post - Untracked Merchandise Shipment (Economy 2-4 Business Days)";
              break;
            case 'Deutsche Post Brief':
              return "OSF810547|DE Post - Untracked Letter (Standard 1-2 Business Days)";
              break;
            case 'DHL Paket mit Alterssichtprüfung ab 18 Jahre':
              return "OSF811057|DHL - Domestic Paket - Visual Check of Age (Standard 1-3 Business Days)";
              break;
            case 'DHL Paket':
              return "OSF810556|DHL - Domestic Paket (Standard 1-3 Business Days)";
              break;
            case 'DHL Päckchen':
              return "OSF810556|DHL - Domestic Paket (Standard 1-3 Business Days)";
              break;
            case 'DE Post Large Letter':
              return "OSF810544|DE Post - Untracked Merchandise Shipment (Economy 2-4 Business Days)";
              break;
            case 'DE Post Small Letter':
              return "OSF810547|DE Post - Untracked Letter (Standard 1-2 Business Days)";
              break;
            case 'DHL Packet Service':
              return "OSF810556|DHL - Domestic Paket (Standard 1-3 Business Days)";
              break;
            case 'DPD Small Parcels':
              return "OSF811934|DPD - Domestic Economic Parcel (Standard 1-3 Business Days)";
              break;
            case 'DPD Normal Parcels':
              return "OSF811934|DPD - Domestic Economic Parcel (Standard 1-3 Business Days)";
              break;
            case 'DPD Classic':
              return "OSF811934|DPD - Domestic Economic Parcel (Standard 1-3 Business Days)";
              break;
            default:
              return null;
              break;
        }
    }

    private function getGermanyEbayPlusShippingService($sku){
        $p = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
        $s = M(C('DB_RC_DE_SALE_PLAN'))->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$sku))->find();
        if($s[C('DB_RC_DE_SALE_PLAN_PRICE')]>10){
            return "DHL Paket";
        }else{
            $winitDeSaleAction = A('Sale/WinitDeSale');
            return $winitDeSaleAction->getWinitLocalShippingWay($s[C('DB_RC_DE_SALE_PLAN_PRICE')],$p[C('DB_PRODUCT_PWEIGHT')],$p[C('DB_PRODUCT_PLENGTH')],$p[C('DB_PRODUCT_PWIDTH')],$p[C('DB_PRODUCT_PHEIGHT')]);
        }        
    }

    private function getWarehouseByCountry($country){
        if($country=='United States'){
            return 'USWC Warehouse';
        }else{
            return 'DE Warehouse';
        }
    }

    private function existedAddress($orderArray, $order){
        if($orderArray!=null){
            foreach ($orderArray as $key => $value) {
                if($value['User Id']==$order['User Id'] && $value['Buyer Fullname']==$order['Buyer Fullname'] && $value['Buyer Address 1']==$order['Buyer Address 1'] && $value['Buyer Address 2']==$order['Buyer Address 2'] && $value['Buyer City']==$order['Buyer City'] && $value['Buyer State']==$order['Buyer State'] && $value['Buyer Zip']==$order['Buyer Zip']  && $value['Buyer Country']==$order['Buyer Country']){
                    return $key;
                }
            }
        }
        return false;
    }

    private function getParentOrder($orderArray, $order){
        if($orderArray!=null){
            foreach ($orderArray as $key => $value) {
                if($value['User Id']==$order['User Id'] && $value['Sales Record Number']==$order['Sales Record Number']){
                    return $key;
                }
            }
        }
        return false;
    }

    private function mergeInExistedOrder($existedOrder, $order){
        $index = round((count($existedOrder)-15)/4)+1;
        $existedOrder['Custom Label'.$index] = $order['Custom Label'];
        $existedOrder['Quantity'.$index] = $order['Quantity'];
        $existedOrder['Item Number'.$index] = $order['Item Number'];
        $existedOrder['Transaction ID'.$index] = $order['Transaction ID'];
        return $existedOrder;
    }

    private function verifyEWSOColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_EBAY_WAITING_SHIPPING_EN_ORDER')[$c]){
                return false;
            }                
        }
        return true;
    }
}

?>