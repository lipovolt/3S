<?php

class WinitDeSaleAction extends CommonAction{
	public function index($account){
		if($_POST['keyword']==""){
			$this->getWinitDeSaleInfo($account);
        }
        else{           
            $this->getWinitDeKeywordSaleInfo($account);
        }
	}

	private function getWinitDeSaleInfo($account){
		$products = M(C('DB_PRODUCT'));
		$salePlanTable=M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $tpl = $products->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($tpl as $key=>$value) {
        	$data[$key][C('DB_PRODUCT_SKU')]=$value[C('DB_PRODUCT_SKU')];
        	$data[$key][C('DB_PRODUCT_CNAME')]=$value[C('DB_PRODUCT_CNAME')];
        	$data[$key][C('DB_PRODUCT_PRICE')]=$value[C('DB_PRODUCT_PRICE')];
        	$data[$key][C('DB_PRODUCT_DETARIFF')]=$value[C('DB_PRODUCT_DETARIFF')]/100;
        	$data[$key]['winit-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TODE')]=$value[C('DB_PRODUCT_TODE')];
        	$data[$key]['way-to-de-fee']=$data[$key][C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['pweight'],$value['plength'],$value['pwidth'],$value['pheight']);

        	$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]=$salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_RC_DE_SALE_PLAN_PRICE'));
        	if($this->getMarketByAccount($account)=='ebay'){
        		$data[$key]['cost']=round($this->getWinitDeCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_DETARIFF')],$data[$key]['winit-fee'],$data[$key]['way-to-de-fee'],$data[$key]['local-shipping-fee'],$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]),2);
        	}else{
        		$this->error('无法找到与 '.$account.' 匹配的平台！不能显示销售表！');
        	}
        	$data[$key]['gprofit']=$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]*100,2).'%';
        	$data[$key]['pweight']=$value[C('DB_PRODUCT_PWEIGHT')];
        	$data[$key]['plength']=$value[C('DB_PRODUCT_PLENGTH')];
        	$data[$key]['pwidth']=$value[C('DB_PRODUCT_PWIDTH')];
        	$data[$key]['pheight']=$value[C('DB_PRODUCT_PHEIGHT')];
        	
        }
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getWinitDeKeywordSaleInfo($account){
		$products = M(C('DB_PRODUCT'));
		$salePlanTable=M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
        import('ORG.Util.Page');
        $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
        $count = $products->where($where)->count();
        $Page = new Page($count);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();        
        $tpl = $products->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        foreach ($tpl as $key=>$value) {
        	$data[$key][C('DB_PRODUCT_SKU')]=$value[C('DB_PRODUCT_SKU')];
        	$data[$key][C('DB_PRODUCT_CNAME')]=$value[C('DB_PRODUCT_CNAME')];
        	$data[$key][C('DB_PRODUCT_PRICE')]=$value[C('DB_PRODUCT_PRICE')];
        	$data[$key][C('DB_PRODUCT_DETARIFF')]=$value[C('DB_PRODUCT_DETARIFF')]/100;
        	$data[$key]['winit-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TODE')]=$value[C('DB_PRODUCT_TODE')];
        	$data[$key]['way-to-de-fee']=$data[$key][C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['pweight'],$value['plength'],$value['pwidth'],$value['pheight']);
        	$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]=$salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_RC_DE_SALE_PLAN_PRICE'));
        	if($this->getMarketByAccount($account)=='ebay'){
        		$data[$key]['cost']=round($this->getWinitDeCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_DETARIFF')],$data[$key]['winit-fee'],$data[$key]['way-to-de-fee'],$data[$key]['local-shipping-fee'],$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]),2);
        	}else{
        		$this->error('无法找到与 '.$account.' 匹配的平台！不能显示销售表！');
        	}
        	$data[$key]['gprofit']=$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_RC_DE_SALE_PLAN_PRICE')]*100,2).'%';
        	$data[$key]['pweight']=$value[C('DB_PRODUCT_PWEIGHT')];
        	$data[$key]['plength']=$value[C('DB_PRODUCT_PLENGTH')];
        	$data[$key]['pwidth']=$value[C('DB_PRODUCT_PWIDTH')];
        	$data[$key]['pheight']=$value[C('DB_PRODUCT_PHEIGHT')];
        }
        $this->assign('keyword',I('post.keyword','','htmlspecialchars'));
        $this->assign('keywordValue',I('post.keywordValue','','htmlspecialchars'));
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	public function suggest($account,$kw=null,$kwv=null){
		if($_POST['keyword']=="" && $kwv==null){
            $Data = D($this->getSalePlanViewModel($account));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $suggest = $Data->order(C('DB_RC_DE_SALE_PLAN_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($suggest as $key => $value) {
	        	$suggest[$key]['profit'] = round(($value[C('DB_RC_DE_SALE_PLAN_PRICE')] - $value[C('DB_RC_DE_SALE_PLAN_COST')]),2);
	        	$suggest[$key]['grate'] = round(($value[C('DB_RC_DE_SALE_PLAN_PRICE')] - $value[C('DB_RC_DE_SALE_PLAN_COST')]) / $value[C('DB_RC_DE_SALE_PLAN_PRICE')]*100,2);
	        }
            $this->assign('suggest',$suggest);
            $this->assign('page',$show);
        }
        else{
        	if($_POST['keyword']==""){
        		$keyword = $kw;
        		$keywordValue = $kwv;
        	}else{
        		$keyword = I('post.keyword','','htmlspecialchars');
        		$keywordValue = I('post.keywordValue','','htmlspecialchars');
        	}
            $where[$keyword] = array('like','%'.$keywordValue.'%');
            $suggest = D($this->getSalePlanViewModel($account))->where($where)->select();
            foreach ($suggest as $key => $value) {
	        	$suggest[$key]['profit'] = $value[C('DB_RC_DE_SALE_PLAN_PRICE')] - $value[C('DB_RC_DE_SALE_PLAN_COST')];
	        	$suggest[$key]['grate'] = round(($value[C('DB_RC_DE_SALE_PLAN_PRICE')] - $value[C('DB_RC_DE_SALE_PLAN_COST')]) / $value[C('DB_RC_DE_SALE_PLAN_PRICE')]*100,2);
	        }
	        $this->assign('suggest',$suggest);
            $this->assign('keyword',$keyword);
            $this->assign('keywordValue',$keywordValue);
        }
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->display();
	}

	public function updateSalePlan($account, $kw=null,$kwv=null){
		$data=null;
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		foreach ($_POST as $key => $value) {
			$arr = explode("-",$key);
			if($arr[0]=='id'){
				$data[$arr[1]]['id']=$value; 
			}
			if($arr[0]=='sku'){
				$data[$arr[1]]['sku']=$value; 
			}
			if($arr[0]=='sale_price'){
				$data[$arr[1]]['sale_price']=$value; 
			}
			if($arr[0]=='status'){
				$data[$arr[1]]['status']=$value; 
			}
		}
		$salePlanTable->startTrans();
		foreach ($data as $key => $value) {
			$value[C('DB_RC_DE_SALE_PLAN_COST')] = $this->calSuggestCost($account,$value['sku'],$value['sale_price']);
			if($value['status']=="on"){
				$value['status']=1;
			}else{
				$value['status']=0;
			}
			$salePlanTable->save($value);
		}
		$salePlanTable->commit();
		$this->calSaleInfoHandle($account);
		if($kwv==null){
			$this->success('保存成功');
		}else{
			$this->success('修改已保存',U('suggest',array('account'=>$account,'kw'=>$kw,'kwv'=>$kwv)));
		}		
	}

	private function calSuggestCost($account,$sku,$sale_price=null){
		//计算产品销售成本
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_DETARIFF')]=$product[C('DB_PRODUCT_DETARIFF')]/100;
    	$data['winit-fee']=$this->calWinitSIOFee($product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]);
    	$data['way-to-de-fee']=$product[C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]);
    	$data['local-shipping-fee']=$this->getWinitLocalShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]);
		
		$salePlan = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$sku))->find();
		if($sale_price!=null){
			if($this->getMarketByAccount($account)=='ebay'){
				return $this->getWinitDeCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_DETARIFF')],$data['winit-fee'],$data['way-to-de-fee'],$data['local-shipping-fee'],$sale_price);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');
		}elseif($salePlan[C('DB_RC_DE_SALE_PLAN_PRICE')]!=0 && $salePlan[C('DB_RC_DE_SALE_PLAN_PRICE')]!=null && $salePlan[C('DB_RC_DE_SALE_PLAN_PRICE')]!=''){
			if($this->getMarketByAccount($account)=='ebay'){
				return $this->getWinitDeCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_DETARIFF')],$data['winit-fee'],$data['way-to-de-fee'],$data['local-shipping-fee'],$salePlan[C('DB_RC_DE_SALE_PLAN_PRICE')]);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');
		}else{
			if($this->getMarketByAccount($account)=='ebay'){
				$tmpSalePrice = $this->getWinitEbayISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_DETARIFF')],$data['winit-fee'],$data['way-to-de-fee'],$data['local-shipping-fee']);
				return $this->getWinitDeCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_DETARIFF')],$data['winit-fee'],$data['way-to-de-fee'],$data['local-shipping-fee'],$salePlan[C('DB_RC_DE_SALE_PLAN_PRICE')]);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');			
		}
	}

	public function calSaleInfo($account){
		$this->calSaleInfoHandle($account);
		$this->redirect('suggest',array('account'=>$account));
	}

	private function calSaleInfoHandle($account){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$winitDeProduct = M(C('DB_WINIT_DE_STORAGE'))->distinct(true)->field(C('DB_WINIT_DE_STORAGE_SKU'))->select();
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error("无法找到匹配的销售表！");
		}
		foreach ($winitDeProduct as $key => $p) {
			$usp = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$p[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			if($usp == null){
				$this->addProductToUsp($account,$p[C('DB_WINIT_DE_STORAGE_SKU')]);
				$usp = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$p[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			}else{
				$usp[C('DB_RC_DE_SALE_PLAN_COST')]=$this->calSuggestCost($account,$p[C('DB_WINIT_DE_STORAGE_SKU')],$usp[C('DB_RC_DE_SALE_PLAN_PRICE')]);
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$p[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($p[C('DB_WINIT_DE_STORAGE_SKU')])){
				//产品信息不全，建议完善产品信息
				$usp[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('complete_product_info');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$p[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			}elseif(!$this->isWinitDeSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息
				$usp[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('complete_sale_info');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$p[C('DB_WINIT_DE_STORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlanTable->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calSuggest($account,$p[C('DB_WINIT_DE_STORAGE_SKU')]);
					$usp[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_RC_DE_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_RC_DE_SALE_PLAN_SUGGEST')];
					$this->updateUsp($account,$usp);
				}
			}
			$usp=null;
		}
	}

	private function addProductToUsp($account,$sku){
		//添加产品到ussw_sale_plan表
		$newUsp[C('DB_RC_DE_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_RC_DE_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_RC_DE_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')] =null;
		$newUsp[C('DB_RC_DE_SALE_PLAN_COST')] = $this->calSuggestCost($account,$sku);
		$price =  M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE'));
		if($price==null || $price==0){
			$price = $newUsp[C('DB_RC_DE_SALE_PLAN_COST')];
		}
		$newUsp[C('DB_RC_DE_SALE_PLAN_PRICE')] = $this->calWinitDeInitialPrice($account,$sku);
		$newUsp[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_RC_DE_SALE_PLAN_STATUS')] = 1;

		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->add($newUsp);
	}

	private function calWinitDeInitialPrice($account,$sku){
		//计算产品美自建仓初始售价
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_DETARIFF')]=$product[C('DB_PRODUCT_DETARIFF')]/100;
    	$data['winit-fee']=$this->calWinitSIOFee($product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]);
    	$data['way-to-de-fee']=$product[C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]);
    	$data['local-shipping-fee']=$this->getWinitLocalShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product['plength'],$product['pwidth'],$product['pheight']);

    	if($this->getMarketByAccount($account)=='ebay'){
			return $this->getWinitEbayISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_DETARIFF')],$data['winit-fee'],$data['way-to-de-fee'],$data['local-shipping-fee']);
		}
		$this->error('无法找到与 '.$account.' 匹配的平台！不能计算初始售价！');
	}

	private function updateUsp($account,$usp){
		//更新产品销售建议
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->save($usp);
	}

	private function isProductInfoComplete($sku){
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
		if($product[C('DB_PRODUCT_PRICE')]==null || $product[C('DB_PRODUCT_PRICE')]==0)
			return false;
		if($product[C('DB_PRODUCT_WEIGHT')]==null || $product[C('DB_PRODUCT_WEIGHT')]==0)
			return false;
		if($product[C('DB_PRODUCT_LENGTH')]==null || $product[C('DB_PRODUCT_LENGTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_WIDTH')]==null || $product[C('DB_PRODUCT_WIDTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_HEIGHT')]==null || $product[C('DB_PRODUCT_HEIGHT')]==0)
			return false;
		if($product[C('DB_PRODUCT_PWEIGHT')]==null || $product[C('DB_PRODUCT_PWEIGHT')]==0)
			return false;
		if($product[C('DB_PRODUCT_PLENGTH')]==null || $product[C('DB_PRODUCT_PLENGTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_PWIDTH')]==null || $product[C('DB_PRODUCT_PWIDTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_PHEIGHT')]==null || $product[C('DB_PRODUCT_PHEIGHT')]==0)
			return false;
		return true;
	}

	private function isWinitDeSaleInfoComplete($usp){
		if($usp[C('DB_RC_DE_SALE_PLAN_COST')]==null || $usp[C('DB_RC_DE_SALE_PLAN_COST')]==0)
			return false;
		return true;
	}

	private function calSuggest($account,$sku){
		//返回数组包含销售建议和价格
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$saleplan = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_SKU')=>$sku))->find();
		$cost = $saleplan[C('DB_RC_DE_SALE_PLAN_COST')];
		$price = $saleplan[C('DB_RC_DE_SALE_PLAN_PRICE')];
		$status = $saleplan[C('DB_RC_DE_SALE_PLAN_STATUS')];

		if($status==0){
			//item needn't to calculate.
			$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$sugg[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
			return $sugg;
		}

		$metaMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'))->where($metaMap)->find();		
		$clear_nod = $metadata[C('DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD')];
		$relisting_nod = $metadata[C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD')];
		$adjust_period = $metadata[C('DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD')];
		$spr1 = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR1')];
		$spr2 = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR2')];
		$spr3 = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR3')];
		$spr4 = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR4')];
		$spr5 = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR5')];
		$pcr = $metadata[C('DB_USSW_SALE_PLAN_METADATA_PCR')];
		$sqnr = $metadata[C('DB_USSW_SALE_PLAN_METADATA_SQNR')];
		$denominator = $metadata[C('DB_USSW_SALE_PLAN_METADATA_DENOMINATOR')];
		$grfr = $metadata[C('DB_USSW_SALE_PLAN_METADATA_GRFR')];
		$standard_period = $metadata[C('DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD')];	

		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$asqsq = intval($this->calWinitDeSaleQuantity($account,$sku,$startDate))*intval($standard_period)/intval($adjust_period);
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$lspsq = $this->calWinitDeSaleQuantity($account,$sku,$startDate,$endDate)*$standard_period/$adjust_period;

		//检查是否需要重新刊登
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
			$relistingNodSaleQuantity = $this->calWinitDeSaleQuantity($account,$sku,$startDate);
			if($relistingNodSaleQuantity==0){
				$sugg=null;
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $cost+$cost*$this->getCostClass($cost)/100;
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_RELISTING');
				return $sugg;
			}
		}

		//检查是否需要清货清货
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$clear_nod);
			$clearNodSaleQuantity = $this->calWinitDeSaleQuantity($account,$sku,$startDate);
			if($clearNodSaleQuantity==0){
				$sugg=null;
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_CLEAR');
				return $sugg;
			}
		}


		//检查是否需要调价
		$diff = $asqsq-$lspsq;
		if($lspsq<$sqnr){
			$lspsq = $denominator;
		}
		if($diff/$lspsq>$grfr/100){
			$sugg=null;
			$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $price+$price*($pcr/100);
			$sugg[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_UP');
			return $sugg;
		}
		if($diff/$lspsq<-($grfr/100)){
			$sugg=null;
			if($price-$price*($pcr/100)<$cost){
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
			}else{
				$sugg[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = $price-$price*($pcr/100);
			}			
			$sugg[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_DOWN');
			return $sugg;
		}

	}

	//calculate ebay initial sale price according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee)
	private function getWinitEbayISP($pPrice,$tariff,$wFee,$tFee,$sFee){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($pPrice+0.5)/$exchange+($pPrice/$exchange)*$tariff+$wFee+$tFee+$sFee;
		return round ((($cost+0.35)*(1+$this->getCostClass($cost)/100))/(1-0.144*(1+$this->getCostClass($cost)/100)),2);
	}

	private function calWinitDeSaleQuantity($account, $sku, $startDate, $endDate=null){
		if($endDate==null)
			$endDate = date('Y-m-d H:i:s',time());
		$WinitDeOutboundItem = D("WinitOutboundView");
		$map[C('DB_WINIT_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
		$map[C('DB_WINIT_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
		$map[C('DB_WINIT_OUTBOUND_SELLER_ID')] = array('eq',$account);
		$map[C('DB_WINIT_OUTBOUND_BUYER_COUNTRY')] = array('neq','US');
		return $usswOutboundItem->where($map)->sum(C('DB_WINIT_OUTBOUND_ITEM_QUANTITY'));
	}

	public function bIgnoreHandle($account,$kw=null,$kwv=null){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			$data[C('DB_RC_DE_SALE_PLAN_ID')] = $value;
			$data[C('DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			$data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
			$salePlanTable->save($data);
		}
		$salePlanTable->commit();
		$this->success('保存成功');
	}

	public function bModifyHandle($account){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			$data = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_ID')=>$value))->find();
			if($data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_RC_DE_SALE_PLAN_SUGGEST')]!=null){
				$data[C('DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
				if($data[C('DB_RC_DE_SALE_PLAN_SUGGEST')]=='relisting'){
					$data[C('DB_RC_DE_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_RC_DE_SALE_PLAN_RELISTING_TIMES')])+1;
				}

				if($data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')]==null){
					$data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_RC_DE_SALE_PLAN_PRICE')].' '.date('ymd',time());
				}else{
					$data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_RC_DE_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
				}

				$data[C('DB_RC_DE_SALE_PLAN_PRICE')] = $data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')];
				$data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
				$salePlanTable->save($data);
			}
		}
		$salePlanTable->commit();
		$this->success('修改成功');
	}

	public function confirmSuggest($account,$id){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$data = $salePlanTable->where(array(C('DB_RC_DE_SALE_PLAN_ID')=>$id))->find();
		if($data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_RC_DE_SALE_PLAN_SUGGEST')]!=null){
			$data[C('DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			if($data[C('DB_RC_DE_SALE_PLAN_ID_SUGGEST')]=='relisting'){
				$data[C('DB_RC_DE_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_RC_DE_SALE_PLAN_RELISTING_TIMES')])+1;
			}

			if($data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')]==null){
				$data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_RC_DE_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}else{
				$data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_RC_DE_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_RC_DE_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
			}

			$data[C('DB_RC_DE_SALE_PLAN_PRICE')] = $data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')];
			$data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
			$salePlanTable->save($data);
		}else{
			$this->error('无法保存，当前产品没有销售建议');
		}
		$this->success('保存成功');
	}

	public function ignoreSuggest($account,$id){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$data[C('DB_RC_DE_SALE_PLAN_ID')] = $id;
		$data[C('DB_RC_DE_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
		$data[C('DB_RC_DE_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$data[C('DB_RC_DE_SALE_PLAN_SUGGEST')] = null;
		$salePlanTable->save($data);
		$this->success('保存成功');
	}

	private function getCostClass($cost){
		$metaMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'))->find();
		if($cost<=10)
			return $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR1')];
		if($cost>10 && $cost<=20)
			return $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR2')];
		if($cost>20 && $cost<=30)
			return $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR3')];
		if($cost>30 && $cost<=50)
			return $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR4')];
		if($cost>50)
			return $metadata[C('DB_USSW_SALE_PLAN_METADATA_SPR5')];
	}

	//Return the sale plan view model according to the account
    private function getSalePlanViewModel($account){
    	switch ($account) {
    		case 'rc-helicar':
    			return 'RcDeSalePlanView';
    		default:
    			return null;
    	}
    }

    //Return the market according to the account
    private function getMarketByAccount($account){
    	switch ($account) {
    		case 'rc-helicar':
    			return 'ebay';
    		default:
    			return null;
    	}
    }

    //Return the sale plan table name according to the account
    private function getSalePlanTableName($account){
    	switch ($account) {
    		case 'rc-helicar':
    			return C('DB_RC_DE_SALE_PLAN');
    		default:
    			return null;
    	}
    }

	private function calWinitSIOFee($weight,$l,$w,$h){
		//月仓储费=立方米*每日每立方租金*30天
		$monthlyStorageFee = ($l*$w*$h)/1000000*1.2*30;
		$itemInOutFee = 0;
		if($weight>0 And $weight <= 500){
			$itemInOutFee = 0.17 + 0.04;
		}
		elseif($weight>500 and $weight <= 1000){
			$itemInOutFee = 0.24 + 0.05;
		}
		elseif($weight>1000 and $weight <= 2000){
			$itemInOutFee = 0.39 + 0.06;
		}
		elseif($weight>2000 and $weight <= 10000){
			$itemInOutFee = 0.5 + 0.14;
		}
		elseif($weight>10000 and $weight <= 20000){
			$itemInOutFee = 1.05 + 0.2;
		}
		elseif($weight>20000 and $weight <= 30000){
			$itemInOutFee = 1.39 + 0.28;
		}
		elseif((1.39 + (floatval(($weight - 30000) / 10000) + 1) * 0.7 + 0.28 + (floatval((weight - 30000) / 10000) + 1) * 0.14) < (13.29 + 1.4) ){
			$itemInOutFee = 1.39 + (floatval(($weight - 30000) / 10000) + 1) * 0.7 + 0.28 + (floatval((weight - 30000) / 10000) + 1) * 0.14;
		}
		else{
			$itemInOutFee = 13.29 + 1.4;
		}
		return round($monthlyStorageFee+$itemInOutFee,2);
	}

	private function getWinitAirFirstTransportFee($weight,$l,$w,$h){
		$eurToUsd = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTOUSD'));
		/*if(($weight/1000)>=($l * $w * $h / 6000)){
			return round($weight / 1000 * 5.8 / $eurToUsd,2);
		}
		else{
			return round(($l * $w * $h) / 6000 * 5.8  / $eurToUsd,2);
		}*/
		return round($weight / 1000 * 5.8 / $eurToUsd,2);	
	}

	private function getWinitSeaFirstTransportFee($l,$w,$h){
		$eurToUsd = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTOUSD'));
		return round(($l * $w * $h) / 1000000 * 170  / $eurToUsd,2);
	}

	private function getWinitLocalShippingWay($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'DE Post Small Letter',
				2=>'DE Post Large Letter',
				3=>'DPD Small Parcels',
				4=>'DPD Normal Parcels',
				5=>'DHL Packet Service'
			);
		$fees=array(
				0=>0,
				1=>$this->calWinitPostSmallFee($weight,$l,$w,$h),
				2=>$this->calWinitPostLargeFee($weight,$l,$w,$h),
				3=>$this->calWinitDPDSmallFee($weight,$l,$w,$h),
				4=>$this->calWinitDPDNormalFee($weight,$l,$w,$h),
				5=>$this->calWinitDHLFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 6; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getWinitLocalShippingFee($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calWinitPostSmallFee($weight,$l,$w,$h),
				2=>$this->calWinitPostLargeFee($weight,$l,$w,$h),
				3=>$this->calWinitDPDSmallFee($weight,$l,$w,$h),
				4=>$this->calWinitDPDNormalFee($weight,$l,$w,$h),
				5=>$this->calWinitDHLFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 6; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function calWinitPostSmallFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 500 and $l >=10 and $l<=35.3 and $w>=7 and $w<=25 and $h>0 and $h<=2){
			return 1.67;
		}else{
			return 0;
		}		
	}

	private function calWinitPostLargeFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 1000 and $l >=10 and $l<=35.3 and $w>=7 and $w<=30 and $h>0 and $h<=15){
			if($weight>=0 and $weight<=500){
				return 2.06;
			}
			elseif($weight>500 and $weight<=800){
				return 2.69;
			}
			elseif($weight>800 and $weight<=1000){
				return 2.76;
			}			
		}
		else{
			return 0;
		}
	}

	private function calWinitDPDSmallFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 3000 and $l<=50 and $l>=16 and $w>=11 and $h>=2 and ($l + 2 * ($w + $h))<=110){
			return 3.32;
		}
		else{
			return 0;
		}
	}

	private function calWinitDPDNormalFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 31500 and $l<=175 and $l>=16 and $w>=11 and $h>=2 and ($l + 2 * ($w + $h))<=300){
			return 3.75;
		}else{
			return 0;
		}
	}

	private function calWinitDHLFee($weight,$l,$w,$h){
		$fee = 0;
		if($weight>0 And $weight <= 31500 and $l<=200 and $w<=200 and $h<=200 and ($l + 2 * ($w + $h))<=360){
			if($weight>0 and $weight<=1000){
				$fee = 3.28;
			}elseif($weight>1000 and $weight<=5000){
				$fee = 3.52;
			}elseif($weight>5000 and $weight<=10000){
				$fee = 3.95;
			}elseif($weight>10000 and $weight<=20000){
				$fee = 4.76;
			}elseif($weight>20000 and $weight<=31500){
				$fee = 5.2;
			}
			if(($l>=120 or $w>=60 or $h>=60) and ($l<=200 or $w<=200 or $h<=200) and ($l + 2 * ($w + $h))<=360){
				$fee = $fee+12;
			}
		}
		return $fee;
	}

	private function getWinitDeCost($pPrice,$tariff,$wFee,$tFee,$sFee,$sPrice){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTORMB'));
		$c = ($pPrice+0.5)/$exchange+($pPrice/$exchange)*$tariff+$wFee+$tFee+$sFee+$sPrice*0.164+0.35;
		return $c;
	}

	public function updateSalePrice($market,$account){
    	$this->assign('market',$market);
    	$this->assign('account',$account);
    	$this->display();
    }

    public function updateSalePriceHandle($market,$account){
    	if($market=='ebay'){
			$this->updateEbaySalePriceHandle($account);
		}else{
			$this->error('没有 '.$market.' 平台');
		}    	
    }

    private function updateEbaySalePriceHandle($account){
    	if (!empty($_FILES)) {
    		import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/updateSalePrice/',
			 'saveRule'=>'ebay'.'_'.$account.'_'.time(),
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

			//creat excel writer
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			

			$objPHPExcel->setActiveSheetIndex(0);
			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow(); // 取得总行数
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数

			//excel first column name verify
            for($c='A';$c<=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
            }

            if($this->verifyEbayFxtcn($firstRow)){
            	$salePlan=M($this->getSalePlanTableName($account));
            	$salePlan->startTrans();
            	for($i=2;$i<=$highestRow;$i++){
            		$sku = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
            		$country = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            		if($country==null || $country==''){
            			for ($r=$i-1; $r>1; $r--) { 
            				$country = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
            				if($country!=null && $country!=''){
            					break;
            				}
            			}
            		}
            		if($sku!='' && $sku!=null && $country=='Germany'){
            			$map[C("DB_USSW_SALE_PLAN_SKU")]=array("eq",$sku);
            			$actualPrice = $salePlan->where($map)->getField(C("DB_USSW_SALE_PLAN_PRICE"));
            			if($actualPrice!=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue()){
	            			$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
							if($data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]==null){
								$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('ymd',time());
							}else{
								$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
							}
							$data[C("DB_USSW_SALE_PLAN_PRICE")]=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
							$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
							$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
							$salePlan->where($map)->save($data);
							$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]=null;
	            		}
            		}
            	}
            	$salePlan->commit();
            	$this->success("更新产品售价成功");
            }else{
            	$this->error("模板错误，请检查模板！");
            }
    	}else{
    		$this->error("请选择上传的文件");
    	}
    }

    //Verify imported file exchange template column name
	private function verifyEbayFxtcn($firstRow){
        for($c='B';$c<=max(array_keys(C('IMPORT_EBAY_FXT')));$c++){
            if($firstRow[$c] != C('IMPORT_EBAY_FXT')[$c])
                return false;
        }
        return true;
    }

    public function fileExchange($market,$account){
		$this->assign('market',$market);
		$this->assign('account',$account);
		$this->display();
	}

	public function fileExchangeHandle($market,$account){
		if($market=='ebay'){
			$this->ebayFileExchangeHandle($account);
		}else{
			$this->error('没有 '.$market.' 平台');
		}

	}

	private function ebayFileExchangeHandle($account){
		if (!empty($_FILES)) {
			import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/fileExchange/',
			 'saveRule'=>'ebayFileExchange'.'_'.time(),
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

			//creat excel writer
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			

			$objPHPExcel->setActiveSheetIndex(0);
			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow(); // 取得总行数
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数

			//excel first column name verify
            for($c='A';$c<=$highestColumn;$c++){
                $firstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
            }

            if($this->verifyEbayFxtcn($firstRow)){
            	$storageTable=M($this->getStorageTableName($account));
            	$salePlanTable=M($this->getSalePlanTableName($account));

                for($i=2;$i<=$highestRow;$i++){
                	$data[$i-2][$firstRow['A']]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
        			$data[$i-2][$firstRow['B']]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
        			$data[$i-2][$firstRow['C']]=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
        			$data[$i-2][$firstRow['D']]=$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
        			$data[$i-2][$firstRow['E']]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
        			$data[$i-2][$firstRow['F']]=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
        			$data[$i-2][$firstRow['G']]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                	$data[$i-2][$firstRow['H']]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
                	$data[$i-2][$firstRow['I']]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
        			$data[$i-2][$firstRow['J']]=$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
        			$data[$i-2][$firstRow['K']]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();

                	$country = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            		if($country==null || $country==''){
            			for ($r=$i-1; $r>1; $r--) { 
            				$country = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
            				if($country!=null && $country!=''){
            					break;
            				}
            			}
            		}

            		if($country=='Germany'){
            			$splitSku = $this->splitSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
						if(count($splitSku)==1){
	                		//Single sku
	                		$salePlan=$salePlanTable->where(array('sku'=>$splitSku[0][0]))->find();
	                		$ainventory=$storageTable->where(array('sku'=>$splitSku[0][0]))->getField('ainventory');
	                		if($splitSku[0][1]==1){
	                			//Single sku and Single sale quantity, get the ainventory quantity and the suggested sale price
	                			$data[$i-2]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
	                			$data[$i-2]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
	                			$data[$i-2][$firstRow['H']]=$ainventory;
	                		}else{
	                			//Single sku and multiple sale quantity
	                			$data[$i-2]['Suggest']="多个一组销售商品，无法给出建议售价";
	                			$data[$i-2][$firstRow['H']]=intval($ainventory/$splitSku[0][1]);
	                		}

	                	}else{
	                		//Multiple sku
	                		$data[$i-2]['Suggest']="组合销售商品，无法给出建议售价";
	                		$data[$i-2][$firstRow['H']]=65536;
	                		foreach ($splitSku as $key => $skuQuantity){
	                			$ainventory=$storageTable->where(array('sku'=>$skuQuantity[0]))->getField('ainventory');
	                			if($skuQuantity[1]==1){
	                				//Multiple sku and Single sale quantity
	                				if($ainventory<$data[$i-2][$firstRow['H']]){
	                					$data[$i-2][$firstRow['H']]=$ainventory;
	                				}
	                			}else{
	                				//Multiple sku and Multiple sale quantity
	                				if(intval($ainventory/$skuQuantity[1])<$data[$i-2][$firstRow['H']]){
	                					$data[$i-2][$firstRow['H']]=intval($ainventory/$skuQuantity[1]);
	                				}
	                			}
	                		}
	                	} 
            		}                	                
                }

                //find item in stock but not listed
                $map[C('DB_WINIT_DE_STORAGE_AINVENTORY')] = array('gt',0);
                $storages=$storageTable->where($map)->select();
                $newIndex = $highestRow+1;
                foreach ($storages as $key => $value) {

                	$listed=false;
                	for ($i=2;$i<=$highestRow;$i++) {
                		$country = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
	            		if($country==null || $country==''){
	            			for ($r=$i-1; $r>1; $r--) { 
	            				$country = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
	            				if($country!=null && $country!=''){
	            					break;
	            				}
	            			}
	            		}
                		if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()==$value[C('DB_WINIT_DE_STORAGE_SKU')] && $country=='Germany'){
                			$listed=true;
                		}
                	}
                	if($listed==false){
                		$data[$newIndex][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
                		$data[$newIndex]['Suggest']="未刊登商品";
                		$newIndex++;
                	}
                }

                $excelCellName[0]=$objPHPExcel->getActiveSheet()->getCell("A1")->getValue();
                $excelCellName[1]=$objPHPExcel->getActiveSheet()->getCell("B1")->getValue();
                $excelCellName[2]=$objPHPExcel->getActiveSheet()->getCell("C1")->getValue();
                $excelCellName[3]=$objPHPExcel->getActiveSheet()->getCell("D1")->getValue();
                $excelCellName[4]=$objPHPExcel->getActiveSheet()->getCell("E1")->getValue();
                $excelCellName[5]=$objPHPExcel->getActiveSheet()->getCell("F1")->getValue();
                $excelCellName[6]='SuggestPrice';
                $excelCellName[7]='Suggest';
                $excelCellName[8]=$objPHPExcel->getActiveSheet()->getCell("G1")->getValue();
                $excelCellName[9]=$objPHPExcel->getActiveSheet()->getCell("H1")->getValue();
                $excelCellName[10]=$objPHPExcel->getActiveSheet()->getCell("I1")->getValue();
                $excelCellName[11]=$objPHPExcel->getActiveSheet()->getCell("J1")->getValue();
                $excelCellName[12]=$objPHPExcel->getActiveSheet()->getCell("K1")->getValue();
                $this->exportEbayFileExchangeExcel('RcFileExchange',$excelCellName,$data); 
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
        }
	}

	//Return the storage table name according to the account
    private function getStorageTableName($account){
    	switch ($account) {
    		case 'rc-helicar':
    			return C('DB_WINIT_DE_STORAGE');
    		default:
    			return null;
    	}
    }

    //Split sku according to | and *, then return a 2d array. 
    private function splitSku($sku){
    	$skuDepart = explode("|",$sku);
    	foreach ($skuDepart as $key => $departedSku) {
            $skuQuantityDepart = explode("*",$departedSku);
            if(count($skuQuantityDepart)==1){
                $splitSku[$key][0]=$skuQuantityDepart[0];
                $splitSku[$key][1]=1;
            }else{
                $splitSku[$key][0]=$skuQuantityDepart[0];
                $splitSku[$key][1]=$skuQuantityDepart[1];
            }
        }
        return $splitSku;
    }

    private function exportEbayFileExchangeExcel($expTitle,$expCellName,$expTableData){
        $fileName = $expTitle.date('_Ymd');
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i]); 
        } 

        for($i=0;$i<$dataNum;$i++){
        	$objPHPExcel->getActiveSheet()->getStyle('B'.($i+2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            for($j=0;$j<$cellNum;$j++){
            	if($i>0 && $expTableData[$i][$expCellName[6]] !=null && $expTableData[$i][$expCellName[5]]!=$expTableData[$i][$expCellName[6]]){
            		$objPHPExcel->getActiveSheet()->getStyle( 'F'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'F'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
            		$objPHPExcel->getActiveSheet()->getStyle( 'G'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'G'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
            	}
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j]]);
            }             
        }  

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output');
        exit;   
    }
}

?>