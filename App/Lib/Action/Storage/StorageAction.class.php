<?php

class StorageAction extends CommonAction{

	public function usstorage(){
		if($_POST['keyword']==""){
            $Data = M(C('DB_USSTORAGE'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $this->usstorage = M(C('DB_USSTORAGE'))->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
	}

  public function szstorage(){
    if($_POST['keyword']==""){
            $Data = M(C('DB_SZSTORAGE'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('szstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $this->usstorage = M(C('DB_SZSTORAGE'))->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
  }

  private function inventoryWarning(){
      $data = M(C('DB_USSTORAGE'))->select();
      $warning = null;
      $indexOfWarning = 0;
      $product = M(C('DB_PRODUCT'));
      $user = M(C('DB_USER'));
      foreach ($data as $key => $value) {
          if($value[C('DB_USSTORAGE_AINVENTORY')]+$value[C('DB_USSTORAGE_IINVENTORY')]<=1){

            $manager = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_MANAGER'));
            $warning[$indexOfWarning][C('DB_PRODUCT_MANAGER')] = $manager;
            $warning[$indexOfWarning][C('DB_PRODUCT_SKU')] = $value[C('DB_USSTORAGE_SKU')];
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