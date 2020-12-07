<?php

class GgsUsswSaleAction extends CommonAction{

	public function index($account){
		if($_POST['keyword']==""){
			$this->getUsswSaleInfo($account);
        }
        else{

            $this->getUsswKeywordSaleInfo($account);
        }
	}

	public function usswSaleSuggest($account,$kw=null,$kwv=null){
		if($_POST['keyword']=="" && $kwv==null){
            $Data = D($this->getSalePlanViewModel($account));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $suggest = $Data->order(C('DB_USSW_SALE_PLAN_SKU'))->limit($Page->firstRow.','.$Page->listRows)->select();
            foreach ($suggest as $key => $value) {
	        	$suggest[$key]['profit'] = round(($value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')]),2);
	        	$suggest[$key]['grate'] = round(($value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')]) / $value[C('DB_USSW_SALE_PLAN_PRICE')]*100,2);
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
	        	$suggest[$key]['profit'] = $value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')];
	        	$suggest[$key]['grate'] = round(($value[C('DB_USSW_SALE_PLAN_PRICE')] - $value[C('DB_USSW_SALE_PLAN_COST')]) / $value[C('DB_USSW_SALE_PLAN_PRICE')]*100,2);
	        }
	        $this->assign('suggest',$suggest);
            $this->assign('keyword',$keyword);
            $this->assign('keywordValue',$keywordValue);
            $this->assign('keyword2',$keyword2);
        }
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->display();
	}


	public function calUsswSaleInfo($account){
		$this->calUsswSaleInfoHandle($account);
		/*if($this->getMarketByAccount($account)=='amazon'){
			$this->calFBASaleInfoHandle($account);
		}	*/	
		$this->redirect('usswSaleSuggest',array('account'=>$account));
	}

	private function calUsswSaleInfoHandle($account){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();		
		$ormap[C('DB_USSTORAGE_AINVENTORY')] = array('gt',0);
		$ormap[C('DB_USSTORAGE_IINVENTORY')] = array('gt',0);
		$ormap['_logic'] = 'or';
		$map['_complex'] = $ormap;
		$map[C('DB_PRODUCT_TOUS')] = array('neq','no');
		$usswProduct = M(C('DB_USSTORAGE'))->alias('usst')->join('lipovolt_3s_'.C('DB_PRODUCT').' p'.' ON '.'usst.sku=p.sku')->where($map)->distinct(true)->field('usst.'.C('DB_USSTORAGE_SKU'))->select();
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error("无法找到匹配的销售表！");
		}
		foreach ($usswProduct as $key => $p) {
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			if($usp == null){
				$this->addProductToUsp($account,$p[C('DB_USSTORAGE_SKU')]);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}else{
				$usp[C('DB_USSW_SALE_PLAN_COST')]=$this->calUsswSuggestCost($account,$p[C('DB_USSTORAGE_SKU')],$usp[C('DB_USSW_SALE_PLAN_PRICE')]);
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($p[C('DB_USSTORAGE_SKU')])){
				//产品信息不全，建议完善产品信息
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}elseif(!$this->isUsswSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_SALE_INFO');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlanTable->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calUsswSuggest($account,$p[C('DB_USSTORAGE_SKU')]);
					$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_USSW_SALE_PLAN_SUGGEST')];
					$this->updateUsp($account,$usp);
				}
			}
			$usp=null;
		}
	}

	private function calFBASaleInfoHandle($account){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$fbaProduct = M(C('DB_AMAZON_US_STORAGE'))->distinct(true)->field(C('DB_USSTORAGE_SKU'))->select();
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error("无法找到匹配的销售表！");
		}
		foreach ($fbaProduct as $key => $p) {
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			if($usp == null){
				$this->addFBAToUsp($account,$p[C('DB_USSTORAGE_SKU')]);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}else{

				$usp[C('DB_USSW_SALE_PLAN_COST')]=$this->calFBASuggestCost($account,$p[C('DB_USSTORAGE_SKU')],$usp[C('DB_USSW_SALE_PLAN_PRICE')]);
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}
			if(!$this->isProductInfoComplete($this->fbaSkuToStandardSku($p[C('DB_USSTORAGE_SKU')]))){
				//产品信息不全，建议完善产品信息
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}elseif(!$this->isUsswSaleInfoComplete($usp)){
				//无法计算，建议完善销售信息
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_SALE_INFO');
				$this->updateUsp($account,$usp);
				$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$p[C('DB_USSTORAGE_SKU')]))->find();
			}else{
				$lastModifyDate = $salePlanTable->where(array('sku'=>$p['sku']))->getField('last_modify_date');
				$adjustPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
				if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
					//开始计算该产品的销售建议
					$suggest=null;
					$suggest = $this->calFBASuggest($account,$p[C('DB_USSTORAGE_SKU')]);
					$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
					$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_USSW_SALE_PLAN_SUGGEST')];
					$this->updateUsp($account,$usp);
				}
			}
			$usp=null;
		}
	}

	private function calUsswSaleInfoHandleSingle($account, $id){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error("无法找到匹配的销售表！");
		}
		$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->find();
		if($usp == null){
			$this->addProductToUsp($account,$usp[C('DB_USSW_SALE_PLAN_SKU')]);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}else{
			$usp[C('DB_USSW_SALE_PLAN_COST')]=$this->calUsswSuggestCost($account,$usp[C('DB_USSW_SALE_PLAN_SKU')],$usp[C('DB_USSW_SALE_PLAN_PRICE')]);
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}
		if(!$this->isProductInfoComplete($usp[C('DB_USSW_SALE_PLAN_SKU')])){
			//产品信息不全，建议完善产品信息
			$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO');
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}elseif(!$this->isUsswSaleInfoComplete($usp)){
			//无法计算，建议完善销售信息
			$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_SALE_INFO');
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}else{
			$lastModifyDate = $salePlanTable->where(array('sku'=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->getField('last_modify_date');
			$adjustPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
			if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
				//开始计算该产品的销售建议
				$suggest=null;
				$suggest = $this->calUsswSuggest($account,$usp[C('DB_USSW_SALE_PLAN_SKU')]);
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_USSW_SALE_PLAN_SUGGEST')];
				$this->updateUsp($account,$usp);
			}
		}
	}

	private function calFBASaleInfoHandleSingle($account, $id){
		import('ORG.Util.Date');// 导入日期类
		$Date = new Date();
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error("无法找到匹配的销售表！");
		}
		$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->find();
		if($usp == null){
			$this->addFBAToUsp($account,$usp[C('DB_USSW_SALE_PLAN_SKU')]);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}else{
			$usp[C('DB_USSW_SALE_PLAN_COST')]=$this->calFBASuggestCost($account,$usp[C('DB_USSW_SALE_PLAN_SKU')],$usp[C('DB_USSW_SALE_PLAN_PRICE')]);
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}
		if(!$this->isProductInfoComplete($this->fbaSkuToStandardSku($usp[C('DB_USSW_SALE_PLAN_SKU')]))){
			//产品信息不全，建议完善产品信息
			$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_PRODUCT_INFO');
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}elseif(!$this->isUsswSaleInfoComplete($usp)){
			//无法计算，建议完善销售信息
			$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_COMPLETE_SALE_INFO');
			$this->updateUsp($account,$usp);
			$usp = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->find();
		}else{
			$lastModifyDate = $salePlanTable->where(array('sku'=>$usp[C('DB_USSW_SALE_PLAN_SKU')]))->getField('last_modify_date');
			$adjustPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array('id'=>1))->getField('adjust_period');
			if(-($Date->dateDiff($lastModifyDate))>$adjustPeriod){
				//开始计算该产品的销售建议
				$suggest=null;
				$suggest = $this->calFBASuggest($account,$usp[C('DB_USSW_SALE_PLAN_SKU')]);
				$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $suggest[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
				$usp[C('DB_USSW_SALE_PLAN_SUGGEST')] =  $suggest[C('DB_USSW_SALE_PLAN_SUGGEST')];
				$this->updateUsp($account,$usp);
			}
		}
	}

	public function confirmSuggest($account,$id){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$data = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->find();
		if($data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_USSW_SALE_PLAN_SUGGEST')]!=null && $data[C('DB_USSW_SALE_PLAN_SUGGEST')]!=C('USSW_SALE_PLAN_CLEAR')){
			$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			if($data[C('DB_USSW_SALE_PLAN_ID_SUGGEST')]=='重新刊登'){
				$data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')])+1;
			}

			if($data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]==null){
				$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}else{
				$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}

			$data[C('DB_USSW_SALE_PLAN_PRICE')] = $data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];

			$kpiSaleRecorde[C('DB_KPI_SALE_NAME')] = $_SESSION['username'];
			$kpiSaleRecorde[C('DB_KPI_SALE_SKU')] = $data[C('DB_USSW_SALE_PLAN_SKU')];
			$kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')] = C('USSW');
			$kpiSaleRecorde[C('DB_KPI_SALE_TYPE')] = $data[C('DB_USSW_SALE_PLAN_SUGGEST')];
			$kpiSaleRecorde[C('DB_KPI_SALE_BEGIN_DATE')] = time();
			$kpiSaleRecorde[C('DB_KPI_SALE_BEGIN_SQUANTITY')] = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$data[C('DB_USSW_SALE_PLAN_SKU')]))->sum(C('DB_USSTORAGE_AINVENTORY'));
			$map[C('DB_KPI_SALE_SKU')] = array('eq', $kpiSaleRecorde[C('DB_KPI_SALE_SKU')]);
			$map[C('DB_KPI_SALE_WAREHOUSE')] = array('eq', $kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')]);

			if(M(C('DB_KPI_SALE'))->where($map)->getField(C('DB_KPI_SALE_ID'))!=null){
				$this->error('仓库： '.$kpiSaleRecorde[C('DB_KPI_SALE_WAREHOUSE')] .' 里的该产品编码：'.$kpiSaleRecorde[C('DB_KPI_SALE_SKU')].' 已经在绩效考核表里，如需重新开始绩效考核请先把重复的记录从绩效考核表里删除。');
			}else{
				$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
				$salePlanTable->save($data);
				M(C('DB_KPI_SALE'))->add($kpiSaleRecorde);
			}
		}else{
			$this->error('无法保存，当前产品没有销售建议');
		}
		$this->success('保存成功');
	}

	public function bIgnoreHandle($account,$kw=null,$kwv=null){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->startTrans();
		foreach ($_POST['cb'] as $key => $value) {
			if($salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$value))->getField(C('DB_USSW_SALE_PLAN_SUGGEST'))!=C('USSW_SALE_PLAN_CLEAR')){
				$data[C('DB_USSW_SALE_PLAN_ID')] = $value;
				$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
				$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
				$salePlanTable->save($data);
			}			
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
			$data = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$value))->find();
			if($data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')]!=null && $data[C('DB_USSW_SALE_PLAN_SUGGEST')]!=null && $data[C('DB_USSW_SALE_PLAN_SUGGEST')]!=C('USSW_SALE_PLAN_CLEAR')){
				$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
				if($data[C('DB_USSW_SALE_PLAN_ID_SUGGEST')]=='relisting'){
					$data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = intval($data[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')])+1;
				}

				if($data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]==null){
					$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('ymd',time());
				}else{
					$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')].' | '.$data[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
				}

				$data[C('DB_USSW_SALE_PLAN_PRICE')] = $data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
				$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
				$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
				$salePlanTable->save($data);
			}
		}
		$salePlanTable->commit();
		$this->success('修改成功');
	}

	public function ignoreSuggest($account,$id){
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		if($salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->getField(C('DB_USSW_SALE_PLAN_SUGGEST')) != C('USSW_SALE_PLAN_CLEAR')){
			$data[C('DB_USSW_SALE_PLAN_ID')] = $id;
			$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
			$salePlanTable->save($data);
			$this->success('保存成功');
		}else{
			$this->error('清货建议不能忽略');
		}		
	}

	public function updateUsswSalePlan($account, $kw=null,$kwv=null){
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
			$value[C('DB_USSW_SALE_PLAN_COST')] = $this->calUsswSuggestCost($account,$value['sku'],$value['sale_price']);
			if($value['status']=="on"){
				$value['status']=1;
			}else{
				$value['status']=0;
			}
			$salePlanTable->save($value);
		}
		$salePlanTable->commit();
		$this->calUsswSaleInfoHandle($account);
		if($kwv==null){
			$this->success('保存成功');
		}else{
			$this->success('修改已保存',U('usswSaleSuggest',array('account'=>$account,'kw'=>$kw,'kwv'=>$kwv)));
		}		
	}


	public function updateUsswSalePlanSingle($account, $kw=null,$kwv=null, $id, $salePrice, $status,$sale_status){
		$data=null;
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$data[C('DB_USSW_SALE_PLAN_ID')]=$id; 
		$data[C('DB_USSW_SALE_PLAN_PRICE')]=$salePrice; 
		$data[C('DB_USSW_SALE_PLAN_STATUS')]=$status;
		$data[C('DB_USSW_SALE_PLAN_SALE_STATUS')]=$sale_status; 
		$salePlanTable->startTrans();
		$salePlanTable->save($data);
		$salePlanTable->commit();
		if($this->isFBASku($salePlanTable->where(array(C('DB_USSW_SALE_PLAN_ID')=>$id))->getField(C('DB_USSW_SALE_PLAN_SKU')))){
			$this->calFBASaleInfoHandleSingle($account, $id);
		}else{
			
			$this->calUsswSaleInfoHandleSingle($account, $id);
		}
		if($kwv==null){
			$this->success('保存成功');
		}else{
			$this->success('修改已保存',U('usswSaleSuggest',array('account'=>$account,'kw'=>$kw,'kwv'=>$kwv)));
		}		
	}

	public function deleteSingelSuggest($account, $kw=null,$kwv=null, $id){
		$data=null;
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->delete($id);
		if($kwv==null){
			$this->success('保存成功');
		}else{
			$this->success('修改已保存',U('usswSaleSuggest',array('account'=>$account,'kw'=>$kw,'kwv'=>$kwv)));
		}		
	}

	private function calUsswSuggest($account,$sku){
		//返回数组包含销售建议和价格
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$saleplan = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->find();
		$cost = $saleplan[C('DB_USSW_SALE_PLAN_COST')];
		$price = $saleplan[C('DB_USSW_SALE_PLAN_PRICE')];
		$status = $saleplan[C('DB_USSW_SALE_PLAN_STATUS')];

		if($status==0){
			//item needn't to calculate.
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
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
		$ainventory = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->getField(C('DB_USSTORAGE_AINVENTORY'));

		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$asqsq = intval($this->calUsswSaleQuantity($account,$sku,$startDate))*$standard_period/$adjust_period;
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$lspsq = $this->calUsswSaleQuantity($account,$sku,$startDate,$endDate)*$standard_period/$adjust_period;

		/*//检查是否需要清货
		if($asqsq==0){
			$firstShippingDate = M(C('DB_USSW_SALE_PLAN'))->where(array('sku'=>$sku))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));			
			if(strtotime($firstShippingDate)<(time()-60*60*24*$clear_nod)){
				$startDate = date('Y-m-d H:i:s',time()-60*60*24*$clear_nod);
				$clearNodSaleQuantity = $this->calUsswSaleQuantity(null,$sku,$startDate);
				if($clearNodSaleQuantity==0){
					$sugg=null;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_CLEAR');
					return $sugg;
				}
			}			
		}

		//检查是否需要重新刊登
		if($asqsq==0){
			$firstShippingDate = M(C('DB_USSW_SALE_PLAN'))->where(array('sku'=>$sku))->getField(C('DB_USSW_SALE_PLAN_FIRST_DATE'));			
			if(strtotime($firstShippingDate)<(time()-60*60*24*$relisting_nod)){
				$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
				$relistingNodSaleQuantity = $this->calUsswSaleQuantity($account,$sku,$startDate);
				if($relistingNodSaleQuantity==0){
					$sugg=null;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost+$cost*$this->getCostClass($cost)/100;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_RELISTING');
					return $sugg;
				}
			}
		}*/


		//检查是否需要调价
		$diff = $asqsq-$lspsq;
		if($lspsq<$sqnr){
			$lspsq = $denominator;
		}
		if(($diff/$lspsq)>($grfr/100) && ($cost/$price)<C('DB_SZ_SALE_PLAN_METADATA_PROFIT_LIMIT')){
			$sugg=null;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price+$price*($pcr/100);
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_UP');
			return $sugg;
		}
		if($ainventory>0 && ($asqsq==0 || ($diff/$lspsq)<-($grfr/100))){
			$sugg=null;
			if($price-$price*($pcr/100)<$cost){
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
			}else{
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price-$price*($pcr/100);
			}			
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_DOWN');
			return $sugg;
		}
	}
	
	private function calFBASuggest($account,$sku){
		//返回数组包含销售建议和价格
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$saleplan = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->find();
		$cost = $saleplan[C('DB_USSW_SALE_PLAN_COST')];
		$price = $saleplan[C('DB_USSW_SALE_PLAN_PRICE')];
		$status = $saleplan[C('DB_USSW_SALE_PLAN_STATUS')];

		if($status==0){
			//item needn't to calculate.
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
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
		$ainventory = M(C('DB_AMAZON_US_STORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->getField(C('DB_USSTORAGE_AINVENTORY'));

		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$asqsq = intval($this->calUsFBASaleQuantity($account,$sku,$startDate))*intval($standard_period)/intval($adjust_period);
		$startDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period*2);
		$endDate = date('Y-m-d H:i:s',time()-60*60*24*$adjust_period);
		$lspsq = $this->calUsFBASaleQuantity($account,$sku,$startDate,$endDate)*$standard_period/$adjust_period;
		//检查是否需要清货
		if($asqsq==0){
			$firstShippingDate = M(C('DB_AMAZON_US_STORAGE'))->where(array('sku'=>$sku))->getField(C('DB_AMAZON_US_STORAGE_LASTTIME'));			
			if(strtotime($firstShippingDate)<(time()-60*60*24*$clear_nod)){
				$startDate = date('Y-m-d H:i:s',time()-60*60*24*$clear_nod);
				$clearNodSaleQuantity = $this->calUsFBASaleQuantity(null,$sku,$startDate);
				if($clearNodSaleQuantity==0){
					$sugg=null;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_CLEAR');
					return $sugg;
				}
			}			
		}

		//检查是否需要重新刊登
		if($asqsq==0){
			$firstShippingDate = M(C('DB_AMAZON_US_STORAGE'))->where(array('sku'=>$sku))->getField(C('DB_AMAZON_US_STORAGE_LASTTIME'));			
			if(strtotime($firstShippingDate)<(time()-60*60*24*$relisting_nod)){
				$startDate = date('Y-m-d H:i:s',time()-60*60*24*$relisting_nod);
				$relistingNodSaleQuantity = $this->calUsFBASaleQuantity($account,$sku,$startDate);
				if($relistingNodSaleQuantity==0){
					$sugg=null;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost+$cost*$this->getCostClass($cost)/100;
					$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_RELISTING');
					return $sugg;
				}
			}
		}
		//检查是否需要调价
		$diff = $asqsq-$lspsq;
		if($lspsq<$sqnr){
			$lspsq = $denominator;
		}
		if($diff/$lspsq>$grfr/100){
			$sugg=null;
			$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price+$price*($pcr/100);
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_UP');
			return $sugg;
		}
		if($ainventory>0 && $diff/$lspsq<-($grfr/100)){
			$sugg=null;
			if($price-$price*($pcr/100)<$cost){
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $cost;
			}else{
				$sugg[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = $price-$price*($pcr/100);
			}			
			$sugg[C('DB_USSW_SALE_PLAN_SUGGEST')] = C('USSW_SALE_PLAN_PRICE_DOWN');
			return $sugg;
		}
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

	public function calUsswSaleQuantity($account, $sku, $startDate, $endDate=null){
		if($endDate==null){
			$endDate = date('Y-m-d H:i:s',time());
		}
		if($account==null){
			$usswOutboundItem = D("UsswOutboundView");
			$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
			$map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
			$map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('eq',$account);
			return $usswOutboundItem->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
		}else{
			$usswOutboundItem = D("UsswOutboundView");
			$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
			$map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
			$map[C('DB_USSW_OUTBOUND_SELLER_ID')] = array('eq',$account);
			return $usswOutboundItem->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
		}
	}

	public function calUsFBASaleQuantity($account, $sku, $startDate, $endDate=null){
		if($endDate==null){
			$endDate = date('Y-m-d H:i:s',time());
		}
		$usFBAOutboundItem = D("UsFBAOutboundView");
		$map[C('DB_USSW_OUTBOUND_CREATE_TIME')] = array('between',array($startDate,$endDate));
		$map[C('DB_USSW_OUTBOUND_ITEM_SKU')] = array('eq',$sku);
		return $usFBAOutboundItem->where($map)->sum(C('DB_USSW_OUTBOUND_ITEM_QUANTITY'));
	}

	private function addProductToUsp($account,$sku){
		//添加产品到ussw_sale_plan表
		$newUsp[C('DB_USSW_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_USSW_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =null;
		$price =  M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->getField(C('DB_PRODUCT_GGS_USSW_SALE_PRICE'));
		if($price==null || $price==0){
			$price = $newUsp[C('DB_USSW_SALE_PLAN_COST')];
		}
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE')] = $this->calUsswInitialPrice($account,$sku);		
		$newUsp[C('DB_USSW_SALE_PLAN_COST')] = $this->calUsswSuggestCost($account,$sku,$newUsp[C('DB_USSW_SALE_PLAN_PRICE')]);
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_STATUS')] = 1;

		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->add($newUsp);
	}

	private function addFBAToUsp($account,$sku){
		//添加产品到ussw_sale_plan表
		$newUsp[C('DB_USSW_SALE_PLAN_SKU')] = $sku;
		$newUsp[C('DB_USSW_SALE_PLAN_FIRST_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time()); 
		$newUsp[C('DB_USSW_SALE_PLAN_RELISTING_TIMES')] = 0; 
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =null;
		$price =  M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$this->fbaSkuToStandardSku($sku)))->getField(C('DB_PRODUCT_GGS_USSW_SALE_PRICE'));
		if($price==null || $price==0){
			$price = $newUsp[C('DB_USSW_SALE_PLAN_COST')];
		}
		$newUsp[C('DB_USSW_SALE_PLAN_PRICE')] = $this->calFBAInitialPrice($account,$sku);		
		$newUsp[C('DB_USSW_SALE_PLAN_COST')] = $this->calFBASuggestCost($account,$sku,$newUsp[C('DB_USSW_SALE_PLAN_PRICE')]);
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
		$newUsp[C('DB_USSW_SALE_PLAN_STATUS')] = 1;

		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$salePlanTable->add($newUsp);
	}

	private function inUsstorage($sku){
		$result = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->find();
		if(false !== $result && null !== $result){
            return true;
        }else{
            return false;
        }
	}

	private function updateUsp($account,$usp){
		//更新产品自建仓销售建议
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		if($usp[C('DB_USSW_SALE_PLAN_SUGGEST')]==C('USSW_SALE_PLAN_PRICE_UP') || $usp[C('DB_USSW_SALE_PLAN_SUGGEST')]==C('USSW_SALE_PLAN_PRICE_DOWN')){
			$usp[C('DB_USSW_SALE_PLAN_PRICE')] = $usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
			$usp[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
			$usp[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')]=null;
			$usp[C('DB_USSW_SALE_PLAN_SUGGEST')]=null;
			if($usp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]==null){
				$usp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('ymd',time());
			}else{
				$usp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $usp[C('DB_USSW_SALE_PLAN_PRICE_NOTE')].' | '.$usp[C('DB_USSW_SALE_PLAN_PRICE')].' '.date('Y-m-d',time());
			}
		}
		$salePlanTable->save($usp);
	}

	private function calUsswSuggestCost($account,$sku,$sale_price=null){
		//计算产品美自建仓销售成本
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    	$data['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
		
		$salePlan = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->find();
		if($sale_price!=null){
			if($this->getMarketByAccount($account)=='ebay'){
				return $this->getUsswEbayCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$sale_price);
			}
			if($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
				return $this->getUsswAmazonCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$sale_price);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');
		}elseif($salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=0 && $salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=null && $salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=''){
			if($this->getMarketByAccount($account)=='ebay'){
				return $this->getUsswEbayCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$salePlan[C('DB_USSW_SALE_PLAN_PRICE')]);
			}
			if($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
				return $this->getUsswAmazonCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$salePlan[C('DB_USSW_SALE_PLAN_PRICE')]);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');
		}else{
			if($this->getMarketByAccount($account)=='ebay'){
				$tmpSalePrice = $this->getUsswEbayISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1']);
				return $this->getUsswEbayCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$salePlan[C('DB_USSW_SALE_PLAN_PRICE')]);
			}
			if($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
				$tmpSalePrice = $this->getUsswAmazonISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1']);
				return $this->getUsswAmazonCost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1'],$tmpSalePrice);
			}
			$this->error('无法找到与 '.$account.' 匹配的平台！不能计算销售建议表成本！');			
		}
	}

	private function calUsswInitialPrice($account,$sku){
		//计算产品美自建仓初始售价
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    	$data['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product['length'],$product['width'],$product['height']);

    	if($this->getMarketByAccount($account)=='ebay'){
			return $this->getUsswEbayISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1']);
		}
		if($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
			return $this->getUsswAmazonISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['local-shipping-fee1']);
		}
		$this->error('无法找到与 '.$account.' 匹配的平台！不能计算初始售价！');
	}

	private function calFBASuggestCost($account,$sku,$sale_price=null){
		//计算产品FBA仓销售成本
		$salePlanTable = M($this->getSalePlanTableName($account));
		if($salePlanTable==null){
			$this->error('无法找到匹配的销售表！');
		}
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$this->fbaSkuToStandardSku($sku)))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    	$data['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['local-shipping-fee1']=$this->getFBAShippingWayFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')])[1];
    	$data['transportToFbaFee'] = 0.8*$product[C('DB_PRODUCT_PWEIGHT')]/1000;
		
		$salePlan = $salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->find();
		if($sale_price!=null){
			if($this->getMarketByAccount($account)=='amazon'){
				return $this->getFBACost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['transportToFbaFee'],$data['local-shipping-fee1'],$sale_price);
			}
			$this->error($account.' 不是FBA账号，不能计算销售建议表成本！');
		}elseif($salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=0 && $salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=null && $salePlan[C('DB_USSW_SALE_PLAN_PRICE')]!=''){
			if($this->getMarketByAccount($account)=='amazon'){
				return $this->getFBACost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['transportToFbaFee'],$data['local-shipping-fee1'],$salePlan[C('DB_USSW_SALE_PLAN_PRICE')]);
			}
			$this->error($account.' 不是FBA账号，不能计算销售建议表成本！');
		}else{
			if($this->getMarketByAccount($account)=='amazon'){
				$tmpSalePrice = $this->getFBAISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['transportToFbaFee'],$data['local-shipping-fee1']);
				return $this->getFBACost($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['transportToFbaFee'],$data['local-shipping-fee1'],$tmpSalePrice);
			}
			$this->error($account.' 不是FBA账号，不能计算销售建议表成本！');		
		}
	}

	private function calFBAInitialPrice($account,$sku){
		//计算产品FBA仓初始售价
		$product = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$this->fbaSkuToStandardSku($sku)))->find();
    	$data[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
    	$data[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    	$data['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
    	$data['local-shipping-fee1']=$this->getFBAShippingWayFee($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product['length'],$product['width'],$product['height'])[1];
    	$data['transportToFbaFee'] = 0.5*$product[C('DB_PRODUCT_PWEIGHT')]/1000;
		if($this->getMarketByAccount($account)=='amazon'){
			return $this->getFBAISP($data[C('DB_PRODUCT_PRICE')],$data[C('DB_PRODUCT_USTARIFF')],$data['ussw-fee'],$data['way-to-us-fee'],$data['transportToFbaFee'],$data['local-shipping-fee1']);
		}
		$this->error($account.' 不是FBA账号，不能计算初始售价！');
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

	public function ggsUsswItemTest(){
		if($this->isPost()){
			$p = I('post.price','','htmlspecialchars');
			$usRate = $p*1.2*0.05;
			$usswFee = $this->calUsswSIOFee(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$wayToUs = I('post.way-to-us','','htmlspecialchars');
			$wayToUsFee = $wayToUs=="air"?$this->getUsswAirFirstTransportFee(I('post.weight','','htmlspecialchars'),1,1,1):$this->getUsswSeaFirstTransportFee(I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$localShippingWay = $this->getUsswLocalShippingWay1(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$localShippingFee = $this->getUsswLocalShippingFee1(I('post.weight','','htmlspecialchars'),I('post.length','','htmlspecialchars'),I('post.width','','htmlspecialchars'),I('post.height','','htmlspecialchars'));
			$salePrice = I('post.saleprice','','htmlspecialchars');
			$testCost = $this->getUsswEbayCost($p,0.05,$usswFee,$wayToUsFee,$localShippingFee,$salePrice);
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

	private function getUsswSaleInfo($account){
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
        	$data[$key][C('DB_PRODUCT_USTARIFF')]=$value[C('DB_PRODUCT_USTARIFF')]/100;
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$value[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way1']=$this->getUsswLocalShippingWay1($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee1']=$this->getUsswLocalShippingFee1($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way2']=$this->getUsswLocalShippingWay2($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee2']=$this->getUsswLocalShippingFee2($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way3']=$this->getUsswLocalShippingWay3($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee3']=$this->getUsswLocalShippingFee3($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_PRICE'));
        	if($this->getMarketByAccount($account)=='ebay'){
        		$data[$key]['cost']=$this->getUsswEbayCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_USTARIFF')],$data[$key]['ussw-fee'],$data[$key]['way-to-us-fee'],$data[$key]['local-shipping-fee1'],$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]);
        	}
        	elseif($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
        		$data[$key]['cost']=$this->getUsswAmazonCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_USTARIFF')],$data[$key]['ussw-fee'],$data[$key]['way-to-us-fee'],$data[$key]['local-shipping-fee1'],$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]);
        	}else{
        		$this->error('无法找到与 '.$account.' 匹配的平台！不能显示销售表！');
        	}
        	
        	$data[$key]['gprofit']=$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_PWEIGHT')]*0.0352740,2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')]*0.3937008,2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')]*0.3937008,2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')]*0.3937008,2);
        	$data[$key]['sale_status']=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_SALE_STATUS'));
        	$data[$key]['upc']=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_UPC'));
        }
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	private function getUsswKeywordSaleInfo($account){
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
        	$data[$key][C('DB_PRODUCT_USTARIFF')]=$value[C('DB_PRODUCT_USTARIFF')]/100;
        	$data[$key]['ussw-fee']=$this->calUsswSIOFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key][C('DB_PRODUCT_TOUS')]=$value[C('DB_PRODUCT_TOUS')];
        	$data[$key]['way-to-us-fee']=$data[$key][C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($value[C('DB_PRODUCT_WEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-way1']=$this->getUsswLocalShippingWay1($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee1']=$this->getUsswLocalShippingFee1($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way2']=$this->getUsswLocalShippingWay2($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee2']=$this->getUsswLocalShippingFee2($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key]['local-shipping-way3']=$this->getUsswLocalShippingWay3($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value[C('DB_PRODUCT_LENGTH')],$value[C('DB_PRODUCT_WIDTH')],$value[C('DB_PRODUCT_HEIGHT')]);
        	$data[$key]['local-shipping-fee3']=$this->getUsswLocalShippingFee3($value[C('DB_PRODUCT_PWEIGHT')]==0?$value[C('DB_PRODUCT_WEIGHT')]:$value[C('DB_PRODUCT_PWEIGHT')],$value['length'],$value['width'],$value['height']);
        	$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_PRICE'));
        	if($this->getMarketByAccount($account)=='ebay'){
        		$data[$key]['cost']=$this->getUsswEbayCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_USTARIFF')],$data[$key]['ussw-fee'],$data[$key]['way-to-us-fee'],$data[$key]['local-shipping-fee1'],$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]);
        	}
        	elseif($this->getMarketByAccount($account)=='amazon' || $this->getMarketByAccount($account)=='groupon'){
        		$data[$key]['cost']=$this->getUsswAmazonCost($data[$key][C('DB_PRODUCT_PRICE')],$data[$key][C('DB_PRODUCT_USTARIFF')],$data[$key]['ussw-fee'],$data[$key]['way-to-us-fee'],$data[$key]['local-shipping-fee1'],$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]);
        	}else{
        		$this->error('无法找到与 '.$account.' 匹配的平台！不能显示销售表！');
        	}
        	$data[$key]['gprofit']=$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]-$data[$key]['cost'];
        	$data[$key]['grate']=round($data[$key]['gprofit']/$data[$key][C('DB_USSW_SALE_PLAN_PRICE')]*100,2).'%';
        	$data[$key]['weight']=round($value[C('DB_PRODUCT_PWEIGHT')]*0.0352740,2);
        	$data[$key]['length']=round($value[C('DB_PRODUCT_LENGTH')]*0.3937008,2);
        	$data[$key]['width']=round($value[C('DB_PRODUCT_WIDTH')]*0.3937008,2);
        	$data[$key]['height']=round($value[C('DB_PRODUCT_HEIGHT')]*0.3937008,2);
        	$data[$key]['sale_status']=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_SALE_STATUS'));
        	$data[$key]['upc']=$salePlanTable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$value[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSW_SALE_PLAN_UPC'));
        }
        $this->assign('market',$this->getMarketByAccount($account));
        $this->assign('account',$account);
        $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
        $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
        $this->assign('data',$data);
        $this->assign('page',$show);
        $this->display();
	}

	//calculate ebay cost according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee) $sPrice(sale price)
	private function getUsswEbayCost($pPrice,$tariff,$wFee,$tFee,$sFee,$sPrice){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		return round((($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$sFee+$sPrice*0.129+0.3),2);
	}

	//calculate amazon cost according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee) $sPrice(sale price)
	private function getUsswAmazonCost($pPrice,$tariff,$wFee,$tFee,$sFee,$sPrice){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$aFee = $sPrice*0.15<1?1:$sPrice*0.15;
		return round((($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$sFee+$aFee),2);
	}

	//calculate ebay initial sale price according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee)
	private function getUsswEbayISP($pPrice,$tariff,$wFee,$tFee,$sFee){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$sFee;
		return abs(round(($cost+0.3)/(1-0.129-$this->getCostClass($cost)/100),2));
	}

	//calculate amazon initial sale price according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee)
	private function getUsswAmazonISP($pPrice,$tariff,$wFee,$tFee,$sFee,$profitPercent=null){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$sFee;
		if($profitPercent==null){
			return abs(round($cost/(1-0.15-$this->getCostClass($cost)/100),2));
		}else{
			return abs(round($cost/(1-0.15-$profitPercent/100),2));
		}
	}

	//calculate FBA cost according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee) $sPrice(sale price)
	private function getFBACost($pPrice,$tariff,$wFee,$tFee,$tfFee,$sFee,$sPrice){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$aFee = $sPrice*0.15<1?1:$sPrice*0.15;
		return round((($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$tfFee+$sFee+$aFee),2);
	}

	//calculate FBA initial sale price according to the $pPrice(purchase price),$tariff(us tariff),$wFee(warehouse storage input output fee) $tFee(transport fee from china to usa) $sFee(usa domectic shipping fee)
	private function getFBAISP($pPrice,$tariff,$wFee,$tFee,$tfFee,$sFee,$profitPercent=null){
		$exchange = M(C('DB_METADATA'))->where(C('DB_METADATA_ID'))->getField(C('DB_METADATA_USDTORMB'));
		$cost = ($pPrice+0.5)/$exchange+($pPrice*1.2/$exchange)*$tariff+$wFee+$tFee+$tfFee+$sFee;
		if($profitPercent==null){
			return abs(round($cost/(1-0.15-$this->getCostClass($cost)/100),2));
		}else{
			return abs(round($cost/(1-0.15-$profitPercent/100),2));
		}
	}

	private function calUsswSIOFee($weight,$l,$w,$h){
		//月仓储费=立方米*每日每立方租金*30天
		$monthlyStorageFee = ($l*$w*$h)/1000000*1.2*30;
		$itemInOutFee = 0;
		if($weight>0 And $weight <= 500){
			$itemInOutFee = 0.12 + 0.18 + 0.05;
		}
		elseif($weight>500 and $weight <= 1000){
			$itemInOutFee = 0.12 + 0.25 + 0.06;
		}
		elseif($weight>1000 and $weight <= 2000){
			$itemInOutFee = 0.12 + 0.51 + 0.09;
		}
		elseif($weight>2000 and $weight <= 10000){
			$itemInOutFee = 0.15 + 0.65 + 0.18;
		}
		elseif($weight>10000 and $weight <= 20000){
			$itemInOutFee = 0.2 + 1.37 + 0.27;
		}
		elseif($weight>20000 and $weight <= 30000){
			$itemInOutFee = 0.35 + 1.82 + 0.36;
		}
		elseif((1.82 + (round(($weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (round(($weight - 30000) / 10000) + 1) * 0.18) < (18.2 + 1.8) ){
			$itemInOutFee = 1.82 + (round(($weight - 30000) / 10000) + 1) * 0.91 + 0.36 + (round(($weight - 30000) / 10000) + 1) * (0.18+0.12);
		}
		else{
			$itemInOutFee = 18.2 + 1.8;
		}
		return round($monthlyStorageFee+$itemInOutFee,2);
	}

	private function getUsswAirFirstTransportFee($weight,$l,$w,$h){
		return round($weight / 1000 * 7.5,2);	
	}

	private function getUsswSeaFirstTransportFee($l,$w,$h){
		return round(($l * $w * $h) / 1000000 * 220,2);
	}

	private function getUsswLocalShippingWay1($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'USPS First Class Mail',
				2=>'USPS Priority Mail Flat Rate Envelope',
				3=>'USPS Priority Mail Small Flat Rate Box',
				4=>'USPS Priority Mail Medium Flat Rate Box',
				5=>'USPS Priority Mail Large Flat Rate Box',
				6=>'USPS Priority Mail Package',
				7=>'Fedex Smart Post',
				8=>'Fedex Home Delivery',
				9=>'USPS Priority Regional Box A'
			);
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPriorityFlatRateEnvelopeFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				6=>$this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h),
				7=>$this->calUsswFedexSmartPostFee($weight,$l,$w,$h),
				8=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h),
				9=>$this->calUsswUspsPriorityRegionalBoxAFee($weight,$l,$w,$h)
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

	private function getUsswLocalShippingFee1($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsFirstClassFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPriorityFlatRateEnvelopeFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				6=>$this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h),
				7=>$this->calUsswFedexSmartPostFee($weight,$l,$w,$h),
				8=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h),
				9=>$this->calUsswUspsPriorityRegionalBoxAFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 8; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getUsswLocalShippingWay2($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'Priority Mail Flat Rate Envelope',
				2=>'Priority Mail Small Flat Rate Box',
				3=>'Priority Mail Medium Flat Rate Box',
				4=>'Priority Mail Large Flat Rate Box',
				5=>'Priority Mail Package',
				6=>'Fedex Smart Post',
				7=>'Fedex Home Delivery',
				8=>'Priority Regional Box A'
			);
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsPriorityFlatRateEnvelopeFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h),
				6=>$this->calUsswFedexSmartPostFee($weight,$l,$w,$h),
				7=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h),
				8=>$this->calUsswUspsPriorityRegionalBoxAFee($weight,$l,$w,$h)
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

	private function getUsswLocalShippingFee2($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsPriorityFlatRateEnvelopeFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h),
				4=>$this->calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h),
				5=>$this->calUsswUspsPriorityPackageFee($weight,$l,$w,$h),
				6=>$this->calUsswFedexSmartPostFee($weight,$l,$w,$h),
				7=>$this->calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h),
				8=>$this->calUsswUspsPriorityRegionalBoxAFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 8; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	private function getUsswLocalShippingWay3($weight,$l,$w,$h){
		$ways=array(
				0=>'No way',
				1=>'Priority Express-Package',
				2=>'Priority Express-Flat Rate Envelope',
				3=>'Priority Express-Legal Flat Rate Envelope'
			);
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsPriorityExpressPackageFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPriorityExpressFlatFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityExpressLegalFee($weight,$l,$w,$h)
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

	private function getUsswLocalShippingFee3($weight,$l,$w,$h){
		$fees=array(
				0=>0,
				1=>$this->calUsswUspsPriorityExpressPackageFee($weight,$l,$w,$h),
				2=>$this->calUsswUspsPriorityExpressFlatFee($weight,$l,$w,$h),
				3=>$this->calUsswUspsPriorityExpressLegalFee($weight,$l,$w,$h)
			);
		$cheapest=65536;
		for ($i=0; $i < 8; $i++) { 
			if(($cheapest > $fees[$i]) And ($fees[$i] != 0)){
				$cheapest = $fees[$i];
			}
		}
		return $cheapest;
	}

	Private function calUsswUspsPriorityExpressPackageFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 31751 and ($l + 2 * ($w + $h)) <= 213){
			if($weight<=226){
				return 33.07;
			}
			elseif($weight>226 && $weight<=453){
				return 38.79;
			}
			elseif($weight>453 && $weight<=906){
				return 42.56;
			}
			elseif($weight>906){
				return 42.46+ceil($weight/453)*6;
			}
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityExpressFlatFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 31751 and ((($l+$h/2)<=24 and ($w + $h) <= 31) or (($l+$h/2)<=31 and ($w + $h) <= 24))){
			return 22.95;
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityExpressLegalFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 31751 and ((($l+$h/2)<=38 and ($w + $h) <= 24) or (($l+$h/2)<=24 and ($w + $h) <= 38))){
			return 22.95;
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsFirstClassFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 453 And ($l + 2 * ($w + $h)) <= 210){
			if($weight<85){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>85))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=85 and $weight<226){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>226))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=226 and $weight<255){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>255))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=255 and $weight<283){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>283))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=283 and $weight<311){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>311))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=311 and $weight<340){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>340))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=340 and $weight<368){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>368))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=368 and $weight<396){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>396))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=396 and $weight<425){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>425))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
			elseif($weight>=425 and $weight<=453){
				return M(C('DB_USSW_POSTAGE_FIRSTCLASS'))->where(array(C('DB_USSW_POSTAGE_FIRSTCLASS_GR')=>453))->getField(C('DB_USSW_POSTAGE_FIRSTCLASS_FEE'));
			}
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityFlatRateEnvelopeFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 31751 and ((($l+$h/2) <= 31 and ($w+$h) <= 24) or (($l+$h/2) <= 24 and ($w+$h) <= 31))){
			return M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->where(array(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')=>4))->getField(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE'));
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityRegionalBoxAFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 31751 and $l <= 25 and $w <= 17 and $h <= 12){
			return M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->where(array(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')=>6))->getField(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE'));
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPrioritySmallFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 31751 and $l <= 21.5 and $w <= 13.3 and $h <= 4){
			return M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->where(array(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')=>1))->getField(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE'));
		}
		else{
			return 0;
		}
	}	

	private function calUsswUspsPriorityMediumFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 31751 and (($l <= 34 and $w <= 29 and $h <= 8) Or ($l <= 27 and $w <= 21 and $h <= 13))){
			return M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->where(array(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')=>2))->getField(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE'));
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityLargeFlatRateBoxFee($weight,$l,$w,$h){
		if ($weight>0 And $weight <= 31751 And $l <= 30 And $w <= 30 And $h <= 15){
			return M(C('DB_USSW_POSTAGE_PRIORITYFLATRATE'))->where(array(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_ID')=>3))->getField(C('DB_USSW_POSTAGE_PRIORITYFLATRATE_FEE'));
		}
		else{
			return 0;
		}
	}

	private function calUsswUspsPriorityPackageFee($weight,$l,$w,$h){
		if($weight>0 And $weight <= 31751 and ($l + 2 * ($w + $h)) <= 274){
			if($weight<453){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>453))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=453 and $weight<907){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>907))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=907 and $weight<1360){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>1360))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=1360 and $weight<1814){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>1814))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=1814 and $weight<2268){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>2268))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=2268 and $weight<2721){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>2721))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=2721 and $weight<3175){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>3175))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=3175 and $weight<3628){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>3628))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=3628 and $weight<4082){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>4082))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=4082 and $weight<4536){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>4536))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=4536 and $weight<=4989){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>4989))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=4989 and $weight<=5443){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>5443))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=5443 and $weight<=5896){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>5896))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=5896 and $weight<=6350){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>6350))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=6350 and $weight<=6804){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>6804))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=6804 and $weight<=7257){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>7257))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=7257 and $weight<=7711){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>7711))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=7711 and $weight<=8164){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>8164))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=8164 and $weight<=8618){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>8618))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=8618 and $weight<=9072){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>9072))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=9072 and $weight<=9525){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>9525))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=9525 and $weight<=9979){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>9979))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=9979 and $weight<=10432){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>10432))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=10432 and $weight<=10886){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>10886))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}
			elseif($weight>=10886 and $weight<=11340){
				return M(C('DB_USSW_POSTAGE_PRIORITY'))->where(array(C('DB_USSW_POSTAGE_PRIORITY_GR')=>11340))->getField(C('DB_USSW_POSTAGE_PRIORITY_FEE'));
			}

		}
		else{
			return 0;
		}
	}

	private function calUsswFedexSmartPostFee($weight,$l,$w,$h){
		if($weight>0 and $weight <= 31751 and ($l + $w + $h) <= 325 and  $l > 16 and $w > 11 and $h > 2.5 ){
			if($weight<453){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>453))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=453 and $weight<907){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>907))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=907 and $weight<1360){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>1360))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=1360 and $weight<1814){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>1814))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=1814 and $weight<2268){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>2268))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=2268 and $weight<2721){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>2721))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=2721 and $weight<3175){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>3175))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=3175 and $weight<3628){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>3628))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=3628 and $weight<4082){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>4082))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=4082 and $weight<4536){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>4536))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=4536 and $weight<=4989){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>4989))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=4989 and $weight<=5443){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>5443))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=5443 and $weight<=5896){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>5896))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=5896 and $weight<=6350){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>6350))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=6350 and $weight<=6804){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>6804))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=6804 and $weight<=7257){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>7257))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=7257 and $weight<=7711){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>7711))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=7711 and $weight<=8164){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>8164))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=8164 and $weight<=8618){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>8618))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=8618 and $weight<=9072){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>9072))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=9072 and $weight<=9525){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>9525))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=9525 and $weight<=9979){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>9979))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=9979 and $weight<=10432){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>10432))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=10432 and $weight<=10886){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>10886))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}
			elseif($weight>=10886 and $weight<=11340){
				return M(C('DB_USSW_POSTAGE_FEDEX_SMART'))->where(array(C('DB_USSW_POSTAGE_FEDEX_SMART_GR')=>11340))->getField(C('DB_USSW_POSTAGE_FEDEX_SMART_FEE'));
			}

		}
		else{
			return 0;
		}
	}

	private function calUsswUspsFedexHomeDeliveryFee($weight,$l,$w,$h){
		if($weight>0 and $weight <= 31751 and ($l + $w + $h) <= 325 and  $l > 16 and $w > 11 and $h > 2.5 ){
			if($weight<453){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>453))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=4536 and $weight<=4989){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>4989))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=4989 and $weight<=5443){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>5443))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=5443 and $weight<=5896){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>5896))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=5896 and $weight<=6350){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>6350))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=6350 and $weight<=6804){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>6804))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=6804 and $weight<=7257){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>7257))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=7257 and $weight<=7711){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>7711))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=7711 and $weight<=8164){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>8164))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=8164 and $weight<=8618){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>8618))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=8618 and $weight<=9072){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>9072))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=9072 and $weight<=9525){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>9525))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=9525 and $weight<=9979){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>9979))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=9979 and $weight<=10432){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>10432))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=10432 and $weight<=10886){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>10886))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}
			elseif($weight>=10886 and $weight<=11340){
				return M(C('DB_USSW_POSTAGE_FEDEX_HOME'))->where(array(C('DB_USSW_POSTAGE_FEDEX_HOME_GR')=>11340))->getField(C('DB_USSW_POSTAGE_FEDEX_HOME_FEE'));
			}

		}
		else{
			return 0;
		}
	}

	private function getUsswFbaTransportFee($weight){
		return $weight*0.5;
	}

	private function getFBAShippingWayFee($weight,$l,$w,$h){
		if($this->isSmallStandardSize($weight,$l,$w,$h)){
			return array('Small Standard', 2.48);
		}elseif($this->isLargeStandardSize($weight,$l,$w,$h) && ($weight*0.0022046)<1){
			return array('Large Standard', 3.28);
		}elseif ($this->isLargeStandardSize($weight,$l,$w,$h) && ($weight*0.0022046)<2) {
			return array('Large Standard',4.76);
		}elseif($this->isLargeStandardSize($weight,$l,$w,$h)){
			return array('Large Standard', 5.26+ceil($weight*0.0022046-2)*0.38);
		}elseif ($this->isSmallOverSize($weight,$l,$w,$h)) {
			return array('Small Oversize', 8.26+ceil($weight*0.0022046-2)*0.38);
		}elseif ($this->isMediumOverSize($weight,$l,$w,$h)) {
			return array('Medium Oversize', 9.79+ceil($weight*0.0022046-2)*0.38);
		}elseif ($this->isLargeOverSize($weight,$l,$w,$h)) {
			return array('Large Oversize', 75.58+ceil($weight*0.0022046-2)*0.79);
		}elseif ($this->isSpecialOverSize($weight,$l,$w,$h)) {
			return array('Special Oversize', 137.32+ceil($weight*0.0022046-2)*0.91);
		}else{
			return array('No way', 65536);
		}
	}

	private function isSmallStandardSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if( $arr[0]<0.75 && $arr[1]<12 && $arr[2]<15 && $weight*0.035274<12){
			return true;
		}else{
			return false;
		}
	}

	private function isLargeStandardSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if($arr[0]<8 && $arr[1]<14 && $arr[2]<18 && $weight*0.0022046<20){
			return true;
		}else{
			return false;
		}
	}

	private function isSmallOverSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if((2*($arr[0]+$arr[1])+$arr[2])<130 && $arr[1]<30 && $arr[2]<60 && $weight*0.0022046<70){
			return true;
		}else{
			return false;
		}
	}

	private function isMediumOverSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if((2*($arr[0]+$arr[1])+$arr[2])<130 && $arr[2]<108 && $weight*0.0022046<150){
			return true;
		}else{
			return false;
		}
	}

	private function isLargeOverSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if((2*($arr[0]+$arr[1])+$arr[2])<165 && $arr[2]<108 && $weight*0.0022046<150){
			return true;
		}else{
			return false;
		}
	}

	private function isSpecialOverSize($weight,$l,$w,$h){
		$arr = array($l*0.3937008,$w*0.3937008,$h*0.3937008);
		sort($arr);
		if((2*($arr[0]+$arr[1])+$arr[2])>165 && $arr[2]>108 && $weight*0.0022046>150){
			return true;
		}else{
			return false;
		}
	}

	public function fileExchange($market,$account){
		$this->assign('market',$market);
		$this->assign('account',$account);
		$this->display();
	}

	public function fileExchangeHandle($market,$account){
		if($market=='ebay'){
			$this->ebayFileExchangeHandle($account);
		}elseif($market=='amazon'){
			$this->amazonFileExchangeHandle($account);
		}elseif($market=='groupon'){
			$this->grouponFileExchangeHandle($account);
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
            $excludeSheet = $objPHPExcel->getSheet(1);
            $excludeHighestRow = $excludeSheet->getHighestRow(); // 取得总行数
			$excludeHighestColumn = $excludeSheet->getHighestColumn(); // 取得总列数
			//excel first column name of exclude table verify
            for($c='A';$c<=$excludeHighestColumn;$c++){
                $excludeFirstRow[$c] = $excludeSheet->getCell($c.'1')->getValue();
            }

            if($this->verifyEbayFxtcn($firstRow) && $this->verifyExcludeFxt($excludeFirstRow)){
            	$storageTable=M($this->getStorageTableName($account));
            	$salePlanTable=M($this->getSalePlanTableName($account));
            	$productTable=M(C('DB_PRODUCT'));
            	$usswInboundViewTable=D("UsswInboundView");
                for($i=2;$i<=$highestRow;$i++){
                	$splitSku = $this->splitSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
                	foreach ($splitSku as $splitskukey => $splitskuvalue) {
                		$splitSku[$splitskukey][0]=$this->toTextSku($this->skuDecode($splitskuvalue[0]));               		
                	}

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

                	if(count($splitSku)==1){
                		//Single sku
                		$oinventory=0;
                		for($e=1;$e<=$excludeHighestRow;$e++){
                			if($this->skuDecode($excludeSheet->getCell("A".$e)->getValue())==$splitSku[0][0]){
                				$oinventory=$excludeSheet->getCell("B".$e)->getValue();
                			}
                		}
                		$salePlan=$salePlanTable->where(array('sku'=>$splitSku[0][0]))->find();
                		$ainventory=$storageTable->where(array('sku'=>$splitSku[0][0]))->getField('ainventory');
                		if($ainventory!=null){
                			$ainventory=($ainventory-$oinventory)<0?0:($ainventory-$oinventory);
                		}
                		$map[C('DB_USSW_INBOUND_STATUS')] = array('neq','已入库');
						$map[C('DB_USSW_INBOUND_ITEM_SKU')] = array('eq',$splitSku[0][0]);
                		$iinventory=$usswInboundViewTable->where($map)->sum(C('DB_USSW_INBOUND_ITEM_DQUANTITY'));
                		$map=null;
                		if($splitSku[0][1]==1){
                			//Single sku and Single sale quantity, get the ainventory quantity and the suggested sale price
                			$data[$i-2]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
                			$data[$i-2][$firstRow['H']]=$ainventory;
                			if($productTable->where(array(C('DB_PRODUCT_SKU')=>$splitSku[0][0]))->getField(C('DB_PRODUCT_TOUS')) == '无' && $ainventory==0 && $iinventory==0){
                				$data[$i-2]['Suggest']='不做的商品，需要下架';
                			}else{
                				$data[$i-2]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
                			}
                			if($data[$i-2][$firstRow['E']]=='USD' || $data[$i-2][$firstRow['E']]==null){
                				$data[$i-2][$firstRow['F']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
                			}               			
                		}else{
                			//Single sku and multiple sale quantity
                			$data[$i-2]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
                			$data[$i-2][$firstRow['H']]=intval($ainventory/$splitSku[0][1]);
                			if($productTable->where(array(C('DB_PRODUCT_SKU')=>$splitSku[0][0]))->getField(C('DB_PRODUCT_TOUS')) == '无' && $ainventory==0 && $iinventory==0){
                				$data[$i-2]['Suggest']='不做的商品，需要下架';
                			}else{
                				$data[$i-2]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
                			}
                		}

                	}else{
                		//Multiple sku
                		$data[$i-2]['Suggest']="组合销售商品，无法给出建议售价";
                		$data[$i-2][$firstRow['H']]=65536;
                		foreach ($splitSku as $key => $skuQuantity){
                			$oinventory=0;
	                		for($e=1;$e<=$excludeHighestRow;$e++){
	                			if($excludeSheet->getCell("A".$e)->getValue()==$skuQuantity[0]){
	                				$oinventory=$excludeSheet->getCell("B".$e)->getValue();
	                			}
	                		}
                			$ainventory=$storageTable->where(array('sku'=>$skuQuantity[0]))->getField('ainventory');
                			if($ainventory!=null){
                				$ainventory=($ainventory-$oinventory)<0?0:($ainventory-$oinventory);
                			}
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

                //find item in stock but not listed
                $map[C('DB_USSTORAGE_AINVENTORY')] = array('gt',0);
                $storages=$storageTable->where($map)->select();

                /*//Check the item is ended manual. If the item in TODO. Then do not add to list.
                $todoWhere[C('DB_TODO_STATUS')] = array('eq', 0);
				$todoWhere[C('DB_TODO_TASK')] = array('like', '%'.$account.'销售建议重新刊登：%');
				$todo = M(C('DB_TODO'))->where($todoWhere)->getField(C('DB_TODO_TASK'));
				$todo= str_replace(':', '：', $todo);
				$todoTask = explode('：', str_replace(',', '，', $todo));
				$relistSku = explode('，', $todoTask[1]);*/				
                $newIndex = $highestRow-1;
                foreach ($storages as $key => $value) {

                	$listed=false;

                	for ($i=2;$i<=$highestRow;$i++) {
                		$splitSku = $this->splitSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
	                	if(count($splitSku)==1){
	                		if($this->toTextSku($splitSku[0][0])==$this->toTextSku($value[C('DB_USSTORAGE_SKU')])){
	                			$listed=true;
	                			break;
	                		}
	                	}                		
                	}
                	/*//Check the item is ended manual. If the item in TODO. Then do not add to list.
                	$waitingRelist = array_search($value[C('DB_USSTORAGE_SKU')], $relistSku) == false? false: true;
                	if($listed==false && !$waitingRelist){*/
                	if($listed==false){
                		$data[$newIndex][$firstRow['K']]=$value[C('DB_USSTORAGE_SKU')];
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
                $this->exportEbayFileExchangeExcel($account.'FileExchange',$excelCellName,$data); 
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
    
    //Verify imported file exchange exclude template column name
	private function verifyExcludeFxt($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_EXCLUDE_STOCK_FXT')));$c++){
            if($firstRow[$c] != C('IMPORT_EXCLUDE_STOCK_FXT')[$c])
                return false;
        }
        return true;
    }

    //Verify imported file exchange with sell template column name
	private function verifyWithSellFxt($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_WITHSELL_FXT')));$c++){
            if($firstRow[$c] != C('IMPORT_WITHSELL_FXT')[$c])
                return false;
        }
        return true;
    }

    //Verify imported file exchange template column name
	private function verifyAmazonFxt($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_AMAZON_FXT')));$c++){
            if($firstRow[$c] != C('IMPORT_AMAZON_FXT')[$c])
                return false;
        }
        return true;
    }

    //Verify imported file exchange template column name
	private function verifyGrouponFxt($firstRow){
        for($c='A';$c<=max(array_keys(C('IMPORT_GROUPON_FXT')));$c++){
            if($firstRow[$c] != C('IMPORT_GROUPON_FXT')[$c])
                return false;
        }
        return true;
    }

    private function amazonFileExchangeHandle($account){
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
			$excludeSheet = $objPHPExcel->getSheet(0);
			$excludeHighestRow = $excludeSheet->getHighestRow(); // 取得总行数
			$excludeHighestColumn = $excludeSheet->getHighestColumn(); // 取得总列数
			//excel first column name verify
            for($c='A';$c<=$excludeHighestColumn;$c++){
                $excludeFirstRow[$c] = $objPHPExcel->getActiveSheet()->getCell($c.'1')->getValue();
            }

            $withSellSheet = $objPHPExcel->getSheet(1);
			$withSellHighestRow = $withSellSheet->getHighestRow(); // 取得总行数
			$withSellHighestColumn = $withSellSheet->getHighestColumn(); // 取得总列数
			//excel first column name verify
            for($c='A';$c<=$withSellHighestColumn;$c++){
                $withSellFirstRow[$c] = $withSellSheet->getCell($c.'1')->getValue();
       		}
				

            if($this->verifyExcludeFxt($excludeFirstRow) && $this->verifyWithSellFxt($withSellFirstRow)){
            	if($_POST['fbaFxc']==0){
            		$fxcmap['sku'] = array('notlike',array('FBA_%'));
            		$salePlan=M($this->getSalePlanTableName($account))->where($fxcmap)->limit($_POST['startRow'], $_POST['endRow']==-1?M($this->getSalePlanTableName($account))->count():$_POST['endRow'])->select();
            	}else{
            		$salePlan=M($this->getSalePlanTableName($account))->limit($_POST['startRow'], $_POST['endRow']==-1?M($this->getSalePlanTableName($account))->count():$_POST['endRow'])->select();
            	}            	
		    	$storageTable=M($this->getStorageTableName($account));
		    	$productTable=M(C("DB_PRODUCT"));
		    	$storageTable->startTrans();
		    	$productTable->startTrans();
		    	foreach ($salePlan as $key => $value) {
		    		$data[$key]["sku"]=$value[C("DB_USSW_SALE_PLAN_SKU")];
		    		$data[$key]["price"]=$value[C("DB_USSW_SALE_PLAN_PRICE")];
		    		if($this->isFBASku($value[C("DB_USSW_SALE_PLAN_SKU")])){
		    			$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$this->fbaSkuToStandardSku($value[C("DB_USSW_SALE_PLAN_SKU")])))->find();
		    		}else{
		    			$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$value[C("DB_USSW_SALE_PLAN_SKU")]))->find();
		    		}
		    		
			    	$p[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')];
			    	$p[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
			    	$p['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
			    	$p['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);
			    	$p['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]:$product[C('DB_PRODUCT_PWEIGHT')],$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]);


			    	$data[$key]["minimum-seller-allowed-price"]=$this->getUsswAmazonISP($p[C('DB_PRODUCT_PRICE')],$p[C('DB_PRODUCT_USTARIFF')],$p['ussw-fee'],$p['way-to-us-fee'],$p['local-shipping-fee1'],$_POST["minbbpPercent"]);
			    	$data[$key]["maximum-seller-allowed-price"]=$this->getUsswAmazonISP($p[C('DB_PRODUCT_PRICE')],$p[C('DB_PRODUCT_USTARIFF')],$p['ussw-fee'],$p['way-to-us-fee'],$p['local-shipping-fee1'],$_POST["maxbbpPercent"]);

		    		if($value[C("DB_USSW_SALE_PLAN_PRICE")]<$data[$key]["minimum-seller-allowed-price"]){
		    			$data[$key]["minimum-seller-allowed-price"]=$value[C("DB_USSW_SALE_PLAN_PRICE")];
		    		}
		    		if($value[C("DB_USSW_SALE_PLAN_PRICE")]>$data[$key]["maximum-seller-allowed-price"]){
		    			$data[$key]["maximum-seller-allowed-price"]=$value[C("DB_USSW_SALE_PLAN_PRICE")];
		    		}
		    		$oinventory=0;
            		for($e=1;$e<=$excludeHighestRow;$e++){
            			if($excludeSheet->getCell("A".$e)->getValue()==$value[C("DB_USSW_SALE_PLAN_SKU")]){
            				$oinventory= $objPHPExcel->getActiveSheet()->getCell('B'.$e)->getValue();
            			}
            		}
		    		
		    		if(!$this->isFBASku($value[C("DB_USSW_SALE_PLAN_SKU")])){
		    			if($storageTable->where(array(C("DB_USSTORAGE_SKU")=>$value[C("DB_USSW_SALE_PLAN_SKU")]))->getField(C("DB_USSTORAGE_AINVENTORY"))==null){
			    			$data[$key]["quantity"]=0;
			    		}else{
			    			$ainventory=($storageTable->where(array(C("DB_USSTORAGE_SKU")=>$value[C("DB_USSW_SALE_PLAN_SKU")]))->getField(C("DB_USSTORAGE_AINVENTORY")))-$oinventory;
			    			$data[$key]["quantity"]=$ainventory<0?0:$ainventory;
			    		}
		    			$data[$key]["leadtime-to-ship"]=3; 
		    		}
		    		
		    		$data[$key]["suggested-price"]=$value[C("DB_USSW_SALE_PLAN_SUGGESTED_PRICE")];
		    		$data[$key]["suggest"]=$value[C("DB_USSW_SALE_PLAN_SUGGEST")];		
		    	}

		    	$lengthOfData = count($data);
	    		for ($wsi=2; $wsi <= $withSellHighestRow; $wsi++) { 
		    		$data[$lengthOfData]['sku'] = $withSellSheet->getCell("A".$wsi)->getValue();
		    		$data[$lengthOfData]['price'] = $withSellSheet->getCell("B".$wsi)->getValue();
		    		$explodedSku = explode('_', $withSellSheet->getCell("A".$wsi)->getValue());
		    		$splitSku = $this->splitSku($explodedSku[0]);
		    		$oinventory=0;
		    		if(count($splitSku)==1){
		    			//single sku
		    			if($splitSku[0][1]==1){
                			//Single sku and Single sale quantity, get the ainventory quantity and the suggested sale price
                			for($e=2;$e<=$excludeHighestRow;$e++){
		            			if($excludeSheet->getCell("A".$e)->getValue()==$splitSku[0][0]){
		            				$oinventory= $objPHPExcel->getSheet(0)->getCell('B'.$e)->getValue();
		            				break;
		            			}
		            		}
                			$ainventory=($storageTable->where(array(C("DB_USSTORAGE_SKU")=>$splitSku[0][0]))->getField(C("DB_USSTORAGE_AINVENTORY")))-$oinventory;
		    				$data[$lengthOfData]["quantity"]=$ainventory>0?$ainventory:0;
                		}else{
                			//Single sku and multiple sale quantity
                			for($e=2;$e<=$excludeHighestRow;$e++){
		            			if($excludeSheet->getCell("A".$e)->getValue()==$splitSku[0][0]){
		            				$oinventory= $objPHPExcel->getSheet(0)->getCell('B'.$e)->getValue();
		            				break;
		            			}
		            		}
		            		$ainventory=($storageTable->where(array(C("DB_USSTORAGE_SKU")=>$splitSku[0][0]))->getField(C("DB_USSTORAGE_AINVENTORY")))/$splitSku[0][1]-$oinventory;
		            		$data[$lengthOfData]["quantity"]=$ainventory>0?$ainventory:0;
                		}

		    		}else{
		    			//multiple sku
		    			$data[$lengthOfData]["quantity"]=65536;
		    			foreach ($splitSku as $key => $skuQuantity){
                			$oinventory=0;
	                		for($e=1;$e<=$excludeHighestRow;$e++){
	                			if($excludeSheet->getCell("A".$e)->getValue()==$skuQuantity[0]){
	                				$oinventory=$excludeSheet->getCell("B".$e)->getValue();
	                			}
	                		}
                			if($skuQuantity[1]==1){
                				//Multiple sku and Single sale quantity
                				$ainventory=$storageTable->where(array('sku'=>$skuQuantity[0]))->getField('ainventory');
	                			if($ainventory!=null){
	                				$ainventory=($ainventory-$oinventory)<0?0:($ainventory-$oinventory);
	                			}
                				if($ainventory<$data[$lengthOfData]["quantity"]){
                					$data[$lengthOfData]["quantity"]=$ainventory;
                				}
                			}else{
                				//Multiple sku and Multiple sale quantity
                				$ainventory=$storageTable->where(array('sku'=>$skuQuantity[0]))->getField('ainventory');
	                			if($ainventory!=null){
	                				$ainventory=($ainventory/$skuQuantity[1]-$oinventory)<0?0:($ainventory/$skuQuantity[1]-$oinventory);
	                			}
                				if(intval($ainventory/$skuQuantity[1])<$data[$lengthOfData]["quantity"]){
                					$data[$lengthOfData]["quantity"]=intval($ainventory/$skuQuantity[1]);
                				}

                			}
                		}
                		if($data[$lengthOfData]["quantity"]==65536){
        					$data[$lengthOfData]["quantity"]=0;
        				}
		    		}
		    		$lengthOfData++;
		    	}
		    	$productTable->commit();
		    	$storageTable->commit();
		    	$excelCellName[0]='sku';
		    	$excelCellName[1]='price';
		    	$excelCellName[2]='suggested-price';
		    	$excelCellName[3]='suggest';
		    	$excelCellName[4]='minimum-seller-allowed-price';
		    	$excelCellName[5]='maximum-seller-allowed-price';
		    	$excelCellName[6]='quantity';
		    	$excelCellName[7]='leadtime-to-ship';
		    	$excelCellName[8]='fulfillment-channel';
		    	$this->exportAmazonFileExchangeExcel($account."_amazon_update_file",$excelCellName,$data);
            }else{
                $this->error("模板错误，请检查模板！");
            }
    	}else{
            $this->error("请选择上传的文件");
        }
    }

    private function grouponFileExchangeHandle($account){
    	if (!empty($_FILES)) {
			import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/fileExchange/',
			 'saveRule'=>'grouponFileExchange'.'_'.time(),
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

            $excludeSheet = $objPHPExcel->getSheet(1);
            $excludeHighestRow = $excludeSheet->getHighestRow(); // 取得总行数
			$excludeHighestColumn = $excludeSheet->getHighestColumn(); // 取得总列数
			//excel first column name of exclude table verify
            for($c='A';$c<=$excludeHighestColumn;$c++){
                $excludeFirstRow[$c] = $excludeSheet->getCell($c.'1')->getValue();
            }

            if($this->verifyGrouponFxt($firstRow) && $this->verifyExcludeFxt($excludeFirstRow)){
            	$storageTable=M($this->getStorageTableName($account));
            	$salePlanTable=M($this->getSalePlanTableName($account));
            	$product = M(C('DB_PRODUCT'));
                for($i=2;$i<=$highestRow;$i++){
                	$splitSku = $this->splitSku($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());

                	$data[$i-2][$firstRow['A']]=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
        			$data[$i-2][$firstRow['B']]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
        			$data[$i-2][$firstRow['C']]=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
        			$data[$i-2][$firstRow['D']]=$objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
        			$data[$i-2][$firstRow['E']]=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
        			$data[$i-2][$firstRow['F']]=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
        			$data[$i-2][$firstRow['G']]=$objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
                	$data[$i-2][$firstRow['H']]='open';
        			$data[$i-2][$firstRow['J']]=$objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
        			$data[$i-2][$firstRow['K']]=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
        			$data[$i-2][$firstRow['L']]=$product->where(array(C('DB_PRODUCT_SKU')=>$data[$i-2][$firstRow['K']]))->getField(C('DB_PRODUCT_UPC'));
        			$data[$i-2][$firstRow['M']]=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
        			$data[$i-2][$firstRow['N']]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
        			$data[$i-2][$firstRow['O']]=$objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();

                	if(count($splitSku)==1){
                		//Single sku
                		$oinventory=0;
                		for($e=1;$e<=$excludeHighestRow;$e++){
                			if($excludeSheet->getCell("A".$e)->getValue()==$splitSku[0][0]){
                				$oinventory=$excludeSheet->getCell("B".$e)->getValue();
                			}
                		}
                		$salePlan=$salePlanTable->where(array('sku'=>$splitSku[0][0]))->find();
                		$ainventory=$storageTable->where(array('sku'=>$splitSku[0][0]))->getField('ainventory')-$oinventory;
                		if($ainventory<0){
                			$ainventory=0;
                		}
                		if($splitSku[0][1]==1){
                			//Single sku and Single sale quantity, get the ainventory quantity and the suggested sale price
                			
                			if($salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')]>$data[$i-2][$firstRow['N']]){
                				$data[$i-2]['SuggestPrice']=$data[$i-2][$firstRow['N']];
                			}else{
                				$data[$i-2]['SuggestPrice']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')];
                			}
                			$data[$i-2]['Suggest']=$salePlan[C('DB_USSW_SALE_PLAN_SUGGEST')];
                			$data[$i-2][$firstRow['J']]=$ainventory;
                			$data[$i-2][$firstRow['O']]=$salePlan[C('DB_USSW_SALE_PLAN_PRICE')];
                		}else{
                			//Single sku and multiple sale quantity
                			$data[$i-2]['Suggest']="多个一组销售商品，无法给出建议售价";
                			$data[$i-2][$firstRow['J']]=intval($ainventory/$splitSku[0][1]);
                		}

                	}else{
                		$data[$i-2]['Suggest']="组合销售商品，无法给出建议售价";
                		//Multiple sku
                		$data[$i-2][$firstRow['J']]=65536;
                		foreach ($splitSku as $key => $skuQuantity){
                			$oinventory=0;
	                		for($e=1;$e<=$excludeHighestRow;$e++){
	                			if($excludeSheet->getCell("A".$e)->getValue()==$skuQuantity[0]){
	                				$oinventory=$excludeSheet->getCell("B".$e)->getValue();
	                			}
	                		}
                			$ainventory=$storageTable->where(array('sku'=>$skuQuantity[0]))->getField('ainventory')-$oinventory;
                			if($ainventory<0){
                				$ainventory=0;
                			}
                			if($skuQuantity[1]==1){
                				//Multiple sku and Single sale quantity
                				if($ainventory<$data[$i-2][$firstRow['J']]){
                					$data[$i-2][$firstRow['J']]=$ainventory;
                				}
                			}else{
                				//Multiple sku and Multiple sale quantity
                				if(intval($ainventory/$skuQuantity[1])<$data[$i-2]['Ainventory']){
                					$data[$i-2][$firstRow['J']]=intval($ainventory/$skuQuantity[1]);
                				}
                			}
                		}
                	}                 
                }

                //find item in stock but not listed
                $map[C('DB_USSTORAGE_AINVENTORY')] = array('gt',0);
                $storages=$storageTable->where($map)->select();
                $newIndex = $highestRow-1;
                foreach ($storages as $key => $value) {

                	$listed=false;
                	for ($i=2;$i<=$highestRow;$i++) {
                		if($objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue()==$value[C('DB_USSTORAGE_SKU')]){
                			$listed=true;
                		}
                	}
                	if($listed==false){
                		$data[$newIndex][$firstRow['K']]=$value[C('DB_USSTORAGE_SKU')];
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
                $excelCellName[6]=$objPHPExcel->getActiveSheet()->getCell("G1")->getValue();
                $excelCellName[7]=$objPHPExcel->getActiveSheet()->getCell("H1")->getValue();
                $excelCellName[8]=$objPHPExcel->getActiveSheet()->getCell("I1")->getValue();
                $excelCellName[9]=$objPHPExcel->getActiveSheet()->getCell("J1")->getValue();
                $excelCellName[10]=$objPHPExcel->getActiveSheet()->getCell("K1")->getValue();
                $excelCellName[11]=$objPHPExcel->getActiveSheet()->getCell("L1")->getValue();
                $excelCellName[12]=$objPHPExcel->getActiveSheet()->getCell("M1")->getValue();
                $excelCellName[13]=$objPHPExcel->getActiveSheet()->getCell("N1")->getValue();
                $excelCellName[14]=$objPHPExcel->getActiveSheet()->getCell("O1")->getValue();
                $excelCellName[15]='SuggestPrice';
                $excelCellName[16]='Suggest';
                $this->exportGrouponFileExchangeExcel('G-lipovoltFileExchange',$excelCellName,$data); 
            }else{
                $this->error("模板错误，请检查模板！");
            }   
        }else{
            $this->error("请选择上传的文件");
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
                if($cellName[$j]=='M' && strlen($expTableData[$i][$expCellName[$j]])==6 && substr($expTableData[$i][$expCellName[$j]], 4,1)=='.' && substr($expTableData[$i][$expCellName[$j]], 5,1)==1){
            		$objPHPExcel->getActiveSheet()->getStyle ($cellName[$j].($i+2))->getNumberFormat()->setFormatCode ("0.00");
            		$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), number_format($expTableData[$i][$expCellName[$j]],2,".",""));
            	}else{
            		$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j]]);
            	}
            }             
        }  

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output');
        exit;   
    }

    private function exportGrouponFileExchangeExcel($expTitle,$expCellName,$expTableData){
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
        	$objPHPExcel->getActiveSheet()->getStyle('L'.($i+2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            for($j=0;$j<$cellNum;$j++){
            	if($i>0 && $expTableData[$i][$expCellName[14]] !=null && $expTableData[$i][$expCellName[14]]!=$expTableData[$i][$expCellName[13]]){
            		$objPHPExcel->getActiveSheet()->getStyle( 'O'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'O'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
            		$objPHPExcel->getActiveSheet()->getStyle( 'P'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            		$objPHPExcel->getActiveSheet()->getStyle( 'P'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
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

    public function updateSalePrice($market,$account){
    	$this->assign('market',$market);
    	$this->assign('account',$account);
    	$this->display();
    }

    public function updateSalePriceHandle($market,$account){
    	if($market=='ebay'){
			$this->updateEbaySalePriceHandle($account);
		}elseif($market=='amazon'){
			$this->updateAmazonSalePriceHandle($account);
		}elseif($market=='groupon'){
			$this->updateGrouponSalePriceHandle($account);
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
            		if($sku!='' && $sku!=null){
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

    private function updateAmazonSalePriceHandle($account){
    	if (!empty($_FILES)) {
    		import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/updateSalePrice/',
			 'saveRule'=>'amazon'.'_'.$account.'_'.time(),
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

            if($this->verifyAmazonFxt($firstRow)){
            	$salePlan=M($this->getSalePlanTableName($account));
            	$salePlan->startTrans();
            	for($i=2;$i<=$highestRow;$i++){
            		$sku = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            		$map[C("DB_USSW_SALE_PLAN_SKU")]=array("eq",$sku);
            		$actualPrice = $salePlan->where($map)->getField(C("DB_USSW_SALE_PLAN_PRICE"));
            		$priceNote = $salePlan->where($map)->getField(C("DB_USSW_SALE_PLAN_PRICE_NOTE"));
            		if($actualPrice!=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue()){
            			$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
						if($priceNote==null){
							$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue().' '.date('ymd',time());
						}else{
							$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $priceNote.' | '.$$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue().' '.date('Y-m-d',time());
						}
						$data[C("DB_USSW_SALE_PLAN_PRICE")]=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
						$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
						$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
						$salePlan->where($map)->save($data);
						$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]=null;
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

    private function updateGrouponSalePriceHandle($account){
    	if (!empty($_FILES)) {
    		import('ORG.Net.UploadFile');
			$config=array(
			 'allowExts'=>array('xls'),
			 'savePath'=>'./Public/upload/updateSalePrice/',
			 'saveRule'=>'groupon'.'_'.$account.'_'.time(),
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

            if($this->verifyGrouponFxt($firstRow)){
            	$salePlan=M($this->getSalePlanTableName($account));
            	$salePlan->startTrans();
            	for($i=2;$i<=$highestRow;$i++){
            		$sku = $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
            		$map[C("DB_USSW_SALE_PLAN_SKU")]=array("eq",$sku);
            		$actualPrice = $salePlan->where($map)->getField(C("DB_USSW_SALE_PLAN_PRICE"));
            		$priceNote = $salePlan->where($map)->getField(C("DB_USSW_SALE_PLAN_PRICE_NOTE"));
            		if($actualPrice!=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue()){
            			$data[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
						if($priceNote==null){
							$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue().' '.date('ymd',time());
						}else{
							$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')] =  $priceNote.' | '.$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue().' '.date('Y-m-d',time());
						}
						$data[C("DB_USSW_SALE_PLAN_PRICE")]=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
						$data[C('DB_USSW_SALE_PLAN_SUGGESTED_PRICE')] = null;
						$data[C('DB_USSW_SALE_PLAN_SUGGEST')] = null;
						$salePlan->where($map)->save($data);
						$data[C('DB_USSW_SALE_PLAN_PRICE_NOTE')]=null;
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

    public function compareFbaUsswCost(){
    	$where[C('DB_PRODUCT_TOUS')] = array('neq', '无');
    	$products = M(C('DB_PRODUCT'))->where($where)->select();
    	$usStorageTable = M(C('DB_USSTORAGE'));
    	$amazonUsStorageTable = M(C('DB_AMAZON_US_STORAGE'));
    	foreach ($products as $key => $p) {
    		$data[$key][C('DB_PRODUCT_SKU')] = $p[C('DB_PRODUCT_SKU')];
    		$data[$key][C('DB_PRODUCT_CNAME')] = $p[C('DB_PRODUCT_CNAME')];
    		$data[$key][C('DB_PRODUCT_MANAGER')] = $p[C('DB_PRODUCT_MANAGER')];
    		$data[$key]['quantity'] = $usStorageTable->where(array(C('DB_USSTORAGE_SKU')=>$p[C('DB_PRODUCT_SKU')]))->getField(C('DB_USSTORAGE_AINVENTORY'));
    		$data[$key]['fba_shipping_fee'] = $this->getFBAShippingWayFee($p[C('DB_PRODUCT_PWEIGHT')]==0?$p[C('DB_PRODUCT_WEIGHT')]:$p[C('DB_PRODUCT_PWEIGHT')],$p[C('DB_PRODUCT_LENGTH')],$p[C('DB_PRODUCT_WIDTH')],$p[C('DB_PRODUCT_HEIGHT')])[1];
    		$data[$key]['fba_cost'] = $this->calFBASuggestCost('lipovolt', $p[C('DB_PRODUCT_SKU')]);
    		$data[$key]['ussw_shipping_fee'] = $this->getUsswLocalShippingFee1($p[C('DB_PRODUCT_PWEIGHT')]==0?$p[C('DB_PRODUCT_WEIGHT')]:$p[C('DB_PRODUCT_PWEIGHT')],$p[C('DB_PRODUCT_LENGTH')],$p[C('DB_PRODUCT_WIDTH')],$p[C('DB_PRODUCT_HEIGHT')]);
    		$data[$key]['ussw_cost'] = $this->calUsswSuggestCost('lipovolt', $p[C('DB_PRODUCT_SKU')]);
    		$data[$key]['difference'] = $data[$key]['ussw_cost']-$data[$key]['fba_cost'];
    		$startDate = date('Y-m-d H:i:s',time()-60*60*24*30);
    		$data[$key]['30DaysSaleQuantity'] = $this->calUsswSaleQuantity('lipovolt',$p[C('DB_PRODUCT_SKU')],$startDate);
    		$startDate = date('Y-m-d H:i:s',time()-60*60*24*15);
    		$data[$key]['15DaysSaleQuantity'] = $this->calUsswSaleQuantity('lipovolt',$p[C('DB_PRODUCT_SKU')],$startDate);
    		$startDate = date('Y-m-d H:i:s',time()-60*60*24*7);
    		$data[$key]['7DaysSaleQuantity'] = $this->calUsswSaleQuantity('lipovolt',$p[C('DB_PRODUCT_SKU')],$startDate);
    		
    		if($amazonUsStorageTable->where(array('sku'=>'FBA_'.$p[C('DB_PRODUCT_SKU')]))->find() ==null || $amazonUsStorageTable->where(array('sku'=>'FBA_'.$p[C('DB_PRODUCT_SKU')]))->find() ==false){
    			$data[$key]['inFBA'] = '否';
    		}else{
    			$data[$key]['inFBA'] = '是';
    		}
    	}
    	foreach($data as $val){
			$key_arrays[]=$val['difference'];
		}
    	array_multisort($key_arrays,SORT_DESC, $data);
    	$xlsCell  = array(
	        array(C('DB_PRODUCT_SKU'),'产品编码'),
	        array(C('DB_PRODUCT_CNAME'),'产品名称'),
	        array(C('DB_PRODUCT_MANAGER'),'产品经理'),
	        array('quantity','自建仓可用库存'),
	        array('fba_shipping_fee','FBA本地运费'),
	        array('fba_cost','FBA成本'),
	        array('ussw_shipping_fee','自建仓本地运费'),
	        array('ussw_cost','自建仓成本'),
	        array('difference','USSW-FBA成本差'),
	        array('30DaysSaleQuantity','amazon30天自发货销量'),
	        array('15DaysSaleQuantity','amazon15天自发货销量'),
	        array('7DaysSaleQuantity','amazon7天自发货销量'),
	        array('inFBA','已入FBA')
	        );
    	$this->exportExcel('CompareFbaUssw',$xlsCell,$data);
    }

    public function exportEbayBulkDiscount($account){
    	$usStorage = M(C('DB_USSTORAGE'))->select();
    	$productTable = M(C('DB_PRODUCT'));
    	$salePlanTable = M($this->getSalePlanTableName($account));
    	$data = array();
    	foreach ($usStorage as $key => $value) {
    		$product = $productTable->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->find();
    		$sale_price = $salePlanTable->where(array(C('DB_USSTORAGE_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_USSW_SALE_PLAN_PRICE'));
    		//2 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*2;
    		$tmp[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    		$tmp['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')]*2,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*2);
    		$tmp['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')]*2,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*2):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*2);
    		$tmp['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]*2:$product[C('DB_PRODUCT_PWEIGHT')]*2,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*2); 		
    		$tier2Cost =  $this->getUsswEbayCost($tmp[C('DB_PRODUCT_PRICE')],$tmp[C('DB_PRODUCT_USTARIFF')],$tmp['ussw-fee'],$tmp['way-to-us-fee'],$tmp['local-shipping-fee1'],$sale_price*2);
    		$tier2ProfitRate = ((2*$sale_price)-$tier2Cost)/(2*$sale_price);

    		//3 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*3;
    		$tmp[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    		$tmp['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')]*3,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*3);
    		$tmp['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')]*3,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*3):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*3);
    		$tmp['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]*3:$product[C('DB_PRODUCT_PWEIGHT')]*3,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*3);
    		$tier3Cost =  $this->getUsswEbayCost($tmp[C('DB_PRODUCT_PRICE')],$tmp[C('DB_PRODUCT_USTARIFF')],$tmp['ussw-fee'],$tmp['way-to-us-fee'],$tmp['local-shipping-fee1'],$sale_price*3);
    		$tier3ProfitRate = ((3*$sale_price)-$tier2Cost)/(3*$sale_price);

			//4 PCS
    		$tmp[C('DB_PRODUCT_PRICE')]=$product[C('DB_PRODUCT_PRICE')]*4;
    		$tmp[C('DB_PRODUCT_USTARIFF')]=$product[C('DB_PRODUCT_USTARIFF')]/100;
    		$tmp['ussw-fee']=$this->calUsswSIOFee($product[C('DB_PRODUCT_WEIGHT')]*4,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*4);
    		$tmp['way-to-us-fee']=$product[C('DB_PRODUCT_TOUS')]=="空运"?$this->getUsswAirFirstTransportFee($product[C('DB_PRODUCT_WEIGHT')]*4,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*4):$this->getUsswSeaFirstTransportFee($product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*4);
    		$tmp['local-shipping-fee1']=$this->getUsswLocalShippingFee1($product[C('DB_PRODUCT_PWEIGHT')]==0?$product[C('DB_PRODUCT_WEIGHT')]*4:$product[C('DB_PRODUCT_PWEIGHT')]*4,$product[C('DB_PRODUCT_LENGTH')],$product[C('DB_PRODUCT_WIDTH')],$product[C('DB_PRODUCT_HEIGHT')]*4);
    		$tier4Cost =  $this->getUsswEbayCost($tmp[C('DB_PRODUCT_PRICE')],$tmp[C('DB_PRODUCT_USTARIFF')],$tmp['ussw-fee'],$tmp['way-to-us-fee'],$tmp['local-shipping-fee1'],$sale_price*4);
    		$tier4ProfitRate = ((4*$sale_price)-$tier2Cost)/(4*$sale_price);

    		if($tier2ProfitRate>0.15 && $tier3ProfitRate>0.2 && $tier4ProfitRate>0.3 && !$this->isLongSku($value[C('DB_USSTORAGE_SKU')])){
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

    public function isLongSku($sku){
    	if(strlen($sku)>4)
    		return true;
    	else
    		return false;    		
    }

    public function exportSaleSuggestTable($account){
    	$Data = D($this->getSalePlanViewModel($account));
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

   /* private function deleteNotInSaleSuggest($account){
    	$salePlanTableName = $this->getSalePlanTableName($account);
		$Model = new Model();
		$psubquery = $Model->table('lipovolt_3s_'.C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_TOUS')=>'无'))->buildSql();
		$usstsubquery = $Model->table('lipovolt_3s_'.C('DB_USSTORAGE'))->where(array(array(C('DB_USSTORAGE_AINVENTORY')=>0),array(C('DB_USSTORAGE_IINVENTORY')=>0)))->buildSql();
		$szstsubquery = $Model->table('lipovolt_3s_'.C('DB_SZSTORAGE'))->where(array(C('DB_SZSTORAGE_AINVENTORY')=>0))->buildSql();
		$subrmap[C('DB_RESTOCK_WAREHOUSE')] = array('in',array('美自建仓','万邑通美西'));
		$subrmap[C('DB_RESTOCK_STATUS')] = array('not in',array('待发货','延迟返货'));
		$rsubquery = $Model->table('lipovolt_3s_'.C('DB_RESTOCK'))->where($subrmap)->buildSql();
		$tet = $Model->query('select '. 'distinct(sptn.sku) from '.'lipovolt_3s_'.$salePlanTableName.' sptn,'.$psubquery.' p,'.$usstsubquery.' usst,'.$szstsubquery.' szst,'.$rsubquery.' r where p.sku=sptn.sku AND usst.sku=sptn.sku AND szst.sku=sptn.sku AND r.sku=sptn.sku');
		dump($tet);
		dump($Model->getLastSql());die;
    }*/

    public function allocatUpc($account,$id){
        $sale = M($this->getSalePlanTableName($account))->where(array('id'=>$id))->find();
        if(!$this->isFBASku($sale['sku'])){
        	$sale['upc'] = $this->generateUPC();
	        M($this->getSalePlanTableName($account))->save($sale);
	        $this->success("UPC码已保存");
        }else{
        	$this->success("FBA商品不生成自己的UPC,使用自发货UPC");
        }
    }


    //Return the sale plan table name according to the account
    public function getSalePlanTableName($account){
        switch ($account) {
            case 'greatgoodshop':
                return C('DB_USSW_SALE_PLAN');
                break;
            case 'lipovolt':
                return C('DB_USSW_SALE_PLAN2');
                break;
            case 'g-lipovolt':
                return C('DB_USSW_SALE_PLAN3');
                break;
            case 'ali-retail':
                return C('DB_USSW_SALE_PLAN4');
                break;
            default:
                return null;
                break;
        }
    }


    //Return the sale plan view model according to the account
    public function getMarketByAccount($account){
        switch ($account) {
            case 'greatgoodshop':
                return 'ebay';
                break;
            case 'lipovolt':
                return 'amazon';
                break;
            case 'g-lipovolt':
                return 'groupon';
                break;
            case 'ali-retail':
                return 'ebay';
                break;
            default:
                return null;
                break;
        }
    }


    //Return the storage table name according to the account
    public function getStorageTableName($account){
        switch ($account) {
            case 'greatgoodshop':
                return C('DB_USSTORAGE');
                break;
            case 'vtkg5755':
                return C('DB_SZSTORAGE');
                break;
            case 'lipovolt':
                return C('DB_USSTORAGE');
                break;
            case 'g-lipovolt':
                return C('DB_USSTORAGE');
                break;
            case 'ali-retail':
                return C('DB_USSTORAGE');
                break;
            default:
                return null;
                break;
        }
    }


    //Return the sale plan view model according to the account
    public function getSalePlanViewModel($account){
        switch ($account) {
            case 'greatgoodshop':
                return 'UsswSalePlanView';
                break;
            case 'lipovolt':
                return 'UsswSalePlan2View';
                break;
            case 'g-lipovolt':
                return 'UsswSalePlan3View';
                break;
            case 'ali-retail':
                return 'UsswSalePlan4View';
                break;
            default:
                return null;
                break;
        }
    }
}

?>