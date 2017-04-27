<?php

class SzSaleAction extends CommonAction{

	public function suggest($account,$country=null,$kw=null,$kwv=null){
		$Data=D($this->getSalePlanViewModelName($account,$country));
		if($_POST['keyword']=="" && $kwv==null){ 
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $suggest = $Data->order('sku')->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($suggest as $key => $value) {
	        	$suggest[$key]['profit'] = $value[C('DB_SZ_US_SALE_PLAN_PRICE')] - $value[C('DB_SZ_US_SALE_PLAN_COST')];
	        	$suggest[$key]['grate'] = round(($value[C('DB_SZ_US_SALE_PLAN_PRICE')] - $value[C('DB_SZ_US_SALE_PLAN_COST')]) / $value[C('DB_SZ_US_SALE_PLAN_PRICE')]*100,2);
	        }
	        
            $this->assign('suggest',$suggest);
            $this->assign('country',$country);
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
            $suggest = $Data->where($where)->select();
            foreach ($suggest as $key => $value) {
	        	$suggest[$key]['profit'] = $value[C('DB_SZ_US_SALE_PLAN_PRICE')] - $value[C('DB_SZ_US_SALE_PLAN_COST')];
	        	$suggest[$key]['grate'] = round(($value[C('DB_SZ_US_SALE_PLAN_PRICE')] - $value[C('DB_SZ_US_SALE_PLAN_COST')]) / $value[C('DB_SZ_US_SALE_PLAN_PRICE')]*100,2);
	        }
	        $this->assign('keyword',$keyword);
            $this->assign('keywordValue',$keywordValue);
            $this->assign('country',$country);
            $this->assign('suggest',$suggest);
        }
        $this->assign('account',$account);
	    $this->assign('market',$this->getMarketByAccountCountry($account,$country));
        $this->display();
	}

	public function getSuggest($account,$country=null,$kw=null,$kwv=null){
		$this->getSuggestHandle($account,$country);
		$this->success('更新成功！', U('suggest',array('account'=>$account,'country'=>$country,'kw'=>$kw,'kwv'=>$kwv)));
	}

	private function getSuggestHandle($account,$country=null){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$salePlan=M($this->getSalePlanTableName($account,$country));
		$szProduct = M(C('DB_SZSTORAGE'))->distinct(true)->field(C('DB_SZSTORAGE_SKU'))->select();
		foreach ($szProduct as $key => $p) {
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			if($usp == null){
				$this->addProductToUsp($p[C('DB_SZSTORAGE_SKU')],$account,$country);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}else{
				
				$usp[C('DB_SZ_US_SALE_PLAN_COST')]=$this->calSuggestCost($p[C('DB_SZSTORAGE_SKU')],$account,$country,$usp[C('DB_SZ_US_SALE_PLAN_PRICE')]);
				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($p[C('DB_SZSTORAGE_SKU')])){
				//产品信息不全，建议完善产品信息,退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = 'complete_product_info';
				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}elseif(!$this->isSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息，退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = 'complete_sale_info';
				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlan->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calSuggest($p[C('DB_SZSTORAGE_SKU')],$account,$country);
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')];
					$salePlan->save($usp);
				}
			}
		}
	}

	private function isSaleInfoComplete($usp){
		if($usp['cost']==null || $usp['cost']==0)
			return false;
		return true;
	}

	private function isProductInfoComplete($sku){
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
		if($product[C('DB_PRODUCT_PRICE')]==null || $product[C('DB_PRODUCT_PRICE')]==0)
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

	private function addProductToUsp($sku,$account,$country=null){
		//添加产品到sale_plan表
		$salePlan=M($this->getSalePlanTableName($account,$country));
		$newUsp[C('DB_SZ_US_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_SZ_US_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_PRICE')] = $this->calInitialPrice($sku,$account,$country);
		$newUsp[C('DB_SZ_US_SALE_PLAN_COST')] = $this->calSuggestCost($sku,$account,$country,$newUsp[C('DB_SZ_US_SALE_PLAN_PRICE')] );
		$newUsp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_STATUS')] = 1;
		$salePlan->add($newUsp);
	}

	private function calSuggestCost($sku,$account,$country,$sale_price=null){
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
		$salePlan = M($this->getSalePlanTableName($account,$country));
		$sp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$sku))->find();
		if($account=="vtkg5755" && $country == 'us'){
	    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
	    	$data['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
			return $this->getSzUsCost($data[C('DB_PRODUCT_PRICE')],$data['way-to-us-fee'],$sale_price);
		}
		if($account=="vtkg5755" && $country == 'de'){
	    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
	    	$data['way-to-de-fee']=$this->getSzDeShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_DE_SALE_PLAN_REGISTER')]);
			return $this->getSzDeCost($data[C('DB_PRODUCT_PRICE')],$data['way-to-de-fee'],$sale_price);
		}
		if($account=="zuck"){
	    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
	    	$data['globalShippingFee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
			return $this->getWishCost($data[C('DB_PRODUCT_PRICE')],$data['globalShippingFee'],$sale_price);
		}
		return null;
	}

	private function calInitialPrice($sku,$account,$country=null){
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
		if($account=="vtkg5755" && $country == 'us'){
			$register = M(C('DB_SZ_US_SALE_PLAN'))->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$sku))->getField(C('DB_SZ_US_SALE_PLAN_REGISTER'));
			$shippingFee=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$register);
			return $this->calUsInitialPrice($product[C('DB_PRODUCT_PRICE')],$shippingFee);
		}
		if($account=="vtkg5755" && $country == 'de'){
			$register = M(C('DB_SZ_DE_SALE_PLAN'))->where(array(C('DB_SZ_DE_SALE_PLAN_SKU')=>$sku))->getField(C('DB_SZ_DE_SALE_PLAN_REGISTER'));
			$shippingFee=$this->getSzDeShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$register);
			return $this->calDeInitialPrice($product[C('DB_PRODUCT_PRICE')],$shippingFee);
		}
		if($account=="zuck"){
			$register = M(C('DB_SZ_WISH_SALE_PLAN'))->where(array(C('DB_SZ_WISH_SALE_PLAN_SKU')=>$sku))->getField(C('DB_SZ_WISH_SALE_PLAN_REGISTER'));
			$shippingFee=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],1);
			return $this->calWishInitialPrice($product[C('DB_PRODUCT_PRICE')],$shippingFee);
		}
		return null;
	}

	private function calUsInitialPrice($productPrice,$shippingFee){
		$exchange = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		$salePrice = abs(round(($cost+0.3)/(1-0.139-$this->getCostClass($cost)/100),2));
		if($salePrice<12){
			$salePrice = abs(round(($cost+0.05)/(1-0.16-$this->getCostClass($cost)/100),2));
		}
		return $salePrice;
	}

	private function calDeInitialPrice($productPrice,$shippingFee){
		$exchange = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		$salePrice = abs(round(($cost+0.3)/(1-0.139-$this->getCostClass($cost)/100),2));
		if($salePrice<12){
			$salePrice = abs(round(($cost+0.05)/(1-0.16-$this->getCostClass($cost)/100),2));
		}
		return $salePrice;
	}

	private function calWishInitialPrice($productPrice,$shippingFee){
		$exchange = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		$salePrice = ($cost/(1-0.16-$this->getCostClass($cost)/100));
		return abs(round($salePrice,2));
	}

	public function confirmSuggest($id,$account,$country=null){
		$table=M($this->getSalePlanTableName($account,$country));
		$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
		if($data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] !=null){
			$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			if($data[C('DB_SZ_US_SALE_PLAN_ID_SUGGEST')]=='relisting'){
				$data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')])+1;
			}

			if($data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')]==null){
				$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}else{
				$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
			}

			$data[C('DB_SZ_US_SALE_PLAN_PRICE')] = $data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
			$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
			$table->save($data);
		}else{
			$this->error('无法保存，当前产品没有销售建议');
		}
		
		$this->success('修改成功');
	}

	public function ignoreSuggest($id,$account,$country=null){
		$table=M($this->getSalePlanTableName($account,$country));
		$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
		$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
		$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
		$table->save($data);
		$this->success('修改成功');
	}

	public function updateSalePlan($account,$country=null,$kw=null,$kwv=null){
		$salePlan=M($this->getSalePlanTableName($account,$country));
		$salePlan->startTrans();
		foreach ($_POST['id'] as $key => $value) {
			$data[C('DB_SZ_US_SALE_PLAN_ID')]=$value;
			$data[C('DB_SZ_US_SALE_PLAN_PRICE')]=$_POST['sale_price'][$key];
			/*$data[C('DB_SZ_US_SALE_PLAN_COST')] = $this->calSuggestCost($_POST['sku'][$key],$country,$_POST['sale_price'][$key]);*/
			if($_POST['register']==null){
				$data[C('DB_SZ_US_SALE_PLAN_REGISTER')]=0;
			}else{
				$data[C('DB_SZ_US_SALE_PLAN_REGISTER')]=array_search($value, $_POST['register'])===false?0:1;
			}
			if($_POST['status']==null){
				$data[C('DB_SZ_US_SALE_PLAN_STATUS')]=0;
			}else{
				$data[C('DB_SZ_US_SALE_PLAN_STATUS')]=array_search($value, $_POST['status'])===false?0:1;
			}
			$salePlan->save($data);
		}
		$salePlan->commit();
		$this->getSuggestHandle($account,$country);
		$this->success('保存成功！', U('suggest',array('account'=>$account,'country'=>$country,'kw'=>$kw,'kwv'=>$kwv)));
	}


	private function calSuggest($sku,$account,$country){
		//返回数组包含销售建议和价格
		$salePlanTable=M($this->getSalePlanTableName($account,$country));
		$saleplan = $salePlanTable->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$sku))->find();
		$cost = $saleplan[C('DB_SZ_US_SALE_PLAN_COST')];
		$price = $saleplan[C('DB_SZ_US_SALE_PLAN_PRICE')];
		$status = $saleplan[C('DB_SZ_US_SALE_PLAN_STATUS')];

		if($status==0){
			//item needn't to calculate.
			$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$sugg[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
			return $sugg;
		}

		$metaMap[C('DB_SZ_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_SZ_SALE_PLAN_METADATA'))->where($metaMap)->find();		
		$clear_nod = $metadata[C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD')];
		$relisting_nod = $metadata[C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD')];
		$adjust_period = $metadata[C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD')];
		$pcr = $metadata[C('DB_SZ_SALE_PLAN_METADATA_PCR')];
		$sqnr = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SQNR')];
		$denominator = $metadata[C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR')];
		$grfr = $metadata[C('DB_SZ_SALE_PLAN_METADATA_GRFR')];
		$standard_period = $metadata[C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD')];	

		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$asqsq = intval($this->calSaleQuantity($sku,$account,$country,$startDate))*intval($standard_period)/intval($adjust_period);
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$lspsq = $this->calSaleQuantity($sku,$account,$country,$startDate,$endDate)*$standard_period/$adjust_period;

		//检查是否需要重新刊登
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
			$relistingNodSaleQuantity = $this->calSaleQuantity($sku,$account,$country,$startDate);
			if($relistingNodSaleQuantity==0){
				$sugg=null;
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $cost+$cost*$this->getCostClass($cost)/100;
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_RELISTING');
				return $sugg;
			}
		}

		//检查是否需要清货
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$clear_nod);
			$clearNodSaleQuantity = $this->calSaleQuantity($sku,$account,$country,$startDate);
			if($clearNodSaleQuantity==0){
				$sugg=null;
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_US_CLEAR');
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
			$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $price+$price*($pcr/100);
			$sugg[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_PRICE_UP');
			return $sugg;
		}
		if($diff/$lspsq<-($grfr/100)){
			$sugg=null;
			if($price-$price*($pcr/100)<$cost){
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
			}else{
				$sugg[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $price-$price*($pcr/100);
			}			
			$sugg[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_PRICE_DOWN');
			return $sugg;
		}
	}

	private function calSaleQuantity($sku, $account, $country, $startDate, $endDate=null){
		if($endDate==null)
			$endDate = date('Y-m-d H:i:s',time());
		$szOutboundItem = D("SzOutboundView");
		$map[C('DB_SZ_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
		$map[C('DB_SZ_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
		$map[C('DB_SZ_OUTBOUND_SELLER_ID')] = array('eq',$account);
		if($country=='us'){
			$map[C('DB_SZ_OUTBOUND_BUYER_COUNTRY')] = array('in',array('United States','US','USA','Vereinigte Staaten','Vereinigte Staaten von Amerika'));
		}
		if($country=='de'){
			$map[C('DB_SZ_OUTBOUND_BUYER_COUNTRY')] = array('in',array('Germany','DE','Deutschland'));
		}
		return $szOutboundItem->where($map)->sum(C('DB_SZ_OUTBOUND_ITEM_QUANTITY'));
	}

	private function getCostClass($cost){
		$metaMap[C('DB_SZ_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_SZ_SALE_PLAN_METADATA'))->find();
		$spr1 = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR1')];
		$spr2 = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR2')];
		$spr3 = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR3')];
		$spr4 = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR4')];
		$spr5 = $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR5')];
		if($cost<=10)
			return $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR1')];
		if($cost>10 && $cost<=20)
			return $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR2')];
		if($cost>20 && $cost<=30)
			return $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR3')];
		if($cost>30 && $cost<=50)
			return $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR4')];
		if($cost>50)
			return $metadata[C('DB_SZ_SALE_PLAN_METADATA_SPR5')];
	}

	private function getSzUsShippingWay($weight,$l,$w,$h,$register){
		/*//计算出不同物流深圳发美国的最低运费方式
		if($register || $register ==1){
			return $this->getSzUsRsw($weight,$l,$w,$h);
		}else{
			return $this->getSzUsSw($weight,$l,$w,$h);
		}*/

		//计算出运德深圳发美国的最低运费方式
		if($register || $register ==1){
			return "运德南京EUB";
		}else{
			return $this->getWedoSzUsSw($weight,$l,$w,$h);
		}
	}

	private function getSzUsShippingFee($weight,$l,$w,$h,$register){
		/*//计算出不同物流深圳发美国的最低运费
		if($register || $register==1){
			return $this->getSzUsRsf($weight,$l,$w,$h);
		}else{
			return $this->getSzUsSf($weight,$l,$w,$h);
		}*/

		//计算出运德深圳发美国的最低运费
		if($register || $register==1){
			$fee = $this->calWedoNjEubFee($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}else{
			$fee = $this->getWedoSzUsSf($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}
		
	}

	private function getSzUsRsw($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'飞特EUB',
				2=>'运德南京EUB'
			);
		$fees=array(
				0=>0,
				1=>$this->calFlytEubFee($weight,$l,$w,$h),
				2=>$this->calWedoNjEubFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getSzUsRsf($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calFlytEubFee($weight,$l,$w,$h),
				2=>$this->calWedoNjEubFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getSzUsSw($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'飞特中邮',
				2=>'运德漳州平邮',
				3=>'运德广州平邮'
			);
		$fees=array(
				0=>0,
				1=>$this->calFlytCpUsFee($weight,$l,$w,$h),
				2=>$this->calWedoZzCpUsFee($weight,$l,$w,$h),
				3=>$this->calWedoGzCpUsFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 4; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getSzUsSf($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calFlytCpUsFee($weight,$l,$w,$h),
				2=>$this->calWedoZzCpUsFee($weight,$l,$w,$h),
				3=>$this->calWedoGzCpUsFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 4; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getFlytSzUsShippingWay($weight,$l,$w,$h,$register){
		if($register){
			return "飞特EUB";
		}else{
			return "飞特中邮平邮";
		}
	}

	private function getFlytSzUsShippingFee($weight,$l,$w,$h,$register){
		if($register){
			return $this->calFlytEubFee($weight,$l,$w,$h);
		}else{
			return $this->calFlytCpUsFee($weight,$l,$w,$h);
		}
	}

	private function getWedoSzUsShippingWay($weight,$l,$w,$h,$register){
		if($register){
			return "运德南京EUB";
		}else{
			return $this->getWedoSzUsSw($weight,$l,$w,$h);
		}
	}

	private function getWedoSzUsShippingFee($weight,$l,$w,$h,$register){
		if($register){
			return $this->calWedoNjEubFee($weight,$l,$w,$h);
		}else{
			return $this->getWedoSzUsSf($weight,$l,$w,$h);
		}
	}

	private function getWedoSzUsSw($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'运德漳州平邮',
				2=>'运德广州平邮'
			);
		$fees=array(
				0=>0,
				1=>$this->calWedoZzCpUsFee($weight,$l,$w,$h),
				2=>$this->calWedoGzCpUsFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getWedoSzUsSf($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calWedoZzCpUsFee($weight,$l,$w,$h),
				2=>$this->calWedoGzCpUsFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function calFlytEubFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			if($weight>0 and $weight<=200){
				return $weight<50?9+50*0.08:9+$weight*0.08;
			}
			else{
				return 9+$weight*0.075;
			}
		}
		else{
			return 0;
		}
	}

	private function calFlytCprUsFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 8+90.5*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function calFlytCpUsFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return $weight<50?50*90.5:90.5*$weight/1000;
		}
		else{
			return 0;
		}
	}

	//运德美国EUB
	private function calWedoNjEubFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			if($weight<=200){
				return $weight<50?(9+50*0.08):(9+$weight*0.08);
			}
			else{
				return 9+$weight*0.075;
			}
		}
		else{
			return 0;
		}
	}

	//运德漳州小包挂号
	private function calWedoZzCprUsFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 8+90.5*$weight/1000;
		}
		else{
			return 0;
		}
	}

	//运德漳州小包平邮
	private function calWedoZzCpUsFee($weight,$l,$w,$h){
		if($weight>0 And $weight<=2000 And ($l + $w + $h) <= 90 And $l <=60){
			return $weight<50?50*0.085:$weight*0.085;
		}else{
			return 0;
		}
	}

	//运德广州小包平邮
	private function calWedoGzCpUsFee($weight,$l,$w,$h){
		if($weight>0 And $weight<=2000 And ($l + $w + $h) <= 90 And $l <=60){
			return $weight<50?50*0.0905:$weight*0.0905;
		}else{
			return 0;
		}
	}

	//运德广州小包挂号
	private function calWedoGzCprUsFee($weight,$l,$w,$h){
		if($weight>0 And $weight<=2000 And ($l + $w + $h) <= 90 And $l <=60){
			return $weight<50?8+50*0.0905:8+$weight*0.0905;
		}else{
			return 0;
		}
	}

	private function getSzDeShippingWay($weight,$l,$w,$h,$register){
		/*//计算出不同物流深圳发德国的最低运费方式
		if($register||$register==1){
			return $this->getSzDeRsw($weight,$l,$w,$h);
		}else{
			return $this->getSzDeSw($weight,$l,$w,$h);
		}*/

		//计算出当前合作物流的深圳发德国发货方式
		if($register||$register==1){
			return "运德德国小包（新加坡）挂号";
		}else{
			return "运德德国小包（新加坡）平邮";
		}
	}

	private function getSzDeShippingFee($weight,$l,$w,$h,$register){
		/*//计算出不同物流深圳发德国的最低运费
		if($register||$register==1){
			return $this->getSzDeRsf($weight,$l,$w,$h);
		}else{
			return $this->getSzDeSf($weight,$l,$w,$h);
		}*/

		//计算出当前合作物流的深圳发德国发货运费
		if($register||$register==1){
			$fee=$this->calWedoHDRFee($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}else{
			$fee=$this->calWedoHDFee($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}
	}

	private function getSzDeRsw($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'飞特香港德国专线挂号',
				2=>'运德德国小包（香港）挂号'
			);
		$fees=array(
				0=>0,
				1=>$this->calFlytHDRFee($weight,$l,$w,$h),
				2=>$this->calWedoHDRFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getSzDeRsf($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calFlytHDRFee($weight,$l,$w,$h),
				2=>$this->calWedoHDRFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getSzDeSw($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'飞特香港德国专线平邮',
				2=>'运德德国小包（香港）平邮'
			);
		$fees=array(
				0=>0,
				1=>$this->calFlytHDFee($weight,$l,$w,$h),
				2=>$this->calWedoHDFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getSzDeSf($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calFlytHDFee($weight,$l,$w,$h),
				2=>$this->calWedoHDFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 3; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getFlytSzDeShippingWay($weight,$l,$w,$h,$register){
		if($register){
			return "飞特香港德国专线挂号";
		}else{
			return "飞特香港德国专线平邮";
		}
	}

	private function getFlytSzDeShippingFee($weight,$l,$w,$h,$register){
		if($register){
			return $this->calFlytHDRFee($weight,$l,$w,$h);
		}else{
			return $this->calFlytHDFee($weight,$l,$w,$h);
		}
	}

	private function getWedoSzDeShippingWay($weight,$l,$w,$h,$register){
		if($register){
			return "运德德国小包（香港）挂号";
		}else{
			return "运德德国小包（香港）平邮";
		}
	}

	private function getWedoSzDeShippingFee($weight,$l,$w,$h,$register){
		if($register){
			return $this->calWedoHDRFee($weight,$l,$w,$h);
		}else{
			return $this->calWedoHDFee($weight,$l,$w,$h);
		}
	}

	private function calFlytHDRFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 13.9+84*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function calFlytHDFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 4.1+107*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function calWedoHDRFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 14.8+63.21*$weight/1000;
		}
		else{
			return 0;
		}
	}

	//运德德国小包（香港）平邮
	private function calWedoHDFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 2.36+85.26*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function getSzGlobalShippingWay($weight,$l,$w,$h,$register){
		//计算出当前合作物流的深圳发全球发货方式
		if($register||$register==1){
			return "运德漳州/广州挂号";
		}else{
			return "运德漳州/广州平邮";
		}
	}

	private function getSzGlobalShippingFee($weight,$l,$w,$h,$register){
		//计算出当前合作物流的深圳发全球发货运费
		if($register||$register==1){
			return $this->calWedoZprFee($weight,$l,$w,$h);
		}else{
			return $this->calWedoZpFee($weight,$l,$w,$h);
		}
	}

	//运德漳州挂号运费
	private function calWedoZprFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 8+100*$weight/1000;
		}
		else{
			return 65536;
		}
	}

	//运德漳州平邮运费
	private function calWedoZpFee($weight,$l,$w,$h){
		if($weight>0 And $weight<=2000 And ($l + $w + $h) <= 90 And $l <=60){
			return $weight<50?50*0.1:$weight*0.1;
		}else{
			return 65536;
		}
	}

	public function usTestCal(){
		if($this->isPost()){
			$p = I('post.price','','htmlspecialchars');
			$shippingWay = $this->getSzUsShippingWay(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'),I('post.register','','htmlspecialchars'));
			$shippingFee = $this->getSzUsShippingFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'),I('post.register','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = $this->getSzUsCost($p,$shippingFee,$salePrice);
			
			$testData = array(
						'price'=>$p,
						'shipping-way'=>$shippingWay,
						'shipping-fee'=>$shippingFee,
						'saleprice'=> $salePrice,						
						'cost'=>round(($testCost),2),
						'gprofit'=>round($salePrice-$testCost,2),
						'grate'=>round(($salePrice-$testCost)/$salePrice*100,2).'%',
						'weight'=>I('post.weight','','htmlspecialchars'),
						'length'=>I('post.length','','htmlspecialchars'),
						'width'=>I('post.width','','htmlspecialchars'),
						'height'=>I('post.height','','htmlspecialchars'),
						'register'=>I('post.register','','htmlspecialchars')
					);
			$this->testData=$testData;
			$this->display();

		}else{
			$initData = array(
						'price'=>0,
						'shipping-way'=>'',
						'shipping-fee'=>0,
						'saleprice'=>0,
						'cost'=>0,
						'gprofit'=>0,
						'grate'=>'0.0%',
						'weight'=>0,
						'length'=>0,
						'width'=>0,
						'height'=>0
					);
			$this->testData=$initData;
			$this->display();
		}
	}

	public function deTestCal(){
		if($this->isPost()){
			$p = I('post.price','','htmlspecialchars');
			$shippingWay = $this->getSzDeShippingWay(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'),I('post.register','','htmlspecialchars'));
			$shippingFee = $this->getSzDeShippingFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'),I('post.register','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = $this->getSzUsCost($p,$shippingFee,$salePrice);
			$testData = array(
						'price'=>$p,
						'shipping-way'=>$shippingWay,
						'shipping-fee'=>$shippingFee,
						'saleprice'=> $salePrice,						
						'cost'=>round(($testCost),2),
						'gprofit'=>round($salePrice-$testCost,2),
						'grate'=>round(($salePrice-$testCost)/$salePrice*100,2).'%',
						'weight'=>I('post.weight','','htmlspecialchars'),
						'length'=>I('post.length','','htmlspecialchars'),
						'width'=>I('post.width','','htmlspecialchars'),
						'height'=>I('post.height','','htmlspecialchars'),
						'register'=>I('post.register','','htmlspecialchars')
					);
			$this->testData=$testData;
			$this->display();

		}else{
			$initData = array(
						'price'=>0,
						'shipping-way'=>'',
						'shipping-fee'=>0,
						'saleprice'=>0,
						'cost'=>0,
						'gprofit'=>0,
						'grate'=>'0.0%',
						'weight'=>0,
						'length'=>0,
						'width'=>0,
						'height'=>0
					);
			$this->testData=$initData;
			$this->display();
		}
	}

	public function bIgnoreHandle($account,$country,$kw,$kwv){
		$table=M($this->getSalePlanTableName($account,$country));
		$table->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			$data[C('DB_SZ_US_SALE_PLAN_ID')] = $value;
			$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
			$table->save($data);
		}
		$table->commit();
		$this->success('修改成功',U('suggest',array('account'=>$account, 'country'=>$country, 'kw'=>$kw,'kwv'=>$kwv)));
	}

	public function bModifyHandle($account,$country,$kw,$kwv){
		$table=M($this->getSalePlanTableName($account,$country));
		$table->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$value))->find();
			if($data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] !=null){
				$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
				if($data[C('DB_SZ_US_SALE_PLAN_ID_SUGGEST')]=='relisting'){
					$data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')])+1;
				}

				if($data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')]==null){
					$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('ymd',time());
				}else{
					$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
				}

				$data[C('DB_SZ_US_SALE_PLAN_PRICE')] = $data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
				$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
				$table->save($data);
			}
		}
		$table->commit();
		$this->success('修改成功',U('suggest',array('account'=>$account,'country'=>$country, 'kw'=>$kw,'kwv'=>$kwv)));
	}

	public function index($account,$country=null,$kw=null,$kwv=null){
		$products = M(C('DB_PRODUCT'));
		$salePlan = M($this->getSalePlanTableName($account,$country));
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count,20);           
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        if($_POST==null){
        	$tpl = $products->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
        	$where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
        	$tpl = $products->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        }
        foreach ($tpl as $key=>$value) {
        	$sp=$salePlan->where(array(C('DB_SZ_WISH_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->find();
        	$data[$key][C('DB_PRODUCT_SKU')]=$value[C('DB_PRODUCT_SKU')];
        	$data[$key][C('DB_PRODUCT_CNAME')]=$value[C('DB_PRODUCT_CNAME')];
        	$data[$key][C('DB_PRODUCT_PRICE')]=$value[C('DB_PRODUCT_PRICE')];
        	$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]=$sp[C('DB_SZ_US_SALE_PLAN_PRICE')];
        	if($account=="vtkg5755" && $country=="us"){
        		$data[$key]['local_shipping_way']=$this->getSzUsShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['local_shipping_fee']=round($this->getSzUsShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['global_shipping_way']=$this->getSzGlobalShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['global_shipping_fee']=round($this->getSzGlobalShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['cost']=$this->getSzUsCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key]['local_shipping_fee'],$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]);
        	}elseif($account=="vtkg5755" && $country=="de"){
        		$data[$key]['local_shipping_way']=$this->getSzDeShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['local_shipping_fee']=round($this->getSzDeShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['global_shipping_way']=$this->getSzGlobalShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['global_shipping_fee']=round($this->getSzGlobalShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['cost']=$this->getSzDeCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key]['local_shipping_fee'],$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]);
        	}elseif($account=="zuck"){
        		$data[$key]['global_shipping_way']=$this->getSzUsShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['global_shipping_fee']=round($this->getSzUsShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['cost']=$this->getWishCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key]['global_shipping_fee'],$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]);
        	}
        	
        	
        	$data[$key]['gprofit']=round($data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]-$data[$key]['cost'],2);
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')],2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')],2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')],2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')],2);
        }
        $this->assign('keyword',I('post.keyword','','htmlspecialchars'));
        $this->assign('keywordValue',I('post.keywordValue','','htmlspecialchars'));
        $this->assign('data',$data);
        $this->assign('market',$this->getMarketByAccountCountry($account,$country));
        $this->assign('account',$account);
        $this->assign('country',$country);
        $this->assign('page',$show);
        $this->display();
	}

	//Return the sale plan table name according to the account
    private function getSalePlanTableName($account,$country){
    	if($account=="vtkg5755" && $country=="us")
    		return C('DB_SZ_US_SALE_PLAN');
    	elseif($account=="vtkg5755" && $country=="de")
    		return C('DB_SZ_DE_SALE_PLAN');
    	elseif($account=="zuck")
    		return C('DB_SZ_WISH_SALE_PLAN');
    	else{
    		$this->error('账号'.$account.'无法比配到相应的销售表！');
    	}
    }

    //Return the sale plan table view model name according to the account
    private function getSalePlanViewModelName($account,$country){
    	if($account=="vtkg5755" && $country=="us")
    		return "SzUsSalePlanView";
    	elseif($account=="vtkg5755" && $country=="de")
    		return "SzDeSalePlanView";
    	elseif($account=="zuck")
    		return "SzWishSalePlanView";
    	else{
    		$this->error('账号'.$account.'无法比配到相应的销售视图表！');
    	}
    }

    private function getCountryCName($country){
    	switch ($country) {
    		case 'us':
    			return "美国";
    			break;
    		case 'de':
    			return "德国";
    			break;
    		default:
    			return null;
    			break;
    	}
    }

    //Return the sale plan view model according to the account
    private function getMarketByAccountCountry($account,$country=null){
    	if($account=="vtkg5755" && $country=="us")
    		return "ebay.com";
    	elseif ($account=="vtkg5755" && $country=="de") 
    		return "ebay.de";
    	elseif ($account=="zuck") 
    		return "wish.com";
    	else
    		$this->error('无法根据'.$account.'和'.$country.'匹配出销售平台');
    }

    //Calculate item cost for wish $productPrice=product purchase RMB price,$shippingFee=shipping RMB cost,$salePrice=sale price on wish USD
    private function getWishCost($productPrice,$shippingFee,$salePrice){
    	$exchange = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USDTORMB'));
    	if($salePrice==null || $salePrice==0){
			$salePrice = $this->calWishInitialPrice($productPrice,$shippingFee);
		}
		return round((($productPrice+0.5+$shippingFee)/$exchange + $salePrice*0.16),2);
    }

    //Calculate item cost for Germany $productPrice=product purchase RMB price,$shippingFee=shipping RMB cost,$salePrice=sale price on wish USD
    private function getSzDeCost($productPrice,$shippingFee,$salePrice){
    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		if($salePrice==null || $salePrice==0){
			$salePrice = $this->calDeInitialPrice($productPrice,$shippingFee);
		}
		if($salePrice<12){
			$cost = $cost + $salePrice*0.185+0.05;
		}else{
			$cost = $cost+$salePrice*0.164+0.3;
		}
		return round($cost,2);
    }

    //Calculate item cost for United States $productPrice=product purchase RMB price,$shippingFee=shipping RMB cost,$salePrice=sale price on wish USD
    private function getSzUsCost($productPrice,$shippingFee,$salePrice){
    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		if($salePrice==null || $salePrice==0){
			$salePrice = $this->calUsInitialPrice($productPrice,$shippingFee);
		}
		if($salePrice<12){
			$cost = $cost+$salePrice*0.16+0.05;
		}else{
			$cost = $cost+$salePrice*0.139+0.3;
		}
		return round($cost,2);
    }

    public function updateSalePrice($market,$account,$country){
    	$this->assign('market',$market);
    	$this->assign('account',$account);
    	$this->assign('country',$country);
    	$this->display();
    }

    public function updateSalePriceHandle($market,$account,$country){
    	if($market=='ebay.de' || $market=='ebay.com'){
			$this->updateEbaySalePriceHandle($account,$country);
		}else{
			$this->error('没有 '.$market.' 平台');
		}    	
    }

    private function updateEbaySalePriceHandle($account,$country){
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
            	$salePlan=M($this->getSalePlanTableName($account,$country));
            	$salePlan->startTrans();
            	if($country=='de'){
            		$country='Germany';
            	}
            	if($country=='us'){
            		$country='US';
            	}
            	for($i=2;$i<=$highestRow;$i++){
            		$sku = $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
            		$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            		if($countryOfItem==null || $countryOfItem==''){
            			for ($r=$i-1; $r>1; $r--) { 
            				$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
            				if($countryOfItem!=null && $countryOfItem!=''){
            					break;
            				}
            			}
            		}
            		if($sku!='' && $sku!=null && $countryOfItem==$country){
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
		if($market=='ebay' || $market=='ebay.de' || $market=='ebay.com'){
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
            	$storageTable=M(C('DB_SZSTORAGE'));
            	$product=M(C('DB_PRODUCT'));
            	foreach ($this->getSalePlanTableNames($account) as $key => $value) {
            		$salePlanTables[$key]=M($value);
            	}
            	$j=0;
                for($i=2;$i<=$highestRow;$i++){
                	$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
            		if($countryOfItem==null || $countryOfItem==''){
            			for ($r=$i-1; $r>1; $r--) { 
            				$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
            				if($countryOfItem!=null && $countryOfItem!=''){
            					break;
            				}
            			}
            		}
                	if($countryOfItem==$_POST['updateType'] || ($countryOfItem=='eBayMotors' && $_POST['updateType']=='US')){

                		$data[$j][$firstRow['A']]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	        			$data[$j][$firstRow['B']]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
	        			$data[$j][$firstRow['C']]=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
	        			$data[$j][$firstRow['D']]=$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
	        			$data[$j][$firstRow['E']]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
	        			$data[$j][$firstRow['F']]=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
	        			$data[$j][$firstRow['G']]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
	                	$data[$j][$firstRow['H']]=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
	                	$data[$j][$firstRow['I']]=$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
	        			$data[$j][$firstRow['J']]=$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
	        			$data[$j][$firstRow['K']]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();

	                	
	            		if($countryOfItem=='Germany'){
	            			if($product->where(array(C('DB_PRODUCT_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_PRODUCT_TODE'))!='无' || $product->where(array(C('DB_PRODUCT_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_PRODUCT_TOUS'))!='无')
	            				$data[$j][$firstRow['H']]=30;
	            		}
	            		$salePlan=$salePlanTables[$countryOfItem]->where(array('sku'=>$data[$j][$firstRow['K']]))->find();
	            		$data[$j]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
	                	$data[$j]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
	                	$data[$j][$firstRow['F']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
	                	$j++;
                	}
                	                	                
                }
                //find item in stock but not listed
                $storages=$storageTable->where($map)->select();
                foreach ($storages as $key => $value) {                	
                	$toDe = ($product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_TODE'))!='无' || $value[C('DB_SZSTORAGE_AINVENTORY')]>0) && $_POST['updateType']=='Germany';
            		$toUs = ($product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_TOUS'))!='无' || $value[C('DB_SZSTORAGE_AINVENTORY')]>0) && $_POST['updateType']=='US';
            		if($toDe && !$toUs){
            			$listed=false;
            			for ($i=2;$i<=$highestRow;$i++) {
	                		$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
		            		if($countryOfItem==null || $countryOfItem==''){
		            			for ($r=$i-1; $r>1; $r--) { 
		            				$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
		            				if($countryOfItem!=null && $countryOfItem!=''){
		            					break;
		            				}
		            			}
		            		}
	                		if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()==$value[C('DB_WINIT_DE_STORAGE_SKU')] && $countryOfItem=='Germany'){
	                			$listed=true;
	                		}
	                	}
	                	if($listed==false){
	                		$data[$j][$firstRow['D']]='Germany';
	                		$data[$j][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
	                		$data[$j]['Suggest']="未刊登商品";
	                		$j++;
	                	}
            		}
            		if(!$toDe && $toUs){
            			$listed=false;
            			for ($i=2;$i<=$highestRow;$i++) {
	                		$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
		            		if($countryOfItem==null || $countryOfItem==''){
		            			for ($r=$i-1; $r>1; $r--) { 
		            				$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
		            				if($countryOfItem!=null && $countryOfItem!=''){
		            					break;
		            				}
		            			}
		            		}
	                		if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()==$value[C('DB_WINIT_DE_STORAGE_SKU')] && $countryOfItem=='US'){
	                			$listed=true;
	                		}
	                	}
	                	if($listed==false){
	                		$data[$j][$firstRow['D']]='US';
	                		$data[$j][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
	                		$data[$j]['Suggest']="未刊登商品";
	                		$j++;
	                	}
            		}
            		/*if($toDe && $toUs){
            			$listed['US']=false;
            			$listed['Germany']=false;
            			for ($i=2;$i<=$highestRow;$i++) {
	                		$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
		            		if($countryOfItem==null || $countryOfItem==''){
		            			for ($r=$i-1; $r>1; $r--) { 
		            				$countryOfItem = $objPHPExcel->getActiveSheet()->getCell("D".$r)->getValue();
		            				if($countryOfItem!=null && $countryOfItem!=''){
		            					break;
		            				}
		            			}
		            		}
	                		if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()==$value[C('DB_WINIT_DE_STORAGE_SKU')] && $countryOfItem=='US'){
	                			$listed['US']=true;
	                		}
	                		if($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue()==$value[C('DB_WINIT_DE_STORAGE_SKU')] && $countryOfItem=='Germany'){
	                			$listed['Germany']=true;
	                		}
	                	}
	                	foreach ($listed as $lkey => $lvalue) {
	                		if(!$lvalue){
	                			$data[$j][$firstRow['D']]=$lkey;
		                		$data[$j][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
		                		$data[$j]['Suggest']="未刊登商品";
		                		$j++;
	                		}
	                	}
            		}*/
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
                $this->exportEbayFileExchangeExcel('Vtkg_'.$_POST['updateType'].'_FileExchange',$excelCellName,$data); 
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
        }
	}

    private function getSalePlanTableNames($account){
    	if($account=='vtkg5755'){
    		$tableNames['Germany']=C('DB_SZ_DE_SALE_PLAN');
    		$tableNames['US']=C('DB_SZ_US_SALE_PLAN');
    		$tableNames['eBayMotors']=C('DB_SZ_US_SALE_PLAN');
    		return $tableNames;
    	}else{
    		$this->error('无法根据'.$account.'匹配出销售表');
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
        $insertRowNumber=2;
        for($i=0;$i<$dataNum;$i++){
        	$objPHPExcel->getActiveSheet()->getStyle('B'.$insertRowNumber)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            for($j=0;$j<$cellNum;$j++){
            	if($i>0 && $expTableData[$i][$expCellName[6]] !=null && $expTableData[$i][$expCellName[5]]!=$expTableData[$i][$expCellName[6]]){
            		$objPHPExcel->getActiveSheet()->getStyle( 'F'.$insertRowNumber)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'F'.$insertRowNumber)->getFill()->getStartColor()->setARGB('FF808080');
            		$objPHPExcel->getActiveSheet()->getStyle( 'G'.$insertRowNumber)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'G'.$insertRowNumber)->getFill()->getStartColor()->setARGB('FF808080');
            	}
            	if($expTableData[$i][$expCellName[9]]<10){
            		$objPHPExcel->getActiveSheet()->getStyle( 'J'.$insertRowNumber)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'J'.$insertRowNumber)->getFill()->getStartColor()->setARGB('FF808080');
            	}
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].$insertRowNumber, $expTableData[$i][$expCellName[$j]]);
            }
        	$insertRowNumber++;          
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