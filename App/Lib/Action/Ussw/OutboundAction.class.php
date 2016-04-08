<?php

/*
创建美国仓出库表
create table if not exists `3s_ussw_outbound`(
`id` smallint(6) unsigned primary key not null auto_increment,
`saleno` varchar(20) default null,
`status` varchar(10) default null,
`shippingcompany` varchar(20),
`shippingway` varchar(30) default null,
`trackingnumber` varchar(30) default null,
`time` datetime,
`platform` varchar(20) default null,
`buyername` varchar(30) default null,
`buyertel` varchar(20) default null,
`buyeremail` varchar(30) default null,
`buyeraddress1` varchar(50) default null,
`buyeraddress2` varchar(50) default null,
`buyercity` varchar(30) default null,
`buyerstate` varchar(30) default null,
`buyercountry` varchar(30) default null,
`buyerzip` varchar(20) default null
) engine=myisam default charset=utf8;

创建美国出库单产品明细表
create table if not exists `3s_ussw_outbound_items`(
`id` smallint(6) unsigned primary key not null auto_increment,
`orderid` smallint(6),
`sku` varchar(10),
`quantity` smallint(3),
`itemno` varchar(20),
`transactionno` varchar(20)
) engine=myisam default charset=utf8;
*/

class OutboundAction extends CommonAction{

	public function index(){
        if($_POST['keyword']==""){
            $Data = M('ussw_outbound');
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
            $this->outboundOrders = M('ussw_outbound')->where($where)->select();
        }
        $this->display();
	}

	public function itemOutbound(){
    	$where['sku'] = I('post.sku','','htmlspecialchars');
		$where['position'] = I('post.position','','htmlspecialchars');
		$row = M('usstorage')->where($where)->find();
		$data['csales'] = $row['csales']+1;
		$data['ainventory'] = $row['ainventory']-1;

		$result = M('usstorage')->where($where)->save($data);
		
		if($result){
			$this->success('出库成功！');
		}
		else{
			$this->error('出库失败！');
		}
    }

    public function itemBatchOutbound(){
    	$where['sku'] = I('post.sku','','htmlspecialchars');
		$where['position'] = I('post.position','','htmlspecialchars');
		$row = M('usstorage')->where($where)->find();
		$data['csales'] = $row['csales']+I('post.quantity','','htmlspecialchars');
		$data['ainventory'] = $row['ainventory']-I('post.quantity','','htmlspecialchars');

		$result = M('usstorage')->where($where)->save($data);
		
		if($result){
			$this->success('出库成功！');
		}
		else{
			$this->error("出库失败！");
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
                        $outboundOrder[$j]['saleno'] = $saleNo;
                        $outboundOrder[$j]['status'] = '待出库';
                        $outboundOrder[$j]['time']= Date('Y-m-d H:i:s');
                        $outboundOrder[$j]['platform'] = 'ebay.com';
                        $outboundOrder[$j]['buyerid'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                        $outboundOrder[$j]['buyername'] = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                        $outboundOrder[$j]['buyertel'] = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                        $outboundOrder[$j]['buyeremail'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        $outboundOrder[$j]['buyeraddress1'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
                        $outboundOrder[$j]['buyeraddress2'] = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $outboundOrder[$j]['buyercity'] = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $outboundOrder[$j]['buyerstate'] = $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $outboundOrder[$j]['buyerzip'] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                        $outboundOrder[$j]['buyercountry'] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
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
                                if(M('usstorage')->where('sku='.$departedSkuQuantityValue['sku'])->find() == null){
                                    //检查产品编码是否在usstorage中,不存在添加错误信息
                                    $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                    $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                    $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                    $indexForErrorOfFile = $indexForErrorOfFile+1;
                                }else{
                                    $outboundOrderItems[$k]['orderid']=$saleNo;
                                    $outboundOrderItems[$k]['position'] = $positions;
                                    $outboundOrderItems[$k]['sku']=$departedSkuQuantityValue['sku'];
                                    $outboundOrderItems[$k]['quantity']=$departedSkuQuantityValue['quantity'];
                                    $outboundOrderItems[$k]['itemno']=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                                    $outboundOrderItems[$k]['transactionno']=$objPHPExcel->getActiveSheet()->getCell("AG".$i)->getValue();
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
                            if(M('usstorage')->where('sku='.$departedSkuQuantityValue['sku'])->find() == null){
                                //检查产品编码是否在usstorage中,不存在添加错误信息
                                $errorInFile[$indexForErrorOfFile]['saleno'] = $saleNo;
                                $errorInFile[$indexForErrorOfFile]['sku'] = $departedSkuQuantityValue['sku'];
                                $errorInFile[$indexForErrorOfFile]['error'] = '产品编码错误或该产品编码未入美自建仓';
                                $indexForErrorOfFile = $indexForErrorOfFile+1;
                            }else{
                                $outboundOrderItems[$k]['orderid']=$saleNo;
                                $outboundOrderItems[$k]['position'] = $positions;
                                $outboundOrderItems[$k]['sku']=$departedSkuQuantityValue['sku'];
                                $outboundOrderItems[$k]['quantity']=$departedSkuQuantityValue['quantity'];
                                $outboundOrderItems[$k]['itemno']=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
                                $outboundOrderItems[$k]['transactionno']=$objPHPExcel->getActiveSheet()->getCell("AG".$i)->getValue();
                                $k=$k+1;
                            }
                        }                        
                    }

                }
                                                    
            }
            //验证可用库存数量是否大于需要的数量
            foreach ($outboundOrderItems as $key => $outbounditem) {
                $rows = M('usstorage')->where(array('sku='.$outbounditem['sku'],'ainventory!=0'))->select();
                $totalAvailableQuantity = 0;
                $totalNeedQuantity = 0;
                $positions = null;
                foreach ($rows as $key => $row) {
                    //查看该SKU总库存。收集该SKU的货位
                    $totalAvailableQuantity = $row['ainventory'] + $totalAvailableQuantity;
                    $positions = $positions==null?$row['position']:$positions.'|'.$row['position'];
                }
                
                foreach ($outboundOrderItems as $key => $value) {
                    if($value['sku'] == $outbounditem['sku'])
                        $totalNeedQuantity = $totalNeedQuantity+$value['quantity'];
                }

                if($totalAvailableQuantity < $totalNeedQuantity){
                    //总库存小于订单数量，添加错误信息
                    $errorInFile[$indexForErrorOfFile]['saleno']=$outbounditem['orderid'];
                    $errorInFile[$indexForErrorOfFile]['sku']=$outbounditem['sku'];
                    $errorInFile[$indexForErrorOfFile]['error']='库存不足';
                    $indexForErrorOfFile = $indexForErrorOfFile+1;
                }
            }
            if($errorInFile != null){
                //有错误信息，输出错误信息，不做数据库操作。
                $this->assign('errorInFile',$errorInFile);             
                $this->display('importEbayWso');
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
                            if($item['orderid']==$order['saleno']){
                                $outboundOrderItems[$key]['orderid']=$changedToSaleNo;
                            }
                        }
                    }
                }
                //更新已过滤的订单数组到ussw_outbound
                foreach ($filteredOutboundOrder as $key => $value) {
                    M('ussw_outbound')->add($value);
                }
                //更新订单产品数组到ussw_outbound_item
                foreach ($outboundOrderItems as $key => $value) {
                    $oid=M('ussw_outbound')->where('saleno='.$value['orderid'])->getField('id');
                    $value['orderid']=$oid;
                    M('ussw_outbound_items')->add($value);
                    $usstorage = M('usstorage');
                    $rows = $usstorage->where(array('sku='.$value['sku'],'ainventory!=0'))->select();
                    $difference = $value['quantity'];
                    //更新库存信息
                    foreach ($rows as $key => $row) {
                        if($row['ainventory']>=$difference){
                            $data['ainventory'] = $row['ainventory'] - $difference;
                            $data['oinventory'] = $row['oinventory'] + $difference;
                            $usstorage->where('id='.$row['id'])->save($data);
                            break;
                        }else{
                            $data['oinventory'] = $difference- $row['ainventory'];
                            $data['ainventory'] = 0;                            
                            $difference = $difference- $row['ainventory'];
                            $usstorage->where('id='.$row['id'])->save($data);
                        }

                    }

                }
                $this->success('导入成功！');
            }

        }else{
         $this->error("请选择上传的文件");
        }
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
        if(M('ussw_outbound')->where('saleno='.saleNo)->find()!=null)
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
        $this->order=M('ussw_outbound')->where('`id`='.$id)->select();
        $outboundOrderItems=M('ussw_outbound_items')->where('`orderid`='.$id)->select();
        $this->assign('outboundOrderItems',$outboundOrderItems);
        $this->display();
    }

    public function confirmOutboundOrder($id){
        if(M('ussw_outbound')->where('id='.$id)->getField('status')=='已出库'){
            $this->error('该订单已经出库，请勿重复操作！');
        }else{
            $items = M('ussw_outbound_items')->where('orderid='.$id)->select();
            $usstorage = M('usstorage');
            foreach ($items as $key => $item) {
                $positions = explode("/",$item['position']);
                $quantity = $item['quantity'];
                foreach ($positions as $key => $position) {
                    $row=$usstorage->where(array('sku'=>$item['sku'],'position'=>$position))->find();
                    if($row['oinventory']>=$quantity){
                        $data['oinventory'] = $row['oinventory']-$quantity;
                        $data['csales'] = $row['csales']+$quantity;
                        $usstorage->where('id='.$row['id'])->save($data);
                        break;
                    }else{
                        $data['oinventory'] = 0;
                        $data['csales'] = $row['csales']+$row['oinventory'];
                        $usstorage->where('id='.$row['id'])->save($data);
                        $quantity = $quantity - $row['oinventory'];
                    }                
                }
                
            }
            M('ussw_outbound')->where('id='.$id)->setField(array('status'=>'已出库'));
            $this->success('出库成功！');
        }
    }
}

?>