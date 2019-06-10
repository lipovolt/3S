<?php

class MetadataAction extends CommonAction{
	public function index(){
		$data = M(C('DB_METADATA'))->select();
		$pw = array('ebay greatgoodshop'=>'11052019','ebay blackfive'=>'04052016','amazon 498307481@qq.com'=>'09092014','groupon zhangminsy2013@gmail.com'=>'02122019','ebay rc-helicar'=>'09092014','ebay vtkg5755'=>'09092014','ebay yzhan-816'=>'04052016');
		$this->assign('data',$data);
		$this->assign('pw',$pw);
		$this->display();
	}

	public function update(){
		if($this->isPost()){
			$data[C('DB_METADATA_ID')] = I('post.'.C('DB_METADATA_ID'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTOUSD')] = I('post.'.C('DB_METADATA_EURTOUSD'),'','htmlspecialchars');
			$data[C('DB_METADATA_USDTORMB')] = I('post.'.C('DB_METADATA_USDTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_EURTORMB')] = I('post.'.C('DB_METADATA_EURTORMB'),'','htmlspecialchars');
			$data[C('DB_METADATA_DEMWST')] = I('post.'.C('DB_METADATA_DEMWST'),'','htmlspecialchars');
			$data[C('DB_METADATA_USED_UPC')] = I('post.'.C('DB_METADATA_USED_UPC'),'','htmlspecialchars');
			$metadata = M(C('DB_METADATA'));
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

	public function usSalePlanMetadata(){
		$this->data=M(C('DB_USSW_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function updateUsSaleMetaDate(){
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

	public function szSalePlanMetadata(){
		$this->data=M(C('DB_SZ_SALE_PLAN_METADATA'))->select();
		$this->display();
	}

	public function updateSzSaleMetaDate(){
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

	public function email(){
		$email = M(C('DB_SELLER_EMAIL'))->select();
		$this->assign('email',$email);
		$this->display();
	}

	public function updateEmail(){
		$_POST[C('DB_SELLER_EMAIL_STATUS')] = 1; //失效之前要检查邮箱的关联情况
		M(C('DB_SELLER_EMAIL'))->save($_POST);
		$this->redirect('email');
	}

	public function newEmail($id=null){
		if($id!=null){
			$vo = M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$id))->find();
			$this->assign('vo',$vo);
		}
		$this->display();
	}

	public function addEmail(){
		$findEmail = M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_EMAIL')=>$_POST['email']))->find();
		if($findEmail!==false && $findEmail!==null){
			$this->error($_POST['email'].'已经在邮件列表里！');
		}else{
			$return = M(C('DB_SELLER_EMAIL'))->add($_POST);
			$this->redirect('email');
		}
	}

	public function bank(){
		$bank = M(C('DB_BANK'))->select();
		$this->assign('bank',$bank);
		$this->display();
	}

	public function newBank($id=null){
		if($id!=null){
			$vo = M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$id))->find();
			$this->assign('vo',$vo);
		}
		$this->display();
	}

	public function updateBank(){
		$_POST[C('DB_BANK_STATUS')] = 1; //失效之前要检查银行账户的关联情况
		M(C('DB_BANK'))->save($_POST);
		$this->redirect('bank');
	}

	public function addBank(){
		$findBank = M(C('DB_BANK'))->where(array(C('DB_BANK_ACCOUNT')=>$_POST[C('DB_BANK_ACCOUNT')]))->find();
		if($findBank!==false && $findBank!==null){
			$this->error($_POST['account'].'已经在银行列表里！');
		}else{
			M(C('DB_BANK'))->add($_POST);
			$this->redirect('bank');
		}
	}

	public function paypal(){
		$paypal = M(C('DB_PAYPAL'))->select();
		$emailTable = M(C('DB_SELLER_EMAIL'));
		$bankTable = M(C('DB_BANK'));
		foreach ($paypal as $key => $value) {
			$paypal[$key]['email1'] = $emailTable->where(array(array(C('DB_SELLER_EMAIL_PID')=>$value[C('DB_PAYPAL_ID')]),array(C('DB_SELLER_EMAIL_POFFSET')=>2)))->getField(C('DB_SELLER_EMAIL_EMAIL'));
			$email2 = $emailTable->where(array(array(C('DB_SELLER_EMAIL_PID')=>$value[C('DB_PAYPAL_ID')]),array(C('DB_SELLER_EMAIL_POFFSET')=>1)))->getField(C('DB_SELLER_EMAIL_EMAIL'),true);
			if($email2 != null){
				foreach ($email2 as $ekey => $evalue) {
					$paypal[$key]['email2'] = $paypal[$key]['email2'].$evalue;
				}
			}
			
			$bank = $bankTable->where(array(C('DB_BANK_PID')=>$value[C('DB_PAYPAL_ID')]))->select();
			if($bank!=null){
				foreach ($bank as $bkey => $bvalue) {
					$paypal[$key]['bank'] = $paypal[$key]['bank'].$bvalue[C('DB_BANK_HOLDER_NAME')].$bvalue[C('DB_BANK_ACCOUNT')];
				}
			}
			
			//找出关联该paypal账号的卖家账号和平台
			$seller_id = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_PID')=>$value[C('DB_PAYPAL_ID')]))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_SAID'),true);
			if($seller_id!=null){
				$smap[C('DB_SELLER_ACCOUNT_ID')] = array('in', $seller_id);
				$seller = M(C('DB_SELLER_ACCOUNT'))->where($smap)->select();
				if($seller!==null && $seller!==false){
					foreach ($seller as $skey => $svalue) {
						$paypal[$key]['seller'] = $svalue[C('DB_SELLER_ACCOUNT_PLATFORM')].' '.$svalue[C('DB_SELLER_ACCOUNT_ACCOUNT')];
					}
				}
			}
			
		}
		$this->assign('paypal',$paypal);
		$this->display();
	}

	public function newPaypal($id=null){
		if($id!=null){
			$paypal = M(C('DB_PAYPAL'))->where(array(C('DB_PAYPAL_ID')=>$id))->find();
			$this->assign('paypal',$paypal);
		}

		$this->assign('bank',M(C('DB_BANK'))->where(array(C('DB_BANK_STATUS')=>1))->order('id desc')->select());
		$this->assign('email',M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_STATUS')=>1))->order('id desc')->select());
		$this->display();
	}

	public function updatePaypal(){
		if($this->updatePaypalEmailCheck($_POST[C('DB_PAYPAL_ID')],$_POST['email1_id']) && $this->updatePaypalEmailCheck($_POST[C('DB_PAYPAL_ID')],$_POST['email2_id']) && $this->updatePaypalEmailCheck($_POST[C('DB_PAYPAL_ID')],$_POST['email3_id']) && $this->updatePaypalBankCheck($_POST[C('DB_PAYPAL_ID')],$_POST['bank1_id']) && $this->updatePaypalBankCheck($_POST[C('DB_PAYPAL_ID')],$_POST['bank2_id']) && $this->updatePaypalBankCheck($_POST[C('DB_PAYPAL_ID')],$_POST['bank3_id'])){
			$newEmailIds = array($_POST['email1_id'],$_POST['email2_id'],$_POST['email3_id']);
			$emap[C('DB_SELLER_EMAIL_PID')] = array('eq',$_POST[C('DB_PAYPAL_ID')]);
			$emap[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',2);
			$email1 = M(C('DB_SELLER_EMAIL'))->where($emap)->getField(C('DB_SELLER_EMAIL_ID'));
			$emap[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',1);
			$email2 = M(C('DB_SELLER_EMAIL'))->where($emap)->getField(C('DB_SELLER_EMAIL_ID'),true);
			if($email1!=$_POST['email1_id']){
				if($email2!=null && !in_array($email1, $newEmailIds)){
					//原主邮箱已经不使用，检查该邮箱是否关联其他卖家账号，无关联需要设置邮箱状态=0	
					$this->paypalStoppedEmail($email1);				
				}
			}
			if($email2!=null){
				foreach ($email2 as $e2key => $e2value) {
					if(!in_array($e2value, $newEmailIds)){
						//e2value 停用 设置$e2value PID POFFSET ,如果无其他关联status=0
						$this->paypalStoppedEmail($e2value);	
					}
				}
			}
			$this->updatePaypalEmail($_POST[C('DB_PAYPAL_ID')],$_POST['email1_id'],true);
			$this->updatePaypalEmail($_POST[C('DB_PAYPAL_ID')],$_POST['email2_id'],false);
			$this->updatePaypalEmail($_POST[C('DB_PAYPAL_ID')],$_POST['email3_id'],false);

			$newBankIds = array($_POST['bank1_id'],$_POST['bank2_id'],$_POST['bank3_id']);
			$bank = M(C('DB_BANK'))->where(array(C('DB_BANK_PID')=>$_POST[C('DB_PAYPAL_ID')]))->select();
			foreach ($bank as $bkey => $bvalue) {
				if(!in_array($bvalue, $newBankIds)){
					$this->paypalStoppedBank($bvalue);
				}
			}
			foreach ($newBankIds as $nbvkey => $nbvalue) {
				$this->updatePaypalBank($_POST[C('DB_PAYPAL_ID')],$nbvalue);
			}

			$paypal[C('DB_PAYPAL_ID')] = $_POST[C('DB_PAYPAL_ID')];
			$paypal[C('DB_PAYPAL_PID')] = $_POST[C('DB_PAYPAL_PID')];
			$paypal[C('DB_PAYPAL_PASSWORD')] = $_POST[C('DB_PAYPAL_PASSWORD')];
			$paypal[C('DB_PAYPAL_STATUS')] = 1; //失效之前要检查paypal的关联情况
			$paypal[C('DB_PAYPAL_REMARK')] = $_POST[C('DB_PAYPAL_REMARK')];
			M(C('DB_PAYPAL'))->save($paypal);
			$this->redirect('paypal');
		}		
	}

	private function paypalStoppedBank($bank_id){
		$bank[C('DB_BANK_ID')] = $bank_id;
		$bank[C('DB_BANK_PID')] = 0;
		M(C('DB_BANK'))->save($bank);
	}

	private function paypalStoppedEmail($email_id){
		$seller_account = M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_EMAIL_ID')=>$email_id))->find();
		$email[C('DB_SELLER_EMAIL_ID')]=$email_id;
		$email[C('DB_SELLER_EMAIL_PID')]=0;
		$email[C('DB_SELLER_EMAIL_POFFSET')]=0;
		if($seller_account!=null){
			M(C('DB_SELLER_EMAIL'))->save($email);
		}else{
			$email[C('DB_SELLER_EMAIL_STATUS')]=0;
			M(C('DB_SELLER_EMAIL'))->save($email);
		}
	}

	private function updatePaypalEmailCheck($paypal_id,$email_id){
		$smTable = M(C('DB_SELLER_EMAIL'));	
		if(M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$email_id))->getField(C('DB_SELLER_EMAIL_PID'))!=$paypal_id){
			if($this->isPaypalUsedEmail($email_id)){
				//邮箱已经被别的paypal账户关联
				$this->error('选择的邮箱 '.$smTable->where(array(C('DB_SELLER_EMAIL_ID')=>$email_id))->getField(C('DB_SELLER_EMAIL_EMAIL')).' 已经被别的paypal使用');
				return false;
			}else{
				//邮箱尚未被别的paypal关联
				$sellerAccount = $this->isEbaySellerAccountUsedEmail($email_id);
				if($sellerAccount !=null){
					//邮箱被卖家账号关联
					$relatedSellerid = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_PID')=>$paypal_id))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_SAID'),true);
					if(!$this->isEbayAccount($sellerAccount) || ($this->isEbayAccount($sellerAccount) && in_array($sellerAccount[C('DB_SELLER_ACCOUNT_ID')], $relatedSellerid))){
						//邮箱被非ebay账号使用 或者 邮箱被ebay账号使用，但是使用这个邮箱的ebay账号是这个paypal的关联账号, 设置邮箱的PID POFFSET
						return true;
					}else{
						$this->error('选择的邮箱 '.$smTable->where(array(C('DB_SELLER_EMAIL_ID')=>$email_id))->getField(C('DB_SELLER_EMAIL_EMAIL')).' 已经被别的ebay使用，该ebay账户 '.M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_EMAIL_ID')=>$email_id))->getField(C('DB_SELLER_ACCOUNT_ACCOUNT')).' 不是这个paypal的关联账户。');
						return false;
					}					
				}else{
					//邮箱尚未被别的卖家账户使用
					return true;
				}				
			}
		}else{
			//邮箱本就是该paypal的关联邮箱
			return true;
		}	
		
	}

	private function updatePaypalEmail($paypal_id,$email_id,$isMainEmail){		
		$email[C('DB_SELLER_EMAIL_ID')] = $email_id;
		$email[C('DB_SELLER_EMAIL_PID')] = $paypal_id;
		if($isMainEmail){
			$email[C('DB_SELLER_EMAIL_POFFSET')] = 2;
		}else{
			$email[C('DB_SELLER_EMAIL_POFFSET')] = 1;
		}					
		M(C('DB_SELLER_EMAIL'))->save($email);
	}

	private function updatePaypalBankCheck($paypal_id,$bank_id){
		$bank= M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$bank_id))->find();
		if($bank[C('DB_BANK_PID')]!=$paypal_id){
			if($this->isPaypalUsedBank($bank_id)){
				//银行已经被别的paypal账户关联
				$this->error('选择的银行 '.$bank[C('DB_BANK_HOLDER_NAME')].' '.$bank[C('DB_BANK_ACCOUNT')].' 已经被别的Paypal使用');
				return false;
			}else{
				//银行尚未被别的paypal关联
				$sellerAccount = $this->isSellerAccountUsedBank($bank_id);
				if($sellerAccount!=null){
					//银行被卖家账号关联
					$relatedSellerid = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_PID')=>$paypal_id))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_SAID'),true);
					if(!$this->isEbayAccount($sellerAccount) || ($this->isEbayAccount($sellerAccount) && in_array($sellerAccount[C('DB_SELLER_ACCOUNT_ID')], $relatedSellerid))){
						//银行被非ebay账号使用 或者 银行被ebay账号使用，但是使用这个银行的ebay账号是这个paypal的关联账号, 设置邮箱的PID POFFSET
						return true;
					}else{
						$this->error('选择的银行 '.$bank[C('DB_BANK_HOLDER_NAME')].' '.$bank[C('DB_BANK_ACCOUNT')].' 已经被别的ebay使用，该ebay账户 '.M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_BANK_ID')=>$bank_id))->getField(C('DB_SELLER_ACCOUNT_ACCOUNT')).' 不是这个paypal的关联账户。');
						return false;
					}	
				}
				return true;
			}	
		}else{
			//该银行账号已经是这个paypal的关联账号
			return true;
		}
	}

	private function updatePaypalBank($paypal_id,$bank_id){
		$bank[C('DB_BANK_ID')] = $bank_id;
		$bank[C('DB_BANK_PID')] = $paypal_id;
		M(C('DB_BANK'))->save($bank);
	}

	private function isAmazonAccount($seller_account){
		switch ($seller_account[C('DB_SELLER_ACCOUNT_PLATFORM')]) {
			case 'amazon.com':
				return true;
				break;
			case 'amazon.de':
				return true;
				break;
			
			default:
				return false;
				break;
		}
	}


	private function isEbayAccount($seller_account){
		switch ($seller_account[C('DB_SELLER_ACCOUNT_PLATFORM')]) {
			case 'ebay.com':
				return true;
				break;
			case 'ebay.de':
				return true;
				break;
			
			default:
				return false;
				break;
		}
	}

	public function addPaypal(){
		if($this->checkPaypalData($_POST)){
			$paypal[C('DB_PAYPAL_PASSWORD')] = $_POST[C('DB_PAYPAL_PASSWORD')];
			$paypal[C('DB_PAYPAL_STATUS')] = $_POST[C('DB_PAYPAL_STATUS')];
			$paypal[C('DB_PAYPAL_REMARK')] = $_POST[C('DB_PAYPAL_REMARK')];
			$npid = M(C('DB_PAYPAL'))->add($paypal);
			$seller_email[C('DB_SELLER_EMAIL_ID')] = $_POST['email1_id'];
			$seller_email[C('DB_SELLER_EMAIL_PID')] = $npid;
			$seller_email[C('DB_SELLER_EMAIL_POFFSET')] = 2;
			M(C('DB_SELLER_EMAIL'))->save($seller_email);
			if($_POST[C('email2_id')]>0){
				$seller_email[C('DB_SELLER_EMAIL_ID')] = $_POST['email2_id'];
				$seller_email[C('DB_SELLER_EMAIL_PID')] = $npid;
				$seller_email[C('DB_SELLER_EMAIL_POFFSET')] = 1;
				M(C('DB_SELLER_EMAIL'))->save($seller_email);
			}
			if($_POST[C('email3_id')]>0){
				$seller_email[C('DB_SELLER_EMAIL_ID')] = $_POST['email3_id'];
				$seller_email[C('DB_SELLER_EMAIL_PID')] = $npid;
				$seller_email[C('DB_SELLER_EMAIL_POFFSET')] = 1;
				M(C('DB_SELLER_EMAIL'))->save($seller_email);
			}
			$bank[C('DB_BANK_ID')] = $_POST['bank1_id'];
			$bank[C('DB_BANK_PID')] = $npid;
			M(C('DB_BANK'))->save($bank);
			if($_POST['bank2_id']>0){
				$bank[C('DB_BANK_ID')] = $_POST['bank2_id'];
				$bank[C('DB_BANK_PID')] = $npid;
				M(C('DB_BANK'))->save($bank);
			}
			if($_POST['bank3_id']>0){
				$bank[C('DB_BANK_ID')] = $_POST['bank3_id'];
				$bank[C('DB_BANK_PID')] = $npid;
				M(C('DB_BANK'))->save($bank);
			}
			$this->redirect('paypal');
		}		
	}

	private function isPaypalUsedEmail($email_id){
		if($email_id>0 && M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$email_id))->getField(C('DB_SELLER_EMAIL_PID'))>0){
			return true;
		}else{
			return false;
		}		
	}

	private function isEbaySellerAccountUsedEmail($email_id){
		$map[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = array('eq',$email_id);
		$map[C('DB_SELLER_ACCOUNT_PLATFORM')] = array('like','%ebay%');
		if($email_id>0 && M(C('DB_SELLER_ACCOUNT'))->where($map)->getField(C('DB_SELLER_ACCOUNT_ID'))>0){
			return true;
		}else{
			return false;
		}		
	}

	private function isPaypalUsedBank($bank_id){
		if($bank_id>0 && M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$bank_id))->getField(C('DB_BANK_PID'))>0){
			return true;
		}else{
			return false;
		}		
	}

	private function isEbaySellerAccountUsedBank($bank_id){
		$map[C('DB_SELLER_ACCOUNT_BANK_ID')] = array('eq',$bank_id);
		$map[C('DB_SELLER_ACCOUNT_PLATFORM')] = array('like','%ebay%');
		if($bank_id>0 && M(C('DB_SELLER_ACCOUNT'))->where($map)->getField(C('DB_SELLER_ACCOUNT_ID'))>0){
			return true;
		}else{
			return false;
		}		
	}

	private function checkPaypalData($paypal){
		if($this->checkPaypalEmail($paypal) && $this->checkPaypalBank($paypal)){
			return true;
		}else{
			return false;
		}
	}

	private function checkPaypalEmail($paypal){
		if($this->isPaypalUsedEmail($paypal['email1_id'])){
			$this->error('指定的主账号 '.$paypal['email1_id'].' 已经关联到paypal账号');
			return false;
		}elseif ($this->isPaypalUsedEmail($paypal['email2_id'])) {
			$this->error('指定的账号2 '.$paypal['email2_id'].' 已经关联到paypal账号');
			return false;
		}elseif ($this->isPaypalUsedEmail($paypal['email3_id'])){
			$this->error('指定的账号3 '.$paypal['email3_id'].' 已经关联到paypal账号');
			return false;
		}elseif($this->isEbaySellerAccountUsedEmail($paypal['email1_id'])){
			$this->error('指定的主账号 '.$paypal['email1_id'].' 已经关联到ebay卖家账号');
			return false;
		}elseif($this->isEbaySellerAccountUsedEmail($paypal['email2_id'])){
			$this->error('指定的账号2 '.$paypal['email2_id'].' 已经关联到ebay卖家账号');
			return false;
		}elseif($this->isEbaySellerAccountUsedEmail($paypal['email3_id'])){
			$this->error('指定的账号3 '.$paypal['email3_id'].' 已经关联到ebay卖家账号');
			return false;
		}else{
			return true;
		}
	}

	private function checkPaypalBank($paypal){
		if($this->isPaypalUsedBank($paypal['bank1_id'])){
			$this->error('指定的银行账户1 '.$paypal['bank1_id'].' 已经关联到paypal账号');
			return false;
		}elseif ($this->isPaypalUsedBank($paypal['bank2_id'])) {
			$this->error('指定的银行账户2 '.$paypal['bank2_id'].' 已经关联到paypal账号');
			return false;
		}elseif ($this->isPaypalUsedBank($paypal['bank3_id'])){
			$this->error('指定的银行账户3 '.$paypal['bank3_id'].' 已经关联到paypal账号');
			return false;
		}elseif ($this->isEbaySellerAccountUsedBank($paypal['bank1_id'])){
			$this->error('指定的银行账户1 '.$paypal['bank1_id'].' 已经关联到ebay卖家账号');
			return false;
		}elseif ($this->isEbaySellerAccountUsedBank($paypal['bank2_id'])){
			$this->error('指定的银行账户2 '.$paypal['bank2_id'].' 已经关联到ebay卖家账号');
			return false;
		}elseif ($this->isEbaySellerAccountUsedBank($paypal['bank3_id'])){
			$this->error('指定的银行账户3 '.$paypal['bank3_id'].' 已经关联到ebay卖家账号');
			return false;
		}else{
			return true;
		}
	}

	public function sellerAccount(){
		$seller_account = M(C('DB_SELLER_ACCOUNT'))->select();
		$this->assign('platform',$platform);
		$this->assign('seller_account',$seller_account);
		$this->display();
	}

	public function newSellerAccount(){
		if(IS_POST && $_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')]!=0){
			$main_account=M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ID')=>$_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')]))->find();
			$_POST[C('DB_SELLER_ACCOUNT_PLATFORM')] = $main_account[C('DB_SELLER_ACCOUNT_PLATFORM')];
			$_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')] = $main_account[C('DB_SELLER_ACCOUNT_ID')];
			$_POST[C('DB_SELLER_ACCOUNT_HOLDER_NAME')] = $main_account[C('DB_SELLER_ACCOUNT_HOLDER_NAME')];
			$_POST[C('DB_SELLER_ACCOUNT_ADDRESS')] = $main_account[C('DB_SELLER_ACCOUNT_ADDRESS')];
			$_POST[C('DB_SELLER_ACCOUNT_TEL')] = $main_account[C('DB_SELLER_ACCOUNT_TEL')];
			$_POST[C('DB_SELLER_ACCOUNT_IP')] = $main_account[C('DB_SELLER_ACCOUNT_IP')];
			$_POST[C('DB_SELLER_ACCOUNT_BANK_ID')] = $main_account[C('DB_SELLER_ACCOUNT_BANK_ID')];
			if(M(C('DB_BANK'))->where(array(C('DB_SELLER_ACCOUNT_BANK_ID')=>$main_account[C('DB_SELLER_ACCOUNT_BANK_ID')]))->select()!=null){
				$this->assign('bank',M(C('DB_BANK'))->where(array(C('DB_SELLER_ACCOUNT_BANK_ID')=>$main_account[C('DB_SELLER_ACCOUNT_BANK_ID')]))->select());
			}

			$_POST[C('DB_SELLER_ACCOUNT_PID')] = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_SAID')=>$_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')]))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_PID'));
			$this->assign('paypal',M(C('DB_PAYPAL'))->where(array(C('DB_SELLER_ACCOUNT_PID')=>$main_account_pid))->select());

			foreach (M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_STATUS')=>1))->select() as $key => $value) {
				if(!$this->isPaypalUsedEmail($value[C('DB_SELLER_EMAIL_ID')])){
					if($this->isSellerAccountUsedEmail($value[C('DB_SELLER_EMAIL_ID')])==null){
						$email[$key] = $value;
					}
				}else{
					if($this->isSellerAccountUsedPaypal(M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$value[C('DB_SELLER_EMAIL_ID')]))->getField(C('DB_SELLER_EMAIL_PID')))==null){
						$email[$key] = $value;
					}
				}			
			}
			$this->assign('email',$email);		
			$this->assign('available_main_account',array('0'=>$main_account));
			$this->assign('seller_account',$_POST);
		}else{			
			foreach (M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_STATUS')=>1))->select() as $key => $value) {
				if(!$this->isPaypalUsedEmail($value[C('DB_SELLER_EMAIL_ID')])){
					if($this->isSellerAccountUsedEmail($value[C('DB_SELLER_EMAIL_ID')])==null){
						$email[$key] = $value;
					}
				}else{
					if($this->isSellerAccountUsedPaypal(M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$value[C('DB_SELLER_EMAIL_ID')]))->getField(C('DB_SELLER_EMAIL_PID')))==null){
						$email[$key] = $value;
					}
				}			
			}
			$this->assign('email',$email);
			foreach (M(C('DB_BANK'))->where(array(C('DB_BANK_STATUS')=>1))->select() as $key => $value) {
				if($this->isSellerAccountUsedBank($value[C('DB_BANK_ID')])==null && $value[C('DB_BANK_STATUS')]==1){
					$bank[$key] = $value;
				}
			}
			$this->assign('bank',$bank);
			foreach (M(C('DB_PAYPAL'))->where(array(C('DB_PAYPAL_STATUS')=>1))->select() as $key => $value) {
				if($this->isSellerAccountUsedPaypal($value[C('DB_PAYPAL_ID')])==null && $value[C('DB_PAYPAL_STATUS')]==1){
					$paypal[$key] = $value;
					$paypal[$key]['email'] = M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$value[C('DB_PAYPAL_ACCOUNT1')]))->getField(C('DB_SELLER_EMAIL_EMAIL'));
				}
			}
			$this->assign('paypal',$paypal);
			$maccountMap[C('DB_SELLER_ACCOUNT_MACCOUNT')] = array('eq',0);
			$maccountMap[C('DB_SELLER_ACCOUNT_STATUS')] = array('eq',1);
			$this->assign('available_main_account',M(C('DB_SELLER_ACCOUNT'))->where($maccountMap)->select());
		}
		$platform = array(array('platform'=>'amazon.com','url'=>'sellercentral.amazon.com'),array('platform'=>'amazon.de','url'=>'sellercentral.amazon.de'),array('platform'=>'ebay.com','url'=>'ebay.com'),array('platform'=>'ebay.de','url'=>'ebay.de'),array('platform'=>'groupon.com','url'=>'groupon.com'));
		$this->assign('platform',$platform);
		$this->display();
	}

	public function editSellerAccount($id){
		$seller_account = M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ID')=>$id))->find();
		$seller_account[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_SAID')=>$id))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_PID'));
		if($seller_account[C('DB_SELLER_ACCOUNT_MACCOUNT')] == 0){
			$maccountMap[C('DB_SELLER_ACCOUNT_MACCOUNT')] = array('gt',0);
			$maccountMap[C('DB_SELLER_ACCOUNT_STATUS')] = array('eq',1);
			$this->assign('available_main_account',M(C('DB_SELLER_ACCOUNT'))->where($maccountMap)->select());
		}else{
			$this->assign('available_main_account',M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ID')=>$seller_account[C('DB_SELLER_ACCOUNT_MACCOUNT')]))->select());
		}
		
		$platform = array(array('platform'=>'amazon.com','url'=>'sellercentral.amazon.com'),array('platform'=>'amazon.de','url'=>'sellercentral.amazon.de'),array('platform'=>'ebay.com','url'=>'ebay.com'),array('platform'=>'ebay.de','url'=>'ebay.de'),array('platform'=>'groupon.com','url'=>'groupon.com'));
		$this->assign('platform',$platform);
		foreach (M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_STATUS')=>1))->select() as $key => $value) {
			if(!$this->isPaypalUsedEmail($value[C('DB_SELLER_EMAIL_ID')]) || $value[C('DB_SELLER_EMAIL_ID')]==$seller_account[C('DB_SELLER_ACCOUNT_EMAIL_ID')]){
				if($this->isSellerAccountUsedEmail($value[C('DB_SELLER_EMAIL_ID')])==null || $value[C('DB_SELLER_EMAIL_ID')]==$seller_account[C('DB_SELLER_ACCOUNT_EMAIL_ID')]){
					$email[$key] = $value;
				}
			}else{
				if($this->isSellerAccountUsedPaypal(M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$value[C('DB_SELLER_EMAIL_ID')]))->getField(C('DB_SELLER_EMAIL_PID')))==null){
					$email[$key] = $value;
				}
			}			
		}
		$this->assign('email',$email);
		foreach (M(C('DB_BANK'))->where(array(C('DB_BANK_STATUS')=>1))->select() as $key => $value) {
			if($seller_account[C('DB_SELLER_ACCOUNT_BANK_ID')] == $value[C('DB_BANK_ID')]){
				$bank[$key] = $value;
			}else{
				if($this->isSellerAccountUsedBank($value[C('DB_BANK_ID')])==null){
					$bank[$key] = $value;
				}
			}
		}
		$this->assign('bank',$bank);
		foreach (M(C('DB_PAYPAL'))->where(array(C('DB_PAYPAL_STATUS')=>1))->select() as $key => $value) {
			if($seller_account[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] == $value[C('DB_PAYPAL_ID')]){
				$paypal[$key] = $value;
				$tmpmap[C('DB_SELLER_EMAIL_PID')] = array('eq',$value[C('DB_PAYPAL_ID')]);
				$tmpmap[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',2);
				$paypal[$key]['email'] = M(C('DB_SELLER_EMAIL'))->where($tmpmap)->getField(C('DB_SELLER_EMAIL_EMAIL'));
			}else{
				if($this->isSellerAccountUsedPaypal($value[C('DB_PAYPAL_ID')])==null){
					$paypal[$key] = $value;
					$tmpmap[C('DB_SELLER_EMAIL_PID')] = array('eq',$value[C('DB_PAYPAL_ID')]);
					$tmpmap[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',2);
					$paypal[$key]['email'] = M(C('DB_SELLER_EMAIL'))->where($tmpmap)->getField(C('DB_SELLER_EMAIL_EMAIL'));
				}
			}
		}
		$this->assign('seller_account',$seller_account);
		$this->assign('paypal',$paypal);
		$this->display();
	}

	private function isSellerAccountUsedEmail($email_id){
		$map[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = array('eq',$email_id);
		$map[C('DB_SELLER_ACCOUNT_USED_EMAIL')] = array('like','%'.$email_id.'%');
		$map[C('DB_SELLER_ACCOUNT_USED_PMAIL_ID')] = array('like','%'.$email_id.'%');
		$seller_account = M(C('DB_SELLER_ACCOUNT'))->where($map)->find();
		return $seller_account;
	}

	private function isSellerAccountUsedBank($bank_id){
		$map[C('DB_SELLER_ACCOUNT_BANK_ID')] = array('eq',$bank_id);
		$map[C('DB_SELLER_ACCOUNT_USED_BID')] = array('like','%'.$bank_id.'%');
		$seller_account = M(C('DB_SELLER_ACCOUNT'))->where($map)->find();
		return $seller_account;
	}

	private function isSellerAccountUsedPaypal($paypal_id){
		$map[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = array('eq',$paypal_id);
		$map[C('DB_SELLER_ACCOUNT_USED_PID')] = array('like','%'.$paypal_id.'%');
		$seller_account = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where($map)->find();
		return $seller_account;
	}

	public function addSellerAccount(){
		if($_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')]==0){
			if($this->checkSellerData($_POST)){
				//保存新建的销售账号
				$newsa[C('DB_SELLER_ACCOUNT_PLATFORM')] = $_POST[C('DB_SELLER_ACCOUNT_PLATFORM')];
				$newsa[C('DB_SELLER_ACCOUNT_MACCOUNT')] = $_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')];
				$newsa[C('DB_SELLER_ACCOUNT_ACCOUNT')] = $_POST[C('DB_SELLER_ACCOUNT_ACCOUNT')];
				$newsa[C('DB_SELLER_ACCOUNT_PASSWORD')] = $_POST[C('DB_SELLER_ACCOUNT_PASSWORD')];
				$newsa[C('DB_SELLER_ACCOUNT_HOLDER_NAME')] = $_POST[C('DB_SELLER_ACCOUNT_HOLDER_NAME')];
				$newsa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = $_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')];
				$newsa[C('DB_SELLER_ACCOUNT_BANK_ID')] = $_POST[C('DB_SELLER_ACCOUNT_BANK_ID')];
				$newsa[C('DB_SELLER_ACCOUNT_ADDRESS')] = $_POST[C('DB_SELLER_ACCOUNT_ADDRESS')];
				$newsa[C('DB_SELLER_ACCOUNT_TEL')] = $_POST[C('DB_SELLER_ACCOUNT_TEL')];
				$newsa[C('DB_SELLER_ACCOUNT_STATUS')] = $_POST[C('DB_SELLER_ACCOUNT_STATUS')];
				$newsa[C('DB_SELLER_ACCOUNT_QUESTION1')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION1')];
				$newsa[C('DB_SELLER_ACCOUNT_ANSWER1')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER1')];
				$newsa[C('DB_SELLER_ACCOUNT_QUESTION2')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION2')];
				$newsa[C('DB_SELLER_ACCOUNT_ANSWER2')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER2')];
				$newsa[C('DB_SELLER_ACCOUNT_QUESTION3')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION3')];
				$newsa[C('DB_SELLER_ACCOUNT_ANSWER3')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER3')];
				$newsa[C('DB_SELLER_ACCOUNT_REMARK')] = $_POST[C('DB_SELLER_ACCOUNT_REMARK')];
				$newsa[C('DB_SELLER_ACCOUNT_IP')] = $_POST[C('DB_SELLER_ACCOUNT_IP')];
				$newsa[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')] = $_POST[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')];
				$paypal_seller[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = $_POST[C('DB_PAYPAL_SELLER_ACCOUNT_PID')];
				$paypal_seller[C('DB_PAYPAL_SELLER_ACCOUNT_SAID')] = M(C('DB_SELLER_ACCOUNT'))->add($newsa);
				M(C('DB_PAYPAL_SELLER_ACCOUNT'))->add($paypal_seller);
				$this->redirect('sellerAccount');			
			}
		}else{
			if($this->checkSellerDataWithMainAccount($_POST)){
				if($this->isSellerAccountUsedEmail($_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')])==null && !$this->isPaypalUsedEmail($_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')])){
					$newsa[C('DB_SELLER_ACCOUNT_PLATFORM')] = $_POST[C('DB_SELLER_ACCOUNT_PLATFORM')];
					$newsa[C('DB_SELLER_ACCOUNT_MACCOUNT')] = $_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')];
					$newsa[C('DB_SELLER_ACCOUNT_ACCOUNT')] = $_POST[C('DB_SELLER_ACCOUNT_ACCOUNT')];
					$newsa[C('DB_SELLER_ACCOUNT_PASSWORD')] = $_POST[C('DB_SELLER_ACCOUNT_PASSWORD')];
					$newsa[C('DB_SELLER_ACCOUNT_HOLDER_NAME')] = $_POST[C('DB_SELLER_ACCOUNT_HOLDER_NAME')];
					$newsa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = $_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')];
					$newsa[C('DB_SELLER_ACCOUNT_BANK_ID')] = $_POST[C('DB_SELLER_ACCOUNT_BANK_ID')];
					$newsa[C('DB_SELLER_ACCOUNT_ADDRESS')] = $_POST[C('DB_SELLER_ACCOUNT_ADDRESS')];
					$newsa[C('DB_SELLER_ACCOUNT_TEL')] = $_POST[C('DB_SELLER_ACCOUNT_TEL')];
					$newsa[C('DB_SELLER_ACCOUNT_STATUS')] = $_POST[C('DB_SELLER_ACCOUNT_STATUS')];
					$newsa[C('DB_SELLER_ACCOUNT_QUESTION1')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION1')];
					$newsa[C('DB_SELLER_ACCOUNT_ANSWER1')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER1')];
					$newsa[C('DB_SELLER_ACCOUNT_QUESTION2')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION2')];
					$newsa[C('DB_SELLER_ACCOUNT_ANSWER2')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER2')];
					$newsa[C('DB_SELLER_ACCOUNT_QUESTION3')] = $_POST[C('DB_SELLER_ACCOUNT_QUESTION3')];
					$newsa[C('DB_SELLER_ACCOUNT_ANSWER3')] = $_POST[C('DB_SELLER_ACCOUNT_ANSWER3')];
					$newsa[C('DB_SELLER_ACCOUNT_REMARK')] = $_POST[C('DB_SELLER_ACCOUNT_REMARK')];
					$newsa[C('DB_SELLER_ACCOUNT_IP')] = $_POST[C('DB_SELLER_ACCOUNT_IP')];
					$paypal_seller[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = $_POST[C('DB_PAYPAL_SELLER_ACCOUNT_PID')];
					$paypal_seller[C('DB_PAYPAL_SELLER_ACCOUNT_SAID')] = M(C('DB_SELLER_ACCOUNT'))->add($newsa);
					M(C('DB_PAYPAL_SELLER_ACCOUNT'))->add($paypal_seller);
					$this->redirect('sellerAccount');
				}else{
					$this->error('邮箱已被使用！');
				}
			}
		}
		
	}

	private function checkSellerDataWithMainAccount($seller){
		$msa=M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ID')=>$_POST[C('DB_SELLER_ACCOUNT_MACCOUNT')]))->find();
		$msa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]=M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_SAID')=>$msa[C('DB_SELLER_ACCOUNT_ID')]))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_PID'));
		if($msa[C('DB_SELLER_ACCOUNT_TEL')]!=$seller[C('DB_SELLER_ACCOUNT_TEL')]){
			$this->error('有主账号的新账号电话可以跟主账号一致！');
			return false;
		}elseif($msa[C('DB_SELLER_ACCOUNT_BANK_ID')]!=$seller[C('DB_SELLER_ACCOUNT_BANK_ID')]){
			$this->error('有主账号的新账号银行可以跟主账号一致！');
			return false;
		}elseif($msa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]!=$seller[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]){
			$this->error('有主账号的新账号paypal可以跟主账号一致！');
			return false;
		}else{
			return true;
		}

	}

	private function checkSellerData($seller){
		if($this->isSellerAccountUsedEmail($seller[C('DB_SELLER_EMAIL_ID')])!=null){
			$this->error('邮箱已经在平台上使用过！');
			return false;
		}elseif($this->isPaypalUsedEmail($seller[C('DB_SELLER_EMAIL_ID')])){
			$this->error('邮箱已经在paypal上使用过！');
			return false;
		}elseif($this->isUsedSellerTel($seller[C('DB_SELLER_ACCOUNT_TEL')],$seller[C('DB_SELLER_ACCOUNT_PLATFORM')])){
			$this->error('电话已经在该平台上使用过！');
			return false;
		}elseif($this->isUsedSellerBank($seller[C('DB_SELLER_ACCOUNT_BANK_ID')],$seller[C('DB_SELLER_ACCOUNT_PLATFORM')])){
			$this->error('银行账号已经在该平台上使用过！');
			return false;
		}elseif(M(C('DB_BANK'))->where(array(C('DB_BANK_PID')=>$seller[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]))->getField(C('DB_BANK_HOLDER_NAME'))!=$seller[C('DB_SELLER_ACCOUNT_HOLDER_NAME')] && $seller[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')]==1){
			$this->error('关联的paypal银行账号持有人与销售账号持有人不一致！');
			return false;
		}elseif(!$this->haveAccountPaypalBankSameHolder($seller[C('DB_SELLER_ACCOUNT_BANK_ID')],$seller[C('DB_PAYPAL_SELLER_ACCOUNT_PID')],$seller[C('DB_SELLER_ACCOUNT_HOLDER_NAME')])){
			$this->error('paypal和银行持有人不一致！');
			return false;
		}else{
			return true;
		}		
	}

	private function usedSellerAccount($sellerAccount){
		if(M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ACCOUNT')=>$sellerAccount))->find() == null){
			return false;
		}else{
			return true;
		}
	}

	private function isUsedSellerTel($tel,$platform){
		$map1[C('DB_SELLER_ACCOUNT_TEL')]=array('eq',$tel);
		$map1[C('DB_SELLER_ACCOUNT_PLATFORM')]=array('like','%'.explode('.', $platform)[0].'%');
		$map2[C('DB_SELLER_ACCOUNT_PLATFORM')]=array('like','%'.explode('.', $platform)[0].'%');
		$map2[C('DB_SELLER_ACCOUNT_USED_TEL')]=array('like','%'.$tel.'%');
		$map['_complex'] = array($map1, $map2, '_logic' => 'or');
		if(M(C('DB_SELLER_ACCOUNT'))->where($map)->find() == null){
			return false;
		}else{
			return true;
		}
	}

	private function isUsedSellerBank($bank_id,$platform){
		$map1[C('DB_SELLER_ACCOUNT_BANK_ID')]=array('eq',$bank_id);
		$map1[C('DB_SELLER_ACCOUNT_PLATFORM')]=array('like','%'.explode('.', $platform)[0].'%');
		$map2[C('DB_SELLER_ACCOUNT_PLATFORM')]=array('like','%'.explode('.', $platform)[0].'%');
		$map2[C('DB_SELLER_ACCOUNT_USED_BID')]=array('like','%'.$bank_id.'%');
		$map['_complex'] = array($map1, $map2, '_logic' => 'or');
		if(M(C('DB_SELLER_ACCOUNT'))->where($map)->find() == null){
			return false;
		}else{
			return true;
		}
	}

	private function isUsedSellerPaypal($paypal_id){
		$map1[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]=array('eq',$paypal_id);
		$map2[C('DB_SELLER_ACCOUNT_USED_PID')]=array('like','%'.$paypal_id.'%');
		$map['_complex'] = array($map1, $map2, '_logic' => 'or');
		if(M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where($map)->find() == null){
			return false;
		}else{
			return true;
		}
	}

	private function haveAccountPaypalBankSameHolder($bank_id,$paypal_id, $holderName){
		if($bank_id!=0 && $paypal_id!=0){
			$bankHolderName = M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$bank_id))->getField(C('DB_BANK_HOLDER_NAME'));

			$paypalBankHolderName = M(C('DB_BANK'))->where(array(C('DB_BANK_PID')=>$paypal_id))->getField(C('DB_BANK_HOLDER_NAME'));
			if($bankHolderName==$paypalBankHolderName && $paypalBankHolderName==$holderName){
				return true;
			}else{
				return false;
			}
		}elseif($bank_id==0 && $paypal_id!=0){
			$paypalBankHolderName = M(C('DB_BANK'))->where(array(C('DB_BANK_PID')=>$paypal_id))->getField(C('DB_BANK_HOLDER_NAME'));
			if($paypalBankHolderName==$holderName){
				return true;
			}else{
				return false;
			}			
		}elseif($bank_id!=0 && $paypal_id==0){
			$bankHolderName = M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$bank_id))->getField(C('DB_BANK_HOLDER_NAME'));
			if($bankHolderName==$holderName){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}

	public function updateSellerAccount(){
		$sa = M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_ID')=>$_POST[C('DB_SELLER_ACCOUNT_ID')]))->find();
		$sa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_SAID')=>$_POST[C('DB_SELLER_ACCOUNT_ID')]))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_PID'));
		if($this->checkUpdatedSellerAccount($sa, $_POST)!=null){
			$this->error($this->checkUpdatedSellerAccount($sa, $_POST));
		}else{
			if($sa[C('DB_SELLER_ACCOUNT_ACCOUNT')]!=$_POST[C('DB_SELLER_ACCOUNT_ACCOUNT')]){
				$sa[C('DB_SELLER_ACCOUNT_ACCOUNT')] = $_POST[C('DB_SELLER_ACCOUNT_ACCOUNT')];
				$sa[C('DB_SELLER_ACCOUNT_USED_ACCOUNT')] = $sa[C('DB_SELLER_ACCOUNT_USED_ACCOUNT')] .'|'.$_POST[C('DB_SELLER_ACCOUNT_ACCOUNT')];
			}
			if($sa[C('DB_SELLER_ACCOUNT_BANK_ID')] != $_POST[C('DB_SELLER_ACCOUNT_BANK_ID')]){
				$sa[C('DB_SELLER_ACCOUNT_BANK_ID')] = $_POST[C('DB_SELLER_ACCOUNT_BANK_ID')];
				$sa[C('DB_SELLER_ACCOUNT_USED_BID')] = $sa[C('DB_SELLER_ACCOUNT_USED_BID')] .'|'.$_POST[C('DB_SELLER_ACCOUNT_USED_BID')];
			}
			if($sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] != $_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')]){
				$sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = $_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')];
				$sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] = $sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')] .'|'.$_POST[C('DB_SELLER_ACCOUNT_EMAIL_ID')];
				$mailPid = M(C('DB_SELLER_EMAIL'))->where(array(C('DB_SELLER_EMAIL_ID')=>$sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')]))->getField(C('DB_SELLER_EMAIL_PID'));
				$mailRelatedSeller = M(C('DB_SELLER_ACCOUNT'))->where(array(C('DB_SELLER_ACCOUNT_EMAIL_ID')=>$sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')]))->find();
				if($mailPid==0 && $mailRelatedSeller==null){
					$oldMail[C('DB_SELLER_EMAIL_ID')] = $sa[C('DB_SELLER_ACCOUNT_EMAIL_ID')];
					$oldMail[C('DB_SELLER_EMAIL_STATUS')] = 0;
					M(C('DB_SELLER_EMAIL'))->save($oldMail);
				}
			}
			
			if($sa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] != $_POST[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]){
				$oldPaypal = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_PID')=>$sa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]))->find();
				M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_ID')=>$oldPaypal[C('DB_PAYPAL_SELLER_ACCOUNT_ID')]))->delete();
				
				$newPaypal[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = $_POST[C('DB_PAYPAL_SELLER_ACCOUNT_PID')];
				$newPaypal[C('DB_PAYPAL_SELLER_ACCOUNT_SAID')] = $_POST[C('DB_SELLER_ACCOUNT_ID')];
				M(C('DB_PAYPAL_SELLER_ACCOUNT'))->add($newPaypal);
				$sa[C('DB_SELLER_ACCOUNT_USED_PID')] = $sa[C('DB_SELLER_ACCOUNT_USED_PID')] .'|'.$_POST[C('DB_PAYPAL_SELLER_ACCOUNT_PID')];
				$map[C('DB_SELLER_EMAIL_PID')] = array('eq',$sa[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]);
				$map[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',2);
				$relatedMailId = M(C('DB_SELLER_EMAIL'))->where($map)->getField(C('DB_SELLER_EMAIL_ID'));
				$sa[C('DB_SELLER_ACCOUNT_USED_PMAIL_ID')] = $sa[C('DB_SELLER_ACCOUNT_USED_PMAIL_ID')].'|'.$relatedMailId;
			}
			if($sa[C('DB_SELLER_ACCOUNT_IP')] != $_POST[C('DB_SELLER_ACCOUNT_IP')]){
				$sa[C('DB_SELLER_ACCOUNT_IP')] = $_POST[C('DB_SELLER_ACCOUNT_IP')];
			}
			$sa[C('DB_SELLER_ACCOUNT_STATUS')] = $_POST[C('DB_SELLER_ACCOUNT_STATUS')];
			$sa[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')] = $_POST[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')];
			M(C('DB_SELLER_ACCOUNT'))->save($sa);
			$this->redirect('sellerAccount');
		}
	}

	private function checkUpdatedSellerAccount($old,$new){
		$old[C('DB_PAYPAL_SELLER_ACCOUNT_PID')] = M(C('DB_PAYPAL_SELLER_ACCOUNT'))->where(array(C('DB_PAYPAL_SELLER_ACCOUNT_SAID')->$old[C('DB_SELLER_ACCOUNT_ID')]))->getField(C('DB_PAYPAL_SELLER_ACCOUNT_PID'));
		if($old[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]!=$new[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]){
			$relatedBankHolderName = M(C('DB_BANK'))->where(array(C('DB_BANK_PID')=>$new[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]))->getField(C('DB_BANK_HOLDER_NAME'));
			if($relatedBankHolderName != $new[C('DB_SELLER_ACCOUNT_HOLDER_NAME')] && $new[C('DB_SELLER_ACCOUNT_SAME_PHOLDER')]==1){
				return '关联的paypal银行账号持有人与销售账号持有人不一致！';	
			}		
		}
		if($old[C('DB_SELLER_ACCOUNT_BANK_ID')]!=$new[C('DB_SELLER_ACCOUNT_BANK_ID')] && $this->isUsedSellerBank($new[C('DB_SELLER_ACCOUNT_BANK_ID')])){
			return '银行已被别的账号关联';
		}
		if(M(C('DB_BANK'))->where(array(C('DB_BANK_ID')=>$new[C('DB_SELLER_ACCOUNT_BANK_ID')]))->getField(C('DB_BANK_HOLDER_NAME'))!=$new[C('DB_SELLER_ACCOUNT_HOLDER_NAME')]){
			return '关联银行与销售账号持有人不一致！';
		}
		if($old[C('DB_SELLER_ACCOUNT_EMAIL_ID')]!=$new[C('DB_SELLER_ACCOUNT_EMAIL_ID')]){
			if($this->isSellerAccountUsedEmail($new[C('DB_SELLER_ACCOUNT_EMAIL_ID')])){
				return '邮箱已被别的销售账号关联';
			}
			if($this->isPaypalUsedEmail($new[C('DB_SELLER_ACCOUNT_EMAIL_ID')])){
				$map[C('DB_SELLER_EMAIL_PID')] = array('eq',$new[C('DB_PAYPAL_SELLER_ACCOUNT_PID')]);
				$map[C('DB_SELLER_EMAIL_POFFSET')] = array('eq',2);
				$paypal_memail_id = M(C('DB_SELLER_EMAIL'))->where($map)->getField(C('DB_SELLER_EMAIL_ID'));
				if($new[C('DB_SELLER_ACCOUNT_EMAIL_ID')]!=$paypal_memail_id){
					return '邮箱已被别的paypal账号关联';
				}				
			}
		}
		return null;
	}

}

?>