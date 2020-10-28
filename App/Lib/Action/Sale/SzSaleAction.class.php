<?php

class SzSaleAction extends CommonAction{

	public function suggest($account,$country=null,$kw=null,$kwv=null){
		$Data=D($this->getSalePlanViewModelName($account,$country));
		if($_POST['keyword']=="" && $kwv==null && $_POST['keyword2']==""){ 
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

				//Sale Price great than 5 USD/EUR, then register shipping, Otherwise economy shipping.
				if($usp[C('DB_SZ_US_SALE_PLAN_PRICE')]>5){
					$usp[C('DB_SZ_US_SALE_PLAN_REGISTER')] = 1;
				}else{
					$usp[C('DB_SZ_US_SALE_PLAN_REGISTER')] = 0;
				}

				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($p[C('DB_SZSTORAGE_SKU')])){
				//产品信息不全，建议完善产品信息,退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_COMPLETE_PRODUCT_INFO');
				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}elseif(!$this->isSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息，退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_COMPLETE_SALE_INFO');
				$salePlan->save($usp);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlan->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calSuggest($p[C('DB_SZSTORAGE_SKU')],$account,$country);
					if($suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')]==C('SZ_SALE_PLAN_PRICE_UP') || $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')]==C('SZ_SALE_PLAN_PRICE_DOWN')){
						$usp[C('DB_SZ_US_SALE_PLAN_PRICE')]=$suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
						$usp[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
						if($usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')]==null){
							$usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('ymd',time());
						}else{
							$usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')].' | '.$usp[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
						}
					}else{
						$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
						$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')];
					}					
					$salePlan->save($usp);
				}
			}
		}
	}

	private function getSuggestHandleSingle($account,$country=null, $id){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$salePlan=M($this->getSalePlanTableName($account,$country));
		$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
		if($usp == null){
			$this->addProductToUsp($usp[C('DB_SZ_US_SALE_PLAN_SKU')],$account,$country);
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$usp[C('DB_SZ_US_SALE_PLAN_SKU')]))->find();
		}else{
			
			$usp[C('DB_SZ_US_SALE_PLAN_COST')]=$this->calSuggestCost($usp[C('DB_SZ_US_SALE_PLAN_SKU')],$account,$country,$usp[C('DB_SZ_US_SALE_PLAN_PRICE')]);
			$salePlan->save($usp);
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$usp[C('DB_SZ_US_SALE_PLAN_SKU')]))->find();
		}
		if(!$this->isProductInfoComplete($usp[C('DB_SZ_US_SALE_PLAN_SKU')])){
			//产品信息不全，建议完善产品信息,退出循环
			$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_COMPLETE_PRODUCT_INFO');
			$salePlan->save($usp);
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$usp[C('DB_SZ_US_SALE_PLAN_SKU')]))->find();
		}elseif(!$this->isSaleInfoComplete($usp)){
			//无法计算，建议完善销售信息，退出循环
			$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = C('SZ_SALE_PLAN_COMPLETE_SALE_INFO');
			$salePlan->save($usp);
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$usp[C('DB_SZ_US_SALE_PLAN_SKU')]))->find();
		}else{
			$lastModifyDate = $salePlan->where(array('sku'=>$usp[C('DB_SZ_US_SALE_PLAN_SKU')]))->getField('last_modify_date');
			$adjustPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
			if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
				//开始计算该产品的销售建议
				$suggest=null;
				$suggest = $this->calSuggest($usp[C('DB_SZ_US_SALE_PLAN_SKU')],$account,$country);
				if($suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')]==C('SZ_SALE_PLAN_PRICE_UP') || $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')]==C('SZ_SALE_PLAN_PRICE_DOWN')){
					$usp[C('DB_SZ_US_SALE_PLAN_PRICE')]=$suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
					if($usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')]==null){
						$usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('ymd',time());
					}else{
						$usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')].' | '.$usp[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
					}
				}else{
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')];
				}				
				$salePlan->save($usp);
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
		if($account=="rc-helicar" && $country == 'us'){
	    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
	    	$data['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
			return $this->getSzUsCost($data[C('DB_PRODUCT_PRICE')],$data['way-to-us-fee'],$sale_price);
		}
		if($account=="rc-helicar" && $country == 'de'){
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
		if($account=="rc-helicar" && $country == 'us'){
			$register = M(C('DB_SZ_US_SALE_PLAN'))->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$sku))->getField(C('DB_SZ_US_SALE_PLAN_REGISTER'));
			$shippingFee=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]+20:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')],$register);
			return $this->calUsInitialPrice($product[C('DB_PRODUCT_PRICE')],$shippingFee);
		}
		if($account=="rc-helicar" && $country == 'de'){
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
		return $salePrice;
	}

	private function calDeInitialPrice($productPrice,$shippingFee){
		$exchange = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		$salePrice = abs(round(($cost+0.3)/(1-0.139-$this->getCostClass($cost)/100),2));
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
		if($data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] !=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')]!=C('USSW_SALE_PLAN_CLEAR')){
			$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			if($data[C('DB_SZ_US_SALE_PLAN_SUGGEST')]=='relisting'){
				$data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')])+1;
			}

			if($data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')]==null){
				$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}else{
				$data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_SZ_US_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
			}

			$data[C('DB_SZ_US_SALE_PLAN_PRICE')] = $data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];

			$kpiSaleRecorde[C('DB_KPI_SALE_NAME')] = $_SESSION['username'];
			$kpiSaleRecorde[C('DB_KPI_SALE_SKU')] = $data[C('DB_SZ_US_SALE_PLAN_SKU')];
			$kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')] = C('SZSW');
			$kpiSaleRecorde[C('DB_KPI_SALE_TYPE')] = $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')];
			$kpiSaleRecorde[C('DB_KPI_SALE_BEGIN_DATE')] = time();
			$kpiSaleRecorde[C('DB_KPI_SALE_BEGIN_SQUANTITY')] = M(C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_SKU')=>$data[C('DB_SZ_US_SALE_PLAN_SKU')]))->sum(C('DB_SZSTORAGE_AINVENTORY'));
			$map[C('DB_KPI_SALE_SKU')] = array('eq', $kpiSaleRecorde[C('DB_KPI_SALE_SKU')]);
			$map[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', $kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')]);

			if(M(C('DB_KPI_SALE'))->where($map)->getField(C('DB_KPI_SALE_ID'))!=null){
				$this->error('仓库： '.$kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')] .' 里的该产品编码：'.$kpiSaleRecorde[C('DB_KPI_SALE_SKU')].' 已经在绩效考核表里，如需重新开始绩效考核请先把重复的记录从绩效考核表里删除。');
			}else{
				$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
				$table->save($data);
				M(C('DB_KPI_SALE'))->add($kpiSaleRecorde);
			}
		}else{
			$this->error('无法保存，当前产品没有销售建议');
		}
		
		$this->success('修改成功');
	}

	public function ignoreSuggest($id,$account,$country=null){
		$table=M($this->getSalePlanTableName($account,$country));
		$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
		if($data[C('DB_SZ_US_SALE_PLAN_SUGGEST')]!=C('USSW_SALE_PLAN_CLEAR')){
			$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
			$table->save($data);
			$this->success('修改成功');
		}else{
			$this->error('清货建议不能忽略');
		}
		
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

	public function updateSalePlanSingle($account,$country=null,$kw=null,$kwv=null, $obj, $salePrice, $status, $register){
		$salePlan=M($this->getSalePlanTableName($account,$country));
		$salePlan->startTrans();
		$data[C('DB_SZ_US_SALE_PLAN_ID')]=$obj;
		$data[C('DB_SZ_US_SALE_PLAN_PRICE')]=$salePrice;
		$data[C('DB_SZ_US_SALE_PLAN_STATUS')]=$status;
		$data[C('DB_SZ_US_SALE_PLAN_REGISTER')]=$register;
		$salePlan->save($data);
		$salePlan->commit();
		$this->getSuggestHandleSingle($account,$country,$obj);
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
		return $this->calSpeedPAKUSStandardWay($weight,$l,$w,$h);
	}

	private function getSzUsShippingFee($weight,$l,$w,$h,$register){
		return $this->calSpeedPAKUSStandardFee($weight,$l,$w,$h);
	}

	private function calSpeedPAKUSStandardWay($weight,$l,$w,$h){
		if($weight>31500 || $l>66 || ($l+2*($w+$h))>330){
			return 'No way';
		}else{
			return "SpeedPAK Standard";
		}
	}

	private function calSpeedPAKUSStandardFee($weight,$l,$w,$h){
		if($weight>31500 || $l>66 || ($l+2*($w+$h))>274){
			return 65536;
		}else{
			if($l>40 || $w>40 || $h>40){
				$vweight = $l*$w*$h/6000;
			}
			if($vweight>$weight*1.3){
				$weight = $weight+($vweight-$weight*1.3);
			}
			if($weight<50){
				return 15+50*0.13;
			}else{
				return 15+$weight*0.13;
			}
		}
	}

	
	private function getSzDeShippingWay($weight,$l,$w,$h,$register){
		if($register||$register==1){
			if($weight>2000 || $l>60 || ($l+$w+$h)>90){
				return "No way";
			}else{
				return "SpeedPAK Standard";
			}
			
		}else{
			if($weight>2000 || $l>60 || ($l+$w+$h)>90){
				return "No way";
			}else{
				return "SpeedPAK Economy";	
			}
		}
	}

	private function getSzDeShippingFee($weight,$l,$w,$h,$register){
		if($register||$register==1){
			$fee=$this->calSpeedPAKStandardFee($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}else{
			$fee=$this->calSpeedPAKEconomyFee($weight,$l,$w,$h);
			return $fee==0?65536:$fee;
		}
	}

	private function calSpeedPAKStandardFee($weight,$l,$w,$h){
		if($weight>2000 || $l>60 || ($l+$w+$h)>90){
			return 65536;
		}else{
			if($weight<50){
				return 25+50*0.15;
			}else{
				return 25+$weight*0.15;
			}
		}
	}

	private function calSpeedPAKEconomyFee($weight,$l,$w,$h){
		if($weight>2000 || $l>60 || ($l+$w+$h)>90){
			return 65536;
		}else{
			if($weight<50){
				return 15+50*0.15;
			}else{
				return 15+$weight*0.15;
			}
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
			if($table->where(array(C('DB_USSW_SALE_PLAN_ID')=>$value))->getField(C('DB_USSW_SALE_PLAN_SUGGEST'))!=C('USSW_SALE_PLAN_CLEAR')){
				$data[C('DB_SZ_US_SALE_PLAN_ID')] = $value;
				$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
				$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
				$table->save($data);
			}
		}
		$table->commit();
		$this->success('修改成功',U('suggest',array('account'=>$account, 'country'=>$country, 'kw'=>$kw,'kwv'=>$kwv)));
	}

	public function bModifyHandle($account,$country,$kw,$kwv){
		$table=M($this->getSalePlanTableName($account,$country));
		$table->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$value))->find();
			if($data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] !=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')]!=C('USSW_SALE_PLAN_CLEAR')){
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
        	if($account=="rc-helicar" && $country=="us"){
        		$data[$key]['local_shipping_way']=$this->getSzUsShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['local_shipping_fee']=round($this->getSzUsShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['cost']=$this->getSzUsCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key]['local_shipping_fee'],$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]);
        	}elseif($account=="rc-helicar" && $country=="de"){
        		$data[$key]['local_shipping_way']=$this->getSzDeShippingWay($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
        		$data[$key]['local_shipping_fee']=round($this->getSzDeShippingFee($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]+20:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')],$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]),2);
        		$data[$key]['cost']=$this->getSzDeCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key]['local_shipping_fee'],$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]);
        	}
        	
        	
        	$data[$key]['gprofit']=round($data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]-$data[$key]['cost'],2);
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_SZ_WISH_SALE_PLAN_PRICE')]*100,2).'%';
        	if($country=='us'){
        		$data[$key]['weight']=round($value[C('DB_PRODUCT_PWEIGHT')]*0.0352740,2);
	        	$data[$key]['length']=round($value[C('DB_PRODUCT_PLENGTH')]*0.3937008,2);
	        	$data[$key]['width']=round($value[C('DB_PRODUCT_PWIDTH')]*0.3937008,2);
	        	$data[$key]['height']=round($value[C('DB_PRODUCT_PHEIGHT')]*0.3937008,2);
        	}else{
        		$data[$key]['weight']=round($value[C('DB_PRODUCT_PWEIGHT')],2);
	        	$data[$key]['length']=round($value[C('DB_PRODUCT_PLENGTH')],2);
	        	$data[$key]['width']=round($value[C('DB_PRODUCT_PWIDTH')],2);
	        	$data[$key]['height']=round($value[C('DB_PRODUCT_PHEIGHT')],2);
        	}
        	
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
    	if($account=="rc-helicar" && $country=="us")
    		return C('DB_SZ_US_SALE_PLAN');
    	elseif($account=="rc-helicar" && $country=="de")
    		return C('DB_SZ_DE_SALE_PLAN');
    	elseif($account=="zuck")
    		return C('DB_SZ_WISH_SALE_PLAN');
    	else{
    		$this->error('账号'.$account.'无法比配到相应的销售表！');
    	}
    }

    //Return the sale plan table view model name according to the account
    public function getSalePlanViewModelName($account,$country){
    	if($account=="rc-helicar" && $country=="us")
    		return "SzUsSalePlanView";
    	elseif($account=="rc-helicar" && $country=="de")
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
    	if($account=="rc-helicar" && $country=="us")
    		return "ebay.com";
    	elseif ($account=="rc-helicar" && $country=="de") 
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
		$cost = $cost+$salePrice*0.164+0.3;
		return round($cost,2);
    }

    //Calculate item cost for United States $productPrice=product purchase RMB price,$shippingFee=shipping RMB cost,$salePrice=sale price on wish USD
    private function getSzUsCost($productPrice,$shippingFee,$salePrice){
    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($productPrice+0.5+$shippingFee)/$exchange;
		if($salePrice==null || $salePrice==0){
			$salePrice = $this->calUsInitialPrice($productPrice,$shippingFee);
		}
		$cost = $cost+$salePrice*0.139+0.3;
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
			if($_POST['updateType']=='US'){
				$this->ebaySzStorageFileExchangeHandle($account);
			}elseif($_POST['updateType']=='USSW'){
				$this->ebayUsswFileExchangeHandle($account);
			}
		}else{
			$this->error('没有 '.$market.' 平台');
		}

	}

	private function ebaySzStorageFileExchangeHandle($account){
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
            $firstRow['A'] = 'Action(SiteID=US|Country=CN|Currency=USD|Version=585|CC=UTF-8)';

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
            		$listing4OtherWarehouse = explode('_', $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
                	if(($countryOfItem==$_POST['updateType'] || ($countryOfItem=='eBayMotors' && $_POST['updateType']=='US'))&& count($listing4OtherWarehouse)==1){

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
	        			$data[$j][$firstRow['K']]=$this->toTextSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());

	                	
	            		if($product->where(array(C('DB_PRODUCT_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_PRODUCT_TOUS'))!=null && ($product->where(array(C('DB_PRODUCT_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_PRODUCT_TOUS'))!='无' || $product->where(array(C('DB_PRODUCT_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_PRODUCT_TOUS'))!='无')){
	            			//$data[$j][$firstRow['H']]=10;
	            			
							//按照实际库存更新在线listing数量。
	            			$data[$j][$firstRow['H']]=$storageTable->where(array(C('DB_SZSTORAGE_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_SZSTORAGE_AINVENTORY'))>0?$storageTable->where(array(C('DB_SZSTORAGE_SKU')=>$data[$j][$firstRow['K']]))->getField(C('DB_SZSTORAGE_AINVENTORY')):0;
	            		}
	            		$salePlan=$salePlanTables[$countryOfItem]->where(array('sku'=>$data[$j][$firstRow['K']]))->find();
	            		$data[$j]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
	                	$data[$j]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
	                	/*
						德国站不允许刊登22欧元以上物品，所以售价拆分到运费里一部分。这类产品不能根据系统价格自动修改售价。
	                	if($account !='rc-helicar' || ($account=='rc-helicar' &&  !$this->szFxtPriceException($data[$j][$firstRow['K']]))){
	                		$data[$j][$firstRow['F']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
	                	}*/
	                	$data[$j][$firstRow['F']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
	                	$j++;
                	}               	                
                }
                //find item in stock but not listed without excepted sku, that can not be sold on the market.
                if($_POST['updateType']=='US'){
                	$map[C('DB_SZSTORAGE_SKU')] = array('not in', $this->szFxtUsException());
                }                
                $storages=$storageTable->where($map)->select();
				$countOfData=count($data);
				/*//Check the item is ended manual. If the item in TODO. Then do not add to list.
				$todoWhere[C('DB_TODO_STATUS')] = array('eq', 0);
				if($_POST['updateType']=='Germany'){
					$todoWhere[C('DB_TODO_TASK')] = array('like', '%'.$account.'德国销售建议重新刊登：%');
				}
				if($_POST['updateType']=='US'){
					$todoWhere[C('DB_TODO_TASK')] = array('like', '%'.$account.'美国销售建议重新刊登：%');
				}		
				$todo = M(C('DB_TODO'))->where($todoWhere)->getField(C('DB_TODO_TASK'));
				$todo= str_replace(':', '：', $todo);
				$todoTask = explode('：', str_replace(',', '，', $todo));
				$relistSku = explode('，', $todoTask[1]);*/
                foreach ($storages as $key => $value) {
                	//Check weight and dimesion of the parcel, if the weight and dimension exceeds the limit, then do not add the item to list
                	$pweight = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PWEIGHT'));
                	$plength = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PLENGTH'));
                	$pwidth = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PWIDTH'));
                	$pheight = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C('DB_PRODUCT_PHEIGHT'));
                	//weight limit 2000g
                	$weightLimit = $pweight>2000?false:true;
                	//dimension limit                 	
                	if($pwidth == $pheight){
                		//cylinder parcel l+2*w<104cm AND l<=90cm
                		if($plength<=90 && ($plength+2*$pwidth)<104){
                			$sizeLimit = true;
                		}else{
                			$sizeLimit = false;
                		}
                	}else{
                		//square parcel l+w+h<=90cm AND L<=60cm AND l*w>100cm²
                		if($plength<=60 && ($plength+$pwidth+$pheight)<=90 && $plength*$pwidth>100){
                			$sizeLimit = true;
                		}else{
                			$sizeLimit = false;
                		}
                	}
                	/*//Check the item is ended manual. If the item in TODO. Then do not add to list.
                	$waitingRelist = array_search($value[C('DB_SZSTORAGE_SKU')], $relistSku) != false? true: false;

                	if($weightLimit&&$sizeLimit&&!$waitingRelist){*/
                	if($weightLimit&&$sizeLimit){
                		if($_POST['updateType']=='US'){
	                		$toCountry = 'DB_PRODUCT_TOUS';
	                	}elseif($_POST['updateType']=='Germany'){
	                		$toCountry = 'DB_PRODUCT_TODE';
	                	}

	                	$active = ($product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_SZSTORAGE_SKU')]))->getField(C($toCountry))!='无' && $value[C('DB_SZSTORAGE_AINVENTORY')]>0);
	            		if($active){
	            			/*$listed=false;
	            			for ($i=0;$i<$countOfData;$i++) {
		                		if($data[$i][$firstRow['K']]==$value[C('DB_WINIT_DE_STORAGE_SKU')]){
		                			$listed=true;
		                			break;
		                		}
		                	}*/
		                	if(!$this->in_listedItem($value[C('DB_WINIT_DE_STORAGE_SKU')],$data)){
		                		$data[$j][$firstRow['D']]=$_POST['updateType'];
		                		$data[$j][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
		                		$data[$j]['Suggest']="未刊登商品";
		                		$j++;
		                	}
	            		}
                	}	
                }

                
                if($_POST['updateType']=='Germany'){
	            	$excelCellName[0] = 'Action(SiteID=Germany|Country=CN|Currency=EUR|Version=941)';
	            	//添加深圳仓德国站可以销售的产品
	                $validSkus = array('1597','1456.02','1664','1666','1788.01','1837.02','1857.02','1866.02','1960.02','1961.02','2012.02','1997.02','1996.02',);
	                foreach ($validSkus as $vsKey => $vaValue) {
	                	if(!$this->in_listedItem($vaValue,$data)){
	                		$data[$j][$firstRow['D']]=$_POST['updateType'];
		            		$data[$j][$firstRow['K']]=$vaValue;
		            		$data[$j]['Suggest']="未刊登商品";
		            		$j++;
	                	}	                	
	                }
	            }
	            if($_POST['updateType'] == 'US'){
	            	$excelCellName[0] = 'Action(SiteID=US|Country=CN|Currency=USD|Version=585|CC=UTF-8)';
	            }
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
                $this->exportEbayFileExchangeExcel($account.$_POST['updateType'].'_FileExchange',$excelCellName,$data); 
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
        }
	}

	private function ebayUsswFileExchangeHandle($account){
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
            $firstRow['A'] = 'Action(SiteID=US|Country=US|Currency=USD|Version=585|CC=UTF-8)';

            if($this->verifyEbayFxtcn($firstRow)){
            	$storageTable=M(C('DB_US_INVENTORY'));
            	$product=M(C('DB_PRODUCT'));
            	$salePlanTable=M(C('DB_USSW_SALE_PLAN'));
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
            		$listing4OtherWarehouse = explode('_', $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
                	if(($countryOfItem=='US' || ($countryOfItem=='eBayMotors' && $_POST['updateType']=='USSW'))&& $listing4OtherWarehouse[0]=='ussw'){

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
	        			$data[$j][$firstRow['K']]=$this->toTextSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());

	                	
	            		$data[$j][$firstRow['H']]=$storageTable->where(array(C('DB_SZSTORAGE_SKU')=>$listing4OtherWarehouse[1]))->getField(C('DB_SZSTORAGE_AINVENTORY'));
	            		$salePlan=$salePlanTable->where(array('sku'=>$listing4OtherWarehouse[1]))->find();
	            		$data[$j]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
	                	$data[$j]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
	                	$data[$j][$firstRow['F']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
	                	$j++;
                	}               	                
                }
                               
                $storages=$storageTable->where($map)->select();
                foreach ($storages as $key => $value) {                	
                	if(!$this->in_listedItem($value[C('DB_WINIT_DE_STORAGE_SKU')],$data)){
                		$data[$j][$firstRow['D']]=$_POST['updateType'];
                		$data[$j][$firstRow['K']]=$value[C('DB_WINIT_DE_STORAGE_SKU')];
                		$data[$j]['Suggest']="未刊登商品";
                		$j++;
                	}	
                }

	            $excelCellName[0] = 'Action(SiteID=US|Country=US|Currency=USD|Version=585|CC=UTF-8)';
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
                $this->exportEbayFileExchangeExcel($account.$_POST['updateType'].'_FileExchange',$excelCellName,$data); 
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
        }
	}

	private function isUsswListing($sku){
		if(stristr($sku,'ussw')!=false){
			return true;
		}else{
			return false;
		}
	}

	private function szFxtPriceException($sku){
		$exception = array(
			'1076.01','1082','1111','1154','1181.02','1225','1234','1252','1254','1256','1259.01','1274.01','1347','1359','1362','1369','1370.01','1370.02','1375','1376','1412.02','1412.03','1412.04','1415','1424','1431','1432.01','1432.02','1433','1440','1512','1519','1544','1546.01','1546.02','1546.03','1549','1565','1585.01','1585.02','1593','1597','1602','1604','1608.04','1608.05','1621','1666','1681','1692','1704','1724','1764.03','1765.01','1765.02'
		);
		return in_array($sku, $exception);	
	}


	//sku can not be sold on the ebay.com from sz warehouse
	private function szFxtUsException(){
		return array('1517','1588');
	}

    public function getSalePlanTableNames($account){
    	if($account=='rc-helicar'){
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

    private function in_listedItem($sku, $listedItem) {   
	    foreach($listedItem as $item) {   
	        if(!is_array($item)) {   
	            if ($item == $sku) {  
	                return true;  
	            } else {  
	                continue;   
	            }  
	        }else{
	        	if(in_array($sku, $item)) {  
		            return true;      
		        }
	        }   
	    }   
	    return false;   
	}

	public function exportUsEbayBulkDiscount($account){
    	$szStorage = M(C('DB_SZSTORAGE'))->select();
    	$productTable = M(C('DB_PRODUCT'));
    	$salePlanTable = M($this->getSalePlanTableName($account,'us'));
    	$data = array();
    	foreach ($szStorage as $key => $value) {
    		$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->find();
    		$sale_price = $salePlanTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_USSW_SALE_PLAN_PRICE'));
    		//2 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*2;
    		$tmp['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?($product[C('DB_PRODUCT_WEIGHT')]+20)*2:$product[C('DB_PRODUCT_PWEIGHT')]*2,$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]*2,$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
    		$tier2Cost =  $this->getSzUsCost($tmp[C('DB_PRODUCT_PRICE')],$tmp['way-to-us-fee'],$sale_price*2);
    		$tier2ProfitRate = ((2*$sale_price)-$tier2Cost)/(2*$sale_price);

    		//3 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*3;
    		$tmp['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?($product[C('DB_PRODUCT_WEIGHT')]+20)*3:$product[C('DB_PRODUCT_PWEIGHT')]*3,$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]*3,$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
    		$tier3Cost =  $this->getSzUsCost($tmp[C('DB_PRODUCT_PRICE')],$tmp['way-to-us-fee'],$sale_price*3);
    		$tier3ProfitRate = ((3*$sale_price)-$tier2Cost)/(3*$sale_price);

			//4 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*4;
    		$tmp['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_PWEIGHT')]==0?($product[C('DB_PRODUCT_WEIGHT')]+20)*4:$product[C('DB_PRODUCT_PWEIGHT')]*4,$product[C('DB_PRODUCT_PLENGTH')],$product[C('DB_PRODUCT_PWIDTH')],$product[C('DB_PRODUCT_PHEIGHT')]*4,$sp[C('DB_SZ_US_SALE_PLAN_REGISTER')]);
    		$tier4Cost =  $this->getSzUsCost($tmp[C('DB_PRODUCT_PRICE')],$tmp['way-to-us-fee'],$sale_price*4);
    		$tier4ProfitRate = ((4*$sale_price)-$tier2Cost)/(4*$sale_price);

    		if($tier2ProfitRate>0.15 && $tier3ProfitRate>0.2 && $tier4ProfitRate>0.3){
    			$tmpData['sku'] = $value[C('DB_USSTORAGE_SKU')];
    			$tmpData['offsetType'] = "Percentage";
    			$tmpData['t1MinQty'] = 1;
    			$tmpData['t1MaxQty'] = 1;
    			$tmpData['t1Offset'] = 0;
    			$tmpData['t2MinQty'] = 2;
    			$tmpData['t2MaxQty'] = 2;
    			$tmpData['t2Offset'] = 5;
    			$tmpData['t2Cost'] = $tier2Cost;
    			$tmpData['t2SalePrice'] = 2*$sale_price;
    			$tmpData['t2ProfitRate'] = $tier2ProfitRate;
    			$tmpData['t3MinQty'] = 3;
    			$tmpData['t3MaxQty'] = 3;
    			$tmpData['t3Offset'] = 10;
    			$tmpData['t3Cost'] = $tier3Cost;
    			$tmpData['t3SalePrice'] = 3*$sale_price;
    			$tmpData['t3ProfitRate'] = $tier3ProfitRate;
    			$tmpData['t4MinQty'] = 4;
    			$tmpData['t4Offset'] = 15;
    			$tmpData['t4Cost'] = $tier4Cost;
    			$tmpData['t4SalePrice'] = 4*$sale_price;
    			$tmpData['t4ProfitRate'] = $tier4ProfitRate;
    			array_push($data, $tmpData);
    		}
    	}
    	$xlsCell  = array(
	        array('sku','SKU'),
	        array('offsetType','Offset Type(Amount or Percentage)'),
	        array('t1MinQty','T1 Min. Qty'),
	        array('t1MaxQty','T1 Max. Qty'),
	        array('t1Offset','T1 Offset Value'),
	        array('t2MinQty','T2 Min. Qty'),
	        array('t2MaxQty','T2 Max. Qty'),
	        array('t2Offset','T2 Offset Value'),
	        array('t2Cost','t2成本'),
	        array('t2SalePrice','t2售价'),
	        array('t2ProfitRate','t2利润率'),
	        array('t3MinQty','T3 Min. Qty'),
	        array('t3MaxQty','T3 Max. Qty'),
	        array('t3Offset','T3 Offset Value'),
	        array('t3Cost','t3成本'),
	        array('t3SalePrice','t3售价'),
	        array('t3ProfitRate','t3利润率'),
	        array('t4MinQty','T4 Min. Qty'),
	        array('t4MaxQty','T4 Max. Qty'),
	        array('t4Offset','T4 Offset Value'),
	        array('t4Cost','t4成本'),
	        array('t4SalePrice','t4售价'),
	        array('t4ProfitRate','t4利润率')
	        );
    	$this->exportExcel($account.'BulkDiscount',$xlsCell,$data);
    }

    public function exportSzUsSaleSuggestTable(){
    	$Data = D(C('DB_SZ_US_SALE_PLAN'));
        $suggest = $Data->order(C('DB_USSW_SALE_PLAN_SKU'))->select();
        foreach ($suggest as $key => $value) {
        	$suggest[$key]['profit'] = round(($value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')]),2);
        	$suggest[$key]['grate'] = round(($value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')]) / $value[C('DB_USSW_SALE_PLAN_PRICE')]*100,2);
        }
        $xlsCell  = array(
	        array('sku','SKU'),
	        array('first_sale_date','first_sale_date'),
	        array('last_modify_date','last_modify_date'),
	        array('relisting_times','relisting_times'),
	        array('price_note','price_note'),
	        array('cost','cost'),
	        array('sale_price','sale_price'),
	        array('suggested_price','suggested_price'),
	        array('suggest','suggest'),
	        array('status','status'),
	        array('cname','cname'),
	        array('profit','profit'),
	        array('grate','grate')
	        );
        $this->exportExcel($account.'SaleSuggestTable',$xlsCell,$suggest);
    }
}

?>