<?php

class RestockAction extends CommonAction{

	public $outOfStock;
	public $indexOfOutOfStock;

	public function index(){
		if($_POST['keyword']==""){
			$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
			$restock = M(C('DB_RESTOCK'))->where($map)->select();
			$this->assign('restock',$restock);
			$this->display();
		}else{
			if($_POST['keyword']=="country"){
				if($_POST['keywordValue']=="美国"){
					$where[C('DB_RESTOCK_WAREHOUSE')]=array('in', '美自建仓,万邑通美西');
				}
				elseif($_POST['keywordValue']=="德国"){
					$where[C('DB_RESTOCK_WAREHOUSE')]=array('eq', '万邑通德国');
				}
				$where[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
				$restock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
				
			}else{
				$where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
				$restock = M(C('DB_RESTOCK'))->where($where)->select();
			}
            $this->assign('restock', $restock);
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->display();
		}
		
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
	        array('purchase_link','采购链接'),	        
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
		}elseif($country == 'SZ'){
			$this->country='深圳';
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
			}elseif($country=='深圳'){
				$this->findSzOutOfStockItem($dfa,$dfs);
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
			 'savePath'=>'./Public/upload/restock/',
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
                for($i=2;$i<=$highestRow;$i++){
                	$product = M(C('db_product'))->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                    if($product[C('db_product_tode')] != null && $product[C('db_product_tode')] != '无'){
                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

		                    	if($product[C('db_product_tode')]=='空运' && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$this->addRestockOrder('万邑通德国',0,$product);
		                    	}
		                    	if($product[C('db_product_tode')]=='海运' && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$this->addRestockOrder('万邑通德国',0,$product);
		                    	}
                    		}
                    	}else{
	                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
	                    	if($product[C('db_product_tode')]=='空运' && $dayAvailableForSale<$dfa && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$this->addRestockOrder('万邑通德国',ceil(($dfa-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()),$product);
	                    	}
	                    	if($product[C('db_product_tode')]=='海运' && $dayAvailableForSale<$dfs && $this->reallyOutOfStock('万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$this->addRestockOrder('万邑通德国',ceil(($dfs-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()),$product);
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
			 'savePath'=>'./Public/upload/restock/',
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
			$this->findSzswOutOfStockItem();

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

			//excel first column name verify
            for($c='A';$c<=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
            }

            if($this->verifyImportedWinitStorageTemplateColumnName($firstRow)){  
                for($i=2;$i<=$highestRow;$i++){
                	$product = M(C('db_product'))->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();
                    $movedToUssw = $this->isInUSSW($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue());
                    if($product[C('db_product_tous')] != null && $product[C('db_product_tous')] != '无' && !$movedToUssw){
                    	
                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

		                    	if($product[C('db_product_tous')]=='空运' && $this->reallyOutOfStock('美自建仓',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$this->addRestockOrder('美自建仓',0,$product);
		                    	}
		                    	if($product[C('db_product_tous')]=='海运' && $this->reallyOutOfStock('万邑通美西',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$this->addRestockOrder('万邑通美西',0,$product);
		                    	}
                    		}
                    	}else{
	                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
	                    	if($product[C('db_product_tous')]=='空运' && $dayAvailableForSale<$dfa && $this->reallyOutOfStock('美自建仓',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$this->addRestockOrder('美自建仓',ceil(($dfa-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()),$product);
	                    	}
	                    	if($product[C('db_product_tous')]=='海运' && $dayAvailableForSale<$dfs && $this->reallyOutOfStock('万邑通美西',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    		$this->addRestockOrder('万邑通美西',ceil(($dfs-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()),$product);
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
			import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/restock/',
			 'saveRule'=>'winitStock'.'_'.time(),
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
			$this->findSzswOutOfStockItem(); 

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

				//excel first column name verify
	            for($c='A';$c<=$highestColumn;$c++){
	                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
	            }

	            if($this->verifyImportedWinitStorageTemplateColumnName($firstRow) && $this->verifyImportedWinitStorageTemplateSheetName($sheetnames)){  
	            	 
	                for($i=2;$i<=$highestRow;$i++){
	                	$product = M(C('db_product'))->where(array(C('db_product_sku')=>$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()))->find();               	
	                	if($sheetId==0){
	                		$status = $product[C('db_product_tous')];
	                	}else{
	                		$status = $product[C('db_product_tode')];
	                	}
	                    if($status != null && $status != '无' && !$this->hasMovedToUSSW($sheetId,$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
	                    	if($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue()==0){
	                    		if(($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())==0){

			                    	if($status=='空运' && $this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$w = $sheetId==0?'美自建仓':'万邑通德国';
			                    		$this->addRestockOrder($w,0,$product);
			                    	}
			                    	if($status=='海运' && $this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
			                    		$w = $sheetId==0?'万邑通美西':'万邑通德国';
			                    		$this->addRestockOrder($w,0,$product);
			                    	}
	                    		}
	                    	}else{
		                    	$dayAvailableForSale = ($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue() + $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue())/$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
		                    	if($status=='空运' && $dayAvailableForSale<$dfa && $this->reallyOutOfStock($sheetId==0?'美自建仓':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$w = $sheetId==0?'美自建仓':'万邑通德国';
		                    		$q = ceil(($dfa-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$this->addRestockOrder($w,$q,$product);
		                    	}
		                    	if($status=='海运' && $dayAvailableForSale<$dfs && $this->reallyOutOfStock($sheetId==0?'万邑通美西':'万邑通德国',$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue())){
		                    		$w = $sheetId==0?'万邑通美西':'万邑通德国';
		                    		$q = ceil(($dfs-$dayAvailableForSale)*$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		                    		$this->addRestockOrder($w,$q,$product);
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
		
		foreach ($usstorage as $ussk => $ussv) {
			$product = M(C('db_product'))->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->find();
			if($product[C('db_product_tous')] !== null && $product[C('db_product_tous')] !== '无'){
				$msq = $this->getUssw30DaysSales($ussv[C('DB_USSTORAGE_SKU')]);
				if($product[C('db_product_tous')] == '空运'){
					$roos = $this->reallyOutOfStock('美自建仓',$ussv[C('DB_USSTORAGE_SKU')]);
				}else{
					$roos = $this->reallyOutOfStock('万邑通美西',$ussv[C('DB_USSTORAGE_SKU')]);
				}
				if($msq==0 && ($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_INVENTORY')])==0){
					if($product[C('db_product_tous')]=='空运' && $roos){
						$this->addRestockOrder('美自建仓',0,$product);
					}
					if($usstorage=='海运' && $roos){
						$this->addRestockOrder('万邑通美西',0,$product);
					}
				}

				if($msq>0){
					$dayAvailableForSale=($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_IINVENTORY')])/($msq/30);
					if($product[C('db_product_tous')]=='空运' && $dayAvailableForSale<$dfa && $roos){
						$this->addRestockOrder('美自建仓',ceil(($dfa-$dayAvailableForSale)*($msq/30)),$product);
					}
					if($product[C('db_product_tous')]=='海运' && $dayAvailableForSale<$dfs && $roos){
						$this->addRestockOrder('万邑通美西',ceil(($dfs-$dayAvailableForSale)*$msq/30),$product);
					}
				}
			}
			
		}
	}

	public function findSzswOutOfStockItem(){
		$szstorage = M(C('DB_SZSTORAGE'))->select();
	
		foreach ($szstorage as $szsk => $szsv) {
			$product = M(C('db_product'))->where(array(C('db_product_sku')=>$szsv[C('DB_SZSTORAGE_SKU')]))->find();
			if($product!=null){
				$msq = $this->getSzsw30DaysSales($szsv[C('DB_SZSTORAGE_SKU')]);
				if($msq != null && $szsv[C('DB_SZSTORAGE_AINVENTORY')] > 0){
					$dayAvailableForSale=($szsv[C('DB_SZSTORAGE_AINVENTORY')]+$this->getSzIinventory($szsv[C('DB_SZSTORAGE_SKU')]))/($msq/30);
					$roos = $this->reallyOutOfStock('深圳仓',$szsv[C('DB_USSTORAGE_SKU')]);
					if($product[C('DB_PRODUCT_TOUS')]!='无' && $dayAvailableForSale<3 && $roos){				
						$this->addRestockOrder('深圳仓',ceil((3-$dayAvailableForSale)*($msq/30)),$product);
					}
				}elseif ($msq == null && $szsv[C('DB_SZSTORAGE_AINVENTORY')] == 0) {
					if($product[C('DB_PRODUCT_TOUS')]!='无'){				
						$this->addRestockOrder('深圳仓',0,$product);
					}
				}
				
			}			
		} 
	}

	public function exportSzOutOfStock(){
		$this->findSzswOutOfStockItem();
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock'); 
	}

	private function getSzIinventory($sku){
		$map['sku'] = array('eq',$sku);
		$map['status'] = array('in',array('待确认', '待付款', '待发货'));
		$map['warehouse'] = array('eq','深圳仓');
		return D("PurchaseView")->where($map)->sum('quantity');
	}

	private function addRestockOrder($warehouse,$quantity,$product){
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $warehouse;
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $product[C('DB_USSTORAGE_SKU')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $product[C('DB_USSTORAGE_CNAME')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = $quantity;
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $product[C('db_product_manager')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $product[C('DB_PRODUCT_PRICE')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $product[C('DB_PRODUCT_SUPPLIER')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['purchase_link'] = $product[C('DB_PRODUCT_PURCHASE_LINK')];
		$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
		$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
	}

	private function getUssw30DaysSales($sku){
        $map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('gt',date("Y-m-d H:i:s",strtotime("last month")));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        return D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
    }

    private function getSzsw30DaysSales($sku){
        $map[C('DB_SZ_OUTBOUND_CREATE_TIME')] = array('gt',date("Y-m-d H:i:s",strtotime("last month")));
        $map[C('DB_SZ_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        return D("SzOutboundView")->where($map)->sum(C('DB_SZ_OUTBOUND_ITEM_QUANTITY'));
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

    private function deleteDuplicate($list){
    	foreach ($list as $key => $value) {
    		
    	}
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
    	$map[C('DB_PURCHASE_STATUS')] = array('in','待确认,待付款,待发货');
    	$map[C('DB_PURCHASE_ITEM_WAREHOUSE')] = $warehouse;
    	$map[C('DB_PURCHASE_ITEM_SKU')] = $sku;
    	$result=D("PurchaseView")->where($map)->find();
    	if($result !== null && $result!==false){
    		return true;
    	}else{
    		return false;
    	}
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

    private function getIInventory($sku){
    	$iinventory = 0;
    	$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = $sku;
		$iinventory = D("UsswInboundView")->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
		return $iinventory;
  
    }

    private function reallyOutOfStock($warehouse,$sku){
    	if($warehouse=='万邑通德国' && !$this->isInRestock($warehouse,$sku) && !$this->isInPurchaseItem($warehouse,$sku)){
    		return true;
    	}elseif($warehouse=='美自建仓' && !$this->isInRestock($warehouse,$sku) && !$this->isInPurchaseItem($warehouse,$sku) && $this->getIInventory($sku)==0){
    		return true;
    	}elseif($warehouse=='万邑通美西' && !$this->isInOutOfStock($warehouse,$sku) && !$this->isInRestock($warehouse,$sku) && !$this->isInPurchaseItem($warehouse,$sku)){
    		return true;
    	}elseif($warehouse=='深圳仓' && !$this->isInPurchaseItem($warehouse,$sku)){
    		return true;
    	}else{
    		return false;
    	}   	
    }

    public function importRestock(){
    	$this->display();
    }

    public function updateRestockShippingStatus(){
    	if (!empty($_FILES)) {
			import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/restockUpdate',
			 'saveRule'=>time(),
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
                	dump($errorInFile);
                	die;
                	$this->error('内容有错误，无法更新补货表发货状态');
                }else{
                	for($i=2;$i<=$highestRow;$i++){
                		$id = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                		$tmp[C('DB_RESTOCK_STATUS')] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                		$tmp[C('DB_RESTOCK_SHIPPING_DATE')] = date("Y-m-d H:i:s" ,time());
	                	$restock->where(array(C('DB_RESTOCK_ID')=>$id))->save($tmp);
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

    public function deleteRestockOrder($id){
    	if(M(C('DB_RESTOCK'))->where(array(C('DB_RESTOCK_ID')=>$id))->delete()!=false){
    		$this->success('已删除');
    	}else{
    		$this->error('删除失败');
    	}
    }

    public function newRestockOrder(){
    	$this->display();	
    }

    public function newRestockOrderHandle(){
    	$product=M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>I(C('DB_RESTOCK_SKU'))))->find();
    	if($product==null){
    		$this->error('产品编码 '.I(C('DB_RESTOCK_SKU')).'不在产品列表');
    	}
    	$data[C('DB_RESTOCK_SKU')]=I(C('DB_RESTOCK_SKU'));
    	$data[C('DB_RESTOCK_STATUS')]='待发货';
    	$data[C('DB_RESTOCK_CREATE_DATE')]=date("Y-m-d H:i:s" ,time());
    	$data[C('DB_RESTOCK_QUANTITY')]=I(C('DB_RESTOCK_QUANTITY'));
    	$data[C('DB_RESTOCK_WAREHOUSE')]=I(C('DB_RESTOCK_WAREHOUSE'));
    	$data[C('DB_RESTOCK_MANAGER')]=$product[C('DB_PRODUCT_MANAGER')];
    	$data[C('DB_RESTOCK_TRANSPORT')]=I(C('DB_RESTOCK_WAREHOUSE'))=='万邑通德国'?$product[C('DB_PRODUCT_TODE')]:$product[C('DB_PRODUCT_TOUS')];
    	if(M(C('DB_RESTOCK'))->add($data)!=false){
    		$this->success('添加成功',U('Purchase/Restock/index'));
    	}else{
    		$this->error('添加失败');
    	}
    }

    public function exportInvoice(){
		$xlsName  = "Invoice";
        $xlsCell  = array(
	        array(C('DB_RESTOCK_ID'),'补货编号'),
	        array(C('DB_RESTOCK_SKU'),'产品编码'),
	        array(C('DB_PRODUCT_ENAME'),'英文名称'),
	        array(C('DB_PRODUCT_PRICE'),'采购价'),
	        array(C('DB_RESTOCK_QUANTITY'),'数量'),
	        array('sum','总价')
	        );
        $invoices = D("InvoiceView");
        foreach ($_POST['cb'] as $key => $value) {
        	$tmp = $invoices->where(array(C('DB_RESTOCK_ID')=>$value))->find();
        	$tmp['sum']=$tmp[C('DB_PRODUCT_PRICE')]*$tmp[C('DB_RESTOCK_QUANTITY')];
        	$xlsData[$key]=$tmp;
	    }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
    }

   public function exportWIL(){
   		$xlsName  = "WinitInbound";
        $xlsCell  = array(
	        array(C('DB_RESTOCK_ID'),'补货编号'),
	        array(C('DB_RESTOCK_SKU'),'产品编码'),
	        array(C('DB_RESTOCK_QUANTITY'),'数量')
	        );
        $map[C('DB_RESTOCK_ID')] = array('in',$_POST['cb']);
        $xlsData = M(C('DB_RESTOCK'))->where($map)->select();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
   }

    public function winitOutConfirme(){
    	if($this->allIsWinitRestockOrder($_POST['cb'])){
    		$restock = M(C('DB_RESTOCK'));
    		$restock->startTrans();
    		foreach ($_POST['cb'] as $key => $value) {
    			$restock->where(array(C('DB_RESTOCK_ID')=>$value))->setField(C('DB_RESTOCK_STATUS'),'已发货');
    		}
    		$restock->commit();
    		$this->redirect(U('Purchase/Restock/index','','',1));
    	}
    }

    private function allIsWinitRestockOrder($outboundOrders){
    	$restock=M(C('DB_RESTOCK'));
    	$restock->startTrans();
    	foreach ($outboundOrders as $key => $value) {
    		$result = $restock->where(array(C('DB_RESTOCK_ID')=>$value))->find();
    		if( $result== null || $result == false){
    			$restock->commit();
    			$this->error('补货单号： '.$value.' 查询不到！请检查。', U('Purchase/Restock/index'));
    			return false;
    		}elseif($result[C('DB_RESTOCK_WAREHOUSE')] != '万邑通德国' && $result[C('DB_RESTOCK_WAREHOUSE')] != '万邑通美西' ){
    			$restock->commit();
    			$this->error('补货单号： '.$value.' 不是发往万邑通仓库。', U('Purchase/Restock/index'));
    			return false;
    		}
    	}
    	$restock->commit();
    	return true;
    }

    public function editRestockOrder($id){
    	$this->restockOrder=M(C('DB_RESTOCK'))->where(array(C('DB_RESTOCK_ID')=>$id))->find();
    	$this->display();
    }

    public function editRestockOrderHandle(){
    	$result = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$_POST[C('DB_RESTOCK_SKU')]))->find();
    	if($_POST[C('DB_RESTOCK_SKU')]==null || $_POST[C('DB_RESTOCK_SKU')]==''){
    		$this->error('产品编码缺失');
    	}
    	if($result == false || $result==null){
    		$this->error('产品编码错误，不在产品列表');
    	}
    	if($_POST[C('DB_RESTOCK_QUANTITY')]<=0){
    		$this->error('补货数量不能小于0');
    	}
    	if($_POST[C('DB_RESTOCK_WAREHOUSE')]==null ||$_POST[C('DB_RESTOCK_WAREHOUSE')]==''){
    		$this->error('请选择目的仓库');
    	}
    	if($_POST[C('DB_RESTOCK_TRANSPORT')]==null ||$_POST[C('DB_RESTOCK_TRANSPORT')]==''){
    		$this->error('请选择运输方式');
    	}
    	if($_POST[C('DB_RESTOCK_STATUS')]==null ||$_POST[C('DB_RESTOCK_STATUS')]==''){
    		$this->error('请选择状态');
    	}
    	if($_POST[C('DB_RESTOCK_STATUS')]=='待发货'){
    		$_POST[C('DB_RESTOCK_SHIPPING_DATE')]=null;
    	}
    	if($_POST[C('DB_RESTOCK_STATUS')]=='已发货'){
    		$_POST[C('DB_RESTOCK_SHIPPING_DATE')]=date("Y-m-d H:i:s" ,time());
    	}
    	M(C('DB_RESTOCK'))->where(array(C('DB_RESTOCK_ID')=>$_POST[C('DB_RESTOCK_ID')]))->save($_POST);
    	$this->redirect(U('Purchase/Restock/index','','',1));
    }
}

?>