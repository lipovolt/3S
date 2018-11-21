<?php

class StorageAction extends CommonAction{

    public function index(){
    	if($_POST['keyword']=="" && $_GET['sortword']==""){
            $Data = D("WinitDeStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $winitdestorage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select();
            
            foreach ($winitdestorage as $key => $value) {
              $winitdestorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_WINIT_DE_STORAGE_SKU')]);
            }

            $this->assign('winitdestorage',$winitdestorage);
            $this->assign('page',$show);
            $this->display();
        }
        elseif($_POST['keyword']!="" && $_GET['sortword']==""){
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $winitdestorage = D('WinitDeStorageView')->where($where)->select();
            foreach ($winitdestorage as $key => $value) {
              $winitdestorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_WINIT_DE_STORAGE_SKU')]);
            }
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('winitdestorage',$winitdestorage);
            $this->display();
        }
        elseif($_POST['keyword']=="" && $_GET['sortword']!=""){
            $sortword = I('get.sortword','','htmlspecialchars');
            $sort = I('get.sort','','htmlspecialchars');
            $Data = D("WinitDeStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $winitdestorage = $Data->order(array($sortword=>$sort))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('selected',$sortword); 
            $this->assign('sort',$sort); 
            $this->assign('winitdestorage',$winitdestorage);
            $this->assign('page',$show);
            $this->display();
        }
    }

    public function wuIndex(){
        if($_POST['keyword']=="" && $_GET['sortword']==""){
            $Data = D("WinitUsStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $winitusstorage = $Data->order('sku asc')->limit($Page->firstRow.','.$Page->listRows)->select();
            
            foreach ($winitusstorage as $key => $value) {
              $winitusstorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_WINIT_DE_STORAGE_SKU')]);
            }

            $this->assign('winitdestorage',$winitusstorage);
            $this->assign('page',$show);
            $this->display('index');
        }
        elseif($_POST['keyword']!="" && $_GET['sortword']==""){
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $winitusstorage = D('WinitDeStorageView')->where($where)->select();
            foreach ($winitusstorage as $key => $value) {
              $winitusstorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_WINIT_DE_STORAGE_SKU')]);
            }
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('winitdestorage',$winitusstorage);
            $this->display('index');
        }
        elseif($_POST['keyword']=="" && $_GET['sortword']!=""){
            $sortword = I('get.sortword','','htmlspecialchars');
            $sort = I('get.sort','','htmlspecialchars');
            $Data = D("WinitUsStorageView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $winitusstorage = $Data->order(array($sortword=>$sort))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('selected',$sortword); 
            $this->assign('sort',$sort); 
            $this->assign('winitdestorage',$winitusstorage);
            $this->assign('page',$show);
            $this->display('index');
        }
    }

    private function get30DaysSales($sku){
        $map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array('gt',date("Y-m-d H:i:s",strtotime("last month")));
        $map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        $result = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
        if($result!=null){
            return $result;
        }else{
            return 0;
        }
    }

    public function importWinitDeStorage(){
        $this->display();
    }

    public function importWinitDeStorageHandle(){
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

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }

            if($this->verifyWinitStorageColumnName($firstRow)){
                $winitDeStorage=M(C('DB_WINIT_DE_STORAGE'));
                $winitDeStorage->startTrans();
                for($i=2;$i<=$highestRow;$i++){
                    $product=$winitDeStorage->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$this->toTextSku($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())))->find();
                    if($product!==null && $product!==false){
                        $product[C('DB_WINIT_DE_STORAGE_CINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_OINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CSALES')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                        $winitDeStorage->save($product);
                    }else{
                        $product[C('DB_WINIT_DE_STORAGE_SKU')]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_OINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CSALES')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                        $winitDeStorage->add($product);
                    }
                    $product=null;
                }
                $winitDeStorage->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是万邑通库存模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    public function importWinitUsStorageHandle(){
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

            //excel first column name verify
            for($c='A';$c!=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
            }

            if($this->verifyWinitStorageColumnName($firstRow)){
                $winitUsStorage=M(C('DB_WINIT_US_STORAGE'));
                $winitUsStorage->startTrans();
                for($i=2;$i<=$highestRow;$i++){
                    $product=$winitUsStorage->where(array(C('DB_WINIT_US_STORAGE_SKU')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                    if($product!==null && $product!==false){
                        $product[C('DB_WINIT_DE_STORAGE_CINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_OINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CSALES')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                        $winitUsStorage->save($product);
                    }else{
                        $product[C('DB_WINIT_DE_STORAGE_SKU')]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_AINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_OINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_IINVENTORY')]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
                        $product[C('DB_WINIT_DE_STORAGE_CSALES')]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
                        $winitUsStorage->add($product);
                    }
                    $product=null;
                }
                $winitUsStorage->commit();
                $this->success("导入成功");
            }else{
                $this->error("不是万邑通库存模板，请检查");
            }
        }else{
            $this->error("请选择上传的文件");
        }
    }

    private function verifyWinitStorageColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_WINIT_STORAGE')))-1;$c++){
            if(trim($firstRow[$c]) != C('IMPORT_WINIT_STORAGE')[$c]){
                return false;
            }
                
        }
        return true;
    }

    public function dtSimpleOut(){
        if(IS_POST){
            if(I('post.sku','','htmlspecialchars')!=''){
                
                $where[C('DB_WINIT_DE_STORAGE_SKU')] = I('post.sku','','htmlspecialchars');               
                $row = M(C('DB_WINIT_DE_STORAGE'))->where($where)->find();
                if(substr(I('post.sku','','htmlspecialchars'), 0,3)!='DT_'){
                    $this->error('该产品编码 '.I('post.sku','','htmlspecialchars').' 不存在。 德国退货产品编码需加前缀 DT_');
                }
                if($row!==null && $row!==false){
                    $row[C('DB_WINIT_DE_STORAGE_AINVENTORY')] = $row[C('DB_WINIT_DE_STORAGE_AINVENTORY')]-1;

                    $result = M(C('DB_WINIT_DE_STORAGE'))->save($row);
                    if(false !== $result and 0!== $result){
                        $this->success('出库成功！');
                    }
                    else{
                        $this->error('出库失败！');
                    }
                }
            }            
        }
    }

    public function dtSimpleIn(){
        if(IS_POST){
            if(I('post.sku','','htmlspecialchars')!=''){
                
                $where[C('DB_WINIT_DE_STORAGE_SKU')] = I('post.sku','','htmlspecialchars');               
               

                if(M(C('DB_PRODUCT'))->where($where)->find() == null || M(C('DB_PRODUCT'))->where($where)->find() == false){
                    $this->error('该产品编码 '.I('post.sku','','htmlspecialchars').' 不存在');
                }

                $dtSku = 'DT_'.I('post.sku','','htmlspecialchars');
                $where[C('DB_WINIT_DE_STORAGE_SKU')] = $dtSku; 
                $row = M(C('DB_WINIT_DE_STORAGE'))->where($where)->find();
                if($row!==false && $row!==null){
                    $row[C('DB_WINIT_DE_STORAGE_AINVENTORY')] = $row[C('DB_WINIT_DE_STORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');
                    $result=M(C('DB_WINIT_DE_STORAGE'))->save($row);                    
                }elseif($row==null){
                    $data[C('DB_WINIT_DE_STORAGE_SKU')] = $dtSku;
                    $data[C('DB_WINIT_DE_STORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');
                    $result=M(C('DB_WINIT_DE_STORAGE'))->add($data);
                }else{
                    $this->error('数据库查询异常');
                }

                if(false !== $result and 0!== $result){
                    $this->success('入库成功！');
                }
                else{
                    $this->error('入库失败！');
                }
            }
            
        }
    }

    public function winitDtSimpleIn(){
        $this->display();
    }

    public function winitDtSimpleOut(){
        $this->display();
    }
}

?>