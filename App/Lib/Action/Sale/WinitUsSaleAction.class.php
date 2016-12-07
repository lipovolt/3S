<?php

class WinitUsSaleAction extends CommonAction{
	public function index(){
		if($_POST['keyword']==""){
			$this->getWinitUsSaleInfo();
        }
        else{           
            $this->getWinitUsKeywordSaleInfo();
        }
	}

	private function getWinitUsSaleInfo(){
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
        	$data[$key]['ussw-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$data[$key][C('DB_PRODUCT_TOUS')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['pweight'],$value['plength'],$value['pwidth'],$value['pheight']);
        	$data[$key][C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]=$value[C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getWinitUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]*100,2).'%';
        	$data[$key]['pweight']=round($value[C('DB_PRODUCT_PWEIGHT')]*0.0352740,2);
        	$data[$key]['plength']=round($value[C('DB_PRODUCT_PLENGTH')]*0.3937008,2);
        	$data[$key]['pwidth']=round($value[C('DB_PRODUCT_PWIDTH')]*0.3937008,2);
        	$data[$key]['pheight']=round($value[C('DB_PRODUCT_PHEIGHT')]*0.3937008,2);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getWinitUsKeywordSaleInfo(){
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
        	$data[$key]['ussw-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$data[$key][C('DB_PRODUCT_TOUS')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_PLENGTH')],$value[C('DB_PRODUCT_PWIDTH')],$value[C('DB_PRODUCT_PHEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['pweight'],$value['plength'],$value['pwidth'],$value['pheight']);
        	$data[$key][C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]=$value[C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getWinitUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]*100,2).'%';
        	$data[$key]['pweight']=round($value[C('DB_PRODUCT_PWEIGHT')]*0.0352740,2);
        	$data[$key]['plength']=round($value[C('DB_PRODUCT_PLENGTH')]*0.3937008,2);
        	$data[$key]['pwidth']=round($value[C('DB_PRODUCT_PWIDTH')]*0.3937008,2);
        	$data[$key]['pheight']=round($value[C('DB_PRODUCT_PHEIGHT')]*0.3937008,2);
        }
        $this->assign('keyword',I('post.keyword','','htmlspecialchars'));
        $this->assign('keywordValue',I('post.keywordValue','','htmlspecialchars'));
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function calWinitSIOFee($weight,$l,$w,$h){
		//月仓储费=立方米*每日每立方租金*30天
		$monthlyStorageFee = ($l*$w*$h)/1000000*1.2*30;
		$itemInOutFee = 0;
		if($weight>0 And $weight <= 500){
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
		elseif((1.82 + (floatval(($weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (floatval((weight - 30000) / 10000) + 1) * 0.18) < (18.2 + 1.8) ){
			$itemInOutFee = 1.82 + (floatval((weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (floatval((weight - 30000) / 10000) + 1) * 0.18;
		}
		else{
			$itemInOutFee = 18.2 + 1.8;
		}
		return round($monthlyStorageFee+$itemInOutFee,2);
	}

	private function getWinitAirFirstTransportFee($weight,$l,$w,$h){
		/*if(($weight/1000)>=($l * $w * $h / 6000)){
			return round($weight / 1000 * 5.8,2);
		}
		else{
			return round(($l * $w * $h) / 6000 * 5.8,2);
		}*/	
		return round($weight / 1000 * 5.8,2);
	}

	private function getWinitSeaFirstTransportFee($l,$w,$h){
		return round(($l * $w * $h) / 1000000 * 220,2);
	}

	private function getWinitLocalShippingWay($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'USPS First Class Mail',
				2=>'UPS Sure Post',
				3=>'UPS Ground Service',
				4=>'USPS Priority Mail Package'
			);
		$fees=array(
				0=>0,
				1=>$this->calWinitUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calWinitUPSSurePostFee($weight,$l,$w,$h),
				3=>$this->calWinitUPSGroundPostFee($weight,$l,$w,$h),
				4=>$this->calWinitUspsPriorityPackageFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		$way=0;
		for ($i=0; $i < 5; $i++) { 
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
				1=>$this->calWinitUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calWinitUPSSurePostFee($weight,$l,$w,$h),
				3=>$this->calWinitUPSGroundPostFee($weight,$l,$w,$h),
				4=>$this->calWinitUspsPriorityPackageFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 5; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function calWinitUspsFirstClassFee($weight,$l,$w,$h){
		if($weight <= 453 And ($l + 2 * ($w + $h)) <= 274){
			if($weight>0 and $weight<85){
				return 2.8;
			}
			elseif($weight>=85 and $weight<113){
				return 2.81;
			}
			elseif($weight>=113 and $weight<141){
				return 3.05;
			}
			elseif($weight>=141 and $weight<170){
				return 3.1;
			}
			elseif($weight>=170 and $weight<198){
				return 3.11;
			}
			elseif($weight>=198 and $weight<226){
				return 3.15;
			}
			elseif($weight>=226 and $weight<255){
				return 4;
			}
			elseif($weight>=255 and $weight<283){
				return 4.04;
			}
			elseif($weight>=283 and $weight<311){
				return 4.08;
			}
			elseif($weight>=311 and $weight<340){
				return 4.11;
			}
			elseif($weight>=340 and $weight<368){
				return 4.15;
			}
			elseif($weight>=368 and $weight<396){
				return 4.61;
			}
			elseif($weight>=396 and $weight<425){
				return 4.68;
			}
			elseif($weight>=425 and $weight<=453){
				return 4.74;
			}
		}
		else{
			return 0;
		}
	}

	private function calWinitUPSSurePostFee($weight,$l,$w,$h){
		if($weight <= 4082 and $l<= 86 and $w<=43 and $l*$w*$h/1000000<=0.028){
			if($weight>0 and $weight<453){
				return 6.28;
			}
			elseif($weight>=453 and $weight<907){
				return 7.11;
			}
			elseif($weight>=907 and $weight<1360){
				return 7.83;
			}
			elseif($weight>=1360 and $weight<1814){
				return 8.4;
			}
			elseif($weight>=1814 and $weight<2268){
				return 8.82;
			}
			elseif($weight>=2268 and $weight<2721){
				return 8.93;
			}
			elseif($weight>=2721 and $weight<3175){
				return 9.26;
			}
			elseif($weight>=3175 and $weight<3628){
				return 9.62;
			}
			elseif($weight>=3628 and $weight<4082){
				return 10.24;
			}
			
		}
		else{
			return 0;
		}
	}

	private function calWinitUspsPriorityPackageFee($weight,$l,$w,$h){
		if($weight <= 11340 and ($l + 2 * ($w + $h)) <= 274){
			if($weight>0 and $weight<453){
				return 7.66;
			}
			elseif($weight>=453 and $weight<907){
				return 11.33;
			}
			elseif($weight>=907 and $weight<1360){
				return 15.36;
			}
			elseif($weight>=1360 and $weight<1814){
				return 18.5;
			}
			elseif($weight>=1814 and $weight<2268){
				return 21.44;
			}
			elseif($weight>=2268 and $weight<2721){
				return 24.56;
			}
			elseif($weight>=2721 and $weight<3175){
				return 27.58;
			}
			elseif($weight>=3175 and $weight<3628){
				return 30.97;
			}
			elseif($weight>=3628 and $weight<4082){
				return 34.43;
			}
			elseif($weight>=4082 and $weight<4536){
				return 37.44;
			}
			elseif($weight>=4536 and $weight<=4989){
				return 40.57;
			}
			elseif($weight>=4989 and $weight<=5443){
				return 43.5;
			}
			elseif($weight>=5443 and $weight<=5896){
				return 45.05;
			}
			elseif($weight>=5896 and $weight<=6350){
				return 47.28;
			}
			elseif($weight>=6350 and $weight<=6804){
				return 48.53;
			}
			elseif($weight>=6804 and $weight<=7257){
				return 51.2;
			}
			elseif($weight>=7257 and $weight<=7711){
				return 53.9;
			}
			elseif($weight>=7711 and $weight<=8164){
				return 56.61;
			}
			elseif($weight>=8164 and $weight<=8618){
				return 59.31;
			}
			elseif($weight>=8618 and $weight<=9072){
				return 62.05;
			}
			elseif($weight>=9072 and $weight<=9525){
				return 62.84;
			}
			elseif($weight>=9525 and $weight<=9979){
				return 63.57;
			}
			elseif($weight>=9979 and $weight<=10432){
				return 63.94;
			}
			elseif($weight>=10432 and $weight<=10886){
				return 65.5;
			}
			elseif($weight>=10886 and $weight<=11340){
				return 66.64;
			}

		}
		else{
			return 0;
		}
	}

	private function calWinitUPSGroundPostFee($weight,$l,$w,$h){
		$fee = 0;
		$vWeight = $l*$w*$h/6000;
		if($weight<$vWeight){
			$weight = $vWeight;
		}
		if($weight <= 68040 and $l<=274.32){
			if($weight>0 and $weight<453){
				$fee = 9.88;
			}
			elseif($weight>=453 and $weight<907){
				$fee = 9.92;
			}
			elseif($weight>=907 and $weight<1360){
				$fee = 10.17;
			}
			elseif($weight>=1360 and $weight<1814){
				$fee = 10.72;
			}
			elseif($weight>=1814 and $weight<2268){
				$fee = 11.25;
			}
			elseif($weight>=2268 and $weight<2721){
				$fee = 11.5;
			}
			elseif($weight>=2721 and $weight<3175){
				$fee = 11.82;
			}
			elseif($weight>=3175 and $weight<3628){
				$fee = 12.15;
			}
			elseif($weight>=3628 and $weight<4082){
				$fee = 12.71;
			}
			elseif($weight>=4082 and $weight<4536){
				$fee = 13.34;
			}
			elseif($weight>=4536 and $weight<4989){
				$fee = 13.67;
			}
			elseif($weight>=4989 and $weight<5443){
				$fee = 14.25;
			}
			elseif($weight>=5443 and $weight<5896){
				$fee = 14.95;
			}
			elseif($weight>=5896 and $weight<6350){
				$fee = 15.62;
			}
			elseif($weight>=6350 and $weight<6804){
				$fee = 16.26;
			}
			elseif($weight>=6804 and $weight<7257){
				$fee = 16.73;
			}
			elseif($weight>=7257 and $weight<7711){
				$fee = 17.44;
			}
			elseif($weight>=7711 and $weight<8164){
				$fee = 18.16;
			}
			elseif($weight>=8164 and $weight<8618){
				$fee = 18.68;
			}
			elseif($weight>=8618 and $weight<9072){
				$fee = 19.39;
			}
			elseif($weight>=9072 and $weight<9525){
				$fee = 19.45;
			}
			elseif($weight>=9525 and $weight<9979){
				$fee = 20.16;
			}
			elseif($weight>=9979 and $weight<10432){
				$fee = 20.85;
			}
			elseif($weight>=10432 and $weight<10886){
				$fee = 21.53;
			}
			elseif($weight>=10886 and $weight<11340){
				$fee = 22.21;
			}
			elseif($weight>=11340 and $weight<15876){
				$fee = 27.32;
			}
			elseif($weight>=15876 and $weight<20412){
				$fee = 31.58;
			}
			elseif($weight>=20412 and $weight<24948){
				$fee = 34.82;
			}
			elseif($weight>=24948 and $weight<34020){
				$fee = 39.19;
			}
			elseif($weight>=34020 and $weight<47628){
				$fee = 51.2;
			}
			elseif($weight>=47628 and $weight<56700){
				$fee = 58.96;
			}
			elseif($weight>=56700 and $weight<=68040){
				$fee = 69.93;
			}
		}
		if($l>=121.9 or $w>76.2 or $weight>=31752){
			$fee = $fee+8.5;
		}
		if(($l+2*($w+$h))>=330.2 and ($l+2*($w+$h))<=419.1){
			$fee = $fee+51;
		}
		return $fee;
	}

	private function getWinitUsCost($data){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$c = ($data[C('DB_PRODUCT_PRICE')]+0.5)/$exchange+($data[C('DB_PRODUCT_PRICE')]*1.2/$exchange)*$data[C('DB_PRODUCT_USTARIFF')]+$data['ussw-fee']+$data['way-to-us-fee']+$data['local-shipping-fee']+$data[C('DB_PRODUCT_RC_WINIT_US_SALE_PRICE')]*0.144+0.35;
		return $c;
	}
}

?>