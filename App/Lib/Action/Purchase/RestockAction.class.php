<?php

class RestockAction extends CommonAction{

	public $outOfStock;
	public $indexOfOutOfStock;

	public function index(){
		if($_POST['keyword']==""){
			$map[C('DB_RESTOCK_STATUS')] = array('eq','待发货');
			$wrestock = M(C('DB_RESTOCK'))->where($map)->select();
			$this->assign('rvolume', $this->getRestockedItemsVolume($wrestock));
			$this->assign('rweight', $this->getRestockedItemsWeight($wrestock));
			$map[C('DB_RESTOCK_STATUS')] = array('eq','延迟发货');
			$drestock = M(C('DB_RESTOCK'))->where($map)->select();
			$this->assign('dvolume', $this->getRestockedItemsVolume($drestock));
			$this->assign('dweight', $this->getRestockedItemsWeight($drestock));
			$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
			$restock = M(C('DB_RESTOCK'))->where($map)->select();
			$this->assign('restock',$restock);
			$this->assign('ussw_lock',M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK')));
			$this->assign('winitde_lock',M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK')));
			$this->display();
		}else{
			if($_POST['keyword']=="country"){
				if($_POST['keywordValue']=="美国"){
					$where[C('DB_RESTOCK_WAREHOUSE')]=array('in', '美自建仓,万邑通美西');
					$where[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
					$restock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
					$where[C('DB_RESTOCK_TRANSPORT')] = array('eq','空运');
					$wrestock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
					$this->assign('arvolume', $this->getRestockedItemsVolume($wrestock));
					$this->assign('arweight', $this->getRestockedItemsWeight($wrestock));
					$where[C('DB_RESTOCK_TRANSPORT')] = array('eq','海运');
					$wrestock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
					$this->assign('srvolume', $this->getRestockedItemsVolume($wrestock));
					$this->assign('srweight', $this->getRestockedItemsWeight($wrestock));
					$where1[C('DB_RESTOCK_WAREHOUSE')]=array('in', '美自建仓,万邑通美西');
					$where1[C('DB_RESTOCK_STATUS')] = array('eq','延迟发货');
					$drestock = M(C('DB_RESTOCK'))->where($where1)->select();
					$this->assign('dvolume', $this->getRestockedItemsVolume($drestock));
					$this->assign('dweight', $this->getRestockedItemsWeight($drestock));
					
				}
				elseif($_POST['keywordValue']=="德国"){
					$where[C('DB_RESTOCK_WAREHOUSE')]=array('eq', '万邑通德国');
					$where[C('DB_RESTOCK_STATUS')] = array('eq','待发货');
					$wrestock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
					
					$this->assign('srvolume', $this->getRestockedParcelVolume($wrestock));
					$this->assign('srweight', $this->getRestockedParcelWeight($wrestock));
					$where[C('DB_RESTOCK_STATUS')] = array('eq','延迟发货');
					$drestock = M(C('DB_RESTOCK'))->where($where)->select();
					$this->assign('dvolume', $this->getRestockedParcelVolume($drestock));
					$this->assign('dweight', $this->getRestockedParcelWeight($drestock));
					$where[C('DB_RESTOCK_WAREHOUSE')]=array('eq', '万邑通德国');
					$where[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
					$restock = M(C('DB_RESTOCK'))->order(C('DB_RESTOCK_SKU'))->where($where)->select();
				}
				
			}else{
				$where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
				$restock = M(C('DB_RESTOCK'))->where($where)->select();
				$this->assign('rvolume', $this->getRestockedItemsVolume($restock));
				$this->assign('rweight', $this->getRestockedItemsWeight($restock));
			}
			$this->assign('ussw_lock',M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK')));
			$this->assign('winitde_lock',M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK')));
            $this->assign('restock', $restock);
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->display();
		}
		
	}

	private function getRestockedItemsVolume($restock){
		$product = M(C('DB_PRODUCT'));
		foreach ($restock as $key => $value) {
			$volume = $volume + $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))/1000000*$value[C('DB_RESTOCK_QUANTITY')];
		}
		return $volume;
	}

	private function getRestockedParcelVolume($restock){
		$product = M(C('DB_PRODUCT'));
		foreach ($restock as $key => $value) {
			$volume = $volume + $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))/1000000*$value[C('DB_RESTOCK_QUANTITY')];
		}
		return $volume;
	}

	private function getRestockedItemsWeight($restock){
		$product = M(C('DB_PRODUCT'));
		foreach ($restock as $key => $value) {
			$weight = $weight + $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')];
		}
		return $weight/1000;
	}

	private function getRestockedParcelWeight($restock){
		$product = M(C('DB_PRODUCT'));
		foreach ($restock as $key => $value) {
			$weight = $weight + $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')];
		}
		return $weight/1000;
	}

	public function exportRestock(){
		$xlsName  = "restock";
        $xlsCell  = array(
	        array(C('DB_RESTOCK_ID'),'补货编号'),
	        array(C('DB_RESTOCK_MANAGER'),'产品经理'),
	        array(C('DB_RESTOCK_SKU'),'产品编码'),
	        array('cname','中文名称'),
	        array('battery','带电'),
	        array('position','货位'),
	        array(C('DB_RESTOCK_QUANTITY'),'数量'),
	        array(C('DB_RESTOCK_WAREHOUSE'),'仓库'),
	        array(C('DB_RESTOCK_TRANSPORT'),'运输方式'),
	        array(C('DB_RESTOCK_STATUS'),'状态'),
	        array('pack_requirement','包装要求'),
	        array(C('DB_RESTOCK_REMARK'),'备注')  
	        );
        $xlsModel = M(C('DB_RESTOCK'));
        $product = M(C('DB_PRODUCT'));
        $szstorag = M(C('DB_SZSTORAGE'));
        $ppRequirement = M(C('DB_PRODUCT_PACK_REQUIREMENT'));
    	$map[C('DB_RESTOCK_STATUS')] = array('neq', '已发货');
    	$xlsData  = $xlsModel->where($map)->select();
        foreach ($xlsData as $key => $value) {
        	$p = $product->where(array('sku'=>$value[C('DB_RESTOCK_SKU')]))->find();
        	$xlsData[$key]['cname'] = $p[C('DB_PRODUCT_CNAME')];
        	$xlsData[$key]['battery'] = $p[C('DB_PRODUCT_BATTERY')];
        	$xlsData[$key]['position'] = $szstorag->where(array('sku'=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_SZSTORAGE_POSITION'));
        	$map[C('DB_PRODUCT_PACK_REQUIREMENT_PRODUCT_ID')] = array('eq', $p[C('DB_PRODUCT_ID')]);
        	$map[C('DB_PRODUCT_PACK_REQUIREMENT_WAREHOUSE')] = array('eq', C($value[C('DB_RESTOCK_WAREHOUSE')]));
        	$xlsData[$key]['pack_requirement'] = $ppRequirement->where($map)->getField(C('DB_PRODUCT_PACK_REQUIREMENT_REQUIREMENT'));
        }
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
	}
	
	public function exportOutOfStock(){
        $xlsCell  = array(
	        array('date','日期'),
	        array('sku','产品编码'),
	        array('cname','中文名称'),
	        array('price','单价'),
	        array('quantity','数量'),
	        array('warehouse','仓库'), 
	        array('wayToWarehouse','头程方式'),        
	        array('manager','产品经理'),
	        array('supplier','供货商'),
	        array('purchase_link','采购链接'),
	        array('ainventory','可用库存'),
	        array('iinventory','在途数量'),
	        array('csales','周期销量'),
	        array('rquantity','待发货数量'),
	        array('iquantity','待收货数量'),	 
	        array('sz_ainventory','深圳仓库存'),	 
	        array('pweight','带包装重量g'),	 
	        array('plength','包装长cm'),	 
	        array('pwidth','包装宽cm'),	 
	        array('pheight','包装高cm')

	        );
        $this->exportExcel('OutOfStock',$xlsCell,F('out'));
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
		$this->assign('restockPara',M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find());
		$this->display();
	}

	public function findOutOfStockItem($country){
		if(IS_POST){
			if($country=='美国'){
				$this->findUsOutOfStockItem();
			}elseif($country=='德国'){
				$this->findDeOutOfStockItem();
			}elseif($country=='深圳'){
				$this->findSzswOutOfStockItem();
			}
		}		
	}

	public function findDeOutOfStockItem(){
		$GLOBALS["outOfStock"] = null;
		$GLOBALS["indexOfOutOfStock"] = 0;
		$this->findWinitDeOutOfStockItem();
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock');   
	}

	public function findUsOutOfStockItem($start, $end){
        $GLOBALS["outOfStock"] = null;
		$GLOBALS["indexOfOutOfStock"] = 0;
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$this->findUsswOutOfStockItem($start, $end);
		if($end==-1){
			$this->findUsFbaOutOfStockItemPurhaseView();
		}
		//$this->findWinitUsOutOfStockItem();
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock');
	}

	private function findUsswOutOfStockItem($start, $end){
		if($end==-1){
			$end=M(C('DB_USSTORAGE'))->count();
		}
		$usstorage = M(C('DB_USSTORAGE'))->limit($start, $end)->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$productTable = M(C('db_product'));
		foreach ($usstorage as $ussk => $ussv) {
			$product = $productTable->where(array(C('db_product_sku')=>$ussv[C('DB_USSTORAGE_SKU')]))->find();
			$ussv[C('DB_USSTORAGE_IINVENTORY')]=$this->getUsswIInventory($ussv[C('DB_USSTORAGE_SKU')]);
			if($product[C('db_product_tous')] !== null && $product[C('db_product_tous')] !== '无'){
				if($product[C('db_product_tous')]=='空运'){
					$preCalRQ = $this->preCalRestockQuantity('美自建仓',$ussv[C('DB_USSTORAGE_SKU')]);
					if($preCalRQ==0){
						$msq = 0;
					}elseif ($preCalRQ==-1) {
						$msq = $this->getUsswMads($ussv[C('DB_USSTORAGE_SKU')], $restockPara['ussw_air_ad'], $restockPara['exclude_large_quantity']);
					}else{
						$msq = round($preCalRQ/$restockPara[C('DB_RESTOCK_PARA_NOD')]*$restockPara['ussw_air_ad']);
					}					
					$neededQuantity = $msq/$restockPara['ussw_air_ad']*$restockPara['ussw_air_td'];
					$availableQuantity = $ussv[C('DB_USSTORAGE_AINVENTORY')] + $ussv[C('DB_USSTORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]) + $this->getPurchasedQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]);
					if($msq==0 && ($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]) + $this->getPurchasedQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]))==0){
						$this->addRestockOrder('美自建仓',0,$product,0,0,0);
					}
					if($msq>0 && $neededQuantity>$availableQuantity){
						$q=ceil($neededQuantity-$availableQuantity);
						$this->addRestockOrder('美自建仓',$q,$product,$ussv[C('DB_USSTORAGE_AINVENTORY')],$ussv[C('DB_USSTORAGE_IINVENTORY')],$msq);
					}
				}else{
					$preCalRQ = $this->preCalRestockQuantity('美自建仓',$ussv[C('DB_USSTORAGE_SKU')]);
					if($preCalRQ==0){
						$msq = 0;
					}elseif ($preCalRQ==-1) {
						$msq = $this->getUsswMads($ussv[C('DB_USSTORAGE_SKU')], $restockPara['ussw_sea_ad'],$restockPara['exclude_large_quantity']);
					}else{
						$msq = round($preCalRQ/$restockPara[C('DB_RESTOCK_PARA_NOD')]*$restockPara['ussw_sea_ad']);
					}	
					
					$neededQuantity = $msq/$restockPara['ussw_sea_ad']*$restockPara['ussw_sea_td'];
					$availableQuantity = $ussv[C('DB_USSTORAGE_AINVENTORY')] + $ussv[C('DB_USSTORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]) + $this->getPurchasedQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]);
					if($msq==0 && ($ussv[C('DB_USSTORAGE_AINVENTORY')]+$ussv[C('DB_USSTORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]) + $this->getPurchasedQuantity('美自建仓', $ussv[C('DB_USSTORAGE_SKU')]))==0){
						$this->addRestockOrder('美自建仓',0,$product,0,0,0);
					}
					if($msq>0 && $neededQuantity>$availableQuantity){
						$q=ceil($neededQuantity-$availableQuantity);
						$this->addRestockOrder('美自建仓',$q,$product,$ussv[C('DB_USSTORAGE_AINVENTORY')],$ussv[C('DB_USSTORAGE_IINVENTORY')],$msq);
					}
				}
			}			
		}		
	}

	private function findWinitDeOutOfStockItem(){
		$winitdestorage = M(C('DB_WINIT_DE_STORAGE'))->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$productTable = M(C('DB_PRODUCT'));
		foreach ($winitdestorage as $wdkey => $wdvalue) {
			$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			if($product[C('DB_PRODUCT_TODE')] !=null && $product[C('DB_PRODUCT_TODE')] !='无'){
				if($product[C('DB_PRODUCT_TODE')] == '空运'){
					$preCalRQ = $this->preCalRestockQuantity('万邑通德国',$wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]);
					if($preCalRQ==0){
						$msq = 0;
					}elseif ($preCalRQ==-1) {
						$msq = $this->getWinitMads($wdvalue[C('DB_WINIT_DE_STORAGE_SKU')], 1, $restockPara['winitde_air_ad'], $restockPara['exclude_large_quantity']);
					}else{
						$msq = round($preCalRQ/$restockPara[C('DB_RESTOCK_PARA_NOD')]*$restockPara['winitde_air_ad']);
					}

					if($msq==0){
            			if(($wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')] + $wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')] )==0 && ($this->getRestockQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]) + $this->getPurchasedQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]))==0){
            				$this->addRestockOrder('万邑通德国',0,$product,$wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')],$wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')],0);	
            			}
            		}else{
            			$neededQuantity = $msq/$restockPara['winitde_air_ad']*$restockPara['winitde_air_td'];
            			$availableQuantity = $wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')]  + $wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')]  + $this->getRestockQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]) + $this->getPurchasedQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]);
            			if($neededQuantity>$availableQuantity){
            				$this->addRestockOrder('万邑通德国',ceil($neededQuantity-$availableQuantity),$product,$wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')],$wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')],$msq);
            			}
            		}
				}else{
					$preCalRQ = $this->preCalRestockQuantity('万邑通德国',$wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]);
					if($preCalRQ==0){
						$msq = 0;
					}elseif ($preCalRQ==-1) {
						$msq = $this->getWinitMads($wdvalue[C('DB_WINIT_DE_STORAGE_SKU')], 1, $restockPara['winitde_sea_ad'], $restockPara['exclude_large_quantity']);
					}else{
						$msq = round($preCalRQ/$restockPara[C('DB_RESTOCK_PARA_NOD')]*$restockPara['winitde_sea_ad']);
					}

					if($msq==0){
            			if(($wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')] + $wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')])==0 && ($this->getRestockQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]) + $this->getPurchasedQuantity('万邑通德国',$wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]))==0){
            				$this->addRestockOrder('万邑通德国',0,$product,$wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')],$wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')],0);	
            			}
            		}else{
            			$neededQuantity = $msq/$restockPara['winitde_sea_ad']*$restockPara['winitde_sea_td'];
            			$availableQuantity = $wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')] + $wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')] + $this->getRestockQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]) + $this->getPurchasedQuantity('万邑通德国', $wdvalue[C('DB_WINIT_DE_STORAGE_SKU')]);
            			if($neededQuantity>$availableQuantity){
            				$this->addRestockOrder('万邑通德国',ceil($neededQuantity-$availableQuantity),$product,$wdvalue[C('DB_WINIT_DE_STORAGE_AINVENTORY')],$wdvalue[C('DB_WINIT_DE_STORAGE_IINVENTORY')],$msq);
            			}
            		}
				}
			}
		}
	}

	private function findWinitUsOutOfStockItem(){
		$winitusstorage = M(C('DB_WINIT_US_STORAGE'))->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$productTable = M(C('DB_PRODUCT'));
		foreach ($winitusstorage as $wukey => $wuvalue) {
			$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$wuvalue[C('DB_WINIT_US_STORAGE_SKU')]))->find();
    		if($product[C('DB_PRODUCT_TOUS')] != null && $product[C('DB_PRODUCT_TOUS')] != '无' && !$this->hasMovedToUSSW(0,$wuvalue[C('DB_WINIT_US_STORAGE_SKU')])){
    			if($wuvalue[C('DB_WINIT_US_STORAGE_AINVENTORY')]==0){
    				$this->addRestockOrder('美自建仓',0,$product,$wuvalue[C('DB_WINIT_US_STORAGE_AINVENTORY')],$wuvalue[C('DB_WINIT_US_STORAGE_IINVENTORY')],0);	
    			}
    		}
		}		
	}

	public function findSzswOutOfStockItem(){
		$GLOBALS["outOfStock"] = null;
		$GLOBALS["indexOfOutOfStock"] = 0;
		$szstorage = M(C('DB_SZSTORAGE'))->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		foreach ($szstorage as $szsk => $szsv) {
			$product = M(C('db_product'))->where(array(C('db_product_sku')=>$szsv[C('DB_SZSTORAGE_SKU')]))->find();
			if($product!=null && ($product[C('DB_PRODUCT_TOUS')]!=='无' || $product[C('DB_PRODUCT_TODE')]!=='无')){
				$msq = $this->getSzsw30DaysSales($szsv[C('DB_SZSTORAGE_SKU')]);
				if($msq != null){
					$dayAvailableForSale=($szsv[C('DB_SZSTORAGE_AINVENTORY')]+$this->getSzIinventory($szsv[C('DB_SZSTORAGE_SKU')]))/($msq/30);
					if($dayAvailableForSale<$restockPara['szsw_ad']){
						$q = ($restockPara['szsw_ad']-$dayAvailableForSale)*($msq/30);
						$this->addRestockOrder('深圳仓',$q,$product,$szsv[C('DB_SZSTORAGE_AINVENTORY')],$this->getSzIinventory($szsv[C('DB_SZSTORAGE_SKU')]),$msq);
					}
				}				
			}			
		}
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock'); 
	}

	public function findUsFBAOutofStockItem(){
		$GLOBALS["outOfStock"] = null;
		$GLOBALS["indexOfOutOfStock"] = 0;
		$fbastorage = M(C('DB_AMAZON_US_STORAGE'))->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		foreach ($fbastorage as $fbask => $fbasv) {
			$product = M(C('db_product'))->where(array(C('db_product_sku')=>$this->fbaSkuToStandardSku($fbasv[C('DB_AMAZON_US_STORAGE_SKU')])))->find();
			if($product!=null && ($product[C('DB_PRODUCT_TOUS')]!=='无')){
				$msq = $this->getFBADaysSaleQuantity($fbasv[C('DB_AMAZON_US_STORAGE_SKU')],$restockPara['ussw_air_ad'],$restockPara['exclude_large_quantity']);
				if(($fbasv[C('DB_AMAZON_US_STORAGE_IINVENTORY')]+$fbasv[C('DB_AMAZON_US_STORAGE_AINVENTORY')])<$msq){
					$this->addRestockOrder('美国FBA',$msq-$fbasv[C('DB_AMAZON_US_STORAGE_AINVENTORY')]-$fbasv[C('DB_AMAZON_US_STORAGE_IINVENTORY')],$product,$fbasv[C('DB_AMAZON_US_STORAGE_AINVENTORY')],0,$msq);
				}
			}
		}
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock'); 
	}

	private function findUsFbaOutOfStockItemPurhaseView(){
		$usFbastorage = M(C('DB_AMAZON_US_STORAGE'))->select();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$productTable = M(C('db_product'));
		$usStorageTable = M(C('DB_USSTORAGE'));
		$usStorageAction = A('Ussw/Storage');
		foreach ($usFbastorage as $ussk => $ussv) {
			$product = $productTable->where(array(C('db_product_sku')=>$this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])))->find();
			$usstorage = $usStorageTable->where(array(C('DB_USSTORAGE_SKU')=>$this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])))->find();
			if($product[C('db_product_tous')] !== null && $product[C('db_product_tous')] !== '无'){
				$preCalRQ = $this->preCalRestockQuantity('美国FBA',$ussv[C('DB_AMAZON_US_STORAGE_SKU')]);
				if($preCalRQ==0){
					$msq = 0;
				}elseif ($preCalRQ==-1) {
					$msq = $this->getFBADaysSaleQuantity($ussv[C('DB_AMAZON_US_STORAGE_SKU')], $restockPara['ussw_sea_ad'], $restockPara['exclude_large_quantity']);
				}else{
					$msq = round($preCalRQ/$restockPara[C('DB_RESTOCK_PARA_NOD')]*$restockPara['ussw_sea_ad']);
				}					
				$neededQuantity = $msq/$restockPara['ussw_sea_ad']*$restockPara['ussw_sea_td'];
				$availableQuantity = $ussv[C('DB_AMAZON_US_STORAGE_AINVENTORY')] + $ussv[C('DB_AMAZON_US_STORAGE_IINVENTORY')] + $usstorage[C('DB_USSTORAGE_AINVENTORY')] + $usStorageAction->getIInventory($this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) + $this->getRestockQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) + $this->getPurchasedQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) - $this->getUsswMads($this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')]), $restockPara['ussw_sea_ad'],$restockPara['exclude_large_quantity']);
				if($msq==0 && ($ussv[C('DB_AMAZON_US_STORAGE_AINVENTORY')]+$ussv[C('DB_AMAZON_US_STORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) + $this->getPurchasedQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])))==0){
					$this->addRestockOrder('美国FBA',0,$product,0,0,0);
				}
				if($msq>0 && $neededQuantity>$availableQuantity){
					$q=ceil($neededQuantity-$availableQuantity);
					$this->addRestockOrder('美国FBA',$q,$product,$ussv[C('DB_AMAZON_US_STORAGE_AINVENTORY')],$ussv[C('DB_AMAZON_US_STORAGE_IINVENTORY')],$msq);
				}
			}			
		}		
	}

	/**
	 * Prepare Calculate the restock quantity. 
	 * If the available quantity is not zero and the order quantity is zero within no_order_days in restock parameters, then
	 * this item needn't to restock.
	 * And calculate 0-7days, 8-15days, 16-23days, 24-31days sold quantity. If the quantitys are decremented, then the restock * quantity of this item should be calculated according to the minimum value.
	 * @param warehouse
	 * @param sku
	 * @return int or -1, 0=needn't restock, int=last 7 days sale quantity exclude large order quantity, -1=no result need 
	 * to be calculated more.
	*/
	private function preCalRestockQuantity($warehouse,$sku){
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		if($warehouse=='美自建仓'){
			$ainventory = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->getField(C('DB_USSTORAGE_AINVENTORY'));
			$sq1 = $this->getUsswMads($sku,$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
			$sq2 = $this->getUsswMads($sku,2*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1;
			$sq3 = $this->getUsswMads($sku,3*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2;
			$sq4 = $this->getUsswMads($sku,4*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2-$sq3;
		}elseif($warehouse=='万邑通德国'){
			$ainventory = M(C('DB_WINIT_DE_STORAGE'))->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$sku))->getField(C('DB_WINIT_DE_STORAGE_AINVENTORY'));
			$sq1 = $this->getWinitDaysSaleQuantity($sku,1,$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
			$sq2 = $this->getWinitDaysSaleQuantity($sku,1,2*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1;
			$sq3 = $this->getWinitDaysSaleQuantity($sku,1,3*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2;
			$sq4 = $this->getWinitDaysSaleQuantity($sku,1,4*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2-$sq3;
		}elseif($warehouse=='美国FBA'){
			$ainventory = M(C('DB_AMAZON_US_STORAGE'))->where(array(C('DB_AMAZON_US_STORAGE_SKU')=>$sku))->getField(C('DB_AMAZON_US_STORAGE_AINVENTORY'));
			$sq1 = $this->getFBADaysSaleQuantity($sku,$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
			$sq2 = $this->getFBADaysSaleQuantity($sku,2*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1;
			$sq3 = $this->getFBADaysSaleQuantity($sku,3*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2;
			$sq4 = $this->getFBADaysSaleQuantity($sku,4*$restockPara[C('DB_RESTOCK_PARA_NOD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')])-$sq1-$sq2-$sq3;
		}else{
			$this->error('仓库 '.$warehouse.' 无法识别');
		}
		if($ainventory>0 && $sq1==0){
			return 0;
		}elseif($ainventory>0 && $sq4>=$sq3 && $sq3>=$sq2 && $sq2>=$sq1){
			return $sq1;
		}else{
			return -1;
		}
	}

	public function editRestockPara(){
		$this->assign('restockPara', M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find());
		$this->display();
	}

	public function editRestockParaHandle(){
		M(C('DB_RESTOCK_PARA'))->save($_POST);
		$this->success('已保存');
	}

	public function lockUsswRestockTable(){
		M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->setField(C('DB_RESTOCK_PARA_USSW_LOCK'),1);
		$this->success('美自建仓补货表已锁定');
	}

	public function unlockUsswRestockTable(){
		M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->setField(C('DB_RESTOCK_PARA_USSW_LOCK'),0);
		$this->success('美自建仓补货表已解锁');
	}

	public function lockWinitdeRestockTable(){
		M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->setField(C('DB_RESTOCK_PARA_WINITDE_LOCK'),1);
		$this->success('万邑通德国补货表已锁定');
	}

	public function unlockWinitdeRestockTable(){
		M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->setField(C('DB_RESTOCK_PARA_WINITDE_LOCK'),0);
		$this->success('万邑通德国补货表已解锁');
	}

	public function precalUsswRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK'))==1){
			$this->error('美自建仓补货表被锁定,无法计算');
		}else{
			$restockTable = M(C('DB_RESTOCK'));
			$usstorageTable = M(C('DB_USSTORAGE'));
			$product = M(C('DB_PRODUCT'));
			$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
			$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq','美自建仓');
			$map[C('DB_RESTOCK_TRANSPORT')] = array('eq','空运');
			$data = $restockTable->where($map)->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
			foreach ($data as $key => $value) {
				if($value[C('DB_RESTOCK_TRANSPORT')] == '空运'){
					$msq = $this->getUsswMads($value[C('DB_RESTOCK_SKU')], $restockPara['ussw_air_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'美自建仓',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
					$iinventoryQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_IINVENTORY'));
					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*ceil($msq)/1000;
							$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*ceil($msq)/1000000;
						}else{
							$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
							$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
						$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
					}else{
						if($msq>0 && $days>$restockPara['ussw_air_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>$msq){
								$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*ceil($msq)/1000;
								$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*ceil($msq)/1000000;
							}else{
								$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
								$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
							}
						}
					}
										
				}else{
					$msq = $this->getUsswMads($value[C('DB_RESTOCK_SKU')], $restockPara['ussw_sea_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'美自建仓',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
					$iinventoryQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_IINVENTORY'));

					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*ceil($msq)/1000;
							$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*ceil($msq)/1000000;
						}else{
							$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
							$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
						$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
					}else{
						if($msq>0 && $days>$restockPara['ussw_sea_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*ceil($msq)/1000;
								$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*ceil($msq)/1000000;
							}else{
								$usswWeight = $usswWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
								$usswVolumen = $usswVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_LENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_WIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_HEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
							}
						}
					}					
				}
			}
			$this->assign('usswWeight',$usswWeight);
			$this->assign('usswVolumen',$usswVolumen);
			$this->display();
		}
	}

	/*
	算法目的：从待发货商品里找出那些需要发快递的商品计算出快递待发货的体积和重量

	需要发快递的货物需要具备以下条件 ：新产品 和 快要缺货的空运头程产品

	算法判断
	1. 产品是空运头程试算，首次刊登时间小于两个月并且产品采购次数小于3次视为新产品，所有待发货产品发空运。
	2. 产品是空运头程试算，仓库可用库存可售天数小于海运预估到仓时间，计算出能坚持到海运到仓的数量，发空运。
	3. 产品是空运头程试算，可用加在途库存等于0. 补30天的销量过去。
	*/

	public function precalUsswRestockTableNew($setFirstWay=false){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK'))==1 && $setFirstWay==true){
			$this->error('美自建仓补货表被锁定,无法计算');
		}else{
			if($setFirstWay==true){
				$this->resetUsswRestockTableHandle();
			}	
			$restockTable = M(C('DB_RESTOCK'));
			$usstorageTable = M(C('DB_USSTORAGE'));
			$product = M(C('DB_PRODUCT'));
			$usswSalePlan = M(C('DB_USSW_SALE_PLAN'));
			
			$purchase = D('PurchaseView');
			$map[C('DB_RESTOCK_STATUS')] = array('in',array('待发货','延迟发货'));
			$map[C('DB_RESTOCK_WAREHOUSE')] = array('in',array('美自建仓','万邑通美西'));
			$data = $restockTable->where($map)->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();

			$changeToAirShipping=array();
			foreach ($data as $key => $value) {
				$p = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->find();
				$usswFirstSaleDate = $usswSalePlan->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));
				if($usswFirstSaleDate==null){
					$daysFirstSale=0;
				}else{
					$cntFirstSale=time()-strtotime($usswFirstSaleDate);//与已知时间的差值
					$daysFirstSale = ceil($cntFirstSale/(3600*24));//算出天数
				}				
				$purchaseMap[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$value[C('DB_RESTOCK_SKU')]);
				$purchaseMap[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('in',array('美自建仓','万邑通美西'));
				$purchaseMap[C('DB_PURCHASE_STATUS')] = array('in',array('部分到货','全部到货'));
				$purchaseCount = $purchase->where($purchaseMap)->count();
				
				if(($purchaseCount>$restockPara[C('DB_RESTOCK_PARA_USSW_AFCL')] || $daysFirstSale>$restockPara[C('DB_RESTOCK_PARA_USSW_AFDL')]) && $p[C('DB_PRODUCT_TOUS')]=='空运'){
					
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$msq = $this->getUsswMads($value[C('DB_RESTOCK_SKU')],6,$restockPara[C('DB_RESTOCK_PARA_ELQ')])/6;
						$ainventory = $usstorageTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'))+$this->getUsswInboundAirIInventory($value[C('DB_RESTOCK_SKU')]);
						if($ainventory<=0){
							$airQuantity=$this->getUsswMads($value[C('DB_RESTOCK_SKU')],30,$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
							if($airQuantity<$value[C('DB_RESTOCK_QUANTITY')]){
								$airQuantity = $value[C('DB_RESTOCK_QUANTITY')]-$airQuantity;
								$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
								$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
							}
							$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$airQuantity;
							$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$airQuantity;
							$value['change_to_air_quantity'] = $airQuantity;
							if(!$this->isSkuChangedToAir($value,$changeToAirShipping) && $airQuantity>0){
								array_push($changeToAirShipping, $value);
							}
						}else{
							if($msq==0){
								$ainventorySaleDays = 65536;
							}else{
								$ainventorySaleDays = ceil($ainventory/$msq);
							}						
							if($this->getUsswInboundSeaShippingDate($value[C('DB_RESTOCK_SKU')])==null){
								$seaEstimatedArriveDays=$restockPara[C('DB_RESTOCK_PARA_USSW_EDSS')];//与已知时间的差值
							}else{
								$cntSeaShippingTimes=time()-strtotime($this->getUsswInboundSeaShippingDate($value[C('DB_RESTOCK_SKU')]));//与已知时间的差值
								$seaEstimatedArriveDays = ceil($restockPara[C('DB_RESTOCK_PARA_USSW_EDSS')]-($cntSeaShippingTimes/(3600*24)));//算出天数
							}
							if($seaEstimatedArriveDays>0 && $ainventorySaleDays<$seaEstimatedArriveDays){
								if(intval(($seaEstimatedArriveDays-$ainventorySaleDays)*$msq)<$value[C('DB_RESTOCK_QUANTITY')]){
									$airQuantity = intval(($seaEstimatedArriveDays-$ainventorySaleDays)*$msq);
									$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
									$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
								}else{
									$airQuantity = $value[C('DB_RESTOCK_QUANTITY')];
								}							
								$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$airQuantity;
								$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$airQuantity;
								$value['change_to_air_quantity'] = $airQuantity;
								if(!$this->isSkuChangedToAir($value,$changeToAirShipping) && $airQuantity>0){
									array_push($changeToAirShipping, $value);
								}							
							}elseif($seaEstimatedArriveDays<0){
								$this->error('海运预估到仓天数减去 商品编码: '.$value[C('DB_RESTOCK_SKU')].'的最早一批海运在途已发出天数为负数，无法计算空运补货数量！可以通过更改海运预估到仓天数重新计算','',15);
							}
						}
						
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}					
				}elseif($purchaseCount<=$restockPara[C('DB_RESTOCK_PARA_USSW_AFCL')] && $daysFirstSale<=$restockPara[C('DB_RESTOCK_PARA_USSW_AFDL')] && $p[C('DB_PRODUCT_TOUS')]=='空运'){
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$value[C('DB_RESTOCK_QUANTITY')];
						$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$value[C('DB_RESTOCK_QUANTITY')];
						$value['change_to_air_quantity'] = $value[C('DB_RESTOCK_QUANTITY')];
						if(!$this->isSkuChangedToAir($value,$changeToAirShipping)){
							array_push($changeToAirShipping, $value);
						}						
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}					
				}else{
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$value[C('DB_RESTOCK_QUANTITY')];
						$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$value[C('DB_RESTOCK_QUANTITY')];
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}	
				}
			}

			if($setFirstWay==true && $changeToAirShipping!=null){
				foreach ($changeToAirShipping as $key => $cvalue) {
					if($cvalue[C('DB_RESTOCK_QUANTITY')]>$cvalue['change_to_air_quantity']){
						$newRestock[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
						$newRestock[C('DB_RESTOCK_MANAGER')] = $cvalue[C('DB_RESTOCK_MANAGER')];
						$newRestock[C('DB_RESTOCK_SKU')] = $cvalue[C('DB_RESTOCK_SKU')];
						$newRestock[C('DB_RESTOCK_QUANTITY')] = $cvalue['change_to_air_quantity'];
						$newRestock[C('DB_RESTOCK_WAREHOUSE')] = '美自建仓';
						$newRestock[C('DB_RESTOCK_TRANSPORT')] = '空运';
						$newRestock[C('DB_RESTOCK_STATUS')] = '待发货';
						$newRestock[C('DB_RESTOCK_REMARK')] = '自动计算空运待发货，从补货单号 '.$cvalue[C('DB_RESTOCK_ID')]. '里分出来的补货单';
						if($restockTable->add($newRestock)!=false){
							$cvalue[C('DB_RESTOCK_QUANTITY')] = $cvalue[C('DB_RESTOCK_QUANTITY')]-$cvalue['change_to_air_quantity'];
							$restockTable->save($cvalue);
						}						

					}else{
						$cvalue[C('DB_RESTOCK_WAREHOUSE')] = '美自建仓';
						$cvalue[C('DB_RESTOCK_TRANSPORT')] = '空运';
						$cvalue[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($cvalue);
					}					
				}
			}

			if($setFirstWay==true){
				$this->assign('arweight',$airweight);
				$this->assign('arvolume',$airvolume);
				$this->assign('srweight',$seaweight);
				$this->assign('srvolume',$seavolume);
				$this->display('index');
			}else{
				$this->assign('airweight',$airweight);
				$this->assign('airvolume',$airvolume);
				$this->assign('seaweight',$seaweight);
				$this->assign('seavolume',$seavolume);
				$this->assign('changeToAir',$changeToAirShipping);
				$this->display();
			}			
		}
	}

	private function blockedItemInUssw($sku){
		$banned = true;
		foreach ($this->getUsswSalePlanTableNames() as $key => $value) {
			$salePlan=M($value);
			if($salePlan->where(array('sku'=>$sku))->getField('sale_status')==1){
				$banned = $banned && true;
			}else{
				$banned = $banned && false;
			}
		}
		return $banned;
	}

	private function blockedItemInWinitDe($sku){
		$banned = true;
		foreach ($this->getWinitDeSalePlanTableNames() as $key => $value) {
			$salePlan=M($value);
			if($salePlan->where(array('sku'=>$sku))->getField('sale_status')==1){
				$banned = $banned && true;
			}else{
				$banned = $banned && false;
			}
		}
		return $banned;
	}

	private function isSkuChangedToAir($new,$changeToAir){
		foreach ($changeToAir as $key => $value) {
			if($new[C('DB_RESTOCK_SKU')]==$value[C('DB_RESTOCK_SKU')] && $new[C('DB_RESTOCK_WAREHOUSE')]==$value[C('DB_RESTOCK_WAREHOUSE')]){
				return true;
			}
		}
		return false;
	}

	private function getUsswInboundAirIInventory($sku){
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
		$map[C('DB_USSW_INBOUND_SHIPPING_WAY')] = array('eq','空运');
		return D('UsswInboundView')->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
	}

	private function getUsswInboundSeaIInventory($sku){
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
		$map[C('DB_USSW_INBOUND_SHIPPING_WAY')] = array('eq','海运');
		return D('UsswInboundView')->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
	}

	private function getUsswInboundSeaShippingDate($sku){
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		$map[C('DB_USSW_INBOUND_STATUS')] = array('eq','待入库');
		$map[C('DB_USSW_INBOUND_SHIPPING_WAY')] = array('eq','海运');
		return D('UsswInboundView')->where($map)->limit(1)->getField(C('DB_USSW_INBOUND_DATE'));
	}

	public function updateUsswRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK'))==1){
			$this->error('美自建仓补货表被锁定,无法计算');
		}else{
			$this->resetUsswRestockTableHandle();
			$restockTable = M(C('DB_RESTOCK'));
			$usstorageTable = M(C('DB_USSTORAGE'));
			$restockTable->startTrans();
			$data = $restockTable->where(array(C('DB_RESTOCK_STATUS')=>'延迟发货',C('DB_RESTOCK_WAREHOUSE')=>'美自建仓'))->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
			foreach ($data as $key => $value) {
				if($value[C('DB_RESTOCK_TRANSPORT')] == '空运'){
					$msq = $this->getUsswMads($value[C('DB_RESTOCK_SKU')], $restockPara['ussw_air_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'美自建仓',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
					$iinventoryQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_IINVENTORY'));
					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
							$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
							$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
							$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
							$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
							$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
							$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
							$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
							$restockTable->add($tmp);
							$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
							$restockTable->save($value);
						}else{
							$value[C('DB_RESTOCK_STATUS')] = '待发货';
							$restockTable->save($value);
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$value[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($value);
					}else{
						if($msq>0 && $days>$restockPara['ussw_air_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
								$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
								$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
								$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
								$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
								$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
								$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
								$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
								$restockTable->add($tmp);
								$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
								$restockTable->save($value);
							}else{
								$value[C('DB_RESTOCK_STATUS')] = '待发货';
								$restockTable->save($value);
							}
						}
					}
										
				}else{
					$msq = $this->getUsswMads($value[C('DB_RESTOCK_SKU')], $restockPara['ussw_sea_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'美自建仓',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
					$iinventoryQuantity = $usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_IINVENTORY'));

					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
							$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
							$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
							$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
							$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
							$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
							$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
							$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
							$restockTable->add($tmp);
							$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
							$restockTable->save($value);
						}else{
							$value[C('DB_RESTOCK_STATUS')] = '待发货';
							$restockTable->save($value);
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$value[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($value);
					}else{
						if($msq>0 && $days>$restockPara['ussw_sea_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>$msq){
								$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
								$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
								$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
								$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
								$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
								$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
								$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
								$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
								$restockTable->add($tmp);
								$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
								$restockTable->save($value);
							}else{
								$value[C('DB_RESTOCK_STATUS')] = '待发货';
								$restockTable->save($value);
							}
						}
					}					
				}
			}
			$this->success('已更新美自建仓补货表');
		}
	}

	public function resetUsswRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK'))==1){
			$this->error('美自建仓补货表被锁定,无法计算');
		}else{
			$this->resetUsswRestockTableHandle();
			$this->success('已重置美自建仓补货表');
		}
	}

	private function resetUsswRestockTableHandle(){
		$restockTable = M(C('DB_RESTOCK'));
		$restockTable->startTrans();
		$map[C('DB_RESTOCK_STATUS')] = array('eq','待发货');
		$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq','美自建仓');
		$data = $restockTable->where($map)->select();
		foreach ($data as $key => $value) {
			$delayShipping = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>$value[C('DB_RESTOCK_WAREHOUSE')],C('DB_RESTOCK_STATUS')=>'延迟发货'))->select();
			if($delayShipping!==false && $delayShipping!==null){
				if(count($delayShipping)>1){
					for ($i=1; $i < count($delayShipping); $i++){
						$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$delayShipping[i][C('DB_RESTOCK_QUANTITY')];
						$restockTable->where(array(C('DB_RESTOCK_ID')=>$delayShipping[i][C('DB_RESTOCK_ID')]))->delete();
					}
					$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$value[C('DB_RESTOCK_QUANTITY')];
					$restockTable->save($delayShipping[0]);
					$restockTable->where(array(C('DB_RESTOCK_ID')=>$value[C('DB_RESTOCK_ID')]))->delete();
				}else{
					$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$value[C('DB_RESTOCK_QUANTITY')];
					$restockTable->save($delayShipping[0]);
					$restockTable->where(array(C('DB_RESTOCK_ID')=>$value[C('DB_RESTOCK_ID')]))->delete();
				}
				
			}else{
				$value[C('DB_RESTOCK_STATUS')] = '延迟发货';
				$restockTable->save($value);
			}			
		}
		$restockTable->commit();
	}

	/*
	算法目的：从待发货商品里找出那些需要发快递的商品计算出快递待发货的体积和重量

	需要发快递的货物需要具备以下条件 ：新产品 和 快要缺货的空运头程产品

	算法判断
	1. 产品是空运头程试算，首次刊登时间小于两个月并且产品采购次数小于3次视为新产品，所有待发货产品发空运。
	2. 产品是空运头程试算，仓库可用库存可售天数小于海运预估到仓时间，计算出能坚持到海运到仓的数量，发空运。
	*/

	public function precalWinitdeRestockTableNew($setFirstWay=false){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK'))==1 && $setFirstWay==true){
			$this->error('万邑通德国补货表被锁定,无法计算');
		}else{
			if($setFirstWay==true){
				$this->resetWinitDeRestockTableHandle();
			}	
			$restockTable = M(C('DB_RESTOCK'));
			$winitdestorageTable = M(C('DB_WINIT_DE_STORAGE'));
			$product = M(C('DB_PRODUCT'));
			$salePlan = M(C('DB_YZHAN_816_PL_SALE_PLAN'));
			
			$purchase = D('PurchaseView');
			$map[C('DB_RESTOCK_STATUS')] = array('in',array('待发货','延迟发货'));
			$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq','万邑通德国');
			$data = $restockTable->where($map)->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();

			$changeToAirShipping=array();
			foreach ($data as $key => $value) {
				$p = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->find();
				$winitFirstSaleDate = $salePlan->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));
				if($winitFirstSaleDate==null){
					$daysFirstSale=0;
				}else{
					$cntFirstSale=time()-strtotime($winitFirstSaleDate);//与已知时间的差值
					$daysFirstSale = ceil($cntFirstSale/(3600*24));//算出天数
				}				
				$purchaseMap[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$value[C('DB_RESTOCK_SKU')]);
				$purchaseMap[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('eq','万邑通德国');
				$purchaseMap[C('DB_PURCHASE_STATUS')] = array('in',array('部分到货','全部到货'));
				$purchaseCount = $purchase->where($purchaseMap)->count();
				
				if(($purchaseCount>$restockPara[C('DB_RESTOCK_PARA_WINITDE_AFCL')] || $daysFirstSale>$restockPara[C('DB_RESTOCK_PARA_WINITDE_AFDL')]) && $p[C('DB_PRODUCT_TODE')]=='空运'){
					
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$msq = $this->getWinitMads($value[C('DB_RESTOCK_SKU')],1,6,$restockPara[C('DB_RESTOCK_PARA_ELQ')])/6;
						$ainventory = $winitdestorageTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
						if($ainventory<=0){
							$airQuantity=$this->getWinitMads($value[C('DB_RESTOCK_SKU')],1,30,$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
							if($airQuantity<$value[C('DB_RESTOCK_QUANTITY')]){
								$airQuantity = $value[C('DB_RESTOCK_QUANTITY')]-$airQuantity;
								$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
								$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
							}
							$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$airQuantity;
							$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$airQuantity;
							$value['change_to_air_quantity'] = $airQuantity;
							if(!$this->isSkuChangedToAir($value,$changeToAirShipping) && $airQuantity>0){
								array_push($changeToAirShipping, $value);
							}
						}else{
							if($msq==0){
								$ainventorySaleDays = 65536;
							}else{
								$ainventorySaleDays = ceil($ainventory/$msq);
							}						
							$seaEstimatedArriveDays=30;
							if($seaEstimatedArriveDays>0 && $ainventorySaleDays<$seaEstimatedArriveDays){
								if(intval(($seaEstimatedArriveDays-$ainventorySaleDays)*$msq)<$value[C('DB_RESTOCK_QUANTITY')]){
									$airQuantity = intval(($seaEstimatedArriveDays-$ainventorySaleDays)*$msq);
									$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
									$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*($value[C('DB_RESTOCK_QUANTITY')]-$airQuantity);
								}else{
									$airQuantity = $value[C('DB_RESTOCK_QUANTITY')];
								}							
								$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$airQuantity;
								$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$airQuantity;
								$value['change_to_air_quantity'] = $airQuantity;
								if(!$this->isSkuChangedToAir($value,$changeToAirShipping) && $airQuantity>0){
									array_push($changeToAirShipping, $value);
								}
							}elseif($seaEstimatedArriveDays<0){
								$this->error('海运预估到仓天数减去 商品编码: '.$value[C('DB_RESTOCK_SKU')].'的最早一批海运在途已发出天数为负数，无法计算空运补货数量！可以通过更改海运预估到仓天数重新计算','',15);
							}
						}
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}					
				}elseif($purchaseCount<=$restockPara[C('DB_RESTOCK_PARA_WINITDE_AFCL')] && $daysFirstSale<=$restockPara[C('DB_RESTOCK_PARA_WINIT_AFDL')] && $p[C('DB_PRODUCT_TODE')]=='空运'){
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$airweight = $airweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$value[C('DB_RESTOCK_QUANTITY')];
						$airvolume = $airvolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$value[C('DB_RESTOCK_QUANTITY')];
						$value['change_to_air_quantity'] = $value[C('DB_RESTOCK_QUANTITY')];
						if(!$this->isSkuChangedToAir($value,$changeToAirShipping)){
							array_push($changeToAirShipping, $value);
						}
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}					
				}else{
					if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
						$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]/1000*$value[C('DB_RESTOCK_QUANTITY')];
						$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$value[C('DB_RESTOCK_QUANTITY')];
					}else{
						$this->error('无法计算，产品 '.$value[C('DB_RESTOCK_SKU')].' 包装信息缺失');
					}	
				}
			}

			if($setFirstWay==true && $changeToAirShipping!=null){
				foreach ($changeToAirShipping as $key => $cvalue) {
					if($cvalue[C('DB_RESTOCK_QUANTITY')]>$cvalue['change_to_air_quantity']){
						$newRestock[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
						$newRestock[C('DB_RESTOCK_MANAGER')] = $cvalue[C('DB_RESTOCK_MANAGER')];
						$newRestock[C('DB_RESTOCK_SKU')] = $cvalue[C('DB_RESTOCK_SKU')];
						$newRestock[C('DB_RESTOCK_QUANTITY')] = $cvalue['change_to_air_quantity'];
						$newRestock[C('DB_RESTOCK_WAREHOUSE')] = '万邑通德国';
						$newRestock[C('DB_RESTOCK_TRANSPORT')] = '空运';
						$newRestock[C('DB_RESTOCK_STATUS')] = '待发货';
						$newRestock[C('DB_RESTOCK_REMARK')] = '自动计算空运待发货，从补货单号 '.$cvalue[C('DB_RESTOCK_ID')]. '里分出来的补货单';
						if($restockTable->add($newRestock)!=false){
							$cvalue[C('DB_RESTOCK_QUANTITY')] = $cvalue[C('DB_RESTOCK_QUANTITY')]-$cvalue['change_to_air_quantity'];
							$restockTable->save($cvalue);
						}						

					}else{
						$cvalue[C('DB_RESTOCK_WAREHOUSE')] = '万邑通德国';
						$cvalue[C('DB_RESTOCK_TRANSPORT')] = '空运';
						$cvalue[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($cvalue);
					}					
				}
			}

			if($setFirstWay==true){
				$this->assign('arweight',$airweight);
				$this->assign('arvolume',$airvolume);
				$this->assign('srweight',$seaweight);
				$this->assign('srvolume',$seavolume);
				$this->display('index');
			}else{
				$this->assign('airweight',$airweight);
				$this->assign('airvolume',$airvolume);
				$this->assign('seaweight',$seaweight);
				$this->assign('seavolume',$seavolume);
				$this->assign('changeToAir',$changeToAirShipping);
				$this->display();
			}			
		}
	}

	public function precalWinitdeRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK'))==1){
			$this->error('万邑通德国补货表被锁定,无法计算');
		}else{
			$restockTable = M(C('DB_RESTOCK'));
			$winitdestorageTable = M(C('DB_WINIT_DE_STORAGE'));
			$product = M(C('DB_PRODUCT'));
			$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
			$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq','万邑通德国');
			$data = $restockTable->where($map)->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
			foreach ($data as $key => $value) {
				if($value[C('DB_RESTOCK_TRANSPORT')] == '空运'){
					$msq = $this->getWinitMads($value[C('DB_RESTOCK_SKU')], 1, $restockPara['winitde_air_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'万邑通德国',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $winitdestorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_AINVENTORY'));
					$iinventoryQuantity = $winitdestorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_IINVENTORY'));
					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*ceil($msq)/1000;
							$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*ceil($msq)/1000000;
						}else{
							$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
							$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
						$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
					}else{
						if($msq>0 && $days>$restockPara['winitde_air_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*ceil($msq)/1000;
								$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*ceil($msq)/1000000;
							}else{
								$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
								$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
							}
						}
					}
										
				}else{
					$msq = $this->getWinitMads($value[C('DB_RESTOCK_SKU')], 1, $restockPara['winitde_sea_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'万邑通德国',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $winitdestorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_AINVENTORY'));
					$iinventoryQuantity = $winitdestorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_IINVENTORY'));

					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*ceil($msq)/1000;
							$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*ceil($msq)/1000000;
						}else{
							$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
							$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
						$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
					}else{
						if($msq>0 && $days>$restockPara['winitde_sea_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*ceil($msq)/1000;
								$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*ceil($msq)/1000000;
							}else{
								$winitdeWeight = $winitdeWeight+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000;
								$winitdeVolumen = $winitdeVolumen+$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'))*$product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'))*$value[C('DB_RESTOCK_QUANTITY')]/1000000;
							}
						}
					}					
				}
			}
			$this->assign('winitdeWeight',$winitdeWeight);
			$this->assign('winitdeVolumen',$winitdeVolumen);
			$this->display();
		}
	}

	public function updateWinitdeRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK'))==1){
			$this->error('万邑通德国补货表被锁定,无法计算');
		}else{
			$this->resetWinitdeRestockTableHandle();
			$restockTable = M(C('DB_RESTOCK'));
			$winitDeStorageTable = M(C('DB_WINIT_DE_STORAGE'));
			$restockTable->startTrans();
			$data = $restockTable->where(array(C('DB_RESTOCK_STATUS')=>'延迟发货',C('DB_RESTOCK_WAREHOUSE')=>'万邑通德国'))->select();
			$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
			foreach ($data as $key => $value) {
				if($value[C('DB_RESTOCK_TRANSPORT')] == '空运'){
					$msq = $this->getWinitMads($value[C('DB_RESTOCK_SKU')],1, $restockPara['winitde_air_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'万邑通德国',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $winitDeStorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_AINVENTORY'));
					$iinventoryQuantity = $winitDeStorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_IINVENTORY'));
					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
							$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
							$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
							$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
							$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
							$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
							$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
							$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
							$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
							$restockTable->add($tmp);
							$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
							$restockTable->save($value);
						}else{
							$value[C('DB_RESTOCK_STATUS')] = '待发货';
							$restockTable->save($value);
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$value[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($value);
					}else{
						if($msq>0 && $days>$restockPara['winit_air_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
								$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
								$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
								$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
								$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
								$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
								$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
								$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
								$restockTable->add($tmp);
								$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
								$restockTable->save($value);
							}else{
								$value[C('DB_RESTOCK_STATUS')] = '待发货';
								$restockTable->save($value);
							}
						}
					}										
				}else{
					$msq = $this->getWinitMads($value[C('DB_RESTOCK_SKU')], 1,$restockPara['winitde_sea_ad']);
					$lastShippingDate = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>'万邑通德国',C('DB_RESTOCK_STATUS')=>'已发货'))->max(C('DB_RESTOCK_SHIPPING_DATE'));
					
					$cnt=time()-strtotime($lastShippingDate);//与已知时间的差值
					$days = ceil($cnt/(3600*24));//算出天数

					$availableQuantity = $winitDeStorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_AINVENTORY'));
					$iinventoryQuantity = $winitDeStorageTable->where(array(C('DB_WINIT_DE_STORAGE_SKU')=>$value[C('DB_RESTOCK_SKU')]))->getField(C('DB_WINIT_DE_STORAGE_IINVENTORY'));
					if($msq>0 && $msq>($availableQuantity+$iinventoryQuantity)){
						if($value[C('DB_RESTOCK_QUANTITY')]>($msq-($availableQuantity+$iinventoryQuantity))){
							$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
							$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
							$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
							$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
							$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
							$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
							$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
							$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
							$restockTable->add($tmp);
							$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
							$restockTable->save($value);
						}else{
							$value[C('DB_RESTOCK_STATUS')] = '待发货';
							$restockTable->save($value);
						}
					}elseif(($availableQuantity+$iinventoryQuantity)==0){
						$value[C('DB_RESTOCK_STATUS')] = '待发货';
						$restockTable->save($value);
					}else{
						if($msq>0 && $days>$restockPara['winit_sea_id']){
							if($value[C('DB_RESTOCK_QUANTITY')]>ceil($msq)){
								$tmp[C('DB_RESTOCK_SKU')] = $value[C('DB_RESTOCK_SKU')];
								$tmp[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
								$tmp[C('DB_RESTOCK_MANAGER')] = $value[C('DB_RESTOCK_MANAGER')];
								$tmp[C('DB_RESTOCK_QUANTITY')] = ceil($msq);
								$tmp[C('DB_RESTOCK_WAREHOUSE')] = $value[C('DB_RESTOCK_WAREHOUSE')];
								$tmp[C('DB_RESTOCK_TRANSPORT')] = $value[C('DB_RESTOCK_TRANSPORT')];
								$tmp[C('DB_RESTOCK_STATUS')] = '待发货';
								$tmp[C('DB_RESTOCK_REMARK')] = $value[C('DB_RESTOCK_REMARK')];
								$restockTable->add($tmp);
								$value[C('DB_RESTOCK_QUANTITY')] = $value[C('DB_RESTOCK_QUANTITY')]-ceil($msq);
								$restockTable->save($value);
							}else{
								$value[C('DB_RESTOCK_STATUS')] = '待发货';
								$restockTable->save($value);
							}
						}
					}						
				}
			}
			$this->success('已更新万邑通德国仓补货表');
		}
	}

	public function resetWinitdeRestockTable(){
		if(M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK'))==1){
			$this->error('万邑通德国补货表被锁定,无法计算');
		}else{
			$this->resetWinitdeRestockTableHandle();
			$this->success('已重置万邑通德国补货表');
		}
	}

	public function resetWinitdeRestockTableHandle(){
		$restockTable = M(C('DB_RESTOCK'));
		$restockTable->startTrans();
		$map[C('DB_RESTOCK_STATUS')] = array('eq','待发货');
		$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq','万邑通德国');
		$data = $restockTable->where($map)->select();
		foreach ($data as $key => $value) {
			$delayShipping = $restockTable->where(array(C('DB_RESTOCK_SKU')=>$value[C('DB_RESTOCK_SKU')],C('DB_RESTOCK_WAREHOUSE')=>$value[C('DB_RESTOCK_WAREHOUSE')],C('DB_RESTOCK_STATUS')=>'延迟发货'))->select();
			if($delayShipping!==false && $delayShipping!==null){
				if(count($delayShipping)>1){
					for ($i=1; $i < count($delayShipping); $i++){
						$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$delayShipping[i][C('DB_RESTOCK_QUANTITY')];
						$restockTable->where(array(C('DB_RESTOCK_ID')=>$delayShipping[i][C('DB_RESTOCK_ID')]))->delete();
					}
					$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$value[C('DB_RESTOCK_QUANTITY')];
					$restockTable->save($delayShipping[0]);
					$restockTable->where(array(C('DB_RESTOCK_ID')=>$value[C('DB_RESTOCK_ID')]))->delete();
				}else{
					$delayShipping[0][C('DB_RESTOCK_QUANTITY')] = $delayShipping[0][C('DB_RESTOCK_QUANTITY')]+$value[C('DB_RESTOCK_QUANTITY')];
					$restockTable->save($delayShipping[0]);
					$restockTable->where(array(C('DB_RESTOCK_ID')=>$value[C('DB_RESTOCK_ID')]))->delete();
				}
				
			}else{
				$value[C('DB_RESTOCK_STATUS')] = '延迟发货';
				$restockTable->save($value);
			}			
		}
		$restockTable->commit();
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
		return D("PurchaseView")->where($map)->sum('purchase_quantity');
	}

	private function getSzAinventory($sku){
		$map['sku'] = array('eq',$sku);
		return M(C('DB_SZSTORAGE'))->where($map)->sum(C('DB_SZSTORAGE_AINVENTORY'));
	}

	private function addRestockOrder($warehouse,$quantity,$product,$ainventory,$iinventory,$csales){
		$szstorageTable = M(C('DB_SZSTORAGE'));
		$restockTable = M(C('DB_RESTOCK'));
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$szai = $szstorageTable->where(array(C('DB_SZSTORAGE_SKU')=>$product[C('DB_PRODUCT_SKU')]))->find();
		if($quantity>0 && $szai!==null && $szai!=false && (($warehouse=='美自建仓' && $restockPara[C('DB_RESTOCK_PARA_USSW_AUTO_MOVE')]==1) || ($warehouse=='万邑通德国' && $restockPara[C('DB_RESTOCK_PARA_WINITDE_AUTO_MOVE')]==1)) && ($szai[C('DB_SZSTORAGE_AINVENTORY')]-M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_SZSW_MIN_AI')))>=$quantity){
			//满足条件自动转仓不添加到补货表里
			$restock=$restockTable->where(array(C('DB_RESTOCK_SKU')=>$product[C('DB_PRODUCT_SKU')],C('DB_RESTOCK_STATUS')=>'延迟发货',C('DB_RESTOCK_WAREHOUSE')=>$warehouse))->find();
			if($restock!==null && $restock!==false){
				$restock[C('DB_RESTOCK_QUANTITY')]=$restock[C('DB_RESTOCK_QUANTITY')]+$quantity; 
				$szai[C('DB_SZSTORAGE_AINVENTORY')]=$szai[C('DB_SZSTORAGE_AINVENTORY')]-$quantity;
				$szai[C('DB_SZSTORAGE_CINVENTORY')]=$szai[C('DB_SZSTORAGE_CINVENTORY')]-$quantity;
				$szstorageTable->save($szai);
				$restockTable->save($restock);
			}else{
				$data[C('DB_RESTOCK_SKU')]=$product[C('DB_PRODUCT_SKU')];
				$data[C('DB_RESTOCK_CREATE_DATE')]= date("Y-m-d H:i:s",time());
				$data[C('DB_RESTOCK_MANAGER')]= $product[C('DB_PRODUCT_MANAGER')];
				$data[C('DB_RESTOCK_QUANTITY')]= $quantity;
				$data[C('DB_RESTOCK_WAREHOUSE')]= $warehouse;
				$data[C('DB_RESTOCK_STATUS')]= '延迟发货';
				if($warehouse=='美自建仓' || $warehouse=='万邑通美西' || $warehouse=='美国FBA'){
					$data[C('DB_RESTOCK_TRANSPORT')]= $product[C('DB_PRODUCT_TOUS')];
				}
				if($warehouse=='万邑通德国'){
					$data[C('DB_RESTOCK_TRANSPORT')]= $product[C('DB_PRODUCT_TODE')];
				}
				$szai[C('DB_SZSTORAGE_AINVENTORY')]=$szai[C('DB_SZSTORAGE_AINVENTORY')]-$quantity;
				$szai[C('DB_SZSTORAGE_CINVENTORY')]=$szai[C('DB_SZSTORAGE_CINVENTORY')]-$quantity;
				$szstorageTable->save($szai);
				$restockTable->add($data);
			}
			
		}else{
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['warehouse'] = $warehouse;
			if($warehouse=='美自建仓' || $warehouse=='万邑通美西'){
				$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['wayToWarehouse'] = $product[C('DB_PRODUCT_TOUS')];
			}elseif($warehouse=='万邑通德国') {
				$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['wayToWarehouse'] = $product[C('DB_PRODUCT_TODE')];
			}
			
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sku'] = $product[C('DB_USSTORAGE_SKU')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['cname'] = $product[C('DB_USSTORAGE_CNAME')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['quantity'] = $quantity;
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['manager'] = $product[C('db_product_manager')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['price'] = $product[C('DB_PRODUCT_PRICE')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['supplier'] = $product[C('DB_PRODUCT_SUPPLIER')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['purchase_link'] = $product[C('DB_PRODUCT_PURCHASE_LINK')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['date'] = Date('Y-m-d');
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['rquantity'] = $this->getRestockQuantity($warehouse,$product[C('DB_PRODUCT_SKU')]);
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['iquantity'] = $this->getPurchasedQuantity($warehouse,$product[C('DB_PRODUCT_SKU')]);
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sz_ainventory'] = $this->getSzIinventory($product[C('DB_PRODUCT_SKU')]);
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['ainventory'] = $ainventory;
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['iinventory'] = $iinventory;
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['csales'] = $csales;
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['sz_ainventory'] = $this->getSzAinventory($product[C('DB_PRODUCT_SKU')]);
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['pweight'] = $product[C('DB_PRODUCT_PWEIGHT')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['plength'] = $product[C('DB_PRODUCT_PLENGTH')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['pwidth'] = $product[C('DB_PRODUCT_PWIDTH')];
			$GLOBALS["outOfStock"][$GLOBALS["indexOfOutOfStock"]]['pheight'] = $product[C('DB_PRODUCT_PHEIGHT')];
			$GLOBALS["indexOfOutOfStock"] = $GLOBALS["indexOfOutOfStock"]+1;
		}		
	}

	// return max periode sales of 3 periods of sku. excludeLargeQuantity means the quantity over this value will be calculated special. 0==winit us warehouse, 1== winit de warehouse
	private function getWinitMads($sku, $sheetId,$days,$excludeLargeQuantity=null){
		$actualTime = time();
		$fisrtPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days);
		$secondPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days*2);
		$thirdPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days*3);
        if($sheetId==0){
        	$map[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')] = array('in', 'United States, USA, US');
        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        	$map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        	if($excludeLargeQuantity!=null){
        		$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq0 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq0 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[0] = $elelq0+round($gelq0/$excludeLargeQuantity);
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$fisrtPeriod),array('gt',$secondPeriod));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq1 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq1 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[1] = $elelq1+round($gelq1/$excludeLargeQuantity);
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$secondPeriod),array('gt',$thirdPeriod));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq2 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq2 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[2] = $elelq2+round($gelq2/$excludeLargeQuantity);
        	}else{
        		$sq[0] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$fisrtPeriod),array('gt',$secondPeriod));
	        	$sq[1] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$secondPeriod),array('gt',$thirdPeriod));
	        	$sq[2] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
        	}
        }else{
        	$map[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')] = array('not in', 'United States, USA, US');        	
        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        	$map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        	if($excludeLargeQuantity!=null){
        		$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq0 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq0 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[0] = $elelq0+round($gelq0/$excludeLargeQuantity);
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$fisrtPeriod),array('gt',$secondPeriod));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq1 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq1 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[1] = $elelq1+round($gelq1/$excludeLargeQuantity);
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$secondPeriod),array('gt',$thirdPeriod));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        		$elelq2 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq2 = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq[2] = $elelq2+round($gelq2/$excludeLargeQuantity);
        	}else{
        		$sq[0] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$fisrtPeriod),array('gt',$secondPeriod));
	        	$sq[1] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',$secondPeriod),array('gt',$thirdPeriod));
	        	$sq[2] = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
        	}
        }        
         if($sq[0]>$sq[1]){
        	if($sq[0]>$sq[2]){
        		return $sq[0];
        	}else{
        		return $sq[2];
        	}
        }else{
        	if($sq[1]>$sq[2]){
        		return $sq[1];
        	}else{
        		return $sq[2];
        	}
        }
    }

    // return winit sale quantity of days
	private function getWinitDaysSaleQuantity($sku, $sheetId,$days,$excludeLargeQuantity=null){
		$actualTime = time();
		$fisrtPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days);
        if($sheetId==0){
        	$map[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')] = array('in', 'United States, USA, US');
        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        	$map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        	if($excludeLargeQuantity!=null){
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
	        	$elelq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq = $elelq+round($gelq/$excludeLargeQuantity);
	        }else{
	        	$sq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')); 
	        }
        	       	
        }else{
        	$map[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')] = array('not in', 'United States, USA, US');        	
        	$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        	$map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        	if($excludeLargeQuantity!=null){
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
	        	$elelq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$map[C('DB_WINIT_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
	        	$gelq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        	$sq = $elelq+round($gelq/$excludeLargeQuantity);
	        }else{
	        	$sq = D("WinitOutboundView")->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	        }        	
        }        
        return $sq;
    }

    //return las period sales of the sku, excludeLargeQuantity means the quantity over this value will be calculated special.
	private function getUsswMads($sku, $days,$excludeLargeQuantity=null){
		$actualTime = time();
		$fisrtPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days);
        $map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        if($excludeLargeQuantity!=null){
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        	$elelq =  D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
        	$gelq = D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	return $elelq + round($gelq/$excludeLargeQuantity);
        }else{
        	return D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        }       
    }

     // return FBA sale quantity of days
	private function getFBADaysSaleQuantity($sku, $days,$excludeLargeQuantity=null){
		$actualTime = time();
		$fisrtPeriod = date("Y-m-d H:i:s",$actualTime-60*60*24*$days);
        $map[C('DB_AMAZON_US_FBA_OUTBOUND_CREATE_TIME')] = array(array('elt',date("Y-m-d H:i:s",$actualTime)),array('gt',$fisrtPeriod));
        $map[C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        if($excludeLargeQuantity!=null){
        	$map[C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        	$elelq = D("UsFBAOutboundView")->where($map)->sum(C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY'));
        	$map[C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
        	$gelq = D("UsFBAOutboundView")->where($map)->sum(C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY'));
        	return $elelq+round($gelq/$excludeLargeQuantity);
        }else{
        	return D("UsFBAOutboundView")->where($map)->sum(C('DB_AMAZON_US_FBA_OUTBOUND_ITEM_QUANTITY'));
        }         
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

    private function enoughInRestockOrPurchased($warehouse,$sku,$neededQuantity){
    	$restockQuantity = $this->getRestockQuantity($warehouse,$sku);
    	$purchasedQuantity = $this->getPurchasedQuantity($warehouse,$sku);
    	if($warehouse=='美自建仓'){
    		$restockQuantity=$restockQuantity+$this->getUsswIInventory($sku);
    	}
    	if($neededQuantity>($restockQuantity+$purchasedQuantity)){
    		return false;
    	}
    	return true;
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

    private function getRestockQuantity($warehouse,$sku){
    	$map[C('DB_RESTOCK_SKU')] = array('eq',$sku);
    	$map[C('DB_RESTOCK_WAREHOUSE')] = array('eq',$warehouse);
    	$map[C('DB_RESTOCK_STATUS')] = array('neq','已发货');
    	$restock = M(C('DB_RESTOCK'))->where($map)->sum(C('DB_RESTOCK_QUANTITY'));
    	if($restock ==null){
			return 0;
		}else{
			return $restock;
		}
    }

    private function isInPurchaseItem($warehouse,$sku){
    	$map[C('DB_PURCHASE_STATUS')] = array('in','待确认,待付款,待发货');
    	$map[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('eq',$warehouse);
    	$map[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$sku);
    	$result=D("PurchaseView")->where($map)->find();
    	if($result !== null && $result!==false){
    		return true;
    	}else{
    		return false;
    	}
    }

    private function getPurchasedQuantity($warehouse,$sku){
    	$map[C('DB_PURCHASE_STATUS')] = array('in','待确认,待付款,待发货');
    	$map[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('eq',$warehouse);
    	$map[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$sku);
    	$result=D("PurchaseView")->where($map)->sum(C('DB_PURCHASE_ITEM_PURCHASE_QUANTITY'));
    	if($result == null){
    		return 0;
    	}else{
    		return $result;
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

    private function getUsswIInventory($sku){
    	$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		$iinventory = D("UsswInboundView")->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
		return $iinventory==null?0:$iinventory;  
    }

    private function reallyOutOfStock($warehouse,$sku,$neededQuantity){
    	if($warehouse=='万邑通德国' && !$this->enoughInRestockOrPurchased($warehouse,$sku,$neededQuantity)){
    		return true;
    	}elseif($warehouse=='美自建仓' && !$this->enoughInRestockOrPurchased($warehouse,$sku,$neededQuantity)){
    		return true;
    	}elseif($warehouse=='万邑通美西' && !$this->isInOutOfStock($warehouse,$sku) && !$this->enoughInRestockOrPurchased($warehouse,$sku,$neededQuantity)){
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
                	if($restockItem[C('DB_RESTOCK_STATUS')] == null){
                		$errorInFile[$i] = '状态不能为空';
                	}
                }
                if($errorInFile != null){
                	dump($errorInFile);
                	die;
                }else{
                	//更新restock表格状态
                    $restock = M(C('DB_RESTOCK'));
                    $restock->startTrans();                     
                	for($i=2;$i<=$highestRow;$i++){
                		$id = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
                		$restockQuantity = $restock->where(array(C('DB_RESTOCK_ID')=>$id))->getField(C('DB_RESTOCK_QUANTITY'));
                		if($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue()=='已发货'){
                			if($restockQuantity <= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue()){
	                			$tmp[C('DB_RESTOCK_STATUS')] = '已发货';
	                			$tmp[C('DB_RESTOCK_SHIPPING_DATE')] = date("Y-m-d H:i:s" ,time());
	                			$restock->where(array(C('DB_RESTOCK_ID')=>$id))->save($tmp);
	                		}else{
	                			$rest = $restockQuantity - $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
	                            $restock->where(array(C('DB_RESTOCK_ID')=>$id))->setField(C('DB_RESTOCK_QUANTITY'),$rest);                           
	                        }
                		}elseif($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue()=='包装中'){
                			$tmp[C('DB_RESTOCK_STATUS')] = '包装中';
	                		$restock->where(array(C('DB_RESTOCK_ID')=>$id))->save($tmp);
                		}
                		
	                }
	                $restock->commit(); 
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
    			$data[C('DB_RESTOCK_STATUS')]='已发货';
    			$data[C('DB_RESTOCK_SHIPPING_DATE')]=date("Y-m-d H:i:s" ,time());
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

    public function test($warehouse,$shippingWay,$realCal,$sr,$er){
    	$this->ajaxReturn('',$warehouse.$shippingWay.$realCal.$sr.$er,0);
    }

    /*
		补货数量算法
		1.	检查是否该仓库所有账号禁售该产品。不是所有账号禁售，则继续
		2.	检查包装尺寸和重量
		3.	取得补货平均日销量，并且日销量大于0，则继续
			a.	美国空运补货平均日销量=（sku, 美自建仓，30，剔除大单）
			b.	美国海运补货平均日销量=（sku,美自建仓，80，不剔除大单）
			c.	德国空运补货平均日销量=（sku, 万邑通德国，30，剔除大单）
			d.	德国海运补货平均日销量=（sku,万邑通德国，90，不剔除大单）
		4.	空运补货数量=补货平均日销量*空运可用加在途库存最大可售天数-空运在途数量-可用库存数量-海运快到仓的数量
		a.	比较空运补货数量和深圳仓可用库存数量，那个小用哪个数量
		b.	累加空运补货体积和重量
		5.	海运补货数量=补货平均日销量*海运可用加在途库存最大可售天数-空运和海运在途数量-可用库存数量
		a.	空运近一段时间日销量大于0.33（30天剔除大单卖10个以上），可以考虑发海运补货
		b.	空转海补货数量=补货平均日销量*海运预估到仓天数-空运和海运在途数量-可用库存数量
		c.	比较空转海数量和深圳仓可用库存数量，那个小用哪个数量
		d.	累加海运补货体积和重量
    */

    public function calRestockQuantity($warehouse,$shippingWay,$realCal){
    	if($warehouse=='ussw' && M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_USSW_LOCK'))==1){
			$this->error('美自建仓补货表被锁定,无法计算');
		}elseif($warehouse=='winitde' && M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->getField(C('DB_RESTOCK_PARA_WINITDE_LOCK'))==1){
			$this->error('万邑通德国仓补货表被锁定,无法计算');
		}else{

			if($warehouse=='ussw' && $shippingWay=='air'){
				$restock = $this->getUsswAirRestockQuantity();
			}elseif($warehouse=='ussw' && $shippingWay=='sea'){
				$restock = $this->getUsswSeaRestockQuantity($realCal);
			}elseif($warehouse=='winitde' && $shippingWay=='air'){
				$restock = $this->getWinitdeAirRestockQuantity($realCal);
			}elseif($warehouse=='winitde' && $shippingWay=='sea'){
				$restock = $this->getWinitdeSeaRestockQuantity($realCal);
			}

			if($realCal==false){
				$this->assign('weight',$restock['weight']);
				$this->assign('volume',$restock['volume']);
				$this->assign('restock',$restock['restock']);
				$this->assign('shippingWay',$shippingWay);
				$this->assign('warehouse',$warehouse);
				$this->display();		
			}else{
				if($realCal==true && $restock['restock']!=null){
		    		$szstorageTable=M('DB_SZSTORAGE');
					foreach ($restock['restock'] as $key => $cvalue) {
						$newRestock[C('DB_RESTOCK_CREATE_DATE')] = Date('Y-m-d');
						$newRestock[C('DB_RESTOCK_SKU')] = $cvalue['sku'];
						$newRestock[C('DB_RESTOCK_QUANTITY')] = $cvalue['quantity'];
						$newRestock[C('DB_RESTOCK_WAREHOUSE')] = $warehouse;
						$newRestock[C('DB_RESTOCK_TRANSPORT')] = $shippingWay;
						$newRestock[C('DB_RESTOCK_STATUS')] = '待发货';
						if($restockTable->add($newRestock)!=false){
							$cvalue[C('DB_RESTOCK_QUANTITY')] = $cvalue[C('DB_RESTOCK_QUANTITY')]-$cvalue['change_to_air_quantity'];
							$szst = $szstorageTable->where(array(C('DB_SZSTORAGE_SKU')=>$cvalue['sku']))->find();
							$szst[C('DB_SZSTORAGE_AINVENTORY')] = ($szst[C('DB_SZSTORAGE_AINVENTORY')]-$cvalue['quantity'])<0?0:($szst[C('DB_SZSTORAGE_AINVENTORY')]-$cvalue['quantity']);
							$szst[C('DB_SZSTORAGE_CINVENTORY')] = ($szst[C('DB_SZSTORAGE_CINVENTORY')]-$cvalue['quantity'])<0?0:($szst[C('DB_SZSTORAGE_CINVENTORY')]-$cvalue['quantity']);
							$szstorageTable->save($szst);
						}					
					}
				}
				$this->redirect('index');
			}
		}
    }

    public function getUsswAirRestockQuantity(){
		$productTable=M(C('DB_PRODUCT'));
    	$usstorageTable=M(C('DB_USSTORAGE'));
    	$szmap[C('DB_SZSTORAGE_AINVENTORY')] = array('gt',0);
    	$szmap[C('DB_PRODUCT_TOUS')] = array('eq','空运');
    	$data=D('SzStorageView')->where($szmap)->select();
    	$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
    	$airRestock=array();
    	$airweight = 0;
	    $airvolume = 0;
    	foreach ($data as $key => $value) {
    		$p = $productTable->where(array('sku'=>$value['sku']))->find();
    		//if($value[C('DB_SZSTORAGE_AINVENTORY')]>0 && $p[C('DB_PRODUCT_TOUS')]=='空运' && !$this->isBannedByAllAccount($value['sku'],'美自建仓')){    						
    			if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
    				$averangeSaleQuantity = $this->getRestockADSQNew($value['sku'],'美自建仓',$restockPara[C('DB_RESTOCK_PARA_USSW_AIR_AD')],$restockPara[C('DB_RESTOCK_PARA_ELQ')]);
    				$toAir=array();
					$toAir['sku'] = $value['sku'];
					$toAir['asq'] = $averangeSaleQuantity;
					if($this->getUsswInboundSeaShippingDate($value[C('DB_RESTOCK_SKU')])!=null){
						$cntSeaShippingTimes=time()-strtotime($this->getUsswInboundSeaShippingDate($value[C('DB_RESTOCK_SKU')]));//与已知时间的差值
						$seaEstimatedArriveDays = ceil($restockPara[C('DB_RESTOCK_PARA_USSW_EDSS')]-($cntSeaShippingTimes/(3600*24)));//算出天数
					}else{
						$seaEstimatedArriveDays=$restockPara[C('DB_RESTOCK_PARA_USSW_EDSS')];
					}
					if($seaEstimatedArriveDays<ceil($usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value['sku']))->getField(C('DB_USSTORAGE_AINVENTORY'))/$averangeSaleQuantity)){
						$toAir['quantity'] = intval($averangeSaleQuantity*$restockPara[C('DB_RESTOCK_PARA_USSW_AIR_tD')]-$usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value['sku']))->getField(C('DB_USSTORAGE_AINVENTORY'))-$this->getUsswInboundAirIInventory($value['sku'])-$this->getUsswInboundSeaIInventory($value['sku']));
					}else{
						$toAir['quantity'] = intval($averangeSaleQuantity*$restockPara[C('DB_RESTOCK_PARA_USSW_AIR_tD')]-$usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value['sku']))->getField(C('DB_USSTORAGE_AINVENTORY'))-$this->getUsswInboundAirIInventory($value['sku']));
					}
					
					if($toAir['quantity']>0){
						if($toAir['quantity']>$value[C('DB_SZSTORAGE_AINVENTORY')]){
    						$toAir['quantity'] = $value[C('DB_SZSTORAGE_AINVENTORY')];
    					}
    					array_push($airRestock, $toAir);    					
    					$airweight = $airweight+$p[C('DB_PRODUCT_WEIGHT')]*$toAir['quantity']/1000;
    					$airvolume = $airvolume+$p[C('DB_PRODUCT_LENGTH')]*$p[C('DB_PRODUCT_HEIGHT')]*$p[C('DB_PRODUCT_WIDTH')]/1000000*$toAir['quantity'];  
					}    					  					
					$toAir=null;
    			/*}else{
    				$this->error('无法计算，产品 '.$value['sku'].' 包装信息缺失');
    			}*/
    		}
    	}
		return array('weight'=>$airweight,'volume'=>$airvolume,'restock'=>$airRestock);
    }

    public function getUsswSeaRestockQuantity(){
		$productTable=M(C('DB_PRODUCT'));
    	$usstorageTable=M(C('DB_USSTORAGE'));
    	$szmap[C('DB_SZSTORAGE_AINVENTORY')] = array('gt',0);
    	$szmap[C('DB_PRODUCT_TOUS')] = array('neq','无');
    	$data=D('SzStorageView')->where($szmap)->select();
    	$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
    	$seaRestock=array();
	    $seaweight = 0;
	    $seavolume = 0;
    	foreach ($data as $key => $value) {
    		$p = $productTable->where(array('sku'=>$value['sku']))->find();
    		if($value[C('DB_SZSTORAGE_AINVENTORY')]>0 && $p[C('DB_PRODUCT_TOUS')]!='无' && !$this->isBannedByAllAccount($value['sku'],'美自建仓')){
    			if($p[C('DB_PRODUCT_PWEIGHT')]>0 && $p[C('DB_PRODUCT_PLENGTH')]>0 && $p[C('DB_PRODUCT_PHEIGHT')]>0 && $p[C('DB_PRODUCT_PWIDTH')]>0){
					$averangeSaleQuantity = $this->getRestockADSQ($value['sku'],'美自建仓',$restockPara[C('DB_RESTOCK_PARA_USSW_AIR_AD')]);

					//空运货平均销量大于0.33（30天销量10个以上），可以海运补点货
					if($p[C('DB_PRODUCT_TOUS')]=='海运' || ($p[C('DB_PRODUCT_TOUS')]=='空运' && $averangeSaleQuantity>=0.33)){
						$toSea=array();
    					$toSea['sku'] = $value['sku'];
    					$toSea['asq'] = $averangeSaleQuantity;
						$toSea['quantity'] = intval($averangeSaleQuantity*$restockPara[C('DB_RESTOCK_PARA_USSW_SEA_tD')]-$usstorageTable->where(array(C('DB_USSTORAGE_SKU')=>$value['sku']))->getField(C('DB_USSTORAGE_AINVENTORY'))-$this->getUsswInboundAirIInventory($value['sku'])-$this->getUsswInboundSeaIInventory($value['sku']));
    					if($toSea['quantity']>$value[C('DB_SZSTORAGE_AINVENTORY')]){
    						$toSea['quantity'] = $value[C('DB_SZSTORAGE_AINVENTORY')];
    					}
    					array_push($seaRestock, $toSea);  
    					$seaweight = $seaweight+$p[C('DB_PRODUCT_PWEIGHT')]*$toSea['quantity']/1000;
    					$seavolume = $seavolume+$p[C('DB_PRODUCT_PLENGTH')]*$p[C('DB_PRODUCT_PHEIGHT')]*$p[C('DB_PRODUCT_PWIDTH')]/1000000*$toSea['quantity'];
						$toSea=null;
    				}
    			}else{
    				$this->error('无法计算，产品 '.$value['sku'].' 包装信息缺失');
    			}
    		}
    	}
		return array('weight'=>$seaweight,'volume'=>$seavolume,'restock'=>$seaRestock);
    }

    /*
	补货平均日销量算法
	参数: sku, 仓库,天数,是否剔除大单
	1.	计算天数内该仓库是否有这个sku的入库单
		a.	有入库单，读出最早一个入仓日期
			i.	可用库存数量=0
				1.	统计（天数内取得的入库单的入库数量和-天数内大单销售数量）
				2.	从最早一个入库单入仓日期开始，查找这个数量的卖完日期
					a.	如果到当前日期仍得不到天数内取得的入库单的入库数量和， 那么就是有补寄情况，查找统计的入库数量和的80%卖完日期。如果到当前日期还是得不到80%的卖完日期，那么就以当前日期作为卖完日期。
					b.	要考虑剔除大单的数量
					c.	读取不禁售账号的销售数量
				3.	返回 （天数内取得的入库单的入库数量和-天数内大单销售数量）/（卖完日期-天数内最早入仓日期之间的天数）
			ii.	可用库存数量>0
				1.	统计（天数内取得的入库单的入库数量和-天数内大单销售数量）
				2.	读取不禁售账号的销售数量
				3.	返回 （期间剔除大单卖出数量）/（当前日期-天数内最早入仓日期之间的天数）
		b.	没有入库单
			i.	可用库存数量=0
				1.	找到最近一个入库单的入库数量和入库日期。
				2.	从该入库日期开始找这个数量卖完的日期。
					a.	如果到当前日期仍得不到天数内取得的入库单的入库数量和， 那么就是有补寄情况，查找统计的入库数量和的80%卖完日期。如果到当前日期还是得不到80%的买=卖完日期，那么就以当前日期作为卖完日期。
					b.	要考虑剔除大单的数量
					c.	读取不禁售账号的销售数量
				3.	返回 剔除大单入库单的数量/（卖完日期-入仓日期之间的天数）
			ii.	可用库存数量>0
				1.	返回 剔除大单天数内平均日销量。
				2.	读取不禁售账号的销售数量
    */

    private function getRestockADSQ($sku, $warehouse,$days,$excludeLargeQuantity=null){
    	$inboundTable = D($this->getInboundViewTableName($warehouse));
    	$storageTable = M($this->getStorageTableName($warehouse));
    	

    	$daysDateStampe = time()-60*60*24*$days;
    	$inboundmap['receive_date'] = array('gt',date("Y-m-d H:i:s",$daysDateStampe));
    	$inboundmap['sku'] = array('eq',$sku);
    	$inbounds = $inboundTable->where($inboundmap)->select();

    	if($inbounds!=null && $inbounds!=false){
    		//有入库单
    		if($storageTable->where(array('sku'=>$sku))->getField('ainventory')<=0){
    			//可用库存小于等于0
    			$daysInboundQuantity = $inboundTable->where($inboundmap)->sum('confirmed_quantity');
    			$earliestInboundDate = $inboundTable->order('id asc')->where($inboundmap)->limit(1)->getField('receive_date');
    			if($this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity,$earliestInboundDate)!=null){
    				$soldOutDays = ceil((strtotime($this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity,$earliestInboundDate))-strtotime($earliestInboundDate))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$earliestInboundDate,$this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity,$earliestInboundDate),$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDays,2);
    			}elseif($this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity*0.8,$earliestInboundDate)!=null){
    				$soldOutDays = ceil((strtotime($this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity*0.8,$earliestInboundDate))-strtotime($earliestInboundDate))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$earliestInboundDate,$this->getQuantitySoldOutDate($sku,$warehouse,$daysInboundQuantity*0.8,$earliestInboundDate),$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDays,2);
    			}else{
    				$soldOutDays = ceil((time()-strtotime($earliestInboundDate))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$earliestInboundDate,date("Y-m-d H:i:s",time()),$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDays,2);
    			}
    		}else{
    			//可用库存大于0
    			$earliestInboundDate = $inboundTable->order('id asc')->where($inboundmap)->limit(1)->getField('receive_date');
    			$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$earliestInboundDate,date("Y-m-d H:i:s",time()),$excludeLargeQuantity);
    			$calDays = ceil((time()-strtotime($earliestInboundDate))/(60*60*24));
    			return round($quantityExceptLargeOrder/$calDays,2);
    		}	
    	}elseif($inbounds==null){
    		//没有入库单
    		if($storageTable->where(array(C('sku')=>$sku))->getField('ainventory')<=0){
    			//可用库存小于等于0
    			$inboundmap['sku'] = array('eq',$sku);
    			$inbounds = $inboundTable->where($inboundmap)->find();
    			if($this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity'],$inbounds['receive_date'])!=null){
    				$soldOutDays = ceil((strtotime($this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity'],$inbounds['receive_date']))-strtotime($inbounds['receive_date']))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$inbounds['receive_date'],$this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity'],$inbounds['receive_date']),$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDays,2);
    			}elseif($this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity']*0.8,$inbounds['receive_date'])!=null){
    				$soldOutDays = ceil((strtotime($this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity']*0.8,$inbounds['receive_date']))-strtotime($inbounds['receive_date']))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$inbounds['receive_date'],$this->getQuantitySoldOutDate($sku,$warehouse,$inbounds['confirmed_quantity']*0.8,$inbounds['receive_date']),$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDay,2);
    			}else{
    				$soldOutDays = ceil((time()-strtotime($inbounds['receive_date']))/(60*60*24));
    				$quantityExceptLargeOrder = $this->getQuantityExceptLargeOrder($sku,$warehouse,$inbounds['confirmed_quantity']*0.8,$inbounds['receive_date'],$excludeLargeQuantity);
    				return round($quantityExceptLargeOrder/$soldOutDays,2);
    			}
    		}else{
    			//可用库存大于0
    			if($this->getCountry($warehouse)=='us'){
					$quantity = $this->getUsswMads($sku,$days,$excludeLargeQuantity);
				}elseif($this->getCountry($warehouse)=='de'){
					$quantity = $this->getWinitMads($sku,1,$days,$excludeLargeQuantity);
				}
				return round($quantity/$days,2);
    		}
    	}else{
    		$this->error('数据库读取错误，无法计算 '.$sku.' 在 '.$warehouse.' 仓的补货平均日销量');
    	}
    }

    /*
	补货平均日销量算法
	参数: sku, 仓库,天数,是否剔除大单
	1.	可用库存数量=0
		a.	采购次数小于3次并且首次刊登时间小于60天并且是空运，统计首次刊登时间到卖没时间的天数。返回累计补货数量/统计首次刊登时间到卖没时间之间的天数 
		b.	找到最后一个出库单日期
			i.	距离现在小于30天，统计最后一个出库单往前30天的销量。返回销量/30
			ii.	距离现在大于30天，空运返回 0.1，海运统计最后一个出库单往前30天的销量。返回销量/30
	2.	可用库存数量>0，返回近30天平均日销量
    */
    private function getRestockADSQNew($sku, $warehouse,$days,$excludeLargeQuantity){
    	$storage = M($this->getStorageTableName($warehouse))->where(array('sku'=>$sku))->find();
    	if($storage!=null && $storage!=false){
    		if($storage['ainventory']<=0){
    			if($this->isNewProduct($sku,$warehouse)){
    				return round($this->getCinventory($sku,$warehouse,$this->getFirstSaleDate($sku,$warehouse),$this->getLastOutboundDate($sku,$warehouse))/((strtotime($this->getLastOutboundDate($sku,$warehouse))-strtotime($this->getFirstSaleDate($sku,$warehouse)))/(60*60*24)),2);
    			}elseif(!$this->isBannedByAllAccount($sku,$warehouse)){
    				$lastShippingDate = $this->getLastOutboundDate($sku,$warehouse);
    				if(ceil((time()-strtotime($lastShippingDate))/(60*60*24))<30){
    					return round($this->getCsale($sku,$warehouse,date('Y-m-d',strtotime($lastShippingDate)-60*60*24*30),$lastShippingDate,$excludeLargeQuantity)/30,2);
    				}else{
    					if($this->isAirProduct($sku,$warehouse)){
    						return 0.1;
    					}else{
    						return round($this->getCsale($sku,$warehouse,date('Y-m-d',strtotime($lastShippingDate)-60*60*24*30),$lastShippingDate,0)/30,2);
    					}
    				}
    			}
    		}else{
    			return round($this->getCsale($sku,$warehouse,date('Y-m-d',time()-60*60*24*30),date('Y-m-d',time()),$excludeLargeQuantity)/30,2);
    		}
    	}else{
    		return 0;
    	}
    }

    private function isAirProduct($sku,$warehouse){
    	$p=M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	if($this->getCountry($warehouse)=='us'&& $p[C('DB_PRODUCT_TOUS')]=='空运'){
    		return true;
    	}elseif($this->getCountry($warehouse)=='de'&& $p[C('DB_PRODUCT_TODE')]=='空运'){
    		return true;
    	}else{
    		return false;
    	}
    }

    private function isNewProduct($sku,$warehouse){
    	$p=M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	if($this->getCountry($warehouse)=='us'){
    		$salePlan = M(C('DB_USSW_SALE_PLAN'));
    	}elseif($this->getCountry($warehouse)=='de'){
    		$salePlan = M(C('DB_YZHAN_816_PL_SALE_PLAN'));
    	}
    	
    	$firstSaleDate = $salePlan->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));
		if($firstSaleDate==null){
			$daysFirstSale=0;
		}else{
			$cntFirstSale=time()-strtotime($firstSaleDate);//与已知时间的差值
			$daysFirstSale = ceil($cntFirstSale/(3600*24));//算出天数
		}				
		$purchaseMap[C('DB_PURCHASE_ITEM_SKU')] = array('eq',$value[C('DB_RESTOCK_SKU')]);
		if($this->getCountry($warehouse)=='us'){
    		$purchaseMap[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('in',array('美自建仓','万邑通美西'));
    	}elseif($this->getCountry($warehouse)=='de'){
    		$purchaseMap[C('DB_PURCHASE_ITEM_WAREHOUSE')] = array('in',array('万邑通德国'));
    	}		
		$purchaseMap[C('DB_PURCHASE_STATUS')] = array('in',array('部分到货','全部到货'));
		$purchaseCount = M(C('DB_PURCHASE'))->where($purchaseMap)->count();
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		if($purchaseCount<$restockPara[C('DB_RESTOCK_PARA_USSW_AFCL')] && $daysFirstSale<$restockPara[C('DB_RESTOCK_PARA_USSW_AFDL')] ){
			if($this->getCountry($warehouse)=='us'&& $p[C('DB_PRODUCT_TOUS')]=='空运'){
	    		return true;
	    	}elseif($this->getCountry($warehouse)=='de'&& $p[C('DB_PRODUCT_TODE')]=='空运'){
	    		return true;
	    	}
		}
		return false;
    }

    private function getFirstSaleDate($sku,$warehouse){
    	if($this->getCountry($warehouse)=='us'){
    		return M(C('DB_USSW_SALE_PLAN'))->where(array('sku'=>$sku))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));
    	}elseif($this->getCountry($warehouse)=='de'){
    		return M(C('DB_YZHAN_816_PL_SALE_PLAN'))->where(array('sku'=>$sku))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));
    	}else{
    		return null;
    	}
    }

    private function getLastOutboundDate($sku,$warehouse){
    	if($this->getCountry($warehouse)=='us'){
    		return D('UsswOutboundView')->where(array('sku'=>$sku))->getField(C('DB_USSW_OUTBOUND_CREATE_TIME'));
    	}elseif($this->getCountry($warehouse)=='de'){
    		return D('WinitOutboundView')->where(array('sku'=>$sku))->getField(C('DB_USSW_OUTBOUND_CREATE_TIME'));
    	}else{
    		return null;
    	}
    }

    private function getCinventory($sku,$warehouse,$startDate,$endDate){
    	$inboundmap['receive_date'] = array('elt',$endDate);
    	$inboundmap['receive_date'] = array('gt',$startDate);
    	$inboundmap['sku'] = array('eq',$sku);
    	return D($this->getInboundViewTableName($warehouse))->where($inboundmap)->sum('confirmed_quantity');
    }

    private function getCsale($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity){
    	$unbannedAccount = array();
        $saleTableNames = $this->getSalePlanTableNames($warehouse);
    	foreach ($saleTableNames as $key => $value) {
    		if(M($value['sale_table'])->where(array('sku'=>$sku))->getField('sale_status')==0 && !in_array($value['account'], $unbannedAccount)){
    			array_push($unbannedAccount, $value['account']);
    		}
    	}
    	$outboundmap['seller_id'] = array('in',$unbannedAccount);
    	$outboundmap['create_time'] = array('elt',$endDate);
    	$outboundmap['create_time'] = array('gt',$startDate);
    	$outboundmap['sku'] = array('eq',$sku);
    	$outboundmap['quantity'] = array('lt',$excludeLargeQuantity);
    	return D($this->getOutboundViewTableName($warehouse))->where($outboundmap)->sum('quantity');
    }

    private function getQuantitySoldOutDate($sku,$warehouse,$quantity,$startDate){
    	$endDate = $startDate;
    	while (strtotime($endDate)<time()) {
    		$endDate = date("Y-m-d H:i:s",strtotime($startDate)+60*60*24);
    		if($this->getCountry($warehouse)=='us'){
    			$tmpQuantity = $this->getUsswSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate);
    		}elseif($this->getCountry($warehouse)=='de'){
    			$tmpQuantity = $this->getWinitdeSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate);
    		}
    		if($tmpQuantity>=$quantity){
    			return $endDate;
    		}
    	}
    	return null;
    }

    private function getQuantityExceptLargeOrder($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity){
    	if($this->getCountry($warehouse)=='us'){
			$quantity = $this->getUsswSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity);
		}elseif($this->getCountry($warehouse)=='de'){
			$quantity = $this->getWinitdeSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity);
		}
		return $quantity;
    }

	private function getUsswSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity=null){
        $unbannedAccount = array();
        $saleTableNames = $this->getSalePlanTableNames($warehouse);
    	foreach ($saleTableNames as $key => $value) {
    		if(M($value['sale_table'])->where(array('sku'=>$sku))->getField('sale_status')==0 && !in_array($value['account'], $unbannedAccount)){
    			array_push($unbannedAccount, $value['account']);
    		}
    	}

    	$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array(array('elt',$endDate),array('gt',$startDate));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        $map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('in',$unbannedAccount);

        if($excludeLargeQuantity!=null){
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        	$elelq =  D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'))+D("UsFBAOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
        	$gelq = D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'))+D("UsFBAOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	return $elelq + round($gelq/$excludeLargeQuantity);
        }else{
        	return D("UsswOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'))+D("UsFBAOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        }       
    }

    private function getWinitdeSoldQuantityBetweenDate($sku,$warehouse,$startDate,$endDate,$excludeLargeQuantity=null){
        $unbannedAccount = array();
        $saleTableNames = $this->getSalePlanTableNames($warehouse);
    	foreach ($saleTableNames as $key => $value) {
    		if(M($value['sale_table'])->where(array('sku'=>$sku))->getField('sale_status')==0){
    			array_push($unbannedAccount, $value['account']);
    		}
    	}

    	$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array(array('elt',$endDate),array('gt',$startDate));
        $map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
        $map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('in',$unbannedAccount);

        if($excludeLargeQuantity!=null){
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('elt',$excludeLargeQuantity);
        	$elelq =  D("WinitOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	$map[C('DB_USSW_OUTBOUND_ITEM_QUANTITY')] = array('gt',$excludeLargeQuantity);
        	$gelq = D("WinitOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        	return $elelq + round($gelq/$excludeLargeQuantity);
        }else{
        	return D("WinitOutboundView")->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
        }       
    }

    private function setReceivedDate(){
    	$usswInboundTable = M(C('DB_USSW_INBOUND'));
    	foreach ($usswInboundTable->select() as $key => $value) {
    		if($value[C('DB_USSW_INBOUND_SHIPPING_WAY')]=='空运'){
    			$value[C('DB_USSW_INBOUND_RECEIVE_DATE')] = date("Y-m-d H:i:s",strtotime($value[C('DB_USSW_INBOUND_DATE')])+60*60*24*4);
    		}else{
    			$value[C('DB_USSW_INBOUND_RECEIVE_DATE')] = date("Y-m-d H:i:s",strtotime($value[C('DB_USSW_INBOUND_DATE')])+60*60*24*35);
    		}
    		$usswInboundTable->save($value);
    	}

    	$winitInboundTable = M(C('DB_WINITDE_INBOUND'));
    	foreach ($winitInboundTable->select() as $key => $value) {
    		if($value[C('DB_USSW_INBOUND_SHIPPING_WAY')]=='空运'){
    			$value[C('DB_USSW_INBOUND_RECEIVE_DATE')] = date("Y-m-d H:i:s",strtotime($value[C('DB_USSW_INBOUND_DATE')])+60*60*24*15);
    		}else{
    			$value[C('DB_USSW_INBOUND_RECEIVE_DATE')] = date("Y-m-d H:i:s",strtotime($value[C('DB_USSW_INBOUND_DATE')])+60*60*24*65);
    		}
    		$winitInboundTable->save($value);
    	}
    }

    public function isBannedByAllAccount($sku,$warehouse){
        $saleTableNames = $this->getSalePlanTableNames($warehouse);
    	foreach ($saleTableNames as $key => $value) {
    		if(M($value['sale_table'])->where(array('sku'=>$sku))->getField('sale_status')==0){
    			return false;
    		}
    	}
    	return true;
    }
}

?>