<?php

class GgsUsswSaleAction extends CommonAction{

	public function index(){
		if($_POST['keyword']==""){
			$this->getUsswSaleInfo();
        }
        else{           
            $this->getUsswKeywordSaleInfo();
        }
	}

	public function usswSaleSuggest(){
		if($_POST['keyword']==""){
            $Data = D("UsswSalePlanView");
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $suggest = $Data->order(C('DB_USSW_SALE_PLAN_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('suggest',$suggest);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->suggest = D("UsswSalePlanView")->where($where)->select();
        }
        $this->display();
	}


	public function calUsswSaleInfo(){
		$usswProduct = M(C('DB_USSTORAGE'))->distinct(true)->field(C('DB_USSTORAGE_SKU'))->select();

		foreach ($usswProduct as $key => $p) {

			$usp = M(C('DB_USSW_SALE_PLAN'))->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->select();
			if($usp == null){
				$this->addProductToUsp($p[C('DB_USSTORAGE_SKU')]);
				$usp=$this->getUsp($p[C('DB_USSTORAGE_SKU')]);
			}
			if(!$this->isProductInfoComplete($p[C('DB_USSTORAGE_SKU')])){
				//产品信息不全，建议完善产品信息,退出循环
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = 'complete_product_info';
				$this->updateUsp($usp);

			}elseif(!$this->isUsswSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息，退出循环
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = 'complete_sale_info';
				$this->updateUsp($usp);
			}else{
				$lastModifyDate = M('ussw_sale_plan')->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				if($Date->dateDiff($lastModifyDate)>7){
					//开始计算该产品的销售建议
					$suggest = $this->calUsswSuggest($p[C('DB_USSTORAGE_SKU')]);
					$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_USSW_SALE_PLAN_SUGGEST')];
					$this->updateUsp($usp);
				}
			}
		}
		$this->redirect('usswSaleSuggest');
	}

	public function confirmSuggest($id){
		$data = M(C('DB_USSW_SALE_PLAN'))->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->find();
		$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
		if($data[C('DB_USSW_SALE_PLAN_ID_SUGGEST')]=='relisting'){
			$data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')])+1;
		}

		if($data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]==null){
			$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  date('ymd',time()).' '.$data[C('DB_USSW_SALE_PLAN_PRICE')];
		}else{
			$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')].' | '.date('Y-m-d',time()).' '.$data[C('DB_USSW_SALE_PLAN_PRICE')];
		}

		$data[C('DB_USSW_SALE_PLAN_PRICE')] = $data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
		$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
		M(C('DB_USSW_SALE_PLAN'))->save($data);
		$this->redirect('usswSaleSuggest');
	}

	public function ignoreSuggest($id){
		$data = M(C('DB_USSW_SALE_PLAN'))->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->find();
		$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
		$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
		M(C('DB_USSW_SALE_PLAN'))->save($data);
		$this->redirect('usswSaleSuggest');
	}

	private function calUsswSuggest($sku){
		//返回数组包含销售建议和价格
		$saleplanMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',$sku);
		$saleplan = M(C('DB_USSW_SALE_PLAN'))->select();
		$cost = $saleplan[C('DB_USSW_SALE_PLAN_COST')];
		$price = $saleplan[C('DB_USSW_SALE_PLAN_PRICE')];
		$status = $saleplan[C('DB_USSW_SALE_PLAN_STATUS')];
		if($status==0){
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
			return $sugg;
		}

		$metaMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'))->select();		
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

		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$period);
		$asqsq = $this->calUsswSaleQuantity($sku,$startDate)*$standard_period/$adjust_period;
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$period);
		$lspsq = $this->calUsswSaleQuantity($sku,$startDate,$endDate)*$standard_period/$adjust_period;

		//检查是否需要重新刊登
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
			$relistingNodSaleQuantity = $this->calUsswSaleQuantity($sku,$startDate);
			if($relistingNodSaleQuantity==0){
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost*$this->getCostClass($cost);
				$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_RELISTING');
				return $sugg;
			}
		}

		//检查是否需要清货清货
		if($asqsq==0){
			$startDate = date('Y-m-d H:i:s',time()-60*60*24*$clear_nod);
			$clearNodSaleQuantity = $this->calUsswSaleQuantity($sku,$startDate);
			if($clearNodSaleQuantity==0){
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
				$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_CLEAR');
				return $sugg;
			}
		}


		//检查是否需要调价
		$diff = $aspsq-$lspsq;
		if($lspsq<$sqnr){
			$lspsq = $denominator;
		}
		if($diff/$lspsq>$grfr){
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price+$price*$pcr;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_ADJUST_PRICE');
			return $sugg;
		}
		if($diff/$lspsq<-$grfr){
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price-$price*$pcr;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_ADJUST_PRICE');
			return $sugg;
		}

	}

	private function getCostClass($cost){
		$metaMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',1);
		$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'))->select();
		$spr1 = $metadata->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_SPR1'));
		$spr2 = $metadata->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_SPR2'));
		$spr3 = $metadata->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_SPR3'));
		$spr4 = $metadata->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_SPR4'));
		$spr5 = $metadata->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_SPR5'));
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

	private function calUsswSaleQuantity($sku, $startDate, $endDate=null){
		if($endDate==null)
			$endDate = date('Y-m-d H:i:s',time());
		$usswOutboundItem = D("UsswOutboundView");
		$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
		$map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
		return $usswOutboundItem->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
	}

	private function getUsp($sku){
		return M(C('DB_USSW_SALE_PLAN'))->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->select();
	}

	private function addProductToUsp($sku){
		//添加产品到ussw_sale_plan表
		
		$newUsp[C('DB_USSW_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_USSW_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =null;
		$newUsp[C('DB_USSW_SALE_PLAN_COST')] = $this->calUsswSuggestCost($product);
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE')] = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_GGS_USSW_SALE_PRICE'));
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_STATUS')] = 1;

		M(C('DB_USSW_SALE_PLAN'))->add($newUsp);
	}

	private function inUsstorage($sku){
		$result = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->find();
		if(false !== $result && null !== $result){
            return true;
        }else{
            return false;
        }
	}

	private function updateUsp($usp){
		//更新产品自建仓销售建议
		M(C('DB_USSW_SALE_PLAN'))->save($usp);
	}

	private function calUsswSuggestCost($product){
		//计算产品美自建仓销售成本
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    	$data['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['way-to-us-fee']=$data[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['local-shipping-fee']=$this->getUsswLocalShippingFee($product['weight'],$product['length'],$product['width'],$product['height']);

    	$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($data[C('DB_PRODUCT_PRICE')]+0.5)/$exchange+($data[C('DB_PRODUCT_PRICE')]*1.2/$exchange)*$data[C('DB_PRODUCT_USTARIFF')]+$data['ussw-fee']+$data['way-to-us-fee']+$data['local-shipping-fee'];
		$cost = $cost+$cost*0.18;
		return $cost;

	}

	private function isUsswSaleInfoComplete($usp){
		if($usp[C('DB_USSW_SALE_PLAN_COST')]==null || $usp[C('DB_USSW_SALE_PLAN_COST')]==0)
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

	public function usswSalePlanMetadata(){
		$this->data=M(C('DB_USSW_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function updataMetaDate(){
		if($this->isPost()){
			$data[C('DB_USSW_SALE_PLAN_METADATA_ID')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_CLEAR_NOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_ADJUST_PERIOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_STANDARD_PERIOD'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR1')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR1'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR2')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR2'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR3')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR3'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR4')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR4'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SPR5')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SPR5'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_PCR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_PCR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_SQNR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_SQNR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_DENOMINATOR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_DENOMINATOR'),'','htmlspecialchars');
			$data[C('DB_USSW_SALE_PLAN_METADATA_GRFR')] = I('post.'.C('DB_USSW_SALE_PLAN_METADATA_GRFR'),'','htmlspecialchars');
			$metadata = M(C('DB_USSW_SALE_PLAN_METADATA'));
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

	public function ggsUsswItemTest(){
		if($this->isPost()){
			$p = I('post.price','','htmlspecialchars');
			$usRate = I('post.saleprice','','htmlspecialchars')*0.05;
			$usswFee = $this->calUsswSIOFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$wayToUs = I('post.keyword','','htmlspecialchars');
			$wayToUsFee = $wayToUs=="air"?$this->getUsswAirFirstTransportFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars')):$this->getUsswSeaFirstTransportFee(I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$localShippingWay = $this->getUsswLocalShippingWay(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$localShippingFee = $this->getUsswLocalShippingFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = ($p+0.5)/6.35+$salePrice*0.05+$usswFee+$wayToUsFee+$localShippingFee+$salePrice*0.144+0.35;
			$testData = array(
						'price'=>$p,
						'us-rate'=>$usRate,
						'ussw-fee'=>$usswFee,
						'way-to-us'=>$wayToUs,
						'way-to-us-fee'=>$wayToUsFee,
						'local-shipping-way'=>$localShippingWay,
						'local-shipping-fee'=>$localShippingFee,
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
						'us-rate'=>0,
						'ussw-fee'=>0,
						'way-to-us'=>'空运',
						'way-to-us-fee'=>0,
						'local-shipping-way'=>'',
						'local-shipping-fee'=>0,
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

	private function getUsswSaleInfo(){
		$products = M(C('DB_PRODUCT'));
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
        	$data[$key][C('DB_PRODUCT_USTARIFF')]=$value[C('DB_PRODUCT_USTARIFF')]/100;
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$data[$key][C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getUsswLocalShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getUsswLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]=$value[C('DB_PRODUCT_GGS_USSW_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getUsswCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')]*0.0352740,2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')]*0.3937008,2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')]*0.3937008,2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')]*0.3937008,2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getUsswKeywordSaleInfo(){
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
        	$data[$key][C('DB_PRODUCT_USTARIFF')]=$value[C('DB_PRODUCT_USTARIFF')]/100;
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$data[$key][C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getUsswLocalShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getUsswLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]=$value[C('DB_PRODUCT_GGS_USSW_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getUsswCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_WEIGHT')]*0.0352740,2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')]*0.3937008,2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')]*0.3937008,2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')]*0.3937008,2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getUsswCost($data){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$c = ($data[C('DB_PRODUCT_PRICE')]+0.5)/$exchange+($data[C('DB_PRODUCT_PRICE')]*1.2/$exchange)*$data[C('DB_PRODUCT_USTARIFF')]+$data['ussw-fee']+$data['way-to-us-fee']+$data['local-shipping-fee']+$data[C('DB_PRODUCT_GGS_USSW_SALE_PRICE')]*0.144+0.35;
		return $c;
	}

	private function calUsswSIOFee($weight,$l,$w,$h){
		//月仓储费=立方米*每日每立方租金*30天
		$monthlyStorageFee = ($l*$w*$h)/1000000*1.2*30;
		$itemInOutFee = 0;
		if($weight <= 500){
			$itemInOutFee = 0.18 + 0.05;
		}
		elseif($weight>500 and $weight <= 1000){
			$itemInOutFee = 0.25 + 0.06;
		}
		elseif($weight>1000 and $weight <= 2000){
			$itemInOutFee = 0.51 + 0.09;
		}
		elseif($weight>2000 and $weight <= 10000){
			$itemInOutFee = 0.65 + 0.18;
		}
		elseif($weight>10000 and $weight <= 20000){
			$itemInOutFee = 1.37 + 0.27;
		}
		elseif($weight>20000 and $weight <= 30000){
			$itemInOutFee = 1.82 + 0.36;
		}
		elseif((1.82 + (Int(($weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (Int((weight - 30000) / 10000) + 1) * 0.18) < (18.2 + 1.8) ){
			$itemInOutFee = 1.82 + (Int((weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (Int((weight - 30000) / 10000) + 1) * 0.18;
		}
		else{
			$itemInOutFee = 18.2 + 1.8;
		}
		return round($monthlyStorageFee+$itemInOutFee,2);
	}

	private function getUsswAirFirstTransportFee($weight,$l,$w,$h){
		if(($weight/1000)>=($l * $w * $h / 6000)){
			return round($weight / 1000 * 5.8,2);
		}
		else{
			return round(($l * $w * $h) / 6000 * 5.8,2);
		}	
	}

	private function getUsswSeaFirstTransportFee($l,$w,$h){
		return round(($l * $w * $h) / 1000000 * 220,2);
	}

	private function getUsswLocalShippingWay($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'USPS First Class Mail',
				2=>'USPS Priority Mail Small Flat Rate Box',
				3=>'USPS Priority Mail Medium Flat Rate Box',
				4=>'USPS Priority Mail Large Flat Rate Box',
				5=>'USPS Priority Mail Package',
				6=>'Fedex Smart Post',
				7=>'Fedex Home Delivery'
			);
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityPackageFee(),
				6=>$this->calUsswFedexSmartPostFee(),
				7=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 8; $i++) { 
			if(($cheapest > $fees[$i]) and ($fees[$i] != 0)){
				$cheapest = $fees[$i];
				$way = $i;
			}
		}
		return $ways[$way];
	}

	private function getUsswLocalShippingFee($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h),
				6=>$this->calUsswFedexSmartPostFee($weight,$l,$w,$h),
				7=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 8; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function calUsswUspsFirstClassFee($weight,$l,$w,$h){
		if($weight <= 453 And ($l + 2 * ($w + $h)) <= 210){
			if($weight>=0 and $weight<85){
				return 2.57;
			}
			elseif($weight>=85 and $weight<226){
				return 2.72;
			}
			elseif($weight>=226 and $weight<255){
				return 3.68;
			}
			elseif($weight>=255 and $weight<283){
				return 3.91;
			}
			elseif($weight>=283 and $weight<311){
				return 4.15;
			}
			elseif($weight>=311 and $weight<340){
				return 4.27;
			}
			elseif($weight>=340 and $weight<368){
				return 4.51;
			}
			elseif($weight>=368 and $weight<396){
				return 5.25;
			}
			elseif($weight>=396 and $weight<425){
				return 5.25;
			}
			elseif($weight>=425 and $weight<=453){
				return 5.25;
			}
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight <= 31751 and $l <= 21.5 and $w <= 13.3 and $h <= 4){
			return 7.08;
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight <= 31751 and (($l <= 34 and $w <= 29 and $h <= 8) Or ($l <= 27 and $w <= 21 and $h <= 13))){
			return 13.92;
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight <= 31751 And $l <= 30 And $w <= 30 And $h <= 15){
			return 19.02;
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityPackageFee($weight,$l,$w,$h){
		if($weight <= 31751 and ($l + 2 * ($w + $h)) <= 274){
			if($weight>=0 and $weight<453){
				return 8.34;
			}
			elseif($weight>=453 and $weight<907){
				return 12.53;
			}
			elseif($weight>=907 and $weight<1360){
				return 16.84;
			}
			elseif($weight>=1360 and $weight<1814){
				return 19.9;
			}
			elseif($weight>=1814 and $weight<2268){
				return 23.06;
			}
			elseif($weight>=2268 and $weight<2721){
				return 26.41;
			}
			elseif($weight>=2721 and $weight<3175){
				return 29.66;
			}
			elseif($weight>=3175 and $weight<3628){
				return 33.3;
			}
			elseif($weight>=3628 and $weight<4082){
				return 37.03;
			}
			elseif($weight>=4082 and $weight<4536){
				return 40.27;
			}
			elseif($weight>=4536 and $weight<=4989){
				return 43.63;
			}
			elseif($weight>=4989 and $weight<=5443){
				return 46.79;
			}
			elseif($weight>=5443 and $weight<=5896){
				return 48.44;
			}
			elseif($weight>=5896 and $weight<=6350){
				return 50.86;
			}
			elseif($weight>=6350 and $weight<=6804){
				return 52.2;
			}
			elseif($weight>=6804 and $weight<=7257){
				return 55.07;
			}
			elseif($weight>=7257 and $weight<=7711){
				return 57.97;
			}
			elseif($weight>=7711 and $weight<=8164){
				return 60.89;
			}
			elseif($weight>=8164 and $weight<=8618){
				return 63.79;
			}
			elseif($weight>=8618 and $weight<=9072){
				return 66.73;
			}
			elseif($weight>=9072 and $weight<=9525){
				return 67.58;
			}
			elseif($weight>=9525 and $weight<=9979){
				return 68.36;
			}
			elseif($weight>=9979 and $weight<=10432){
				return 68.77;
			}
			elseif($weight>=10432 and $weight<=10886){
				return 70.45;
			}
			elseif($weight>=10886 and $weight<=11340){
				return 71.66;
			}

		}
		else{
			return 0;
		}
	}

	private function calUsswFedexSmartPostFee($weight,$l,$w,$h){
		if($weight <= 31751 and ($l + $w + $h) <= 325 and  $l > 16 and $w > 11 and $h > 2.5 ){
			if($weight>=0 and $weight<453){
				return 8.15;
			}
			elseif($weight>=453 and $weight<907){
				return 9.61;
			}
			elseif($weight>=907 and $weight<1360){
				return 10.9;
			}
			elseif($weight>=1360 and $weight<1814){
				return 11.98;
			}
			elseif($weight>=1814 and $weight<2268){
				return 14.29;
			}
			elseif($weight>=2268 and $weight<2721){
				return 14.4;
			}
			elseif($weight>=2721 and $weight<3175){
				return 14.95;
			}
			elseif($weight>=3175 and $weight<3628){
				return 15.47;
			}
			elseif($weight>=3628 and $weight<4082){
				return 16.44;
			}
			elseif($weight>=4082 and $weight<4536){
				return 24.82;
			}
			elseif($weight>=4536 and $weight<=4989){
				return 26.45;
			}
			elseif($weight>=4989 and $weight<=5443){
				return 27.95;
			}
			elseif($weight>=5443 and $weight<=5896){
				return 29.76;
			}
			elseif($weight>=5896 and $weight<=6350){
				return 31.48;
			}
			elseif($weight>=6350 and $weight<=6804){
				return 33.1;
			}
			elseif($weight>=6804 and $weight<=7257){
				return 33.1;
			}
			elseif($weight>=7257 and $weight<=7711){
				return 34.08;
			}
			elseif($weight>=7711 and $weight<=8164){
				return 35.71;
			}
			elseif($weight>=8164 and $weight<=8618){
				return 37.45;
			}
			elseif($weight>=8618 and $weight<=9072){
				return 38.72;
			}
			elseif($weight>=9072 and $weight<=9525){
				return 41;
			}
			elseif($weight>=9525 and $weight<=9979){
				return 44;
			}
			elseif($weight>=9979 and $weight<=10432){
				return 47;
			}
			elseif($weight>=10432 and $weight<=10886){
				return 50;
			}
			elseif($weight>=10886 and $weight<=11340){
				return 53;
			}

		}
		else{
			return 0;
		}
	}

	private function calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h){
		if($weight <= 31751 and ($l + $w + $h) <= 325 and  $l > 16 and $w > 11 and $h > 2.5 ){
			if($weight>=0 and $weight<453){
				return 8.15;
			}
			elseif($weight>=4536 and $weight<=4989){
				return 20.51;
			}
			elseif($weight>=4989 and $weight<=5443){
				return 20.51;
			}
			elseif($weight>=5443 and $weight<=5896){
				return 21.08;
			}
			elseif($weight>=5896 and $weight<=6350){
				return 22.2;
			}
			elseif($weight>=6350 and $weight<=6804){
				return 23.24;
			}
			elseif($weight>=6804 and $weight<=7257){
				return 24.22;
			}
			elseif($weight>=7257 and $weight<=7711){
				return 24.83;
			}
			elseif($weight>=7711 and $weight<=8164){
				return 25.82;
			}
			elseif($weight>=8164 and $weight<=8618){
				return 26.88;
			}
			elseif($weight>=8618 and $weight<=9072){
				return 28.08;
			}
			elseif($weight>=9072 and $weight<=9525){
				return 41;
			}
			elseif($weight>=9525 and $weight<=9979){
				return 44;
			}
			elseif($weight>=9979 and $weight<=10432){
				return 47;
			}
			elseif($weight>=10432 and $weight<=10886){
				return 50;
			}
			elseif($weight>=10886 and $weight<=11340){
				return 53;
			}

		}
		else{
			return 0;
		}
	}
}

?>