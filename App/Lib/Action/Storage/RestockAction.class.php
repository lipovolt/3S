<?php

class RestockAction extends CommonAction{

	public $outOfStock;
	public $indexOfOutOfStock;

	public function index(){
		$restock = M(C('DB_RESTOCK'))->select();
		$this->assign('restock',$restock);
		$this->display();
	}

	public function exportRestock(){
		$xlsName  = "restock";
        $xlsCell  = array(
	        array(C('DB_RESTOCK_ID'),'补货编号'),
	        array(C('DB_RESTOCK_MANAGER'),'产品经理'),
	        array(C('DB_RESTOCK_SKU'),'产品编码'),
	        array(C('DB_RESTOCK_QUANTITY'),'数量'),
	        array(C('DB_RESTOCK_WAREHOUSE'),'仓库'),
	        array(C('DB_RESTOCK_TRANSPORT'),'运输方式'),
	        array(C('DB_RESTOCK_STATUS'),'状态'),
	        array(C('DB_RESTOCK_REMARK'),'备注')  
	        );
        $xlsModel = M(C('DB_RESTOCK'));
     	$manager = session(C('DB_3S_USER_USERNAME'));
        $xlsData  = $xlsModel->where(array(C('DB_RESTOCK_MANAGER')=>$manager))->select();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
	}
	
	public function exportExcel($expTitle,$expCellName,$expTableData){
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

	public function exportOutOfStock(){
		$outOfStock = F('out');
		$fileName = 'OutOfStock'.date('_Ymd');
        $xlsCell  = array(
	        array('warehouse','仓库'),
	        array('sku','产品编码'),
	        array('quantity','数量'),
	        array('manager','产品经理'),
	        array('date','日期') 
	        );
		$cellNum = count($xlsCell);
		$dataNum = count($outOfStock);
		vendor("PHPExcel.PHPExcel");

		$objPHPExcel = new PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

		//$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
		// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
		for($i=0;$i<$cellNum;$i++){
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $xlsCell[$i][1]); 
		} 
		// Miscellaneous glyphs, UTF-8   
		for($i=0;$i<$dataNum;$i++){
			for($j=0;$j<$cellNum;$j++){
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $outOfStock[$i][$xlsCell[$j][0]]);
			}             
		}  

		header('pragma:public');
		header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
		$objWriter->save('php://output'); 
		F('out',null);
		exit;   
	}

	public function importStorage(){
		$this->display();
	}

	public function findOutOfStockItem(){
		if (!empty($_FILES)) {
			$splitname = explode('.',$file['name']);
			$filename = $splitname[0].'_'.time();
			import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/',
			 'saveRule'=>$filename,
			);
			$upload = new UploadFile($config);
			if (!$upload->upload()) {
				$this->error($upload->getErrorMsg());
			}else {
				$info = $upload->getUploadFileInfo();                 
			}
			vendor("PHPExcel.PHPExcel");
			$file_name=$info[0]['savepath'].$info[0]['savename'];

			$objReader = PHPExcel_IOFactory::createReader('Excel5');
			$objPHPExcel = $objReader->load($file_name,$encode='utf-8');
			$outOfStock = null;
			$indexOfOutOfStock  = 0;
			$this->findUsstorageOutOfStockItem(); 
			for ($sheetId=0; $sheetId < 1; $sheetId++) { 
				$sheet = $objPHPExcel->getSheet($sheetId);
				$highestRow = $sheet->getHighestRow(); // 取得总行数
				$highestColumn = $sheet->getHighestColumn(); // 取得总列数

				for ($i=$highestRow; $i >0 ; $i--) { 
					if($sheet->getCell("A".$i) == null or $sheet->getCell("A".$i) =='')
					    $highestRow = $i;
					else{
					    $highestRow = $i;
					    break;
					}      
				}

				//excel firt column name verify
	            for($c='A';$c<=$highestColumn;$c++){
	                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
	            }

	            if($this->verifyImportedWinitStorageTemplateColumnName($firstRow)){  
	            	 
	                $products = M(C('db_product'));
	                for($i=2;$i<=$highestRow;$i++){
	                	$usStatus = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_tous'));
	                    
	                    if($usStatus != null && $usStatus != '无'){
	                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
	                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

			                    	if($usStatus=='空运' && $this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$outOfStock[$indexOfOutOfStock]['warehouse'] = $sheetId==0?'美自建仓':'万邑通德国';
			                    		$outOfStock[$indexOfOutOfStock]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
			                    		$outOfStock[$indexOfOutOfStock]['quantity'] = 0;
			                    		$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
			                    		$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
			                    		$indexOfOutOfStock = $indexOfOutOfStock+1;
			                    	}
			                    	if($usStatus=='海运' && !$this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$outOfStock[$indexOfOutOfStock]['warehouse'] = $sheetId==0?'万邑通美西':'万邑通德国';
			                    		$outOfStock[$indexOfOutOfStock]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
			                    		$outOfStock[$indexOfOutOfStock]['quantity'] = 0;
			                    		$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
			                    		$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
			                    		$indexOfOutOfStock = $indexOfOutOfStock+1;
			                    	}
	                    		}
	                    	}else{
		                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
		                    	
		                    	if($usStatus=='空运' && $dayAvailableForSale<15 && !$this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$outOfStock[$indexOfOutOfStock]['warehouse'] = $sheetId==0?'美自建仓':'万邑通德国';
		                    		$outOfStock[$indexOfOutOfStock]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$outOfStock[$indexOfOutOfStock]['quantity'] = ceil((15-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
		                    		$indexOfOutOfStock = $indexOfOutOfStock+1;
		                    	}
		                    	if($usStatus=='海运' && $dayAvailableForSale<60 && !$this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$outOfStock[$indexOfOutOfStock]['warehouse'] = $sheetId==0?'万邑通美西':'万邑通德国';
		                    		$outOfStock[$indexOfOutOfStock]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$outOfStock[$indexOfOutOfStock]['quantity'] = ceil((60-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
		                    		$indexOfOutOfStock = $indexOfOutOfStock+1;
		                    	}
		                    }	
	                    } 
	                } 
	            }else{
	                $this->error("模板错误，请检查模板！");
	            }
			}
			F('out',$outOfStock);
			$this->assign('outofstock',$outOfStock);
			$this->display('exportOutOfStock');    
        }else{
            $this->error("请选择上传的文件");
        } 
	}

	private function findUsstorageOutOfStockItem(){
		$usstorage = M(C('DB_USSTORAGE'))->select();
		$products = M(C('db_product'));
		foreach ($usstorage as $ussk => $ussv) {
			$usStatus = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_tous'));
			if($usStatus != null && $usStatus != '无'){
				if($this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')])==0 && ($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_INVENTORY')])==0){
					if($usStatus=='空运'){
						$outOfStock[$indexOfOutOfStock]['warehouse'] = '美自建仓';
						$outOfStock[$indexOfOutOfStock]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$outOfStock[$indexOfOutOfStock]['quantity'] = 0;
						$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
						$indexOfOutOfStock = $indexOfOutOfStock+1;
					}
					if($usstorage=='海运'){
						$outOfStock[$indexOfOutOfStock]['warehouse'] = '万邑通美西';
						$outOfStock[$indexOfOutOfStock]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$outOfStock[$indexOfOutOfStock]['quantity'] = 0;
						$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
						$indexOfOutOfStock = $indexOfOutOfStock+1;
					}
				}
			}
			if($this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')])>0){
				$dayAvailableForSale=($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_IINVENTORY')])/($this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')])/30);
				if($usStatus=='空运' && $dayAvailableForSale<15){
					$outOfStock[$indexOfOutOfStock]['warehouse'] = '美自建仓';
					$outOfStock[$indexOfOutOfStock]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
					$outOfStock[$indexOfOutOfStock]['quantity'] = ceil((15-$dayAvailableForSale)*$this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')]));
					$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
					$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
					$indexOfOutOfStock = $indexOfOutOfStock+1;
				}
				if($usStatus=='海运' && $dayAvailableForSale<60){
					$outOfStock[$indexOfOutOfStock]['warehouse'] = '万邑通美西';
					$outOfStock[$indexOfOutOfStock]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
					$outOfStock[$indexOfOutOfStock]['quantity'] = ceil((60-$dayAvailableForSale)*$this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')]));
					$outOfStock[$indexOfOutOfStock]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
					$outOfStock[$indexOfOutOfStock]['date'] = Date('Y-m-d');
					$indexOfOutOfStock = $indexOfOutOfStock+1;
				}
			}
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

    private function verifyImportedWinitStorageTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_WINIT_STORAGE')));$c++){
            if($firstRow[$c] != C('IMPORT_WINIT_STORAGE')[$c])
                return false;
        }
        return true;
    }

    private function isInOutOfStock($warehouse,$sku){
    	$outOfStock = F('out');
    	foreach ($outOfStock as $key => $value) {
    		if($value['warehouse']==$warehouse && $value['sku']==$sku){
    			return true;
    		}
    	}
    	return false;
    }

    private function isInRestock($warehosue,$sku){
    	$restock = M(C('DB_RESTOCK'))->select();
    	foreach ($restock as $key => $value) {
    		if($value[C('DB_RESTOCK_WAREHOUSE')]==$warehouse && $value[C('DB_RESTOCK_SKU')]==$sku && $value[C('DB_RESTOCK_STATUS')]=='待发货'){
    			return true;
    		}
    	}
    	return false;
    }

    private function isInPurchaseItem($warehosue,$sku){
    	$purchasedItem = M(C('DB_PURCHASE_ITEM'))->select();
    	foreach ($purchasedItem as $key => $value) {
    		if($value[C('DB_PURCHASE_ITEM_WAREHOUSE')]==$warehouse && $value[C('DB_PURCHASE_ITEM_SKU')]==$sku){
    			$purchaseOrderStatus = M(C('DB_PURCHASE'))->where(array(C('DB_PURCHASE_ID')=>$value[C('DB_PURCHASE_ITEM_PURCHASE_ID')]))->getField(C('DB_PURCHASE_STATUS'));
    			if($purchaseOrderStatus == '待确认' || $purchaseOrderStatus == '到付款' || $purchaseOrderStatus == '待发货')
    				return true;
    		}
    	}
    	return false;
    }

    private function reallyOutOfStock($warehouse,$sku){
    	if(!$this->isInOutOfStock($warehouse.$sku) && !$this->isInRestock($warehouse,$sku) && !$this->isInPurchaseItem($warehouse.$sku)){
    		return true;
    	}else{
    		return false;
    	}
    }
}

?>