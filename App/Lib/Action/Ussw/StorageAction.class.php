<?php

class StorageAction extends CommonAction{

    public function index(){
    	if($_POST['keyword']==""){
            $Data = M(C('DB_USSTORAGE'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $products = M(C('DB_PRODUCT'));
            foreach ($usstorage as $key => $value) {
              $usstorage[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
              if($value[C('DB_USSTORAGE_CNAME')]==null){
                $usstorage[$key][C('DB_USSTORAGE_CNAME')] = $products->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_CNAME'));
                $usstorage[$key][C('DB_USSTORAGE_ENAME')] = $products->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_ENAME'));
              }
            }

            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->usstorage = M(C('DB_USSTORAGE'))->where($where)->select();
        }
        $this->display();
    }

    public function sort(){
        if(IS_POST){
            $sortword = I('post.sortword','','htmlspecialchars');
            $sort = I('post.sort','','htmlspecialchars');
            $Data = M(C('DB_USSTORAGE'));
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
            $this->display('index');
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
        $timestart = date("Y-m-d H:i:s",strtotime("last month"));
        $outbound = M(C('DB_USSW_OUTBOUND'))->where(C('DB_USSW_OUTBOUND_CREATE_TIME')>=$timestart)->select();
        $outbounditem = M(C('DB_USSW_OUTBOUND_ITEM'));
        $sku30daysales = 0;
        foreach ($outbound as $ok => $ov) {
          $items = $outbounditem->where(array(C('DB_USSW_OUTBOUND_ITEM_OOID')=>$ov[C('DB_USSW_OUTBOUND_ID')]))->select();
          foreach ($items as $ik => $iv) {
            if($iv[C('DB_USSW_OUTBOUND_ITEM_SKU')] == $sku)
                $sku30daysales = $sku30daysales + $iv[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];
          }
        }
        return $sku30daysales;
    }

    public function export(){
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
            array(C('DB_USSTORAGE_REMARK'),'备注')  
            );
        $xlsModel = M(C('DB_USSTORAGE'));
        $xlsData  = $xlsModel->select();
        foreach ($xlsData as $key => $value) {
            $xlsData[$key]['30dayssales'] = $this->get30DaysSales($value[C('DB_USSTORAGE_SKU')]);
        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }
    
    public function exportExcel($expTitle,$expCellName,$expTableData){
        $fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M');

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