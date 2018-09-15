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

	public function findUsOutOfStockItem(){
        $GLOBALS["outOfStock"] = null;
		$GLOBALS["indexOfOutOfStock"] = 0;
		$restockPara = M(C('DB_RESTOCK_PARA'))->where(array(C('DB_RESTOCK_PARA_ID')=>1))->find();
		$this->findUsswOutOfStockItem();
		$this->findWinitUsOutOfStockItem();
		$this->findUsFbaOutOfStockItemPurhaseView();
		F('out',$GLOBALS["outOfStock"]);
		$this->assign('outofstock',$GLOBALS["outOfStock"]);
		$this->display('exportOutOfStock');
	}

	private function findUsswOutOfStockItem(){
		$usstorage = M(C('DB_USSTORAGE'))->select();
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
				$msq = $this->getFBADaysSaleQuantity($fbasv[C('DB_AMAZON_US_STORAGE_SKU')],30,$restockPara['exclude_large_quantity']);
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
		foreach ($usFbastorage as $ussk => $ussv) {
			$product = $productTable->where(array(C('db_product_sku')=>$this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])))->find();
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
				$availableQuantity = $ussv[C('DB_AMAZON_US_STORAGE_AINVENTORY')] + $ussv[C('DB_AMAZON_US_STORAGE_IINVENTORY')] + $this->getRestockQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) + $this->getPurchasedQuantity('美自建仓', $this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')])) - $this->getUsswMads($this->fbaSkuToStandardSku($ussv[C('DB_AMAZON_US_STORAGE_SKU')]), $restockPara['ussw_sea_ad'],$restockPara['exclude_large_quantity']);
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

	private function fbaSkuToStandardSku($fbaSku){
		if(count(explode('FBA_', $fbaSku))==1){
			return $fbaSku;
		}else{
			return explode('FBA_', $fbaSku)[1];
		}		
	}

	/**
	 * Prepare Calculate the restock quantity. 
	 * If the available quantity is not zero and the order quantity is zero within no_order_days in restock parameters, then
	 * this item needn't to restock.
	 * And calculate 0-7days, 8-15days, 16-23days, 24-31days sold quantity. If the quantitys are decremented, then the restock * quantity of this item should be calculated according to the minimum value.
	 * @param warehouse
	 * @param sku
	 * @return int or null, 0=needn't restock, int=last 7 days sale quantity exclude large order quantity, null=no result need 
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
    	$iinventory = 0;
    	$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
		$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$sku);
		$iinventory = D("UsswInboundView")->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
		return $iinventory;  
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
                	if($restockItem[C('DB_RESTOCK_WAREHOUSE')] == '美自建仓'){
                		$errorInFile[$i] = '该产品目的仓是美自建仓';
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
                		if($restockQuantity <= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue()){
                			$tmp[C('DB_RESTOCK_STATUS')] = '已发货';
                            $tmp[C('DB_RESTOCK_SHIPPING_DATE')] = date("Y-m-d H:i:s" ,time());
                            $restock->where(array(C('DB_RESTOCK_ID')=>$id))->save($tmp);
                		}else{
                            $rest = $restockQuantity - $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                            $restock->where(array(C('DB_RESTOCK_ID')=>$id))->setField(C('DB_RESTOCK_QUANTITY'),$rest);
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
}

?>