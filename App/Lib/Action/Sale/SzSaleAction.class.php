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