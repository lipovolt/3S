<?php

class RestockAction extends CommonAction{

	public $outOfStock;
	public $indexOfOutOfStock;

	public function index(){
		$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
		$restock = M(C('DB_RESTOCK'))->where($map)->select();
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
        $map[C('DB_RESTOCK_STATUS')] = array('neq', '已发货');
        $xlsData  = $xlsModel->where($map)->select();
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
	        array('date','日期'),
	        array('sku','产品编码'),
	        array('cname','中文名称'),
	        array('price','单价'),
	        array('quantity','数量'),
	        array('warehouse','仓库'),	        
	        array('manager','产品经理'),
	        array('supplier','供货商'),	        
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

	public function importStorage($country=null){
		if($country == null){
			$this->country='美国和德国';
			$this->noteForAllCountry = '注意：第一个表单名必须是“万邑通美西库存表”，第二个表单名是“万邑通德国库存表”！！';
		}elseif($country == 'US'){
			$this->country='美国';
		}elseif($country == 'DE'){
			$this->country='德国';
		}
		$this->display();
	}

	public function findOutOfStockItem($country){
		if(IS_POST){
			$dfa = I('dfa','','htmlspecialchars');
			$dfs = I('dfs','','htmlspecialchars');
			if($country=='美国和德国'){
				$this->findAllOutOfStockItem($dfa,$dfs);
			}elseif($country=='美国'){
				$this->findUsOutOfStockItem($dfa,$dfs);
			}elseif($country=='德国'){
				$this->findDeOutOfStockItem($dfa,$dfs);
			}
		}		
	}

	public function findDeOutOfStockItem($dfa,$dfs){
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
			$sheetnames = $objPHPExcel->getSheetNames();
			$GLOBALS["outOfStock"] = null;
			$GLOBALS["indexOfOutOfStock"] = 0;

			$sheet = $objPHPExcel->getSheet(0);
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
                	
                	$status = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_tode'));
                    if($status != null && $status != '无'){
                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

		                    	if($status=='空运' && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通德国';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
		                    	if($status=='海运' && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通德国';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
                    		}
                    	}else{
	                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
	                    	if($status=='空运' && $dayAvailableForSale<$dfa && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通德国';
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfa-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
	                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
	                    	}
	                    	if($status=='海运' && $dayAvailableForSale<60 && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通德国';
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfs-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
	                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
	                    	}
	                    }	
                    }
                    
                } 
            }else{
                $this->error("模板错误，请检查模板！");
            }

			F('out',$GLOBALS["outOfStock"]);
			$this->assign('outofstock',$GLOBALS["outOfStock"]);
			$this->display('exportOutOfStock');    
        }else{
            $this->error("请选择上传的文件");
        }
	}

	public function findUsOutOfStockItem($dfa,$dfs){
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
			$sheetnames = $objPHPExcel->getSheetNames();
			$GLOBALS["outOfStock"] = null;
			$GLOBALS["indexOfOutOfStock"] = 0;
			$this->findUsswOutOfStockItem($dfa,$dfs); 

			$sheet = $objPHPExcel->getSheet(0);
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
                	
                	$status = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_tous'));
                    if($status != null && $status != '无' && !$this->hasMovedToUSSW($sheetId,$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

		                    	if($status=='空运' && $this->reallyOutOfStock('美自建仓',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '美自建仓';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
		                    	if($status=='海运' && $this->reallyOutOfStock('万邑通美西',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通美西';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
                    		}
                    	}else{
	                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
	                    	if($status=='空运' && $dayAvailableForSale<$dfa && $this->reallyOutOfStock('美自建仓',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '美自建仓';
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfa-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
	                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
	                    	}
	                    	if($status=='海运' && $dayAvailableForSale<$dfs && $this->reallyOutOfStock('万邑通美西',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通美西';
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfs-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
	                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
		                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
	                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
	                    	}
	                    }	
                    }
                    
                } 
            }else{
                $this->error("模板错误，请检查模板！");
            }

			F('out',$GLOBALS["outOfStock"]);
			$this->assign('outofstock',$GLOBALS["outOfStock"]);
			$this->display('exportOutOfStock');    
        }else{
            $this->error("请选择上传的文件");
        } 
	}

	public function findAllOutOfStockItem($dfa,$dfs){
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
			$sheetnames = $objPHPExcel->getSheetNames();
			$GLOBALS["outOfStock"] = null;
			$GLOBALS["indexOfOutOfStock"] = 0;
			$this->findUsswOutOfStockItem($dfa,$dfs); 

			for ($sheetId=0; $sheetId < 2; $sheetId++) { 
				$objPHPExcel->setActiveSheetIndex($sheetId);
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

	            if($this->verifyImportedWinitStorageTemplateColumnName($firstRow) && $this->verifyImportedWinitStorageTemplateSheetName($sheetnames)){  
	            	 
	                $products = M(C('db_product'));
	                for($i=2;$i<=$highestRow;$i++){
	                	
	                	if($sheetId==0){
	                		$status = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_tous'));
	                	}else{
	                		$status = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_tode'));
	                	}
	                    if($status != null && $status != '无' && !$this->hasMovedToUSSW($sheetId,$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
	                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

			                    	if($status=='空运' && $this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $sheetId==0?'美自建仓':'万邑通德国';
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
			                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
			                    	}
			                    	if($status=='海运' && $this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $sheetId==0?'万邑通美西':'万邑通德国';
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
			                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
			                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
			                    	}
	                    		}
	                    	}else{
		                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
		                    	$airRestockDays = $sheetId==0?15:45;
		                    	if($status=='空运' && $dayAvailableForSale<$airRestockDays && $this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $sheetId==0?'美自建仓':'万邑通德国';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($airRestockDays-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
			                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
			                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
		                    	if($status=='海运' && $dayAvailableForSale<60 && $this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $sheetId==0?'万邑通美西':'万邑通德国';
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_cname'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil((60-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('db_product_manager'));
		                    		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_PRICE'));
			                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->getField(C('DB_PRODUCT_SUPPLIER'));
			                    	$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		                    		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		                    	}
		                    }	
	                    }
	                    
	                } 
	            }else{
	                $this->error("模板错误，请检查模板！");
	            }
			}

			F('out',$GLOBALS["outOfStock"]);
			$this->assign('outofstock',$GLOBALS["outOfStock"]);
			$this->display('exportOutOfStock');    
        }else{
            $this->error("请选择上传的文件");
        } 
	}

	private function findUsswOutOfStockItem($dfa,$dfs){
		
		$usstorage = M(C('DB_USSTORAGE'))->select();
		$products = M(C('db_product'));
		foreach ($usstorage as $ussk => $ussv) {
			
			$usStatus = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_tous'));
			if($usStatus !== null && $usStatus !== '无'){
				$msq = $this->get30DaysSales($ussv[C('DB_USSTORAGE_SKU')]);
				if($usStatus == '空运'){
					$roos = $this->reallyOutOfStock('美自建仓',$ussv[C('DB_USSTORAGE_SKU')]);
				}else{
					$roos = $this->reallyOutOfStock('万邑通美西',$ussv[C('DB_USSTORAGE_SKU')]);
				}
				if($msq==0 && ($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_INVENTORY')])==0){
					if($usStatus=='空运' && $roos){
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '美自建仓';
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $ussv[C('DB_USSTORAGE_CNAME')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PRICE'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_SUPPLIER'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
						$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
					}
					if($usstorage=='海运' && $roos){
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通美西';
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $ussv[C('DB_USSTORAGE_CNAME')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = 0;
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PRICE'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_SUPPLIER'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
						$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
					}
				}

				if($msq>0){
					$dayAvailableForSale=($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_IINVENTORY')])/($msq/30);
					if($usStatus=='空运' && $dayAvailableForSale<$dfa && $roos){
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '美自建仓';
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $ussv[C('DB_USSTORAGE_CNAME')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfa-$dayAvailableForSale)*($msq/30));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PRICE'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_SUPPLIER'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
						$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
					}
					if($usStatus=='海运' && $dayAvailableForSale<$dfs && $roos){
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = '万邑通美西';
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $ussv[C('DB_USSTORAGE_SKU')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $ussv[C('DB_USSTORAGE_CNAME')];
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = ceil(($dfs-$dayAvailableForSale)*$msq/30);
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('db_product_manager'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PRICE'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $products->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_SUPPLIER'));
						$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
						$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
					}
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
        	$map[C('DB_USSW_OUTBOUND_ITEM_OOID')] = array('eq',$ov[C('DB_USSW_OUTBOUND_ID')]);
        	$map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
          	$items = $outbounditem->where($map)->select();
          	if($items !==null && $items!==false){
	          	foreach ($items as $ik => $iv) {
	            	$sku30daysales = $sku30daysales + $iv[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')];                
	            }
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

    private function verifyImportedWinitStorageTemplateSheetName($sheetnames){
        for($c=0;$c<=1;$c++){
            if($sheetnames[$c] != C('IMPORT_WINIT_STORAGE_SHEET')[$c])
                return false;
        }
        return true;
    }

    private function verifyImportedURSSTemplateColumnName($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_UPDATE_RESTOCK_SHIPPING_STATUS')));$c++){
            if($firstRow[$c] != C('IMPORT_UPDATE_RESTOCK_SHIPPING_STATUS')[$c])
                return false;
        }
        return true;
    }

    private function isInOutOfStock($warehouse,$sku){
    	foreach ($GLOBALS['outOfStock'] as $key => $value) {
    		
    		if($value['warehouse']==$warehouse && $value['sku']==$sku){
    			return true;
    		}

    	}
    	return false;
    }

    private function isInRestock($warehouse,$sku){
    	$map[C('DB_RESTOCK_SKU')] = array('eq',$sku);
    	$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq',$warehouse);
    	$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
    	$restock = M(C('DB_RESTOCK'))->where($map)->select();
    	if($restock !== false && $restock !==null){
			return true;
		}
    	return false;
    }

    private function isInPurchaseItem($warehouse,$sku){
    	$map[C('purchaseOrderStatus')] = array('in','待确认,待付款,待发货');
    	$poid = M(C('DB_PURCHASE'))->where($map)->getField(C('DB_PURCHASE_ID'),true);
    	foreach ($poid as $key => $id) {
    		$mapitem[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('eq',$warehouse);
    		$mapitem[C('DB_PURCHASE_ITEM_PURCHASE_ID')] = array('eq',$id);
    		$mapitem[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$sku);
    		$result = M(C('DB_PURCHASE_ITEM'))->where($mapitem)->select();
    		if($result !== null && $result !==false)
    			return true;
    	}
    	return false;
    }

    private function isInUSSW($sku){
    	$usstorage = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->find();
		if($usstorage!==null && $usstorage !== false){
			return true;
		}else{
			return false;
		}    	
    }

    private function hasMovedToUSSW($sheetId,$sku){
    	if($sheetId==0 && $this->isInUSSW($sku)){
    		return true;
    	}else{
    		return false;
    	}
    }

    private function getIInventory($warehouse, $sku){
    	if($warehouse != '美自建仓'){
    		return 0;
    	}else{
    		$iinventory = 0;
	        $inbound = M(C('DB_USSW_INBOUND'));
	        $inbounditems = M(C('DB_USSW_INBOUND_ITEM'));
	        $ioids = $inbounditems->where(array(C('DB_USSW_INBOUND_ITEM_SKU')=>$sku))->select();
	        
	        foreach ($ioids as $ok => $ov) {
	        	
	          	$status = $inbound->where(array(C('DB_USSW_INBOUND_ID')=>$ov[C('DB_USSW_INBOUND_ITEM_IOID')]))->getField(C('DB_USSW_INBOUND_STATUS'));
	          	if($status=='待入库'){
		            $map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		            $map[C('DB_USSW_INBOUND_ITEM_IOID')] = array('eq',$ov[C('DB_USSW_INBOUND_ITEM_IOID')]);
		            $tmpquantity = $inbounditems->where($map)->getField(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
		            $iinventory = $iinventory + intval($tmpquantity);
		        }
		        
	        } 
	        return $iinventory;
    	}        
    }

    private function reallyOutOfStock($warehouse,$sku){
    	
    	if(!$this->isInOutOfStock($warehouse,$sku)){
    		if(!$this->isInRestock($warehouse,$sku))
    			if(!$this->isInPurchaseItem($warehouse,$sku))
    				if($this->getIInventory($warehouse,$sku)==0)
    					return true;
    	}
    	return false;
    }

    public function importRestock(){
    	$this->display();
    }

    public function updateRestockShippingStatus(){
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

			$sheet = $objPHPExcel->getSheet(0);
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

            if($this->verifyImportedURSSTemplateColumnName($firstRow)){             	 
                $restock = M(C('DB_RESTOCK'));
                for($i=2;$i<=$highestRow;$i++){
                	$restockItem = $restock->where(array(C('DB_RESTOCK_ID')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                	if($restockItem == null){
                		$errorInFile[$i] = '补货表编号不存在';
                	}
                	if($restockItem[C('DB_RESTOCK_STATUS')] == '已发货'){
                		$errorInFile[$i] = '该产品已经发出，无法再次更改状态';
                	}
                	if($restockItem[C('DB_RESTOCK_WAREHOUSE')] == '美自建仓'){
                		$errorInFile[$i] = '该产品目的仓是美自建仓';
                	}
                }
                if($errorInFile != null){
                	$this->error('内容有错误，无法更新补货表发货状态');
                }else{
                	for($i=2;$i<=$highestRow;$i++){
                		$id = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                		$status = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
	                	$restock->where(array(C('DB_RESTOCK_ID')=>$id))->setField(C('DB_RESTOCK_STATUS'),$status);
	                }
	                $this->success('导入成功！');
                }
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
        }
    }
}

?>