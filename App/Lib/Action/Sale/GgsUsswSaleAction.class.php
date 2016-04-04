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
		$products = M('products');
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $tpl = $products->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($tpl as $key=>$value) {

        	$data[$key]['sku']=$value['sku'];
        	$data[$key]['title-cn']=$value['title-cn'];
        	$data[$key]['price']=$value['price'];
        	$data[$key]['us-rate']=round($value['us-rate']/100*$value['ggs-ussw-sp'],2);
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['way-to-us']=$value['way-to-us'];
        	$data[$key]['way-to-us-fee']=$data[$key]['way-to-us']=="空运"?$this->getUsswAirFirstTransportFee($value['weight'],$value['length'],$value['width'],$value['height']):$this->getUsswSeaFirstTransportFee($value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way']=$this->getUsswLocalShippingWay($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-fee']=$this->getUsswLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['ggs-ussw-sp']=$value['ggs-ussw-sp'];
        	$data[$key]['cost']=round($this->getUsswCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key]['ggs-ussw-sp']-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key]['ggs-ussw-sp']*100,2).'%';
        	$data[$key]['weight']=round($value['weight']*0.0352740,2);
        	$data[$key]['length']=round($value['length']*0.3937008,2);
        	$data[$key]['width']=round($value['width']*0.3937008,2);
        	$data[$key]['height']=round($value['height']*0.3937008,2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getUsswKeywordSaleInfo(){
		$products = M('products');
        import('ORG.Util.Page');
        $count = $products->count();
        $Page = new Page($count);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
        $tpl = $products->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        foreach ($tpl as $key=>$value) {

        	$data[$key]['sku']=$value['sku'];
        	$data[$key]['title-cn']=$value['title-cn'];
        	$data[$key]['price']=$value['price'];
        	$data[$key]['us-rate']=round($value['us-rate']/100*$value['ggs-ussw-sp'],2);
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['way-to-us']=$value['way-to-us'];
        	$data[$key]['way-to-us-fee']=$data[$key]['way-to-us']=="空运"?$this->getUsswAirFirstTransportFee($value['weight'],$value['length'],$value['width'],$value['height']):$this->getUsswSeaFirstTransportFee($value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way']=$this->getUsswLocalShippingWay($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-fee']=$this->getUsswLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key]['ggs-ussw-sp']=$value['ggs-ussw-sp'];
        	$data[$key]['cost']=round($this->getUsswCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key]['ggs-ussw-sp']-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key]['ggs-ussw-sp']*100,2).'%';
        	$data[$key]['weight']=round($value['weight']*0.0352740,2);
        	$data[$key]['length']=round($value['length']*0.3937008,2);
        	$data[$key]['width']=round($value['width']*0.3937008,2);
        	$data[$key]['height']=round($value['height']*0.3937008,2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
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
		if(($weight/1000)>=(l * w * h / 6000)){
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
		/*$fees[0] = 0;
		$fees[1] = $this->calUsswUspsFirstClassFee($weight,$l,$w,$h);
		$fees[2] = $this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h);
		$fees[3] = $this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h);
		$fees[4] = $this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h);
		$fees[5] = $this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h);
		$fees[6] = $this->calUsswFedexSmartPostFee($weight,$l,$w,$h);
		$fees[7] = $this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h);*/
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 7; $i++) { 
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
				5=>$this->calUsswUspsPriorityPackageFee(),
				6=>$this->calUsswFedexSmartPostFee(),
				7=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 7; $i++) { 
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

	private function getUsswCost($data){
			$exchange = M('metadata')->where('id=1')->getField('usdtormb');
			$c = ($data['price']+0.5)/$exchange+$data['us-rate']+$data['ussw-fee']+$data['way-to-us-fee']+$data['local-shipping-fee']+$data['ggs-ussw-sp']*0.144+0.35;
			return $c;
		}
}

?>