<?php

class InboundAction extends CommonAction{

	public function index(){
		$usswInOrders = M('ussw_inbound')->select();
        import('ORG.Util.Page');
        foreach ($usswInOrders as $key => $value) {
            $Data[$key]=array(
                        'id'=>$value['id'],
                        'date'=>$value['date'],
                        'way'=>$value['way'],
                        'weight'=>$value['weight'],
                        'volume'=>$value['volume'],
                        'volumeweight'=>$value['volumeweight'],
                        'status'=>$value['status'],
                        'declare-item-quantity'=>$this->getInboundOrderItemQuantity($value['id'],'declare-quantity'),
                        'confirmed-item-quantity'=>$this->getInboundOrderItemQuantity($value['id'],'confirmed-quantity'),
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

    public function getInboundOrderItemQuantity($orderID,$column){
        $quantityArray = M('ussw_inbound_items')->where('`inbound-order-id`='.$orderID)->getField($column,true);
        $quantity=0;
        foreach ($quantityArray as $key => $value) {
            $quantity = $quantity + $value;
        }
        return $quantity;
    }

	public function fileImport(){
		$this->display();
	}

    public function addInbound(){
    	$data['date'] = date('Y-m-d');
        $data['way'] = I('post.wayValue','','htmlspecialchars');
        $data['declare-package-quantity'] = I('post.pQuantityValue','','htmlspecialchars');
        $data['weight'] = I('post.weightValue','','htmlspecialchars');
        $data['volume'] = I('post.volumeValue','','htmlspecialchars');
        $data['volumeWeight'] = (I('post.volumeValue','','htmlspecialchars')*1000000/5000)>I('post.weightValue','','htmlspecialchars')? I('post.volumeValue','','htmlspecialchars')*1000000/5000:I('post.weightValue','','htmlspecialchars');
        $usswInbound = M('ussw_inbound');
        $result =  $usswInbound->add($data);
		 if($result) {
		     $this->success('操作成功！');
		 }else{
		     $this->error('写入错误！');
		 }

    }

    public function importItems($orderID){
        $this->assign('orderID',$orderID);
        $this->display();
    }

    /*创建美国自建仓入库产品明细表
    create table if not exists `3s_ussw_inbound_items` (
    `id` smallint(6) unsigned primary key not null auto_increment,
    `inbound-order-id` smallint(6),
    `sku` varchar(10),
    `declare-quantity` smallint(6),
    `confirmed-quantity` smallint(6)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    
    */


    Public function addItems($orderID){
        $status = M('ussw_inbound')->where('id='.$orderID)->getField('status');
        if($status != '已入库'){
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
                for($i=2;$i<=$highestRow;$i++)
                 {   
                     $data['inbound-order-id'] = $orderID;
                     $data['sku']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                     $data['declare-quantity']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                     $totalQuantity = $totalQuantity+$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                     M('ussw_inbound_items')->add($data);                     
                 }
                $updateInboundOrder=array(
                                    'declare-item-quantity'=>$totalQuantity,
                                    'status'=>'已入库'
                    );
                
                M('ussw_inbound')->where('id='.$orderID)->save($updateInboundOrder);
                $this->success('导入成功！');
             }else
                 {
                     $this->error("请选择上传的文件");
                 }
        }
        else{
            $this->error("该单已入库，无法上传！");
        } 
        
    }

    public function inboundOrderItems($orderID){
        $Data = M('ussw_inbound_items');
        $where=array('inbound-order-id'=>$orderID);
        import('ORG.Util.Page');
        $count = $Data->where('`inbound-order-id`='.$orderID)->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $items = $Data->limit($Page->firstRow.','.$Page->listRows)->where('`inbound-order-id`='.$orderID)->select();
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
        $isInProductTable = M('products')->where('sku='.$skuToVerify)->find();
        if($isInProductTable != null){
            return true;
        }
        else{
            return false;
        }
    }

    public function addConfirmedQuantity(){
        if($this->skuVerify(I('post.sku','','htmlspecialchars'))){
            $data['sku'] = I('post.sku','','htmlspecialchars');
            $data['confirmed-quantity'] = I('post.confirmed-quantity','','htmlspecialchars');
            $usswInboundOrder = M('ussw_inbound_items');
            $where = 'id='.I('post.id','','htmlspecialchars');
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
        $status = M('ussw_inbound')->where('id='.$ioid)->getField('status');
        if($status != "已入库"){
            $items = M('ussw_inbound_'.$ioid)->select();
            $storage = M('usstorage');
            foreach ($items as $value) {
               $a = M('usstorage')->where('sku='.$value['sku'])->getField('ainventory');
               $c = M('usstorage')->where('sku='.$value['sku'])->getField('cinventory');
               $q['sku'] = $value['sku'];
               $q['ainventory'] = $a+$value['cquantity'];
               $q['cinventory'] = $c+$value['cquantity'];
               if($this->isInStorage($value['sku'])!=0){
                    $r = M('usstorage')->where('id='.$this->isInStorage($value['sku']))->save($q);
               }
               else{

                    $r = M('usstorage')->add($q);
               }
               
            }
            if($r){
                $data['status'] = '已入库';
                M('ussw_inbound')->where('id='.$ioid)->save($data);
                $this->success('入库成功！');
            }
            else{
                $this->error('入库失败！');
            }
        }
        else{
            $this->error('该单已入库！');
        }
    }

    private function isInStorage($sku){
        $row = M('usstorage')->where('sku='.$sku)->find();
        if( $row == null){
            return 0;
        } 
        else{
            return $row['id'];
        }
    }

    public function deleteInboundOrder($orderIDToDelete){
        M()->execute('drop table'.C('DB_PREFIX').'ussw_inbound_'.$orderIDToDelete,true);
        M('ussw_inbound')->where('id='.$orderIDToDelete)->delete();
        $this->success('操作成功！');        
    }

}

?>