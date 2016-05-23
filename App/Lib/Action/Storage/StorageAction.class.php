<?php

class StorageAction extends CommonAction{

	public function usstorage(){
		if($_POST['keyword']==""){
            $usstorage = M(C('DB_USSTORAGE'));
            import('ORG.Util.Page');
            $count = $usstorage->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $data = $usstorage->limit($Page->firstRow.','.$Page->listRows)->select();
            $sku30daysales = null;
            $timestart = date("Y-m-d H:i:s",strtotime("last month"));
            $outbound = M(C('DB_USSW_OUTBOUND'))->where(C('DB_USSW_OUTBOUND_CREATE_TIME')>=$timestart)->select();
            $outbounditem = M(C('DB_USSW_OUTBOUND_ITEM'));
            foreach ($outbound as $ok => $ov) {
              $items = $outbounditem->where(array(C('DB_USSW_OUTBOUND_ITEM_OOID')=>$ov[C('DB_USSW_OUTBOUND_ID')]))->select();
              foreach ($items as $ik => $iv) {
                if($sku30daysales == null){
                  $sku30daysales[$iv[C('DB_USSW_OUTBOUND_ITEM_SKU')]] = $iv[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                }else{
                  foreach ($sku30daysales as $sk => $sv) {
                    if($sk == $iv[C('DB_USSW_OUTBOUND_ITEM_SKU')]){
                      $sku30daysales[$sk] = $sku30daysales[$sk]+$iv[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                    }else{
                      $sku30daysales[$iv[C('DB_USSW_OUTBOUND_ITEM_SKU')]] = $iv[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
                    }
                  }
                }
              }
            }
            $newdata =null;
            foreach ($data as $dkey => $dvalue) {
              $newdata[$dkey] = $dvalue;
              foreach ($sku30daysales as $skey => $svalue) {
                if($dvalue[C('DB_USSTORAGE_SKU')] == $skey)
                  $newdata[$dkey]['30dayssales'] = $svalue;
              }
            }

            $this->assign('usstorage',$newdata);
            $this->assign('page',$show);
            
        }
        else{
            $this->usstorage = M(C('DB_USSTORAGE'))->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
	}

  private function inventoryWarning(){
      $data = M(C('DB_USSTORAGE'))->select();
      $warning = null;
      $indexOfWarning = 0;
      $product = M(C('DB_PRODUCT'));
      foreach ($data as $key => $value) {
          if($value[C('DB_USSTORAGE_AINVENTORY')]<=1){

            $manager = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_MANAGER'));
            $warning[$indexOfWarning][C('DB_PRODUCT_MANAGER')] = $manager;
            $warning[$indexOfWarning][C('DB_PRODUCT_SKU')] = $value[C('DB_USSTORAGE_SKU')];
            $warning[$indexOfWarning][C('DB_USSTORAGE_AINVENTORY')] = $value[C('DB_USSTORAGE_AINVENTORY')];
            $indexOfWarning = $indexOfWarning+1;
          }
      }
      return $warning;
  }

  public function checkAinventory(){
    $usstorage=$this->inventoryWarning();
    $this->assign('usstorage',$usstorage);
    $this->display();
  }
}

?>