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
                    $woo[C('DB_WINIT_OUTBOUND_SELLER_ID')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ID')]=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_NAME')]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_TEL')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_EMAIL')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ADDRESS1')]=$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ADDRESS2')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_CITY')]=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_STATE')]=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')]=$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                    $woo[C('DB_WINIT_OUTBOUND_BUYER_ZIP')]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
                    $index=$winitOutboundOrder->add($woo);
                    for($j='Q'; $j!=$highestColumn;$j++) { 
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
                            $wooi=null;
                        }else{
                            break;
                        }
                    }
                    $woo=null;
                }
                $winitOutboundOrder->commit();
                $winitOutboundOrderItem->commit();
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
        for($c='A';$c<='U';$c++){
            if(trim($firstRow[$c]) != C('IMPORT_WINIT_OUTBOUND')[$c]){
                return false;
            }       
        }
        return true;
    }
}

?>