<?php

class StorageAction extends CommonAction{

	public function index(){
      if($_POST['keyword']==""){
            $Data = D('SzStorageView');
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $szstorage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select(); 
            $this->assign('szstorage',$szstorage);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $szstorage = D('SzStorageView')->where($where)->select();
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('szstorage',$szstorage);
        }
        $this->display();
    }

    private function getIInventory($sku){
        $map[C('DB_PURCHASE_STATUS')] = array('in',array('waiting','peding'));
        $map[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$sku);
        $map[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('eq','sz');
        return D('PurchaseView')->where($map)->sum(C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY'));
    }


    private function getOInventory($sku){
        $map[C('DB_SZ_OUTBOUND_STATUS')] = array('eq','待出库');
        $map[C('DB_SZ_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        return D('SzOutboundView')->where($map)->sum(C('DB_SZ_OUTBOUND_ITEM_QUANTITY'));
    }

    private function getCname($sku){
      return M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_CNAME'));
    }

    Public function edit($id){
        $szstorage = M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_ID')=>$id))->find();
        $szstorage['iinventory'] = $this->getIInventory($szstorage[C('DB_SZSTORAGE_SKU')]);
        $szstorage['oinventory'] = $this->getOInventory($szstorage[C('DB_SZSTORAGE_SKU')]);
        $szstorage['cname'] = $this->getCname($szstorage[C('DB_SZSTORAGE_SKU')]);
        $this->assign('szstorage',$szstorage);
        $this->display();
    }

    public function update(){
      $data[C('DB_SZSTORAGE_ID')] = I('post.'.C('DB_SZSTORAGE_ID'),'','htmlspecialchars');
      $data[C('DB_SZSTORAGE_POSITION')] = I('post.'.C('DB_SZSTORAGE_POSITION'),'','htmlspecialchars');
      /*$data[C('DB_SZSTORAGE_SKU')] = I('post.'.C('DB_SZSTORAGE_SKU'),'','htmlspecialchars');*/
      $data[C('DB_SZSTORAGE_CINVENTORY')] = I('post.'.C('DB_SZSTORAGE_CINVENTORY'),'','htmlspecialchars');
      $data[C('DB_SZSTORAGE_AINVENTORY')] = I('post.'.C('DB_SZSTORAGE_AINVENTORY'),'','htmlspecialchars');
      $data[C('DB_SZSTORAGE_CSALES')] = I('post.'.C('DB_SZSTORAGE_CSALES'),'','htmlspecialchars');
      $result =  M(C('DB_SZSTORAGE'))->save($data);
      if(false !== $result || 0 !== $result) {
        $this->success('操作成功！');}
      else{
          $this->error('写入错误！');
       }
    }

    public function moveTo($warehouse,$quantity,$sku,$position){
      $restock = M(C('DB_RESTOCK'));
      $data = $this->getSzStorage($sku);
      if($data==null){
        $sku = $sku.'0';
        $data=$this->getSzStorage($sku);
      }
      if($data==null){
        $this->error('无法转仓，货号： '.$sku.' 不在深圳仓！');
      }
      $data[C('DB_SZSTORAGE_AINVENTORY')]=$data[C('DB_SZSTORAGE_AINVENTORY')]-$quantity;
      $data[C('DB_SZSTORAGE_CINVENTORY')]=$data[C('DB_SZSTORAGE_CINVENTORY')]-$quantity;
      $szstorage = M(C('DB_SZSTORAGE'));
      $result =  $szstorage->save($data);
      $result = true;
      if(false !== $result || 0 !== $result) {
        $isInRestock = $restock->where(array(C('DB_RESTOCK_SKU')=>$sku,C('DB_RESTOCK_WAREHOUSE')=>$warehouse,C('DB_RESTOCK_STATUS')=>'延迟发货'))->find();
        if($isInRestock !=null && $isInRestock!=false){
            $isInRestock[C('DB_RESTOCK_QUANTITY')] = $isInRestock[C('DB_RESTOCK_QUANTITY')]+$quantity;
            $restock->where(array(C('DB_RESTOCK_ID')=>$isInRestock[C('DB_RESTOCK_ID')]))->save($isInRestock);
        }else{
            $restockData[C('DB_RESTOCK_CREATE_DATE')]=date("Y-m-d H:i:s" ,time());
            $restockData[C('DB_RESTOCK_SKU')]=$sku;
            $restockData[C('DB_RESTOCK_QUANTITY')]=$quantity;
            $restockData[C('DB_RESTOCK_WAREHOUSE')]=$warehouse;
            $product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
            $restockData[C('DB_RESTOCK_MANAGER')]=$product[C('DB_PRODUCT_MANAGER')];
            if($warehouse=='美自建仓' || $warehouse=='万邑通美西'){
              $restockData[C('DB_RESTOCK_TRANSPORT')]=$product[C('DB_PRODUCT_TOUS')];
            }
            if($warehouse=='万邑通德国'){
              $restockData[C('DB_RESTOCK_TRANSPORT')]=$product[C('DB_PRODUCT_TODE')];
            }
            $restockData[C('DB_RESTOCK_STATUS')]='延迟发货';

            $result =  $restock->add($restockData);
        }   
        
        if(false !== $result) {
          $this->success('操作成功！');}
        else{
            $this->error('写入错误！');
         }
      }
      else{
          $this->error('写入错误！');
       }
    }

    private function getSzStorage($sku){
      $map[C('DB_SZSTORAGE_SKU')]=array('eq',$sku);
      $szstorage = M(C('DB_SZSTORAGE'));
      $data = $szstorage->where($map)->find();
      if($data==false || $data==null){
        return null;
      }
      return $data;
    }

    public function exportList(){
      $xlsName  = "SzStorage";
      $xlsCell  = array(
       array(C('DB_SZSTORAGE_ID'),'库存编号'),
          array(C('DB_SZSTORAGE_POSITION'),'货位'),
          array(C('DB_SZSTORAGE_SKU'),'产品编码'),
          array(C('DB_PRODUCT_CNAME'),'中文名称'),
          array(C('DB_SZSTORAGE_CINVENTORY'),'累计入库'),
          array(C('DB_SZSTORAGE_AINVENTORY'),'可用库存'),
          array(C('DB_SZSTORAGE_CSALES'),'累计销量'),
          array(C('DB_PRODUCT_PRICE'),'采购价¥'),
          array(C('DB_PRODUCT_WEIGHT'),'重量g')   
        );
      $xlsData  = D("SzStorageView")->order(C('DB_SZSTORAGE_SKU'))->select();
      $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function resetOinventory(){
      $szStorageTable=M(C('DB_SZSTORAGE'));
      $map[C('DB_SZSTORAGE_OINVENTORY')] = array('gt',0);
      $szst = $szStorageTable->where($map)->select();
      foreach ($szst as $key => $value) {
        $value[C('DB_SZSTORAGE_AINVENTORY')]=$value[C('DB_SZSTORAGE_AINVENTORY')]+$value[C('DB_SZSTORAGE_OINVENTORY')];
        $value[C('DB_SZSTORAGE_OINVENTORY')]=0;
        $szStorageTable->save($value);
      }
      $this->success('已重置待出库库存');
    }

    public function importSzswStorage(){
        $this->display();
    }

    public function importSzswStorageHandle(){
        if(!empty($_FILES)){
            import('ORG.Net.UploadFile');
            $config=array(
                'allowExts'=>array('xlsx','xls'),
                'savePath'=>'./Public/upload/szswStorage/',
                'saveRule'=>'szswStorage_'.time(),
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

            if($this->verifySzswStorageColumnName($firstRow)){
                $szswStorage=M(C('DB_SZSTORAGE'));
                $szswStorage->startTrans();
                for($i=2;$i<=$highestRow;$i++){                  
                  $tmp = $szswStorage->where(array(C('DB_SZSTORAGE_ID')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                  $tmp[C('DB_SZSTORAGE_AINVENTORY')] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue()==null?0:$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                  $tmp[C('DB_SZSTORAGE_CINVENTORY')] = $tmp[C('DB_SZSTORAGE_AINVENTORY')]+$tmp[C('DB_SZSTORAGE_OINVENTORY')]+$tmp[C('DB_SZSTORAGE_CSALES')];
                  $tmp[C('DB_SZSTORAGE_OINVENTORY')]=0;
                  if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()!=null){
                    $tmp[C('DB_SZSTORAGE_POSITION')] = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                  }
                  $szswStorage->save($tmp);
                  $tmp=null;
                }
                $szswStorage->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是深圳仓盘点库存模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function verifySzswStorageColumnName($firstRow){
      foreach (array_keys(C('IMPORT_SSW_STORAGE')) as $key => $value) {
        if(trim($firstRow[$value]) != C('IMPORT_SSW_STORAGE')[$value]){
          return false;
        }
      }
      return true; 
    }
}

?>