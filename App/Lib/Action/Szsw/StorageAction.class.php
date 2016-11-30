<?php

class StorageAction extends CommonAction{

	public function index(){
      if($_POST['keyword']==""){
            $Data = M(C('DB_SZSTORAGE'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $szstorage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select();

            foreach ($szstorage as $key => $value) {
              $szstorage[$key]['cname'] = $this->getCname($value[C('DB_SZSTORAGE_SKU')]);
              $szstorage[$key]['iinventory'] = $this->getIInventory($value[C('DB_SZSTORAGE_SKU')]);
              $szstorage[$key]['oinventory'] = $this->getOInventory($value[C('DB_SZSTORAGE_SKU')]);            
            }
            $this->assign('szstorage',$szstorage);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $szstorage = M(C('DB_szstorage'))->where($where)->select();
            $products = M(C('DB_PRODUCT'));
            foreach ($szstorage as $key => $value) {
              $szstorage[$key]['cname'] = $this->getCname($value[C('DB_SZSTORAGE_SKU')]);
              $szstorage[$key]['iinventory'] = $this->getIInventory($value[C('DB_SZSTORAGE_SKU')]);
              $szstorage[$key]['oinventory'] = $this->getOInventory($value[C('DB_SZSTORAGE_SKU')]);
            }
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
        $restockData[C('DB_RESTOCK_STATUS')]='待发货';
        $result =  $restock->add($restockData);
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
      array(C('DB_SZSTORAGE_ID'),'编号'),
      array(C('DB_SZSTORAGE_POSITION'),'货位'),
      array(C('DB_SZSTORAGE_SKU'),'产品编码'),
      array(C('DB_SZSTORAGE_AINVENTORY'),'可用数量')  
      );
    $xlsData  = M(C('DB_SZSTORAGE'))->select();
    $this->exportExcel($xlsName,$xlsCell,$xlsData);
  }

  private function exportExcel($expTitle,$expCellName,$expTableData){
    $fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");

    $objPHPExcel = new PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

    //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
    for($i=0;$i<$cellNum;$i++){
      $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]); 
    } 
    // Miscellaneous glyphs, UTF-8   
    for($i=0;$i<$dataNum;$i++){
      for($j=0;$j<$cellNum;$j++){
        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
      }             
    }  

    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
    $objWriter->save('php://output'); 
    exit;   
  }
}

?>