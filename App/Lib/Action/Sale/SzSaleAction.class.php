<?php

class SzSaleAction extends CommonAction{

	public function usCal(){
		if($_POST['keyword']==""){
			$this->getSzUsSaleInfo();
        }
        else{           
            $this->getSzUsKeywordSaleInfo();
        }
	}

	public function deCal(){
		if($_POST['keyword']==""){
			$this->getSzDeSaleInfo();
        }
        else{           
            $this->getSzDeKeywordSaleInfo();
        }
	}

	public function szSalePlanMetadata(){
		$this->data=M(C('DB_SZ_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function suggest($country){
		if($country=='us'){
			$Data = D("SzUsSalePlanView");
		}
		if($country=='de'){
			$Data = D("SzDeSalePlanView");
		}
		if($_POST['keyword']==""){ 
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $suggest = $Data->order('sku')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('suggest',$suggest);
            $this->assign('country',$country);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->suggest = $Data->where($where)->select();
            $this->assign('country',$country);
        }
        $this->display();
	}

	public function getSuggest($country){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		if($country=='us'){
			$salePlan = D("SzUsSalePlanView");
		}
		if($country=='de'){
			$salePlan = D("SzDeSalePlanView");
		}

		$szProduct = M(C('DB_SZSTORAGE'))->distinct(true)->field(C('DB_SZSTORAGE_SKU'))->select();
		foreach ($szProduct as $key => $p) {
			$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			if($usp == null){
				$this->addProductToUsp($p[C('DB_SZSTORAGE_SKU')],$country);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}else{
				$usp[C('DB_SZ_US_SALE_PLAN_COST')]=$this->calSuggestCost($p[C('DB_SZSTORAGE_SKU')],$country);
				$this->updateUsp($usp,$country);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($p[C('DB_SZSTORAGE_SKU')])){
				//产品信息不全，建议完善产品信息,退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = 'complete_product_info';
				$this->updateUsp($usp,$country);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}elseif(!$this->isSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息，退出循环
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = 'complete_sale_info';
				$this->updateUsp($usp,$country);
				$usp = $salePlan->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$p[C('DB_SZSTORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlan->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calSuggest($p[C('DB_SZSTORAGE_SKU')],$country);
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_SZ_US_SALE_PLAN_SUGGEST')];
					$this->updateUsp($usp,$country);
				}
			}
		}
		$this->redirect('suggest',array('country'=>$country));
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
		if($product[C('DB_PRODUCT_WEIGHT')]==null || $product[C('DB_PRODUCT_WEIGHT')]==0)
			return false;
		if($product[C('DB_PRODUCT_LENGTH')]==null || $product[C('DB_PRODUCT_LENGTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_WIDTH')]==null || $product[C('DB_PRODUCT_WIDTH')]==0)
			return false;
		if($product[C('DB_PRODUCT_HEIGHT')]==null || $product[C('DB_PRODUCT_HEIGHT')]==0)
			return false;
		return true;
	}

	private function addProductToUsp($sku,$country){
		//添加产品到sale_plan表
		if($country=='us'){
			$salePlan = M(C('DB_SZ_US_SALE_PLAN'));
			$field = C('DB_PRODUCT_SZ_US_SALE_PRICE');
		}
		if($country=='de'){
			$salePlan = M(C('DB_SZ_DE_SALE_PLAN'));
			$field = C('DB_PRODUCT_SZ_DE_SALE_PRICE');
		}
		$newUsp[C('DB_SZ_US_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_SZ_US_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_SZ_US_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_SZ_US_SALE_PLAN_PRICE_NOTE')] =null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_COST')] = $this->calSuggestCost($sku,$country);
		$price =  M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField($field);
		if($price==null || $price==0){
			$price = $newUsp[C('DB_SZ_US_SALE_PLAN_COST')];
		}
		$newUsp[C('DB_SZ_US_SALE_PLAN_PRICE')] = $price;
		$newUsp[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_SZ_US_SALE_PLAN_STATUS')] = 1;

		$salePlan->add($newUsp);
	}

	private function calSuggestCost($sku,$country){
		if($country == 'us'){
			return $this->calUsSuggestCost($sku);
		}
		if($country == 'de'){
			return $this->calDeSuggestCost($sku);
		}
		return null;
	}

	private function calUsSuggestCost($sku){
		//计算产品美自建仓销售成本
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data['way-to-us-fee']=$this->getSzUsShippingFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($data[C('DB_PRODUCT_PRICE')]+0.5+$data['way-to-us-fee'])/$exchange;
		$cost = $cost+$cost*0.18;
		return $cost;
	}

	private function calDeSuggestCost($sku){
		//计算产品美自建仓销售成本
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data['way-to-de-fee']=$this->getSzDeShippingFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTORMB'));
		$cost = ($data[C('DB_PRODUCT_PRICE')]+0.5+$data['way-to-de-fee'])/$exchange;
		$cost = $cost+$cost*0.18;
		return $cost;
	}

	public function confirmSuggest($id,$country){
		if($country=='us'){
			$table=M(C('DB_SZ_US_SALE_PLAN'));
		}
		if($country=='de'){
			$table=M(C('DB_SZ_DE_SALE_PLAN'));
		}
		if($data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] !=null){
			$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
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
		
		$this->redirect('suggest',array('country'=>$country));
	}

	public function ignoreSuggest($id,$country){
		if($country=='us'){
			$table=M(C('DB_SZ_US_SALE_PLAN'));
		}
		if($country=='de'){
			$table=M(C('DB_SZ_DE_SALE_PLAN'));
		}
		$data = $table->where(array(C('DB_SZ_US_SALE_PLAN_ID')=>$id))->find();
		$data[C('DB_SZ_US_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
		$data[C('DB_SZ_US_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$data[C('DB_SZ_US_SALE_PLAN_SUGGEST')] = null;
		$table->save($data);
		$this->redirect('suggest',array('country'=>$country));
	}

	private function updateUsp($usp,$country){
		//更新产品自建仓销售建议
		if($country=='us'){
			M(C('DB_SZ_US_SALE_PLAN'))->save($usp);
		}
		if($country=='de'){
			M(C('DB_SZ_DE_SALE_PLAN'))->save($usp);
		}
		
	}

	public function updateSalePlan($country){
		$data=null;
		foreach ($_POST as $key => $value) {
			$arr = explode("-",$key);
			if($arr[0]=='id'){
				$data[$arr[1]]['id']=$value; 
			}
			if($arr[0]=='sale_price'){
				$data[$arr[1]]['sale_price']=$value; 
			}
			if($arr[0]=='status'){
				$data[$arr[1]]['status']=$value; 
			}
		}
		if($country=='us'){
			$salePlan = M(C('DB_SZ_US_SALE_PLAN'));
		}
		if($country=='de'){
			$salePlan = M(C('DB_SZ_DE_SALE_PLAN'));
		}
		$salePlan->startTrans();
		foreach ($data as $key => $value) {
			if($value['status']=="on"){
				$value['status']=1;
			}else{
				$value['status']=0;
			}
			$salePlan->save($value);
		}
		$salePlan->commit();
		$this->redirect('suggest',array('country'=>$country));		
	}


	private function calSuggest($sku,$country){
		//返回数组包含销售建议和价格
		if($country=='us'){
			$salePlanTable = M(C('DB_SZ_US_SALE_PLAN'));
		}
		if($country=='de'){
			$salePlanTable = M(C('DB_SZ_DE_SALE_PLAN'));
		}
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
		$asqsq = intval($this->calSaleQuantity($sku,$country,$startDate))*intval($standard_period)/intval($adjust_period);
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$lspsq = $this->calSaleQuantity($sku,$country,$startDate,$endDate)*$standard_period/$adjust_period;

		//检查是否需要重新刊登
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
			$relistingNodSaleQuantity = $this->calSaleQuantity($sku,$country,$startDate);
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
			$clearNodSaleQuantity = $this->calSaleQuantity($sku,$country,$startDate);
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

	private function calSaleQuantity($sku, $country, $startDate, $endDate=null){
		if($endDate==null)
			$endDate = date('Y-m-d H:i:s',time());
		$szOutboundItem = D("SzOutboundView");
		$map[C('DB_SZ_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
		$map[C('DB_SZ_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
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

	public function updataMetaDate(){
		if($this->isPost()){
			$data[C('DB_SZ_SALE_PLAN_METADATA_ID')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_CLEAR_NOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_RELISTING_NOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_STANDARD_PERIOD'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR1')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR1'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR2')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR2'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR3')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR3'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR4')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR4'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SPR5')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SPR5'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_PCR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_PCR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_SQNR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_SQNR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_DENOMINATOR'),'','htmlspecialchars');
			$data[C('DB_SZ_SALE_PLAN_METADATA_GRFR')] = I('post.'.C('DB_SZ_SALE_PLAN_METADATA_GRFR'),'','htmlspecialchars');
			$metadata = M(C('DB_SZ_SALE_PLAN_METADATA'));
			$metadata->startTrans();
			$result = $metadata->save($data);

			$metadata->commit();
            if(false !== $result || 0 !== $result){
                $this->success('保存成功！');
            }else{
                $this->error("保存不成功！");
            }

		}
	}

	private function getSzUsSaleInfo(){
		$products = M(C('DB_PRODUCT'));
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count,20);           
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $tpl = $products->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($tpl as $key=>$value) {
        	$data[$key][C('DB_PRODUCT_SKU')]=$value[C('DB_PRODUCT_SKU')];
        	$data[$key][C('DB_PRODUCT_CNAME')]=$value[C('DB_PRODUCT_CNAME')];
        	$data[$key][C('DB_PRODUCT_PRICE')]=$value[C('DB_PRODUCT_PRICE')];
        	$data[$key]['shipping-way']=$this->getSzUsShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['shipping-fee']=$this->getSzUsShippingFee($value['weight'],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_SZ_US_SALE_PRICE')]=M(C('DB_SZ_US_SALE_PLAN'))->where(array(C('DB_SZ_US_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_SZ_US_SALE_PLAN_PRICE'));
        	$data[$key]['cost']=round($this->getSzUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_SZ_US_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_SZ_US_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')],2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')],2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')],2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')],2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getSzUsKeywordSaleInfo(){
		$products = M(C('DB_PRODUCT'));
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
        	$data[$key]['shipping-way']=$this->getSzUsShippingWay($value[C('DB_PRODUCT_WEIGHT')]);
        	$data[$key]['shipping-fee']=$this->getSzUsShippingFee($value['weight']);
        	$data[$key][C('DB_PRODUCT_SZ_US_SALE_PRICE')]=$value[C('DB_PRODUCT_SZ_US_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getSzUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_SZ_US_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_SZ_US_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')],2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')],2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')],2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')],2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}


	private function getSzUsShippingWay($weight,$l,$w,$h){
		if($weight<=2000 and $l<=60 and ($l+$w+$h)<=90){
			return 'Flyt EUB';
		}
		elseif($weight<=2000){
			return 'Flyt China Post';
		}
		else{
			return 'No Way';
		}
	}

	private function getSzUsShippingFee($weight,$l,$w,$h){
		if($weight<=2000 and $l<=60 and ($l+$w+$h)<=90){
			return round($this->calFlytEubFee($weight,$l,$w,$h),2);
		}
		elseif($weight<=2000){
			return round($this->calFlytCprUsFee($weight,$l,$w,$h),2);
		}
		else{
			return 0;
		}
	}

	private function calFlytEubFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			if($weight>0 and $weight<=200){
				return 9+$weight*0.08;
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
		if ($weight <= 2000){
			return 8+90.5*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function getSzUsCost($data){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$c = ($data[C('DB_PRODUCT_PRICE')]+0.5+$data['shipping-fee'])/$exchange+$data[C('DB_PRODUCT_SZ_US_SALE_PRICE')]*0.144+0.35;
		return $c;
	}

	private function getSzDeSaleInfo(){
		$products = M(C('DB_PRODUCT'));
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count,20);           
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $tpl = $products->order(C('DB_PRODUCT_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($tpl as $key=>$value) {
        	$data[$key][C('DB_PRODUCT_SKU')]=$value[C('DB_PRODUCT_SKU')];
        	$data[$key][C('DB_PRODUCT_CNAME')]=$value[C('DB_PRODUCT_CNAME')];
        	$data[$key][C('DB_PRODUCT_PRICE')]=$value[C('DB_PRODUCT_PRICE')];
        	$data[$key]['shipping-way']=$this->getSzDeShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['shipping-fee']=$this->getSzDeShippingFee($value['weight'],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_SZ_DE_SALE_PRICE')]=$value[C('DB_PRODUCT_SZ_DE_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getSzDeCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_SZ_DE_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_SZ_DE_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')],2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')],2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')],2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')],2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getSzDeKeywordSaleInfo(){
		$products = M(C('DB_PRODUCT'));
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
        	$data[$key]['shipping-way']=$this->getSzDeShippingWay($value[C('DB_PRODUCT_WEIGHT')]);
        	$data[$key]['shipping-fee']=$this->getSzDeShippingFee($value['weight']);
        	$data[$key][C('DB_PRODUCT_SZ_DE_SALE_PRICE')]=$value[C('DB_PRODUCT_SZ_DE_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getSzDeCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_SZ_DE_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_SZ_DE_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')],2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')],2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')],2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')],2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getSzDeShippingWay($weight,$l,$w,$h){
		if($weight<=2000 and $l<=60 and ($l+$w+$h)<=90){
			return 'Flyt HK-DE';
		}
		elseif($weight<=2000){
			return 'Flyt China Post';
		}
		else{
			return 'No Way';
		}
	}

	private function getSzDeShippingFee($weight,$l,$w,$h){
		if($weight<=2000 and $l<=60 and ($l+$w+$h)<=90){
			return round($this->calFlytHkDeFee($weight,$l,$w,$h),2);
		}
		elseif($weight<=2000){
			return round($this->calFlytCprDeFee($weight,$l,$w,$h),2);
		}
		else{
			return 0;
		}
	}

	private function calFlytHkDeFee($weight,$l,$w,$h){
		if($weight <= 2000 And ($l + $w + $h) <= 90 And $l <=60){
			return 13+79*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function calFlytCprDeFee($weight,$l,$w,$h){
		if ($weight <= 2000){
			return 8+81*$weight/1000;
		}
		else{
			return 0;
		}
	}

	private function getSzDeCost($data){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTORMB'));
		$c = ($data[C('DB_PRODUCT_PRICE')]+0.5+$data['shipping-fee'])/$exchange+$data[C('DB_PRODUCT_SZ_DE_SALE_PRICE')]*0.144+0.35;
		return $c;
	}

	public function usTestCal(){
		if($this->isPost()){
			$p = I('post.price','','htmlspecialchars');
			$shippingWay = $this->getSzUsShippingWay(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$shippingFee = $this->getSzUsShippingFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = ($p+0.5+$shippingFee)/6.35+$salePrice*0.144+0.35;
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
						'height'=>I('post.height','','htmlspecialchars')
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
			$shippingWay = $this->getSzDeShippingWay(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$shippingFee = $this->getSzDeShippingFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = ($p+0.5+$shippingFee)/7+$salePrice*0.144+0.35;
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
						'height'=>I('post.height','','htmlspecialchars')
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
}

?>