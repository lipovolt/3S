<?php

class WinitDeSaleAction extends CommonAction{
	public function index(){
		if($_POST['keyword']==""){
			$this->getWinitDeSaleInfo();
        }
        else{           
            $this->getWinitDeKeywordSaleInfo();
        }
	}

	private function getWinitDeSaleInfo(){
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
        	$data[$key][C('DB_PRODUCT_DETARIFF')]=$value[C('DB_PRODUCT_DETARIFF')]/100;
        	$data[$key]['winit-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TODE')]=$value[C('DB_PRODUCT_TODE')];
        	$data[$key]['way-to-de-fee']=$data[$key][C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]=$value[C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getWinitUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=$value[C('DB_PRODUCT_WEIGHT')];
        	$data[$key]['length']=$value[C('DB_PRODUCT_LENGTH')];
        	$data[$key]['width']=$value[C('DB_PRODUCT_WIDTH')];
        	$data[$key]['height']=$value[C('DB_PRODUCT_HEIGHT')];
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getWinitDeKeywordSaleInfo(){
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
        	$data[$key][C('DB_PRODUCT_DETARIFF')]=$value[C('DB_PRODUCT_DETARIFF')]/100;
        	$data[$key]['winit-fee']=$this->calWinitSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TODE')]=$value[C('DB_PRODUCT_TODE')];
        	$data[$key]['way-to-de-fee']=$data[$key][C('DB_PRODUCT_TODE')]=="空运"?$this->getWinitAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getWinitSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way']=$this->getWinitLocalShippingWay($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee']=$this->getWinitLocalShippingFee($value['weight'],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]=$value[C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')];
        	$data[$key]['cost']=round($this->getWinitUsCost($data[$key]),2);
        	$data[$key]['gprofit']=$data[$key][C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$value[C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]*100,2).'%';
        	$data[$key]['weight']=$value[C('DB_PRODUCT_WEIGHT')];
        	$data[$key]['length']=$value[C('DB_PRODUCT_LENGTH')];
        	$data[$key]['width']=$value[C('DB_PRODUCT_WIDTH')];
        	$data[$key]['height']=$value[C('DB_PRODUCT_HEIGHT')];
        }
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function calWinitSIOFee($weight,$l,$w,$h){
		//月仓储费=立方米*每日每立方租金*30天
		$monthlyStorageFee = ($l*$w*$h)/1000000*1.2*30;
		$itemInOutFee = 0;
		if($weight <= 500){
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
		elseif((1.39 + (Int(($weight - 30000) / 10000) + 1) * 0.7 + 0.28 + (Int((weight - 30000) / 10000) + 1) * 0.14) < (13.29 + 1.4) ){
			$itemInOutFee = 1.39 + (Int(($weight - 30000) / 10000) + 1) * 0.7 + 0.28 + (Int((weight - 30000) / 10000) + 1) * 0.14;
		}
		else{
			$itemInOutFee = 13.29 + 1.4;
		}
		return round($monthlyStorageFee+$itemInOutFee,2);
	}

	private function getWinitAirFirstTransportFee($weight,$l,$w,$h){
		$eurToUsd = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTOUSD'));
		if(($weight/1000)>=($l * $w * $h / 6000)){
			return round($weight / 1000 * 5.8 / $eurToUsd,2);
		}
		else{
			return round(($l * $w * $h) / 6000 * 5.8  / $eurToUsd,2);
		}	
	}

	private function getWinitSeaFirstTransportFee($l,$w,$h){
		$eurToUsd = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_EURTOUSD'));
		return round(($l * $w * $h) / 1000000 * 170  / $eurToUsd,2);
	}

	private function getWinitLocalShippingWay($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'DE Post Small Letter',
				2=>'DE Post Small Letter',
				3=>'DPD Small Parcels',
				4=>'DPD Normal Parcels',
				4=>'DHL Packet Service'
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
		for ($i=0; $i < 7; $i++) { 
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
		for ($i=0; $i < 7; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function calWinitPostSmallFee($weight,$l,$w,$h){
		if($weight <= 500 and $l >=10 and $l<=353 and $w>=7 and $w<=25 and $h>0 and $h<=2){
			return 1.67;
		}else{
			return 0;
		}		
	}

	private function calWinitPostLargeFee($weight,$l,$w,$h){
		if($weight <= 1000 and $l >=10 and $l<=353 and $w>=7 and $w<=30 and $h>0 and $h<=15){
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
		if($weight <= 3000 and $l<=50 and $l>=16 and $w>=11 and $h>=2 and ($l + 2 * ($w + $h))<=110){
			return 3.32;
		}
		else{
			return 0;
		}
	}

	private function calWinitDPDNormalFee($weight,$l,$w,$h){
		if($weight <= 31500 and $l<=175 and $l>=16 and $w>=11 and $h>=2 and ($l + 2 * ($w + $h))<=300){
			return 3.75;
		}else{
			return 0;
		}
	}

	private function calWinitDHLFee($weight,$l,$w,$h){
		$fee = 0;
		if($weight <= 31500 and $l<=200 and $w<=200 and $h<=200 and $l>=15 and $w>=11 and $h>=1 and ($l + 2 * ($w + $h))<=360){
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
			}elseif(($l>=120 or $w>=60 or $h>=60) and ($l<=200 or $w<=200 or $h<=200) and ($l + 2 * ($w + $h))<=360){
				$fee = $fee+8.8;
			}
		}
		return $fee;
	}

	private function getWinitUsCost($data){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_EURTORMB'));
		$c = ($data[C('DB_PRODUCT_PRICE')]+0.5)/$exchange+($data[C('DB_PRODUCT_PRICE')]*1.2/$exchange)*$data[C('DB_PRODUCT_DETARIFF')]+$data['winit-fee']+$data['way-to-de-fee']+$data['local-shipping-fee']+$data[C('DB_PRODUCT_RC_WINIT_DE_SALE_PRICE')]*0.144+0.35;
		return $c;
	}
}

?>