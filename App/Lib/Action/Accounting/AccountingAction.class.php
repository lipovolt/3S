<?php

class AccountingAction extends CommonAction{

	public function incomeCost(){
		$this->assign('data',M(C('DB_INCOMECOST'))->select());
		$this->display();
	}

	public function searchIncomeCost(){
		if($_POST[C('DB_INCOMECOST_MONTH')]!='请选择月份' && $_POST[C('DB_INCOMECOST_MONTH')]!=null){
			$map[C('DB_INCOMECOST_MONTH')]=array('eq',$_POST[C('DB_INCOMECOST_MONTH')]);
			$this->assign(C('DB_INCOMECOST_MONTH'),$_POST[C('DB_INCOMECOST_MONTH')]);
		}
		if($_POST[C('DB_INCOMECOST_SLLERID')]!=null && $_POST[C('DB_INCOMECOST_SLLERID')]!=''){
			$map[C('DB_INCOMECOST_SLLERID')]=array('eq',$_POST[C('DB_INCOMECOST_SLLERID')]);
			$this->assign(C('DB_INCOMECOST_SLLERID'),$_POST[C('DB_INCOMECOST_SLLERID')]);
		}
		if($_POST[C('DB_INCOMECOST_SLLERIDTYPE')]!=null && $_POST[C('DB_INCOMECOST_SLLERIDTYPE')]!=''){
			$map[C('DB_INCOMECOST_SLLERIDTYPE')]=array('eq',$_POST[C('DB_INCOMECOST_SLLERIDTYPE')]);
			$this->assign(C('DB_INCOMECOST_SLLERIDTYPE'),$_POST[C('DB_INCOMECOST_SLLERIDTYPE')]);
		}
		$this->assign('data',M(C('DB_INCOMECOST'))->where($map)->select());
		$this->display('incomeCost');
	}

	public function saleFee(){
		$this->assign('saleFees',M(C('DB_SALEFEE'))->order('id desc')->select());
		$this->display();
	}

	public function searchSaleFee(){
		if($_POST[C('DB_SALEFEE_MONTH')]!='请选择月份' && $_POST[C('DB_SALEFEE_MONTH')]!=null){
			$map[C('DB_SALEFEE_MONTH')]=array('eq',$_POST[C('DB_SALEFEE_MONTH')]);
			$this->assign(C('DB_SALEFEE_MONTH'),$_POST[C('DB_SALEFEE_MONTH')]);
		}
		$this->assign('saleFees',M(C('DB_SALEFEE'))->where($map)->select());
		$this->display('saleFee');
	}

	public function editSaleFee($id=null){
		if($id!=null){
			$this->assign('saleFee',M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_ID')=>$id))->find());
		}
		$this->display();		
	}

	public function editSaleFeeHandle(){
		if($_POST[C('DB_SALEFEE_ID')]!=null){
			M(C('DB_SALEFEE'))->save($_POST);
		}else{
			M(C('DB_SALEFEE'))->add($_POST);
		}
		$this->redirect('saleFee');
	}

	public function managementFee(){
	 	$Data = M(C('DB_MANAGEMENTFEE'));
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $managementFees = $Data->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('managementFees',$managementFees);
		$this->assign('page',$show);
		$this->display();
	}

	public function searchManagementFee(){
		if($_POST[C('DB_MANAGEMENTFEE_MONTH')]!='' && $_POST[C('DB_MANAGEMENTFEE_MONTH')]!=null){
			$map[C('DB_MANAGEMENTFEE_MONTH')]=array('eq',$_POST[C('DB_MANAGEMENTFEE_MONTH')]);
			$this->assign(C('DB_MANAGEMENTFEE_MONTH'),$_POST[C('DB_MANAGEMENTFEE_MONTH')]);
		}
		$this->assign('managementFees',M(C('DB_MANAGEMENTFEE'))->where($map)->select());
		$this->display('managementFee');
	}

	public function editManagementFee($id=null){
		if($id!=null){
			$this->assign('managementFee',M(C('DB_MANAGEMENTFEE'))->where(array(C('DB_MANAGEMENTFEE_ID')=>$id))->find());
		}
		$this->assign('purpose',C('MANAGEMENT_PURPOSE'));
		$this->assign('share_type',C('SHARE_TYPE'));
		$this->display();		
	}

	public function editManagementFeeHandle(){
		if($_POST[C('DB_MANAGEMENTFEE_ID')]!=null){
			M(C('DB_MANAGEMENTFEE'))->save($_POST);
		}else{
			M(C('DB_MANAGEMENTFEE'))->add($_POST);
		}
		$this->redirect('managementFee');
	}

	public function wages(){
		$Data = M(C('DB_WAGES'));
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $wages = $Data->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$usdToRmb=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		foreach ($wages as $key => $value) {
			$wages[$key]['paidWages']=round($value[C('DB_WAGES_BASE')]+$value[C('DB_WAGES_PERFORMANCE')]*$value[C('DB_WAGES_PERCENT')]/100*$usdToRmb-$value[C('DB_WAGES_SI_PERSON')]-$value[C('DB_WAGES_BASE')]/26*$value[C('DB_WAGES_LEAVE_DAYS')],2)+$value[C('DB_WAGES_BONUS')];
			$wages[$key]['WagesCost']=round($value[C('DB_WAGES_BASE')]+$value[C('DB_WAGES_PERFORMANCE')]*$value[C('DB_WAGES_PERCENT')]/100*$usdToRmb+$value[C('DB_WAGES_SI_COMPANY')]-$value[C('DB_WAGES_BASE')]/26*$value[C('DB_WAGES_LEAVE_DAYS')],2)+$value[C('DB_WAGES_BONUS')];
		}
		$this->assign('wages',$wages);
		$this->assign('page',$show);
		$this->display();
	}

	public function searchWages(){
		if($_POST[C('DB_WAGES_MONTH')]!='' && $_POST[C('DB_WAGES_MONTH')]!=null){
			$map[C('DB_WAGES_MONTH')]=array('eq',$_POST[C('DB_WAGES_MONTH')]);
			$this->assign(C('DB_WAGES_MONTH'),$_POST[C('DB_WAGES_MONTH')]);
		}
		$this->assign('wages',M(C('DB_WAGES'))->where($map)->select());
		$this->display('wages');
	}

	public function editWages($id=null){
		if($id!=null){
			$this->assign('wages',M(C('DB_WAGES'))->where(array(C('DB_WAGES_ID')=>$id))->find());
		}
		$this->assign('names',array_keys(C('WAGES_BASE')));
		$this->display();		
	}

	public function editWagesHandle(){
		if($_POST[C('DB_WAGES_ID')]!=null){
			M(C('DB_WAGES'))->save($_POST);
		}else{
			M(C('DB_WAGES'))->add($_POST);
		}
		$this->redirect('wages');
	}

	private function setBonus($month){
		$incomeCost = M(C('DB_INCOMECOST'))->where(array(C('DB_INCOMECOST_MONTH')=>$month))->select();
		$metaData = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->find();
		$bonusBase = 600;
		$bonus = 0;
		foreach ($incomeCost as $key => $value) {
			$usdIncome = $usdIncome + $value[C('DB_INCOMECOST_USDINCOME')];
			$usdIncome = $usdIncome + $value[C('DB_INCOMECOST_EURINCOME')]*$metaData[C('DB_METADATA_EURTOUSD')];
		}
		$usdIncome = $usdIncome - 8750*count(C('WAGES_BASE'));
		if($usdIncome>0){
			$bonus = ceil($usdIncome/5000)*$bonusBase;
		}
		$quantity = count(C('WAGES_BASE'))-2;
		$bonusForEachPerson = intval($bonus/$quantity);
		$wagesTable = M(C('DB_WAGES'));
		$wagesTable->startTrans();
		foreach (C('WAGES_BASE') as $key => $value) {
			if($key != '张昱' && $key != '张旻'){
				$map[C('DB_WAGES_MONTH')] = array('eq', $month);
				$map[C('DB_WAGES_NAME')] = array('eq', $key);
				$wagesTable->where($map)->setField(C('DB_WAGES_BONUS'), $bonusForEachPerson);
			}
		}
		$wagesTable->commit();
	}

	public function importSaleRecord(){
		$this->display();
	}

	public function importSaleRecordHandle(){
		$map[C('DB_INCOMECOST_MONTH')] = array('eq',$_POST['month']);
		$map[C('DB_INCOMECOST_SLLERID')] = array('eq',$_POST['sellerID']);
		if(M(C('DB_INCOMECOST'))->where($map)->find() != null && M(C('DB_INCOMECOST'))->where($map)->find() != false ){
			$this->error($_POST[C('DB_INCOMECOST_MONTH')].' 的 '. $_POST[C('DB_INCOMECOST_SLLERID')]. ' 已经统计过了，无法重新统计！');
		}elseif(empty($_FILES)){
			$this->error("请选择上传的文件");
		}else{
			if($_POST['sellerID']=='greatgoodshop' || $_POST['sellerID']=='blackfive' || $_POST['sellerID']=='vtkg5755' || $_POST['sellerID']=='yzhan-816'){
				$this->importEbayEnSaleRecordHandle();		
			}elseif ($_POST['sellerID']=='rc-helicar') {
				$this->importEbayEnSaleRecordHandle();
			}elseif($_POST['sellerID']=='lipovolt' || $_POST['sellerID']=='shangsitech@qq.com'){
				$this->importAmazonEnSaleRecordHandle();
			}elseif($_POST['sellerID']=='g-lipovolt'){
				$this->importGrouponSaleRecordHandle();				
			}elseif($_POST['sellerID']=='zuck'){
				$this->importWishSaleRecordHandle();
			}else{
				$this->error('账号无法识别');
			}
		}
	}

	private function importGrouponSaleRecordHandle(){
		import('ORG.Net.UploadFile');
        $config=array(
            'allowExts'=>array('xlsx','xls'),
            'savePath'=>'./Public/upload/accounting/',
            'saveRule'=>I('post.sellerID').'_'.I('post.month').'_'.time(),
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
        //excel first column name verify
        for($c='A';$c!=$highestColumn;$c++){
            $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
        }
        if($this->verifyGroupOnOrderAnalyzeColumnName($firstRow)){
        	$usdIncome = 0;
        	$usdMarketFee = 0;
        	$usdTaxCollection = 0;
        	$usdRefund = 0;
        	$usdCost = 0;
        	$usdPmpa = null;
        	$productTable=M(C('DB_PRODUCT'));
        	$kpiSaleRecordTable=M(C('DB_KPI_SALE_RECORD'));
        	$kpiSaleRecordTable->startTrans();
        	for($i=2;$i<=$highestRow;$i++){
        		$sku = $objPHPExcel->getActiveSheet()->getCell("AE".$i)->getValue();
        		$pPrice=$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'));

        		if($pPrice==false || $pPrice==null){
        			$this->error($i.' 行的'.$sku.'无法找到采购价。');
        		}
        		$fDate = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
        		$cDate = $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
        		$rDate = $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
        		$quantity = $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
        		$salePrice = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue()/100;
        		$shippingPrice = $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue()/100;
        		$tax = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue()/100;
        		$marketFee = $objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue()/100;
        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_MANAGER'));
        		if($pmanager==null){
        			$pmanager='Yellow River';
        		}
        		if($fDate!='' && $cDate=='' && $rDate==''){
        			$usdIncome=$usdIncome+$salePrice+$shippingPrice;
        			$usdCost=$usdCost+$pPrice*$quantity;
        			$usdTaxCollection=$usdTaxCollection+$tax;
        			$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$salePrice+$shippingPrice;
        			$usdMarketFee = $usdMarketFee+$marketFee;
        		}elseif($fDate!=''&&$rDate==''){
        			$usdRefund=$usdRefund+$salePrice+$shippingPrice;
        			$usdMarketFee = $usdMarketFee+$marketFee;
        		}

        		//添加价格到销售绩效表
        		$kpiSaleMap[C('DB_KPI_SALE_RECORD_SKU')] = array('eq', $sku);
        		$kpiSaleMap[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = array('eq', C('USSW'));
        		$kpiSaleMap[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = array('eq', $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue());
        		$kpiSaleRecord = $kpiSaleRecordTable->where($kpiSaleMap)->find();
        		if($kpiSaleRecord!=null && $kpiSaleRecord!=false){
        			$kpiSaleRecord[C('DB_KPI_SALE_RECORD_PRICE')] = $salePrice;
        			$kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')] = $shippingPrice;
        			$kpiSaleRecordTable->save($kpiSaleRecord);
        		}
        	}
        	$kpiSaleRecordTable->commit();
        	$data[C('DB_INCOMECOST_MONTH')] = $_POST['month'];
        	$data[C('DB_INCOMECOST_SLLERID')] = $_POST['sellerID'];
        	$data[C('DB_INCOMECOST_SLLERIDTYPE')] = $this->getSellerIDType($_POST['sellerID']);
        	$data[C('DB_INCOMECOST_USDINCOME')] = $usdIncome;
        	$data[C('DB_INCOMECOST_USDITEMCOST')] = $usdCost;
        	$data[C('DB_INCOMECOST_USDRETURN')] = $usdRefund;
        	$data[C('DB_INCOMECOST_MARKETFEE')] = $usdMarketFee;
        	$data[C('DB_INCOMECOST_TAX_COLLECTION')] = $usdTaxCollection;
        	M(C('DB_INCOMECOST'))->add($data);
        	$wagesTable = M(C('DB_WAGES'));
        	$wagesTable->startTrans();
        	foreach ($usdPmpa as $key => $value) {
        		$usdPmpa[$key] = $value;
        		$wmap[C('DB_WAGES_MONTH')] = array('eq', $_POST['month']);
	        	$wmap[C('DB_WAGES_NAME')] = array('eq', C('PRODUCT_MANAGER_NAME')[$key]);
	        	$result = $wagesTable->where($wmap)->find();
	        	if($result !== null && $result !== false){
	        		$result[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key] + $result[C('DB_WAGES_PERFORMANCE')];
	        		$result[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key,$result[C('DB_WAGES_PERFORMANCE')] );
	        		$wagesTable->save($result);
	        	}else{
	        		$newWages[C('DB_WAGES_NAME')] = C('PRODUCT_MANAGER_NAME')[$key];
	        		$newWages[C('DB_WAGES_MONTH')] = $_POST['month'];
	        		$newWages[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key];
	        		$newWages[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key,$newWages[C('DB_WAGES_PERFORMANCE')]);
	        		$newWages[C('DB_WAGES_BASE')] = C('WAGES_BASE')[$newWages[C('DB_WAGES_NAME')]];
	        		$wagesTable->add($newWages);
	        	}
        	}
        	$wagesTable->commit();
			$this->setBonus($_POST['month']);
        	$this->redirect('incomeCost');
        }else{
        	$this->error("模板不正确，请检查");
        }
	}

	private function importEbayEnSaleRecordHandle(){
		import('ORG.Net.UploadFile');
        $config=array(
            'allowExts'=>array('xlsx','xls'),
            'savePath'=>'./Public/upload/accounting/',
            'saveRule'=>I('post.sellerID').'_'.I('post.month').'_'.time(),
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
        //excel first column name verify
        for($c='A';$c!=$highestColumn;$c++){
            $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
        }
        if($this->verifyEbayEnSaleRecordColumnName($firstRow)){
        	$usdIncome = 0;
        	$usdPaypalFee = 0;
        	$usdEbayFee = 0;
        	$usdTaxCollection = 0;
        	$usdRefund = 0;
        	$usdCost = 0;
        	$usdPmpa = null;
        	$eurIncome = 0;
        	$eurPaypalFee = 0;
        	$eurEbayFee = 0;
        	$eurTaxCollection = 0;
        	$eurRefund = 0;
        	$eurCost = 0;
        	$eurPmpa = null;
        	$pfa = $this->getPlatformFeeArray($_POST['sellerID']);
        	$productTable=M(C('DB_PRODUCT'));
        	$kpiSaleRecordTable=M(C('DB_KPI_SALE_RECORD'));
        	$kpiSaleRecordTable->startTrans();
        	for($i=2;$i<=$highestRow;$i++){
        		$sku = $objPHPExcel->getActiveSheet()->getCell("AG".$i)->getValue();
        		$quantity = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
        		$salePriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue());
        		$shippingPriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue());
        		$taxCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue());
    			$totalCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue());
        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_MANAGER'));
        		if($pmanager==null){
        			$pmanager='Yellow River';
        		}

        		//检测该sku是否在销售绩效考核表中。如果是，要补充价格到绩效考核表
        		if($sku!=null){
        			$kpiSaleMap[C('DB_KPI_SALE_RECORD_SKU')] = array('eq', $sku);
        			$kpiSaleMap[C('DB_KPI_SALE_RECORD_WAREHOUSE')] = array('eq', $this->getWarehouse(I('post.sellerID'),$salePriceCM['currency']));
        			$kpiSaleMap[C('DB_KPI_SALE_RECORD_TRANSACTION_NO')] = array('eq', $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue());

        			$kpiSaleRecord = $kpiSaleRecordTable->where($kpiSaleMap)->find();
        			if($kpiSaleRecord!=null && $kpiSaleRecord!=false){
        				$kpiSaleRecord[C('DB_KPI_SALE_RECORD_PRICE')] = $salePriceCM['amount'];
        				$kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')] = $shippingPriceCM['amount'];
        				$kpiSaleRecordTable->save($kpiSaleRecord);
        			}
        		}

        		//如果sku和总价都不为空，可以用总价确认收入，平台费用，产品经理绩效。用sku查找采购成本。通过运算计算是否有退款。
        		if($sku != null && $totalCM['amount']!=null){
	        		if($totalCM['currency']=='usd'){
	        			$usdIncome = $usdIncome+$totalCM['amount'];
	        			$usdTaxCollection = $usdTaxCollection+$taxCM['amount'];
	        			if($totalCM['amount']<12){
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$usdEbayFee = $usdEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        			$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$totalCM['amount'];
	        			$usdCost = $usdCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;

	        		}
	        		if($totalCM['currency']=='eur'){
	        			$eurIncome = $eurIncome+$totalCM['amount'];
	        			$eurTaxCollection = $eurTaxCollection+$taxCM['amount'];
	        			if($totalCM['amount']<12){
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$eurEbayFee = $eurEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        			$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$totalCM['amount'];
	        			$eurCost = $eurCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;
	        		}
	        		$difference=($quantity*$salePriceCM['amount']+$shippingPriceCM['amount']+$taxCM['amount'])-$totalCM['amount'];
	        		if($difference>0){
	        			if($totalCM['currency']=='usd'){
	        				$usdRefund=$usdRefund+$difference;
	        			}
	        			if($totalCM['currency']=='eur'){
	        				$eurRefund=$eurRefund+$difference;
	        			}
	        		}
        		}
        		

        		//如果sku为空,总价不为空
        		if($sku == null && $totalCM['amount']!=null){
        			if($totalCM['currency']=='usd'){
	        			$usdIncome = $usdIncome+$totalCM['amount'];
	        			$usdTaxCollection = $usdTaxCollection+$taxCM['amount'];
	        			if($totalCM['amount']<12){
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$usdEbayFee = $usdEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        		}
	        		if($totalCM['currency']=='eur'){
	        			$eurIncome = $eurIncome+$totalCM['amount'];
	        			$eurTaxCollection = $eurTaxCollection+$taxCM['amount'];
	        			if($totalCM['amount']<12){
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$eurEbayFee = $eurEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        		}

	        		$difference = $salePriceCM['amount']+$shippingPriceCM['amount']+$taxCM['amount']-$totalCM['amount'];
	        		if($difference>0){
	        			if($totalCM['currency']=='usd'){
	        				$usdRefund=$usdRefund+$difference;
	        			}
	        			if($totalCM['currency']=='eur'){
	        				$eurRefund=$eurRefund+$difference;
	        			}
	        		}

	        		for ($j=$i+1; $j<=$highestRow; $j++) { 
	        			if($objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue() == $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()){
	        				$itemSku = $objPHPExcel->getActiveSheet()->getCell("AG".$j)->getValue();
			        		$itemQuantity = $objPHPExcel->getActiveSheet()->getCell("P".$j)->getValue();
			        		$itemSalePriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("Q".$j)->getValue());
			        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_MANAGER'));
			        		if($pmanager==null){
			        			$pmanager='Yellow River';
			        		}
			        		if($totalCM['currency']=='usd'){
		        				$usdCost=$usdCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_PRICE'))*$itemQuantity;
		        				if($difference==0){
		        					$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$itemQuantity*$itemSalePriceCM['amount'];
		        				}
		        			}
		        			if($totalCM['currency']=='eur'){
		        				$eurCost=$eurCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_PRICE'))*$itemQuantity;
		        				if($difference==0){
		        					$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$itemQuantity*$itemSalePriceCM['amount'];
		        				}
		        			}
	        			}
	        		}
        		}
        	}
        	$kpiSaleRecordTable->commit();
        	$eurToUsd=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTOUSD'));
        	$usdEbayFee = $usdEbayFee+$eurEbayFee*$eurToUsd;
        	$usdPaypalFee = $usdPaypalFee+$eurPaypalFee*$eurToUsd;
        	$usdTaxCollection = $usdTaxCollection+$eurTaxCollection*$eurToUsd;
        	$data[C('DB_INCOMECOST_MONTH')] = $_POST['month'];
        	$data[C('DB_INCOMECOST_SLLERID')] = $_POST['sellerID'];
        	$data[C('DB_INCOMECOST_SLLERIDTYPE')] = $this->getSellerIDType($_POST['sellerID']);
        	$data[C('DB_INCOMECOST_USDINCOME')] = $usdIncome;
        	$data[C('DB_INCOMECOST_USDITEMCOST')] = $usdCost;
        	$data[C('DB_INCOMECOST_USDRETURN')] = $usdRefund;
        	$data[C('DB_INCOMECOST_EURINCOME')] = $eurIncome;
        	$data[C('DB_INCOMECOST_EURITEMCOST')] = $eurCost;
        	$data[C('DB_INCOMECOST_EURRETURN')] = $eurRefund;
        	$data[C('DB_INCOMECOST_MARKETFEE')] = $usdEbayFee;
        	$data[C('DB_INCOMECOST_PAYPALFEE')] = $usdPaypalFee;
        	$data[C('DB_INCOMECOST_TAX_COLLECTION')] = $usdTaxCollection;
        	M(C('DB_INCOMECOST'))->add($data);
        	$wagesTable = M(C('DB_WAGES'));
        	$wagesTable->startTrans();
        	foreach (C('PRODUCT_MANAGER_NAME') as $key => $value) {
        		$usdPmpa[$key] = $usdPmpa[$key] + $eurPmpa[$key]*$eurToUsd;
        		$wmap[C('DB_WAGES_MONTH')] = array('eq', $_POST['month']);
	        	$wmap[C('DB_WAGES_NAME')] = array('eq', C('PRODUCT_MANAGER_NAME')[$key]);
	        	$result = $wagesTable->where($wmap)->find();
	        	if($result !== null && $result !== false){
	        		$result[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key] + $result[C('DB_WAGES_PERFORMANCE')];
	        		$result[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key, $result[C('DB_WAGES_PERFORMANCE')] );
	        		$wagesTable->save($result);
	        	}else{
	        		$newWages[C('DB_WAGES_NAME')] = C('PRODUCT_MANAGER_NAME')[$key];
	        		$newWages[C('DB_WAGES_MONTH')] = $_POST['month'];
	        		$newWages[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key];
	        		$newWages[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key, $newWages[C('DB_WAGES_PERFORMANCE')]);
	        		$newWages[C('DB_WAGES_BASE')] = C('WAGES_BASE')[$newWages[C('DB_WAGES_NAME')]];
	        		$wagesTable->add($newWages);
	        	}
        	}
        	$wagesTable->commit();
			$this->setBonus($_POST['month']);
        	$this->redirect('incomeCost');
        }else{
        	$this->error("模板不正确，请检查");
        }
	}

	private function importEbayDeSaleRecordHandle(){
		import('ORG.Net.UploadFile');
        $config=array(
            'allowExts'=>array('xlsx','xls'),
            'savePath'=>'./Public/upload/accounting/',
            'saveRule'=>I('post.sellerID').'_'.I('post.month').'_'.time(),
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
        //excel first column name verify
        for($c='A';$c!=$highestColumn;$c++){
            $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue(); 
        }
        if($this->verifyEbayEnSaleRecordColumnName($firstRow)){
        	$usdIncome = 0;
        	$usdPaypalFee = 0;
        	$usdEbayFee = 0;
        	$usdTaxCollection = 0;
        	$usdRefund = 0;
        	$usdCost = 0;
        	$usdPmpa = null;
        	$eurIncome = 0;
        	$eurPaypalFee = 0;
        	$eurEbayFee = 0;
        	$eurTaxCollection = 0;
        	$eurRefund = 0;
        	$eurCost = 0;
        	$eurPmpa = null;
        	$pfa = $this->getPlatformFeeArray($_POST['sellerID']);
        	$productTable=M(C('DB_PRODUCT'));
        	for($i=2;$i<=$highestRow;$i++){
        		$sku = $objPHPExcel->getActiveSheet()->getCell("AF".$i)->getValue();
        		$quantity = $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
        		$salePriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue());
        		$shippingPriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue());
    			$totalCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue());
        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_MANAGER'));
        		if($pmanager==null){
        			$pmanager='Yellow River';
        		}
        		//如果sku和总价都不为空，可以用总价确认收入，平台费用，产品经理绩效。用sku查找采购成本。通过运算计算是否有退款。
        		if($sku != null && $totalCM['amount']!=null){
	        		if($totalCM['currency']=='usd'){
	        			$usdIncome = $usdIncome+$totalCM['amount'];
	        			if($totalCM['amount']<12){
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$usdEbayFee = $usdEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        			$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$totalCM['amount'];
	        			$usdCost = $usdCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;

	        		}
	        		if($totalCM['currency']=='eur'){
	        			$eurIncome = $eurIncome+$totalCM['amount'];
	        			if($totalCM['amount']<12){
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$eurEbayFee = $eurEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        			$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$totalCM['amount'];
	        			$eurCost = $eurCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;
	        		}
	        		$difference=($quantity*$salePriceCM['amount']+$shippingPriceCM['amount'])-$totalCM['amount'];
	        		if($difference>0){
	        			if($totalCM['currency']=='usd'){
	        				$usdRefund=$usdRefund+$difference;
	        			}
	        			if($totalCM['currency']=='eur'){
	        				$eurRefund=$eurRefund+$difference;
	        			}
	        		}	
        		}
        		

        		//如果sku为空,总价不为空
        		if($sku == null && $totalCM['amount']!=null){
        			if($totalCM['currency']=='usd'){
	        			$usdIncome = $usdIncome+$totalCM['amount'];
	        			if($totalCM['amount']<12){
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$usdPaypalFee = $usdPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$usdEbayFee = $usdEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        		}
	        		if($totalCM['currency']=='eur'){
	        			$eurIncome = $eurIncome+$totalCM['amount'];
	        			if($totalCM['amount']<12){
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal__small_percent']+$pfa['paypal_small_base'];
	        			}else{
	        				$eurPaypalFee = $eurPaypalFee+$totalCM['amount']*$pfa['paypal_percent']+$pfa['paypal_base'];
	        			}
	        			$eurEbayFee = $eurEbayFee+$totalCM['amount']*$pfa['ebay_percent'];
	        		}

	        		$difference = $salePriceCM['amount']+$shippingPriceCM['amount']-$totalCM['amount'];
	        		if($difference>0){
	        			if($totalCM['currency']=='usd'){
	        				$usdRefund=$usdRefund+$difference;
	        			}
	        			if($totalCM['currency']=='eur'){
	        				$eurRefund=$eurRefund+$difference;
	        			}
	        		}

	        		for ($j=$i+1; $j<=$highestRow; $j++) { 
	        			if($objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue() == $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue()){
	        				$itemSku = $objPHPExcel->getActiveSheet()->getCell("AF".$j)->getValue();
			        		$itemQuantity = $objPHPExcel->getActiveSheet()->getCell("P".$j)->getValue();
			        		$itemSalePriceCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("Q".$j)->getValue());
			        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_MANAGER'));
			        		if($pmanager==null){
			        			$pmanager='Yellow River';
			        		}
			        		if($totalCM['currency']=='usd'){
		        				$usdCost=$usdCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_PRICE'))*$itemQuantity;
		        				if($difference==0){
		        					$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$itemQuantity*$itemSalePriceCM['amount'];
		        				}
		        			}
		        			if($totalCM['currency']=='eur'){
		        				$eurCost=$eurCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$itemSku))->getField(C('DB_PRODUCT_PRICE'))*$itemQuantity;
		        				if($difference==0){
		        					$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$itemQuantity*$itemSalePriceCM['amount'];
		        				}
		        			}
	        			}
	        		}
        		}
        	}
        	$eurToUsd=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTOUSD'));
        	$usdEbayFee = $usdEbayFee+$eurEbayFee*$eurToUsd;
        	$usdPaypalFee = $usdPaypalFee+$eurPaypalFee*$eurToUsd;
        	$usdTaxCollection = $usdTaxCollection+$eurTaxCollection*$eurToUsd;
        	$data[C('DB_INCOMECOST_MONTH')] = $_POST['month'];
        	$data[C('DB_INCOMECOST_SLLERID')] = $_POST['sellerID'];
        	$data[C('DB_INCOMECOST_SLLERIDTYPE')] = $this->getSellerIDType($_POST['sellerID']);
        	$data[C('DB_INCOMECOST_USDINCOME')] = $usdIncome;
        	$data[C('DB_INCOMECOST_USDITEMCOST')] = $usdCost;
        	$data[C('DB_INCOMECOST_USDRETURN')] = $usdRefund;
        	$data[C('DB_INCOMECOST_EURINCOME')] = $eurIncome;
        	$data[C('DB_INCOMECOST_EURITEMCOST')] = $eurCost;
        	$data[C('DB_INCOMECOST_EURRETURN')] = $eurRefund;
        	$data[C('DB_INCOMECOST_MARKETFEE')] = $usdEbayFee;
        	$data[C('DB_INCOMECOST_PAYPALFEE')] = $usdPaypalFee;
        	$data[C('DB_INCOMECOST_TAX_COLLECTION')] = $usdTaxCollection;
        	
        	M(C('DB_INCOMECOST'))->add($data);
        	$wagesTable = M(C('DB_WAGES'));
        	$wagesTable->startTrans();
        	foreach ($usdPmpa as $key => $value) {
        		$usdPmpa[$key] = $value + $eurPmpa[$key]*$eurToUsd;
        		$wmap[C('DB_WAGES_MONTH')] = array('eq', $_POST['month']);
	        	$wmap[C('DB_WAGES_NAME')] = array('eq', C('PRODUCT_MANAGER_NAME')[$key]);
	        	$result = $wagesTable->where($wmap)->find();
	        	if($result !== null && $result !== false){
	        		$result[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key] + $result[C('DB_WAGES_PERFORMANCE')];
	        		$result[C('DB_WAGES_PERCENT')] =  $this->getProductManagerPercent($key, $result[C('DB_WAGES_PERFORMANCE')]);
	        		$wagesTable->save($result);
	        	}else{
	        		$newWages[C('DB_WAGES_NAME')] = C('PRODUCT_MANAGER_NAME')[$key];
	        		$newWages[C('DB_WAGES_MONTH')] = $_POST['month'];
	        		$newWages[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key];
	        		$newWages[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key, $newWages[C('DB_WAGES_PERFORMANCE')]);
	        		$newWages[C('DB_WAGES_BASE')] = C('WAGES_BASE')[$newWages[C('DB_WAGES_NAME')]];
	        		$wagesTable->add($newWages);
	        	}
        	}
        	$wagesTable->commit();        	
			$this->setBonus($_POST['month']);
        	$this->redirect('incomeCost');
        }else{
        	$this->error("模板不正确，请检查");
        }
	}

	public function importAmazonEnSaleRecordHandle(){
		import('ORG.Net.UploadFile');
        $config=array(
            'allowExts'=>array('xlsx','xls'),
            'savePath'=>'./Public/upload/accounting/',
            'saveRule'=>I('post.sellerID').'_'.I('post.month').'_'.time(),
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
        //excel first column name verify
        for($c='A';$c!=$highestColumn;$c++){
            $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'4')->getValue(); 
        }
        if($this->verifyAmazonEnSaleRecordColumnName($firstRow)){
        	$usdIncome = 0;
        	$usdAmazonFee = 0;
        	$usdTaxCollection = 0;
        	$usdRefund = 0;
        	$usdCost = 0;
        	$usdPmpa = null;
        	$usdShippingFee=0;
        	$eurIncome = 0;
        	$eurAmazonFee = 0;
        	$eurTaxCollection = 0;
        	$eurRefund = 0;
        	$eurCost = 0;
        	$eurPmpa = null;
        	$eurShippingFee=0;
        	$productTable=M(C('DB_PRODUCT'));
        	$kpiSaleRecordTable=M(C('DB_KPI_SALE_RECORD'));
        	$kpiSaleRecordTable->startTrans();
        	for($i=5;$i<=$highestRow;$i++){
        		$sku = $this->skuDecode($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());
        		$transactionType = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
        		$paymentType = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
        		$paymentDetail = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
        		if(I('post.sellerID')=="shangsitech@qq.com"){
        			$amountCM = array('currency'=>'eur','amount'=>$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue());
        		}else{
        			$amountCM = $this->getCurrencyAmount($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue());
        		}        		
        		$quantity = $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
        		$pmanager = $productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_MANAGER'));

        		//检测该记录是否需要保存到销售绩效考核表里
        		$kpiSaleRecord=$kpiSaleRecordTable->where(array(C('DB_KPI_SALE_RECORD_MARKET_NO')=>$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue()))->find();
        		if($kpiSaleRecord!=null && $kpiSaleRecord!=false){
    				if($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue() == 'Order Payment' && $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue() == 'Product charges'){
    					$kpiSaleRecord[C('DB_KPI_SALE_RECORD_PRICE')] = $amountCM;
    					$kpiSaleRecordTable->save($kpiSaleRecord);
    				}
    				if($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue() == 'Order Payment' && $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue() == 'Other' && $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue() == 'Shipping'){
    					$kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')] = $amountCM;
    					$kpiSaleRecordTable->save($kpiSaleRecord);
    				}
    				if($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue() == 'Refund'){
    					$kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')] = $kpiSaleRecord[C('DB_KPI_SALE_RECORD_PRICE')]+$amountCM;
    					$kpiSaleRecordTable->save($kpiSaleRecord);
    				}
    				if($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue() == 'Shipping services purchased through Amazon' && $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue() == 'Shipping Service Charges' && $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue() == 'Postage for return'){
    					$kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')] = $kpiSaleRecord[C('DB_KPI_SALE_RECORD_SHIPPING_FEE')]+$amountCM;
    					$kpiSaleRecordTable->save($kpiSaleRecord);
    				}
    			}


        		if($pmanager==null){
        			$pmanager='Yellow River';
        		}

        		if($transactionType=='Refund' && $paymentType=='Amazon fees'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Product charges'){
        			if($amountCM['currency']=='usd'){
        				$usdRefund = $usdRefund-$amountCM['amount'];
        				$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurRefund = $eurRefund-$amountCM['amount'];
        				$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Other' && $paymentDetail=='Product Tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Other' && $paymentDetail=='Shipping'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Other' && $paymentDetail=='Return shipping concession'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Promo rebates' && $paymentDetail=='Shipping'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Other' && $paymentDetail=='Shipping tax'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Refund' && $paymentType=='Other' && $paymentDetail=='Restocking fee'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Amazon fees'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Product charges'){
        			if($amountCM['currency']=='usd'){
        				$usdIncome = $usdIncome+$amountCM['amount'];
        				$usdPmpa[$pmanager] = $usdPmpa[$pmanager]+$amountCM['amount'];
        				$usdCost=$usdCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;
        			}
        			if($amountCM['currency']=='eur'){
        				$eurIncome = $eurIncome+$amountCM['amount'];
        				$eurPmpa[$pmanager] = $eurPmpa[$pmanager]+$amountCM['amount'];
        				$eurCost=$eurCost+$productTable->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_PRICE'))*$quantity;
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Other' && $paymentDetail=='Product Tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Other' && $paymentDetail=='Gift Wrap Tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Other' && $paymentDetail=='Shipping'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Other' && $paymentDetail=='Gift wrap'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Other' && $paymentDetail=='Shipping tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Promo rebates' && $paymentDetail==''){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order Payment' && $paymentType=='Promo rebates' && $paymentDetail=='Shipping'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Shipping services purchased through Amazon' && $paymentType=='Amazon fees'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Shipping services purchased through Amazon' && $paymentType=='Shipping Service Charges'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Shipping services purchased through Amazon' && $paymentType=='Shipping Service Refunds'){
        			if($amountCM['currency']=='usd'){
        				$usdRefund = $usdRefund-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurRefund = $eurRefund-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Shipping services purchased through Amazon' && $paymentType=='Shipping Services - Carrier Adjustments'){
        			if($amountCM['currency']=='usd'){
        				$usdShippingFee = $usdShippingFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurShippingFee = $eurShippingFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Service Fees' && $paymentType=='Amazon fees'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Service Fees' && $paymentType=='Transaction Details'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Other' && $paymentType=='Other'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Other' && $paymentType=='FBA Inventory Reimbursement - Lost:Warehouse'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Other' && $paymentType=='FBA Inventory Reimbursement - Customer Return'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Other' && $paymentType=='FBA Inventory Reimbursement - Customer Service Issue'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Other' && $paymentType=='FBA Inventory Reimbursement - Damaged:Warehouse'){
        			if($amountCM['currency']=='usd'){
        				$usdAmazonFee = $usdAmazonFee-$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurAmazonFee = $eurAmazonFee-$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order retrocharge' && $paymentType=='Other'  && $paymentDetail=='Shipping tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}elseif($transactionType=='Order retrocharge' && $paymentType=='Other'  && $paymentDetail=='Tax'){
        			if($amountCM['currency']=='usd'){
        				$usdTaxCollection = $usdTaxCollection+$amountCM['amount'];
        			}
        			if($amountCM['currency']=='eur'){
        				$eurTaxCollection = $eurTaxCollection+$amountCM['amount'];
        			}
        		}
        		else{
        			$this->error('第 '.$i.' 行费用无法确认！');
        		}
        	}
        	$kpiSaleRecordTable->commit();
        	$eurToUsd=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTOUSD'));
        	$usdAmazonFee = $usdAmazonFee+$eurAmazonFee*$eurToUsd;
        	$usdTaxCollection = $usdTaxCollection+$eurTaxCollection*$eurToUsd;
        	$data[C('DB_INCOMECOST_MONTH')] = $_POST['month'];
        	$data[C('DB_INCOMECOST_SLLERID')] = $_POST['sellerID'];
        	$data[C('DB_INCOMECOST_SLLERIDTYPE')] = $this->getSellerIDType($_POST['sellerID']);
        	$data[C('DB_INCOMECOST_USDINCOME')] = $usdIncome;
        	$data[C('DB_INCOMECOST_USDITEMCOST')] = $usdCost;
        	$data[C('DB_INCOMECOST_USDRETURN')] = $usdRefund;
        	$data[C('DB_INCOMECOST_EURINCOME')] = $eurIncome;
        	$data[C('DB_INCOMECOST_EURITEMCOST')] = $eurCost;
        	$data[C('DB_INCOMECOST_EURRETURN')] = $eurRefund;
        	$data[C('DB_INCOMECOST_MARKETFEE')] = $usdAmazonFee;
        	$data[C('DB_INCOMECOST_TAX_COLLECTION')] = $usdTaxCollection;
        	M(C('DB_INCOMECOST'))->add($data);
        	if($usdPmpa==null){        		
	        	foreach ($eurPmpa as $key => $value) {
	        		$usdPmpa[$key] = $value*$eurToUsd;
	        	}
        	}
        	$wagesTable = M(C('DB_WAGES'));
        	$wagesTable->startTrans();
        	foreach ($usdPmpa as $key => $value) {
        		$wmap[C('DB_WAGES_MONTH')] = array('eq', $_POST['month']);
	        	$wmap[C('DB_WAGES_NAME')] = array('eq', C('PRODUCT_MANAGER_NAME')[$key]);
	        	$result = $wagesTable->where($wmap)->find();
	        	if($result !== null && $result !== false){
	        		$result[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key] + $result[C('DB_WAGES_PERFORMANCE')];
	        		$result[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key, $result[C('DB_WAGES_PERFORMANCE')]);
	        		$wagesTable->save($result);
	        	}else{
	        		$newWages[C('DB_WAGES_NAME')] = C('PRODUCT_MANAGER_NAME')[$key];
	        		$newWages[C('DB_WAGES_MONTH')] = $_POST['month'];
	        		$newWages[C('DB_WAGES_PERFORMANCE')] = $usdPmpa[$key];
	        		$newWages[C('DB_WAGES_PERCENT')] = $this->getProductManagerPercent($key, $newWages[C('DB_WAGES_PERFORMANCE')]);
	        		$newWages[C('DB_WAGES_BASE')] = C('WAGES_BASE')[$newWages[C('DB_WAGES_NAME')]];
	        		$wagesTable->add($newWages);
	        	}
        	}
        	$wagesTable->commit();
        	$saleFeeTable=M(C('DB_SALEFEE'));
        	$saleFeeTable->startTrans();
        	$sfmap[C('DB_SALEFEE_MONTH')] = array('eq',$_POST['month']);
        	$sfResult=$saleFeeTable->where($sfmap)->find();
        	if($sfResult !== null && $sfResult !== false){
        		$sfResult[$this->getAmazonLocalShippingFeeRelation($_POST['sellerID'])]=$sfResult[$this->getAmazonLocalShippingFeeRelation($_POST['sellerID'])]+$usdShippingFee+$eurShippingFee*$eurToUsd;
        		$saleFeeTable->save($sfResult);
        	}else{
        		$newSaleFee[C('DB_SALEFEE_MONTH')]=$_POST['month'];
        		$newSaleFee[C('DB_SALEFEE_USSWSFLOCAL')]=$usdShippingFee+$eurShippingFee*$eurToUsd;
        		$saleFeeTable->add($newSaleFee);
        	}
        	$saleFeeTable->commit();        	
			$this->setBonus($_POST['month']);
        	$this->redirect('incomeCost');
        }else{
        	$this->error("模板不正确，请检查");
        }
	}

	private function getProductManagerPercent($manager,$performance){
		if($manager=='Yangtze'){
			if($performance<7000){
				return 0;
			}elseif ($performance<20000) {
				return 1.1;
			}elseif ($performance<30000) {
				return 1.2;
			}elseif ($performance<40000) {
				return 1.3;
			}elseif ($performance<50000) {
				return 1.4;
			}elseif ($performance<60000) {
				return 1.5;
			}elseif ($performance<70000) {
				return 1.6;
			}elseif ($performance<80000) {
				return 1.7;
			}elseif ($performance<90000) {
				return 1.8;
			}elseif ($performance<100000) {
				return 1.9;
			}else{
				return 2;
			}
		}elseif($manager=='Pearl River'){
			if($performance>7000){
				return 1;
			}else{
				return 0;
			}
		}else{
			return C('WAGES_PERFORMANCE_PERCENT')[$manager];
		}
	}

	private function getSellerIDType($sellerID){
		switch ($sellerID) {
			case 'greatgoodshop':
				return 'cooperate';
			case 'blackfive':
				return 'cooperate';
			case 'rc-helicar':
				return 'personal';
			case 'vtkg5755':
				return 'personal';
			case 'yzhan-816':
				return 'personal';
			case 'lipovolt':
				return 'cooperate';
			case 'g-lipovolt':
				return 'cooperate';
			case 'zuck':
				return 'personal';
			case 'shangsitech@qq.com':
				return 'personal';
			default:
				break;
		}
	}

	private function getAmazonLocalShippingFeeRelation($sellerID){
		switch ($sellerID) {
			case 'lipovolt':
				return C('DB_SALEFEE_USSWSFLOCAL');
			case 'shangsitech@qq.com':
				return C('DB_SALEFEE_THIRDPARTYSFLOCAL');
			default:
				break;
		}
	}

	private function getProductManagerPerformanceArray(){
		$productManagerPerformance = null;
		foreach (C('PRODUCT_MANAGER_NAME') as $key => $value) {
			$productManagerPerformance[$key]=0;
		}
		return $productManagerPerformance;
	}

	private function getPlatformFeeArray($sellerID){
		switch ($sellerID) {
			case 'greatgoodshop':
				return array(
					'paypal_percent'=>C('PLATFORM_FEE')['Paypal_US_Percent'], 
					'paypal_base'=>C('PLATFORM_FEE')['Paypal_US_Base'],
					'paypal_small_percent'=>C('PLATFORM_FEE')['Paypal_US_Percent'], 
					'paypal_small_base'=>C('PLATFORM_FEE')['Paypal_US_Base'],
					'ebay_percent'=>C('PLATFORM_FEE')['Ebay_US_Percent'],
					'ebay_shop'=>C('PLATFORM_FEE')['Ebay_US_Shop'],
					);
			case 'blackfive':
				return array(
					'paypal_percent'=>C('PLATFORM_FEE')['Paypal_US_Percent'], 
					'paypal_base'=>C('PLATFORM_FEE')['Paypal_US_Base'],
					'paypal_small_percent'=>C('PLATFORM_FEE')['Paypal_US_Percent'], 
					'paypal_small_base'=>C('PLATFORM_FEE')['Paypal_US_Base'],
					'ebay_percent'=>C('PLATFORM_FEE')['Ebay_US_Percent'],
					'ebay_shop'=>C('PLATFORM_FEE')['Ebay_US_Shop'],
					);
			case 'rc-helicar':
				return array(
					'paypal_percent'=>C('PLATFORM_FEE')['Paypal_CN_Percent'], 
					'paypal_base'=>C('PLATFORM_FEE')['Paypal_CN_Base'],
					'paypal_small_percent'=>C('PLATFORM_FEE')['Paypal_CN_Small_Percent'], 
					'paypal_small_base'=>C('PLATFORM_FEE')['Paypal_CN_Small_Base'],
					'ebay_percent'=>C('PLATFORM_FEE')['Ebay_CN_Percent'],
					'ebay_shop'=>C('PLATFORM_FEE')['Ebay_CN_Shop'],
					);
			case 'vtkg5755':
				return array(
					'paypal_percent'=>C('PLATFORM_FEE')['Paypal_CN_Percent'], 
					'paypal_base'=>C('PLATFORM_FEE')['Paypal_CN_Base'],
					'paypal_small_percent'=>C('PLATFORM_FEE')['Paypal_CN_Small_Percent'], 
					'paypal_small_base'=>C('PLATFORM_FEE')['Paypal_CN_Small_Base'],
					'ebay_percent'=>C('PLATFORM_FEE')['Ebay_CN_Percent'],
					'ebay_shop'=>C('PLATFORM_FEE')['Ebay_CN_Shop'],
					);
			case 'yzhan-816':
				return array(
					'paypal_percent'=>C('PLATFORM_FEE')['Paypal_CN_Percent'], 
					'paypal_base'=>C('PLATFORM_FEE')['Paypal_CN_Base'],
					'paypal_small_percent'=>C('PLATFORM_FEE')['Paypal_CN_Small_Percent'], 
					'paypal_small_base'=>C('PLATFORM_FEE')['Paypal_CN_Small_Base'],
					'ebay_percent'=>C('PLATFORM_FEE')['Ebay_CN_Percent'],
					'ebay_shop'=>C('PLATFORM_FEE')['Ebay_CN_Shop'],
					);
			case 'lipovolt':
				return array(
					'amazon_percent'=>C('PLATFORM_FEE')['Amazon_US_Percent'],
					'amazon_shop'=>C('PLATFORM_FEE')['Amazon_US_Shop'],
					);
			case 'shangsitech@qq.com':
				return array(
					'amazon_percent'=>C('PLATFORM_FEE')['Amazon_US_Percent'],
					'amazon_shop'=>C('PLATFORM_FEE')['Amazon_US_Shop'],
					);
			case 'g-lipovolt':
				return array(
					'groupon_percent'=>C('PLATFORM_FEE')['Grounpon_US_Percent'],
					'groupon_shop'=>C('PLATFORM_FEE')['Grounpon_US_Shop'],
					);
			case 'zuck':
				return array(
					'wish_percent'=>C('PLATFORM_FEE')['Wish_CN_Percent'],
					'wish_shop'=>C('PLATFORM_FEE')['Wish_CN_Shop'],
					);
			default:
				break;
		}
	}

	public function getCurrencyAmount($price){
		if(is_numeric($price)){
			return array('currency'=>'usd','amount'=>$price);
		}elseif(substr($price,0,1)=='$'){
			return array('currency'=>'usd','amount'=>substr($price, 1));
		}elseif(substr($price,0,4)=='US $'){
			return array('currency'=>'usd','amount'=>str_replace(",",".",substr($price, 4)));
		}elseif(substr($price,0,4)=='EUR '){
			return array('currency'=>'eur','amount'=>str_replace(",",".",substr($price, 4)));
		}elseif(!is_numeric($price) && is_numeric(substr($price,0,1))){
			return array('currency'=>'eur','amount'=>str_replace(",",".",$price));
		}else{
			return array('currency'=>null,'amount'=>null);
		}		
	}

	private function verifyGroupOnOrderAnalyzeColumnName($firstRow){
		for($c='A';$c<max(array_keys(C('verifyGroupOnOrderAnalyzeColumnName')));$c++){
            if(trim($firstRow[$c]) != C('verifyGroupOnOrderAnalyzeColumnName')[$c]){
                return false;
            }      
        }
        return true;
	}

	private function verifyEbayEnSaleRecordColumnName($firstRow){
		for($c='A';$c<max(array_keys(C('IMPORT_EBAY_DE_ORDER')));$c++){
            if(trim($firstRow[$c]) != C('IMPORT_EBAY_EN_ORDER')[$c]){
 				return false;   
            }      
        }
        return true;
	}

	private function verifyEbayDeSaleRecordColumnName($firstRow){
		for($c='A';$c<max(array_keys(C('IMPORT_EBAY_DE_ORDER')));$c++){
            if(trim($firstRow[$c]) != C('IMPORT_EBAY_DE_ORDER')[$c]){
                return false;
            }       
        }
        return true;
	}

	private function verifyAmazonEnSaleRecordColumnName($firstRow){
		for($c='A';$c<max(array_keys(C('IMPORT_AMAZON_TV')));$c++){
            if(trim($firstRow[$c]) != C('IMPORT_AMAZON_TV')[$c]){
                return false;
            }       
        }
        return true;
	}

	private function verifyGrouponSaleRecordColumnName($firstRow){
		for($c='A';$c<max(array_keys(C('IMPORT_GROUPON_UNSHIPPED_ORDER')));$c++){
            if(trim($firstRow[$c]) != C('IMPORT_GROUPON_UNSHIPPED_ORDER')[$c]){
                return false;
            }       
        }
        return true;
	}

	public function profitStatistic($month=null){
		if($month==null){
			$month=M(C('DB_INCOMECOST'))->order('month desc')->limit(1)->getField(C('DB_INCOMECOST_MONTH'));
		}
		$this->assign('data',$this->getProfitStatistic($month));
		$this->assign('selectedMonth',$month);
		$this->display();
	}

	public function searchProfitStatistic(){
		//$this->profitStatistic($_POST['month']);
		$this->redirect('profitStatistic',array('month'=>$_POST['month']),1,'页面跳转中...');
	}

	private function getProfitStatistic($month=null){
		if($this->verifyProfitStatisticData($month)){
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['title1']]=$this->getTitle1();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['title2']]=$this->getTitle2();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_title']]=$this->getIncomeTitle();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_usd']]=$this->getUsdIncome($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_eur']]=$this->getEurIncome($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]=$this->getRmbIncome($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]=$this->getRmbItemCost($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_detail_title']]=$this->getSaleFeeDetailTitle();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']]=$this->getSaleFeeSharePercent($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage']]=$this->getSaleFeeFirstSto($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage_sfee']]=$this->getSaleFeeFirstStoShareFee($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf']]=$this->getSaleFeeLocalSf($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf_sfee']]=$this->getSaleFeeLocalSfShareFee($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee']]=$this->getPackingMaterialFee($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee_sfee']]=$this->getPackingMaterialFeeShareFee($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff']]=$this->getTariff($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff_sfee']]=$this->getTariffShareFee($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['platform_fee']]=$this->getPlatformFee($month);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]=$this->getSaleFeeTotal($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_title']]=$this->getManagementTitle();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']]=$this->getManagementPercent($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_detail_title']]=$this->getManagementDetailTitle();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_rent']]=$this->getManagementRent($month,$data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_wages']]=$this->getManagementWages($month,$data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_booking']]=$this->getManagementBooking($month,$data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_other']]=$this->getManagementOther($month,$data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]=$this->getManagementShareFee($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']]=$this->getStatisticTitle($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_analysis']]=$this->getStatisticAnalysis();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_gross_profit_rate']]=$this->getStatisticGpr($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_net_profit_rate']]=$this->getStatisticNpr($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_sale_fee_income_rate']]=$this->getStatisticSfir($data);
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_management_fee_income_rate']]=$this->getStatisticMfir($data);
			return $data;
		}else{
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['title1']]=$this->getTitle1();
			$data[C('PROFIT_STATISTIC_SUBJECT_ROW')['title2']]=$this->getTitle2();
			return $data;
		}
	}

	private function verifyProfitStatisticData($month){
		if($this->verifyWagesData($month) && $this->verifySaleFeeData($month) && $this->verifyIncomeCostData($month)){
			return true;
		}else{
			return false;
		}
	}

	private function verifyWagesData($month){
		$wagesTable=M(C('DB_WAGES'));
		$wagesTableMap[C('DB_WAGES_MONTH')] = array('eq',$month);
		foreach (C('WAGES_BASE') as $key => $value) {
			$wagesTableMap[C('DB_WAGES_NAME')] = array('eq',$key);
			$tmpWages=$wagesTable->where($wagesTableMap)->find();
			if($tmpWages==null||$tmpWages==false){
				$this->error($key.' 的'.$month.' 的工资无记录！','wages');
				return false;
			}else{
				return true;
			}
		}
	}

	private function verifySaleFeeData($month){
		//$this->updateSaleFeeSzswPShippingFee($month);
		$saleFee=M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->find();
		if($saleFee == null || $saleFee== false){
			$this->error($month.' 的销售费用查不到！','saleFee');
			return false;
		}elseif($saleFee[C('DB_SALEFEE_USSWSFCN')]==null || $saleFee[C('DB_SALEFEE_USSWSFCN')]=='' || $saleFee[C('DB_SALEFEE_USSWSFCN')]==0){
			$this->error($month.' 的销售费用里的美自建仓头程费用查不到','saleFee');
			return false;
		}elseif($saleFee[C('DB_SALEFEE_USSWSFLOCAL')]==null || $saleFee[C('DB_SALEFEE_USSWSFLOCAL')]=='' || $saleFee[C('DB_SALEFEE_USSWSFLOCAL')]==0){
			$this->error($month.' 的销售费用里的美自建仓本地运费查不到','saleFee');
			return false;
		}elseif($saleFee[C('DB_SALEFEE_THRIDPARTYSFCN')]==null || $saleFee[C('DB_SALEFEE_THRIDPARTYSFCN')]=='' || $saleFee[C('DB_SALEFEE_THRIDPARTYSFCN')]==0){
			$this->error($month.' 的销售费用里的海外仓头程费用查不到','saleFee');
			return false;
		}elseif($saleFee[C('DB_SALEFEE_THIRDPARTYSFLOCAL')]==null || $saleFee[C('DB_SALEFEE_THIRDPARTYSFLOCAL')]=='' || $saleFee[C('DB_SALEFEE_THIRDPARTYSFLOCAL')]==0){
			$this->error($month.' 的销售费用里的海外仓本地运费查不到','saleFee');
			return false;
		}/*elseif($saleFee[C('DB_SALEFEE_SZSWSF')]==null || $saleFee[C('DB_SALEFEE_SZSWSF')]=='' || $saleFee[C('DB_SALEFEE_SZSWSF')]==0){
			$this->error($month.' 的销售费用里的深圳仓运费查不到','saleFee');
			return false;
		}*/else{
			return true;
		}
	}

	private function verifyIncomeCostData($month){
		$incomeCostTable=M(C('DB_INCOMECOST'));
		$incomeCostMap[C('DB_INCOMECOST_MONTH')] = array('eq',$month);
		$incomeCostMap[C('DB_INCOMECOST_SLLERID')] = array('eq','greatgoodshop');
		$incomeCost = $incomeCostTable->where($incomeCostMap)->find();
		if($incomeCost == null || $incomeCost == false){
			$this->error('greatgoodshop的 '.$month.' 的收入成本查不到！');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]==0){
			$this->error('greatgoodshop的 '.$month.' 的美元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==0){
			$this->error('greatgoodshop的 '.$month.' 的美元售出产品成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_MARKETFEE')]==null ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]==0){
			$this->error('greatgoodshop的 '.$month.' 的ebay费用查不到！','incomeCost');
			return false;
		}
		$incomeCostMap[C('DB_INCOMECOST_SLLERID')] = array('eq','lipovolt');
		$incomeCost = $incomeCostTable->where($incomeCostMap)->find();
		if($incomeCost == null || $incomeCost == false){
			$this->error('lipovolt的 '.$month.' 的收入成本查不到！');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]==0){
			$this->error('lipovolt的 '.$month.' 的美元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==0){
			$this->error('lipovolt的 '.$month.' 的美元售出产品成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_MARKETFEE')]==null ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]==0){
			$this->error('lipovolt的 '.$month.' 的amazon费用查不到！','incomeCost');
			return false;
		}
		//mark
		/*$incomeCostMap[C('DB_INCOMECOST_SLLERID')] = array('eq','rc-helicar');
		$incomeCost = $incomeCostTable->where($incomeCostMap)->find();
		if($incomeCost == null || $incomeCost == false){
			$this->error('rc-helicar的 '.$month.' 的收入成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]==0){
			$this->error('rc-helicar的 '.$month.' 的美元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==0){
			$this->error('rc-helicar的 '.$month.' 的美元售出产品成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_EURINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_EURINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_EURINCOME')]==0){
			$this->error('rc-helicar的 '.$month.' 的欧元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_EURITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_EURITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_EURITEMCOST')]==0){
			$this->error('rc-helicar的 '.$month.' 的欧元售出产品成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_MARKETFEE')]==null ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]==0){
			$this->error('rc-helicar的 '.$month.' 的ebay费用查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_PAYPALFEE')]==null ||$incomeCost[C('DB_INCOMECOST_PAYPALFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_PAYPALFEE')]==0){
			$this->error('rc-helicar的 '.$month.' 的paypal费用查不到！','incomeCost');
			return false;
		}*/
		/*$incomeCostMap[C('DB_INCOMECOST_SLLERID')] = array('eq','vtkg5755');
		$incomeCost = $incomeCostTable->where($incomeCostMap)->find();
		if($incomeCost == null || $incomeCost == false){
			$this->error('vtkg5755的 '.$month.' 的收入成本查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_USDINCOME')]==0){
			$this->error('vtkg5755的 '.$month.' 的美元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_USDITEMCOST')]==0){
			$this->error('vtkg5755的 '.$month.' 的美元售出产品成本查不到！','incomeCost');
			return false;
		}*/
		/*vtkg只做美国，不需要检查欧元收入和成本
		elseif($incomeCost[C('DB_INCOMECOST_EURINCOME')]==null ||$incomeCost[C('DB_INCOMECOST_EURINCOME')]=='' ||$incomeCost[C('DB_INCOMECOST_EURINCOME')]==0){
			$this->error('vtkg5755的 '.$month.' 的欧元收入查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_EURITEMCOST')]==null ||$incomeCost[C('DB_INCOMECOST_EURITEMCOST')]=='' ||$incomeCost[C('DB_INCOMECOST_EURITEMCOST')]==0){
			$this->error('vtkg5755的 '.$month.' 的欧元售出产品成本查不到！','incomeCost');
			return false;
		}
		elseif($incomeCost[C('DB_INCOMECOST_MARKETFEE')]==null ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_MARKETFEE')]==0){
			$this->error('vtkg5755的 '.$month.' 的ebay费用查不到！','incomeCost');
			return false;
		}elseif($incomeCost[C('DB_INCOMECOST_PAYPALFEE')]==null ||$incomeCost[C('DB_INCOMECOST_PAYPALFEE')]=='' ||$incomeCost[C('DB_INCOMECOST_PAYPALFEE')]==0){
			$this->error('vtkg5755的 '.$month.' 的paypal费用查不到！','incomeCost');
			return false;
		}*/
		return true;
	}

	private function getIncomeTitle(){
		$ui['subject']='一、营业收入';
		return $ui;
	}

	private function getTitle1(){
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			if($key==round(count(C('COOPERATE_SELLERID'))/2)){
				$ui[$value]=C('SHARE_TYPE_CNAME')['cooperate'];
			}
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			if($key==round(count(C('PERSONAL_SELLERID'))/2)){
				$ui[$value]=C('SHARE_TYPE_CNAME')['personal'];
			}
		}
		return $ui;
	}

	private function getTitle2(){
		$ui['subject']='项目';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$value;
		}
		$ui['coSub']='小计';
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$value;
		}
		$ui['perSub']='小计';
		$ui['total']='总计';
		return $ui;
	}

	private function getStatisticMfir($dataPara){
		$ui['subject']='管理费用占收入比例';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		$ui['coSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['coSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub'],2);
		$ui['perSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['perSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub'],2);
		$ui['total']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['total']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		return $ui;
	}

	private function getStatisticSfir($dataPara){
		$ui['subject']='销售费用占收入比例';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		$ui['coSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['coSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub'],2);
		$ui['perSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['perSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub'],2);
		$ui['total']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['total']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		return $ui;
	}

	private function getStatisticNpr($dataPara){
		$ui['subject']='净利率';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value] = round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		$ui['coSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']]['coSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub'],2);
		$ui['perSub']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']]['perSub']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub'],2);
		$ui['total']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['statistic_profit']]['total']/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		return $ui;
	}

	private function getStatisticGpr($dataPara){
		$ui['subject']='毛利率';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value] = round(($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']][$value])/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value] = round(($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']][$value])/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value],2);
		}
		$ui['coSub']=round(($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['coSub'])/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub'],2);
		$ui['perSub']=round(($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['perSub'])/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub'],2);
		$ui['total']=round(($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['total'])/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		return $ui;
	}

	private function getStatisticAnalysis(){
		return $ui;
	}

	private function getStatisticTitle($dataPara){
		$ui['subject']='二、营业利润(损失以“-”号填列）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']][$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']][$value]-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']][$value];
		}
		$ui['coSub']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['coSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['coSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['coSub'];
		$ui['perSub']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['perSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['perSub']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['perSub'];
		$ui['total']=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['item_cost']]['total']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_total']]['total']-$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_sfee']]['total'],2);
		return $ui;
	}

	private function getManagementShareFee($dataPara){
		$ui['subject']='分摊管理费用';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_rent']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_wages']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_booking']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_other']][$value];
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_rent']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_wages']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_booking']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['managment_other']][$value];
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getManagementOther($month,$dataPara){
		$ui['subject']='-其他';
		$map[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
		$map[C('DB_MANAGEMENTFEE_PURPOSE')] = array('eq','other');
		$ui['total']=M(C('DB_MANAGEMENTFEE'))->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		return $ui;
	}

	private function getManagementBooking($month,$dataPara){
		$ui['subject']='-代理记账';
		$map[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
		$map[C('DB_MANAGEMENTFEE_PURPOSE')] = array('eq','booking');
		$ui['total']=M(C('DB_MANAGEMENTFEE'))->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		return $ui;
	}

	private function getManagementWages($month,$dataPara){
		$ui['subject']='-职工薪酬';
		$ui['total']=round($this->getWagesCostSum($month),2);
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		return $ui;
	}

	private function getManagementRent($month,$dataPara){
		$ui['subject']='-房屋租赁费（年租金/12）';
		$map[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
		$map[C('DB_MANAGEMENTFEE_PURPOSE')] = array('eq','rent');
		$ui['total']=M(C('DB_MANAGEMENTFEE'))->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'))+M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->getField(C('DB_SALEFEE_USSWSTORAGEFEE'))*M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USDTORMB'));
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['management_percent']][$value]*$ui['total'],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		return $ui;
	}

	private function getManagementDetailTitle(){
		$ui['subject']='明细如下';
		return $ui;
	}

	private function getManagementPercent($dataPara){
		$ui['subject']='分摊比例（按收入百分比分摊）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['total'],2);
		}
		return $ui;
	}

	private function getManagementTitle(){
		$ui['subject']='管理费用';
		return $ui;
	}

	private function getSaleFeeTotal($dataPara){
		$ui['subject']='销售费用';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['platform_fee']][$value];
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff_sfee']][$value]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['platform_fee']][$value];
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getSaleFeeDetailTitle(){
		$ui['subject']='明细如下：';
		return $ui;
	}

	private function getManagementFee($month){
		$managementFeeTable=M(C('DB_MANAGEMENTFEE'));
		$mmap[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
		$mmap[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq','cooperate');

		$ui['subject']='-管理费用';
		$ui['coSub']=$managementFeeTable->where($mmap)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		$mmap[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq','personal');
		$ui['perSub']=$managementFeeTable->where($mmap)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		$mmap[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq','all');
		$ui['total']=$ui['coSub']+$ui['perSub']+$managementFeeTable->where($mmap)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		return $ui;
	}

	private function getPlatformFee($month){
		$metaDate=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->find();
		$incomeCost=$this->getIncomeCostArray($month);
		$ui['subject']='-平台费用';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round(($incomeCost[$value][C('DB_INCOMECOST_MARKETFEE')]+$incomeCost[$value][C('DB_INCOMECOST_PAYPALFEE')])*$metaDate[C('DB_METADATA_USDTORMB')],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round(($incomeCost[$value][C('DB_INCOMECOST_MARKETFEE')]+$incomeCost[$value][C('DB_INCOMECOST_PAYPALFEE')])*$metaDate[C('DB_METADATA_USDTORMB')],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getTariffShareFee($dataPara){
		$ui['subject']='分摊关税';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff']]['coSub'],2);
		}
		$ui['rc-helicar']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['tariff']]['rc-helicar'];
		$ui['perSub']=$ui['rc-helicar'];
		$ui['total']=$ui['perSub']+$ui['coSub'];
		return $ui;
	}

	private function getTariff($month){
		$metaDate=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->find();
		$saleFee=M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->find();
		$ui['subject']='-关税';
		$ui['coSub']=round($saleFee[C('DB_SALEFEE_USSWTARIFF')]*$metaDate[C('DB_METADATA_USDTORMB')],2);
		$ui['rc-helicar']=round($saleFee[C('DB_SALEFEE_THIRDPARTYTARIFF')]*$metaDate[C('DB_METADATA_USDTORMB')],2);
		$ui['perSub']=$ui['rc-helicar'];
		$ui['total']=$ui['perSub']+$ui['coSub'];
		return $ui;
	}

	private function getPackingMaterialFeeShareFee($dataPara){
		$ui['subject']='分摊包材';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee']]['coSub'],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['packing_fee']]['perSub'],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['perSub']+$ui['coSub'];
		return $ui;
	}

	private function getPackingMaterialFee($month){
		$managementFeeTable=M(C('DB_MANAGEMENTFEE'));
		$map[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
		$map[C('DB_MANAGEMENTFEE_PURPOSE')] = array('eq','packing');

		$ui['subject']='-包材';
		$map[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq','cooperate');
		$ui['coSub']=$managementFeeTable->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		$map[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq','personal');
		$ui['perSub']=$managementFeeTable->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getSaleFeeLocalSfShareFee($dataPara){
		$ui['subject']='分摊本地运费';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf']]['coSub'],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			if($value!='rc-helicar'){
				$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf']]['perSub'],2);
			}
		}
		$ui['rc-helicar']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['local_sf']]['rc-helicar'];
		return $ui;
	}

	private function getSaleFeeLocalSf($month){
		$metaDate=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->find();
		$saleFee=M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->find();
		$ui['subject']='-运费（本地运费）';
		$ui['coSub']=$saleFee[C('DB_SALEFEE_USSWSFLOCAL')]*$metaDate[C('DB_METADATA_USDTORMB')];
		$ui['rc-helicar']=$saleFee[C('DB_SALEFEE_THIRDPARTYSFLOCAL')]*$metaDate[C('DB_METADATA_USDTORMB')];
		$ui['perSub']=$saleFeeTable[C('DB_SALEFEE_SZSWSF')];
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getSaleFeeSharePercent($dataPara){
		$ui['subject']='销售费用分摊比例（按收入百分比分摊）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['coSub'],2);
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']][$value]/$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_rmb']]['perSub'],2);
		}
		return $ui;
	}

	private function getSaleFeeFirstSto($month){
		$ussw_sf_cn=M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->getField(C('DB_SALEFEE_USSWSFCN'));
		$usdToRmb=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$saleFee=M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->find();
		$ui['subject']='-头程运费和仓储费用';
		$ui['coSub']=$ussw_sf_cn;
		$ui['rc-helicar']=($saleFee[C('DB_SALEFEE_THRIDPARTYSFCN')]+$saleFee[C('DB_SALEFEE_THIRDPARTYSTORAGEFEE')])*$usdToRmb;
		$ui['vtkg5755']=$saleFee[C('DB_SALEFEE_SZSWSF')];
		$ui['perSub']=$ui['rc-helicar']+$ui['vtkg5755'];
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getSaleFeeFirstStoShareFee($dataPara){
		$ui['subject']='分摊头仓费用';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['sale_fee_share_percent']][$value]*$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage']]['coSub'],2);
		}
		$ui['rc-helicar']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage']]['rc-helicar'];
		$ui['vtkg5755']=$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['firstsf_storage']]['vtkg5755'];
		return $ui;
	}

	private function getUsdIncome($month){
		$incomeCost=$this->getIncomeCostArray($month);
		$ui['subject']='营业收入（美元）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_USDINCOME')];
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_USDINCOME')];
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getEurIncome($month){
		$incomeCost=$this->getIncomeCostArray($month);
		$ui['subject']='营业收入（欧元）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_EURINCOME')];
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_EURINCOME')];
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getRmbIncome($dataPara){
		$metaDate=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->find();
		$ui['subject']='营业收入（人民币）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_usd']][$value]*$metaDate[C('DB_METADATA_USDTORMB')]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_eur']][$value]*$metaDate[C('DB_METADATA_EURTORMB')],2);
			$ui['coSub']=$ui['coSub']+$ui[$value];			
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=round($dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_usd']][$value]*$metaDate[C('DB_METADATA_USDTORMB')]+$dataPara[C('PROFIT_STATISTIC_SUBJECT_ROW')['income_eur']][$value]*$metaDate[C('DB_METADATA_EURTORMB')],2);
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getRmbItemCost($month){
		$incomeCost=$this->getIncomeCostArray($month);
		$ui['subject']='售出商品采购成本（人民币）';
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_USDITEMCOST')]+$incomeCost[$value][C('DB_INCOMECOST_EURITEMCOST')];
			$ui['coSub']=$ui['coSub']+$ui[$value];
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$ui[$value]=$incomeCost[$value][C('DB_INCOMECOST_USDITEMCOST')]+$incomeCost[$value][C('DB_INCOMECOST_EURITEMCOST')];
			$ui['perSub']=$ui['perSub']+$ui[$value];
		}
		$ui['total']=$ui['coSub']+$ui['perSub'];
		return $ui;
	}

	private function getIncomeCostArray($month){
		$incomeCostTable=M(C('DB_INCOMECOST'));
		foreach (C('COOPERATE_SELLERID') as $key => $value) {
			$map[C('DB_INCOMECOST_MONTH')] = array('eq',$month);
			$map[C('DB_INCOMECOST_SLLERID')] = array('eq',$value);
			$incomeCost[$value]=$incomeCostTable->where($map)->find();
		}
		foreach (C('PERSONAL_SELLERID') as $key => $value) {
			$map[C('DB_INCOMECOST_MONTH')] = array('eq',$month);
			$map[C('DB_INCOMECOST_SLLERID')] = array('eq',$value);
			$incomeCost[$value]=$incomeCostTable->where($map)->find();
		}
		return $incomeCost;
	}

	private function getSaleFeeArray($month){
		return M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->find();
	}

	private function getMShareTypeFeeArray($month){
		$managementFeeTable=M(C('DB_MANAGEMENTFEE'));
		foreach (C('SHARE_TYPE') as $key => $value) {
			$map[C('DB_MANAGEMENTFEE_SHARE_TYPE')] = array('eq',$value);
			$map[C('DB_MANAGEMENTFEE_MONTH')] = array('eq',$month);
			$managementFee[$value] = $managementFeeTable->where($map)->sum(C('DB_MANAGEMENTFEE_AMOUNT'));
		}
		return $managementFee;
	}

	private function getWagesCostSum($month){
		$wageCostSum=0;
		$usdToRmb=M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$wages = M(C('DB_WAGES'))->where(array(C('DB_WAGES_MONTH')=>$month))->select();
		foreach ($wages as $key => $value) {
			$wageCostSum = $wageCostSum+$value[C('DB_WAGES_BASE')]+$value[C('DB_WAGES_PERFORMANCE')]*$value[C('DB_WAGES_PERCENT')]/100*$usdToRmb+$value[C('DB_WAGES_SI_COMPANY')]-$value[C('DB_WAGES_BASE')]/26*$value[C('DB_WAGES_LEAVE_DAYS')]+$value[C('DB_WAGES_BONUS')];
		}
		return $wageCostSum;		
	}

	private function getSzswPurchasingShippingFee($month){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date($month);
		$purchaseingMap[C('DB_PURCHASE_CREATE_DATE')] = array('BETWEEN',date('Y-m-d H:i:s',strtotime($month.'-01 00:00:00')),date('Y-m-d H:i:s',strtotime($month.'-'.$Date->maxDayOfMonth().' 23:59:59')));
		return M(C('DB_PURCHASE'))->where($purchaseingMap)->sum(C('DB_PURCHASE_SHIPPING_FEE'));
	}

	private function updateSaleFeeSzswPShippingFee($month){
		M(C('DB_SALEFEE'))->where(array(C('DB_SALEFEE_MONTH')=>$month))->setField(C('DB_SALEFEE_SZSWSF'),$this->getSzswPurchasingShippingFee($month));
	}

	private function getWarehouse($sellerID,$currency){
		if($sellerID=='rc-helicar' && $currency=='eur'){
			return C('SZSW');
		}elseif($sellerID=='rc-helicar' && $currency=='usd'){
			return C('winit_uswc_warehouse');
		}elseif($sellerID=='y-zhan816'){
			return C('winit_de_warehouse');
		}else{
			return C('USSW');
		}
	}
}

?>