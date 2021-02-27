<?php

class StorageAction extends CommonAction{

    public function index(){
    	if($_POST['keyword']=="" && $_GET['sortword']==""){
            $Data = D("UsstorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select();
            
            foreach ($usstorage as $key => $value) {
              $usstorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
              $usstorage[$key][C('DB_USSTORAGE_IINVENTORY')] = $this->getIInventory($value[C('DB_USSTORAGE_SKU')]);
            }

            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
            $this->display();
        }
        elseif($_POST['keyword']!="" && $_GET['sortword']==""){
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $usstorage = D('UsstorageView')->where($where)->select();
            foreach ($usstorage as $key => $value) {
              $usstorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
              $usstorage[$key][C('DB_USSTORAGE_IINVENTORY')] = $this->getIInventory($value[C('DB_USSTORAGE_SKU')]);
            }
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('usstorage',$usstorage);
            $this->display();
        }
        elseif($_POST['keyword']=="" && $_GET['sortword']!=""){
            $sortword = I('get.sortword','','htmlspecialchars');
            $sort = I('get.sort','','htmlspecialchars');
            $Data = D("UsstorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->order(array($sortword=>$sort))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('selected',$sortword); 
            $this->assign('sort',$sort); 
            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
            $this->display();
        }
    }

    Public function edit($id){
        $this->usstorage = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_ID')=>$id))->select();
        $this->display();

    }

    public function update(){
        $data[C('DB_USSTORAGE_ID')] = I('post.'.C('DB_USSTORAGE_ID'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_POSITION')] = I('post.'.C('DB_USSTORAGE_POSITION'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_SKU')] = I('post.'.C('DB_USSTORAGE_SKU'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CNAME')] = I('post.'.C('DB_USSTORAGE_CNAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ENAME')] = I('post.'.C('DB_USSTORAGE_ENAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ATTRIBUTE')] = I('post.'.C('DB_USSTORAGE_ATTRIBUTE'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.'.C('DB_USSTORAGE_CINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.'.C('DB_USSTORAGE_AINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_OINVENTORY')] = I('post.'.C('DB_USSTORAGE_OINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_IINVENTORY')] = I('post.'.C('DB_USSTORAGE_IINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CSALES')] = I('post.'.C('DB_USSTORAGE_CSALES'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_REMARK')] = I('post.'.C('DB_USSTORAGE_REMARK'),'','htmlspecialchars');
        $usstorage = M(C('DB_USSTORAGE'));
    	$result =  $usstorage->save($data);
	    if(false !== $result || 0 !== $result) {
	    	$this->success('操作成功！');}
	    else{
	        $this->error('写入错误！');
	     }
    }

    private function get30DaysSales($sku){
        $map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('gt',date("Y-m-d H:i:s",strtotime("last month")));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        $result = D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        if($result!=null){
            return $result;
        }else{
            return 0;
        }
    }

    public function exportUsStorage(){
        $xlsName  = "usstorage";
        $xlsCell  = array(
            array(C('DB_USSTORAGE_ID'),'库存编号'),
            array(C('DB_USSTORAGE_POSITION'),'货位'),
            array(C('DB_USSTORAGE_SKU'),'产品编码'),
            array(C('DB_USSTORAGE_CNAME'),'中文名称'),
            array(C('DB_USSTORAGE_ENAME'),'英文名称'),
            array(C('DB_USSTORAGE_ATTRIBUTE'),'属性'),
            array(C('DB_USSTORAGE_CINVENTORY'),'累计入库'),
            array(C('DB_USSTORAGE_AINVENTORY'),'可用库存'),
            array(C('DB_USSTORAGE_OINVENTORY'),'待出库'),
            array(C('DB_USSTORAGE_IINVENTORY'),'在途库存'),
            array(C('DB_USSTORAGE_CSALES'),'累计销量'),
            array('30dayssales','30天销量'),
            array(C('DB_PRODUCT_PRICE'),'采购价¥'),
            array(C('DB_PRODUCT_WEIGHT'),'重量g')  
            );
        $xlsData  = D("UsstorageView")->order(C('DB_USSTORAGE_SKU'))->select();
        foreach ($xlsData as $key => $value) {
            $xlsData[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }

    public function getIInventory($sku){
        $map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
        $map[C('DB_USSW_INBOUND_STATUS')] = '待入库';
        $result = D("UsswInboundView")->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
        if($result!=null){
            return $result;
        }else{
            return 0;
        }
    }

    public function moveToAmazon($quantity,$sku){
        $restock = M(C('DB_RESTOCK'));
        $data = $this->getUsswStorage($sku);
        if($data==null){
            $sku = $sku.'0';
            $data=$this->getUsswStorage($sku);
        }
        if($data==null){
            $this->error('无法转仓，货号： '.$sku.' 不在美自建仓！');
        }
        $data[C('DB_USSTORAGE_AINVENTORY')]=$data[C('DB_USSTORAGE_AINVENTORY')]-$quantity;
        $data[C('DB_USSTORAGE_CINVENTORY')]=$data[C('DB_USSTORAGE_CINVENTORY')]-$quantity;
        $usstorage = M(C('DB_USSTORAGE'));
        $result =  $usstorage->save($data);
        $result = true;
        if(false !== $result || 0 !== $result) {
            $amazonUsStorage = M(C('DB_AMAZON_US_STORAGE'))->where(array(C('DB_AMAZON_US_STORAGE_SKU')=>'FBA_'.$sku))->find();
            if($amazonUsStorage!==false && $amazonUsStorage!==null){
                $amazonUsStorage[C('DB_AMAZON_US_STORAGE_CINVENTORY')] = $amazonUsStorage[C('DB_AMAZON_US_STORAGE_CINVENTORY')]+$quantity;
                $amazonUsStorage[C('DB_AMAZON_US_STORAGE_LASTTIME')] = Date('Y-m-d H:i:s');
                $result=M(C('DB_AMAZON_US_STORAGE'))->save($amazonUsStorage);
            }else{
                $amazonUsStorage[C('DB_AMAZON_US_STORAGE_CINVENTORY')] = $quantity;
                $amazonUsStorage[C('DB_AMAZON_US_STORAGE_SKU')] = 'FBA_'.$sku;
                $amazonUsStorage[C('DB_AMAZON_US_STORAGE_LASTTIME')] = Date('Y-m-d H:i:s');
                $result=M(C('DB_AMAZON_US_STORAGE'))->add($amazonUsStorage);
            }

            if(false !== $result) {
              $this->success('操作成功！');}
            else{
                $this->error('写入错误！');
             }
        }else{
            $this->error('写入错误！');
        }
    }

    private function getUsswStorage($sku){
      $map[C('DB_USSTORAGE_SKU')]=array('eq',$sku);
      $usswstorage = M(C('DB_USSTORAGE'));
      $data = $usswstorage->where($map)->find();
      if($data==false || $data==null){
        return null;
      }
      return $data;
    }

    public function importUsswStorage(){
        $this->display();
    }

    public function importUsswStorageHandle(){
        if(!empty($_FILES)){
            import('ORG.Net.UploadFile');
            $config=array(
                'allowExts'=>array('xlsx','xls'),
                'savePath'=>'./Public/upload/usswStorage/',
                'saveRule'=>'usswStorage_'.time(),
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
            for($c='A';$c<=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }

            if($this->verifyUsswStorageColumnName($firstRow)){
                $usswStorage=M(C('DB_USSTORAGE'));
                $usswStorage->startTrans();
                for($i=2;$i<=$highestRow;$i++){                  
                  $tmp = $usswStorage->where(array(C('DB_USSTORAGE_ID')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                  $tmp[C('DB_USSTORAGE_AINVENTORY')] = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue()==null?0:$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
                  $tmp[C('DB_USSTORAGE_CINVENTORY')] = $tmp[C('DB_USSTORAGE_AINVENTORY')]+$tmp[C('DB_USSTORAGE_OINVENTORY')]+$tmp[C('DB_USSTORAGE_CSALES')];
                  $tmp[C('DB_USSTORAGE_OINVENTORY')]=0;
                  $tmp[C('DB_USSTORAGE_POSITION')] = strval($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue());
                  $usswStorage->save($tmp);
                  $tmp=null;
                }
                $usswStorage->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是美自建仓盘点库存模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function verifyUsswStorageColumnName($firstRow){
      foreach (array_keys(C('IMPORT_SSW_STORAGE')) as $key => $value) {
        if(trim($firstRow[$value]) != C('IMPORT_SSW_STORAGE')[$value]){
          return false;
        }
      }
      return true; 
    }
}

?>