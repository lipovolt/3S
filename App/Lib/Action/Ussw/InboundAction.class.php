<?php

class InboundAction extends CommonAction{

	public function index(){
		$Data = M('ussw_inbound');
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $inbounds = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('inbounds',$inbounds);
        $this->assign('page',$show);
		$this->display();
	}

	public function fileImport(){
		$this->display();
	}

    public function addInbound(){
    	$data['date'] = date('Y-m-d');
        $data['way'] = I('post.wayValue','','htmlspecialchars');
        $data['pQuantity'] = I('post.pQuantityValue','','htmlspecialchars');
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
                $sqlCreate = 'create table '.C('DB_PREFIX' ).'ussw_inbound_'.$orderID.' (`id` smallint(6) unsigned primary key NOT NULL AUTO_INCREMENT, `sku` varchar(10) default null, `quantity` smallint(6) default 0, `cquantity` smallint(6) default 0) ENGINE=MyISAM  DEFAULT CHARSET=utf8;';
                M()->execute($sqlCreate,true);
                $result = false;
                for($i=2;$i<=$highestRow;$i++)
                 {   
                     $data['sku']= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();  
                     $data['quantity']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                     $result = M('ussw_inbound_'.$orderID)->add($data);
                 } 
                if($result){
                      $this->success('导入成功！');
                  }
                else{
                    $this->success('导入失败！');
                }
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

    public function addConfirmedQuantity($oid,$rid){
        if($this->skuVerify(I('post.sku','','htmlspecialchars'))){
            $data['sku'] = I('post.sku','','htmlspecialchars');
            $data['cquantity'] = I('post.cQuantity','','htmlspecialchars');
            $usswInboundOrder = M('ussw_inbound_'.$oid);
            $where = 'id='.$rid;
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