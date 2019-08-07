<?php

class amazonUsStorageAction extends CommonAction{

    public function index(){
    	if($_POST['keyword']=="" && $_GET['sortword']==""){
            $Data = D("AmazonUsStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $storage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($storage as $key => $value) {
                $storage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
            }
            $this->assign('storage',$storage);
            $this->assign('page',$show);
            $this->display();
        }
        elseif($_POST['keyword']!="" && $_GET['sortword']==""){
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $storage = D('AmazonUsStorageView')->where($where)->select();
            foreach ($storage as $key => $value) {
              $storage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
            }
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('storage',$storage);
            $this->display();
        }
        elseif($_POST['keyword']=="" && $_GET['sortword']!=""){
            $sortword = I('get.sortword','','htmlspecialchars');
            $sort = I('get.sort','','htmlspecialchars');
            $Data = D("AmazonUsStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $storage = $Data->order(array($sortword=>$sort))->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($storage as $key => $value) {
              $storage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
            }
            $this->assign('selected',$sortword); 
            $this->assign('sort',$sort); 
            $this->assign('storage',$storage);
            $this->assign('page',$show);
            $this->display();
        }
    }

    private function get30DaysSales($sku){
        $map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('gt',date("Y-m-d H:i:s",(time()-60*60*24*30)));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        $result = D("UsFBAOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        if($result!=null){
            return $result;
        }else{
            return 0;
        }
    }

    public function importAmazonUsStorage(){
        $this->display();
    }

    public function importAmazonUsStorageHandle(){
        if(!empty($_FILES)){
            import('ORG.Net.UploadFile');
            $config=array(
                'allowExts'=>array('xlsx','xls'),
                'savePath'=>'./Public/upload/amazonUsStorage/',
                'saveRule'=>'amazonUsStorage'.time(),
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

            if($this->verifyAmazonUsStorageColumnName($firstRow)){
                $storage=M(C('DB_AMAZON_US_STORAGE'));
                $storage->startTrans();
                for($i=2;$i<=$highestRow;$i++){
                    $product=$storage->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue()))->find();
                    if($product!==null && $product!==false){
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                        $product[C('DB_AMAZON_US_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue()+$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue()+$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                        $storage->save($product);
                    }else{
                        $product[C('DB_WINIT_DE_STORAGE_SKU')]=$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
                        $product[C('DB_AMAZON_US_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue()+$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue()+$objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
                        $storage->add($product);
                    }
                    $product=null;
                }
                $storage->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是亚马逊美国库存模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function verifyAmazonUsStorageColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_AMAZON_US_STORAGE')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_AMAZON_US_STORAGE')[$c]){
                return false;
            }
                
        }
        return true;
    }

    public function outboundOrders(){
        if($_POST['keyword']==""){
            $Data = M(C('DB_AMAZON_US_FBA_OUTBOUND'));
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
            $this->outboundOrders = M(C('DB_AMAZON_US_FBA_OUTBOUND'))->where($where)->select();
        }
        $this->display();
    }
}

?>