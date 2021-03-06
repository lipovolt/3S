<?php

class CommonAction extends Action{

	public function _initialize(){
		if (!isset($_SESSION[C('USER_AUTH_KEY')])){
			$this->redirect(U('Index/Index/index'));
		}

		$notAuth = in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, explode(',',C('NOT_AUTH_ACTION')));

		if(C('USER_AUTH_ON') && !$notAuth){
			import('ORG.Util.RBAC');
			RBAC::AccessDecision(GROUP_NAME) || $this->error('没有权限');
		}

        if(I('session.username',0)=='张旻'){
            $this->assign('todoQuantity',M(C('DB_TODO'))->where(array(array(C('DB_TODO_PERSON')=>'Jade'),array(C('DB_TODO_STATUS')=>0)))->count());
        }elseif(I('session.username',0)=='admin'){
            $map[C('DB_TODO_PERSON')] = array('in',array('Yellow River','张昱'));
            $map[C('DB_TODO_STATUS')] = array('eq',0);
            $this->assign('todoQuantity',M(C('DB_TODO'))->where($map)->count());
        }else{
            $this->assign('todoQuantity',M(C('DB_TODO'))->where(array(array(C('DB_TODO_PERSON')=>C('PRODUCT_MANAGER_ENAME')[I('session.username',0)]),array(C('DB_TODO_STATUS')=>0)))->count());
        }
	}

	public function exportExcel($expTitle,$expCellName,$expTableData){
        $fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($i=0;$i<$cellNum;$i++){
          $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]); 
        } 
        // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
          }
        }  
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }

    public function exportMoreSheetsExcel($expTitle,$expCellNameArr,$expTableDataArr){
        $fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
        for($sheetNum=0;$sheetNum<count($expCellNameArr);$sheetNum++){
            $objPHPExcel->createSheet($sheetNum);            
            $objPHPExcel->setActiveSheetIndex($sheetNum);

            $cellNum = count($expCellNameArr[$sheetNum]);
            $dataNum = count($expTableDataArr[$sheetNum]);

            for($i=0;$i<$cellNum;$i++){
              $objPHPExcel->getActiveSheet()->setCellValue($cellName[$i].'1', $expCellNameArr[$sheetNum][$i][1]); 
            } 
            // Miscellaneous glyphs, UTF-8   
            for($i=0;$i<$dataNum;$i++){
              for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet()->setCellValue($cellName[$j].($i+2), $expTableDataArr[$sheetNum][$i][$expCellNameArr[$sheetNum][$j][0]]);
              }
            } 
        }
         
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }

    public function toTextSku($sku){
        if(strlen($sku)==6 && substr($sku, 4,1)=='.' && (substr($sku, 5,1)==1 || substr($sku, 5,1)==2 || substr($sku, 5,1)==3 || substr($sku, 5,1)==4)){
            return $sku.'0';
        }else{
            return $sku;
        }
    }

    public function fbaSkuToStandardSku($fbaSku){
        if(count(explode('FBA_', $fbaSku))==1){
            return $fbaSku;
        }else{
            return explode('FBA_', $fbaSku)[1];
        }       
    }

    public function isFBASku($sku){
        if(count(explode('FBA_', $sku))==1){
            return false;
        }else{
            return true;
        }   
    }

    public function getMaxKeyOfArray($arr){
        end($arr);
        return key($arr);
    }


    public function exportAmazonFileExchangeExcel($expTitle,$expCellName,$expTableData){
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
            for($j=0;$j<$cellNum;$j++){
                if($i>=0 && $expTableData[$i][$expCellName[2]] !=null && $expTableData[$i][$expCellName[1]]!=$expTableData[$i][$expCellName[2]]){
                    $objPHPExcel->getActiveSheet()->getStyle( 'B'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle( 'B'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
                    $objPHPExcel->getActiveSheet()->getStyle( 'C'.($i+2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle( 'C'.($i+2))->getFill()->getStartColor()->setARGB('FF808080');
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

    public function generateUPC(){
        $usedUpc = M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->getField(C('DB_METADATA_USED_UPC'));
        $num=str_split($usedUpc);
        if((int)$num[10]==9){
            $oddSum = (int)$num[0]+(int)$num[2]+(int)$num[4]+(int)$num[6]+(int)$num[8];
            $evenSum = (int)$num[1]+(int)$num[3]+(int)$num[5]+(int)$num[7]+(int)$num[9]+1;
        }else{
            $oddSum = (int)$num[0]+(int)$num[2]+(int)$num[4]+(int)$num[6]+(int)$num[8]+(int)$num[10]+1;
            $evenSum = (int)$num[1]+(int)$num[3]+(int)$num[5]+(int)$num[7]+(int)$num[9];
        }        
        $sum = $oddSum*3+$evenSum;
        $verifyNumber = (10-round($sum%10))==10?0:(10-round($sum%10));
        if($verifyNumber==0){;
            $upc = (substr($usedUpc, 0,11)+1)*10;
        }else{
            $upc = (substr($usedUpc, 0,11)+1)*10+$verifyNumber;
        }
        M(C('DB_METADATA'))->where(array(C('DB_METADATA_ID')=>1))->setField(C('DB_METADATA_USED_UPC'),$upc);
        return $upc;
    }

    public function getUsswSalePlanTableNames(){
        return array(
            array('platform'=>'ebay','account'=>'greatgoodshop','sale_table'=>C('DB_USSW_SALE_PLAN')),
            array('platform'=>'amazon','account'=>'lipovolt','sale_table'=>C('DB_USSW_SALE_PLAN2')),
            array('platform'=>'groupon','account'=>'lipovolt','sale_table'=>C('DB_USSW_SALE_PLAN3')),
            array('platform'=>'ebay','account'=>'blackfive','sale_table'=>C('DB_USSW_SALE_PLAN4'))
        );
    }

    public function getWinitDeSalePlanTableNames(){
        return array(
            array('platform'=>'ebay','account'=>'yzhan-816','sale_table'=>C('DB_YZHAN_816_PL_SALE_PLAN')),
            array('platform'=>'amazon','account'=>'shangsitech@qq.com','sale_table'=>C('DB_WINIT_DE_AMAZON_SALE_PLAN'))
        );
    }

    public function getSzswSalePlanTableNames(){
        return array(
            array('platform'=>'ebay','account'=>'rc-helicar','sale_table'=>C('DB_RC_DE_SALE_PLAN'))
        );
    }

    public function getStorageTableName($warehouse){
        switch ($warehouse) {
            case 'ussw':
                return C('DB_USSTORAGE');
                break;
            case '美自建仓':
                return C('DB_USSTORAGE');
                break;
            case 'winitde':
                return C('DB_WINIT_DE_STORAGE');
                break;
            case '万邑通德国':
                return C('DB_WINIT_DE_STORAGE');
                break;
            case 'szsw':
                return C('DB_SZSTORAGE');
                break;
            case '深圳仓':
                return C('DB_SZSTORAGE');
                break;
            default:
                return null;
                break;
        }
    }

    public function getInboundViewTableName($warehouse){
        switch ($warehouse) {
            case 'ussw':
                return 'UsswInboundView';
                break;
            case '美自建仓':
                return 'UsswInboundView';
                break;
            case 'winitde':
                return 'WinitdeInboundView';
                break;
            case '万邑通德国':
                return 'WinitdeInboundView';
                break;
            default:
                return null;
                break;
        }
    }

    public function getInboundTableName($warehouse){
        switch ($warehouse) {
            case 'ussw':
                return C('DB_USSW_INBOUND');
                break;
            case '美自建仓':
                return C('DB_USSW_INBOUND');
                break;
            case 'winitde':
                return C('DB_WINITDE_INBOUND');
                break;
            case '万邑通德国':
                return C('DB_WINITDE_INBOUND');
                break;
            default:
                return null;
                break;
        }
    }

    public function getOutboundViewTableName($warehouse){
        switch ($warehouse) {
            case 'ussw':
                return 'UsswOutboundView';
                break;
            case '美自建仓':
                return 'UsswOutboundView';
                break;
            case '美国FBA':
                return 'UsFBAOutboundView';
                break;
            case 'winitde':
                return 'WinitOutboundView';
                break;
            case '万邑通德国':
                return 'WinitOutboundView';
                break;
            default:
                return null;
                break;
        }
    }

    public function getCountry($warehouse){
        switch ($warehouse) {
            case 'ussw':
                return 'us';
                break;
            case '美自建仓':
                return 'us';
                break;
            case 'winitde':
                return 'de';
                break;
            case '万邑通德国':
                return 'de';
                break;
            default:
                return null;
                break;
        }
    }

    public function getSalePlanTableNames($warehouse){
        if($this->getCountry($warehouse)=='us'){
            return $this->getUsswSalePlanTableNames();
        }elseif($this->getCountry($warehouse)=='de'){
            return $this->getWinitDeSalePlanTableNames();
        }
    }


    /*
    *decode ali-retail sku to standard sku. Ali-retail sku formate AR1001C： AR prefix, 1001 main sku, c 03
    */
    public function ARSKUDecode($sku){
        if(!is_numeric(substr($sku, 0,1)) && (substr($sku, 0,2)=='AR' || substr($sku, 0,2)=='ar') && is_numeric(substr($sku, 2,1)) && is_numeric(substr($sku, 3,1)) && is_numeric(substr($sku, 4,1)) && is_numeric(substr($sku, 5,1))){
            //Ali-Retail账号, sku格式AR1001 or AR1001A=>1001.01
            $skuExplodArr = explode('|', $sku);
            foreach ($skuExplodArr as $seakey => $seavalue) {
                $qtyExplodeArr = explode('*', $seavalue);
                if($this->checkReplaceAliRetailSku($qtyExplodeArr[0])!=null){
                    if(count($qtyExplodeArr)>1){
                        if($decodedSku!=null){
                            $decodedSku = $decodedSku.'|'.$this->checkReplaceAliRetailSku($qtyExplodeArr[0]).'*'.$qtyExplodeArr[1];
                        }else{
                            $decodedSku = $this->checkReplaceAliRetailSku($qtyExplodeArr[0]).'*'.$qtyExplodeArr[1];
                        }                    
                    }else{
                        if($decodedSku!=null){
                            $decodedSku = $decodedSku.'|'.$this->checkReplaceAliRetailSku($qtyExplodeArr[0]);
                        }else{
                            $decodedSku = $this->checkReplaceAliRetailSku($qtyExplodeArr[0]);
                        }
                    }
                }else{
                    return $sku;//无法解析，直接返回原sku
                }                
            }
            return $decodedSku;            
        }
        return $sku;//不是ali-retail sku 格式，直接返回原sku
    }

    public function isARSKUFormat($sku){
        if(!is_numeric(substr($sku, 0,1)) && (substr($sku, 0,2)=='AR' || substr($sku, 0,2)=='ar') && is_numeric(substr($sku, 2,1)) && is_numeric(substr($sku, 3,1)) && is_numeric(substr($sku, 4,1)) && is_numeric(substr($sku, 5,1))){
            return true;
        }else{
            return false;
        }
    }

    public function isStandardSkuFormat($sku){
        if(is_numeric(substr($sku, 0,1)) && is_numeric(substr($sku, strlen($sku)-1,1))){
            //sku首尾字母都是数字，标准不需要处理，直接返回
            return true;
        }else{
            return false;
        }
    }

    public function skuDecode($sku){
        if($this->isStandardSkuFormat($sku)){
            //sku首尾字母都是数字，标准不需要处理，直接返回
            return $sku;
        }
        if($this->isARSKUFormat($sku)){
            //Ali-Retail账号, sku格式AR1001 or AR1001A=>1001.01
            return $this->ARSKUDecode($sku);
            
        }elseif(!is_numeric(substr($sku, 0,1)) && is_numeric(substr($sku, strlen($sku)-1,1))){
            if(substr($sku, 0,1)=='S' && strlen($sku)==7){
                //孙培华账号,sku格式S100101
                return substr($sku, 1,strlen($sku));
            }elseif(substr($sku, 0,3)=='SZL' && strlen($sku)==9){
                //孙志磊账号,sku格式SZL100101
                return substr($sku,3,strlen($sku));
            }elseif(substr($sku, 0,4)=='FBA_'){
                //标准FBA SKU格式,sku格式FBA_1001.01
                return substr($sku,4,strlen($sku));
            }
        }elseif(is_numeric(substr($sku, 0,1)) && !is_numeric(substr($sku, strlen($sku)-1,1))){
            if(substr($sku, strlen($sku),1)=='Z'){
                //郑德杰账号，sku格式100101Z
                return substr($sku,0,strlen($sku)-1);
            }elseif(substr($sku, strlen($sku)-4,4)=='_gen'){
                //标准跟卖格式，sku格式1001.0_gen
                return substr($sku,0,strlen($sku)-4);
            }
        }
        return null;
    }

    private function checkReplaceAliRetailSku($sku){
        if(strlen($sku)==6){
            return substr($sku, 2,strlen($sku));
        }elseif(strlen($sku)>6){
            for ($i=6; $i < strlen($sku); $i++) { 
                if(is_numeric(substr($sku,$i,1))){
                    return null;
                }
            }
            return substr($sku, 2,4).'.'.$this->ABC2decimal(substr($sku, 6,strlen($sku)-6));
        }
    }

    //字母（26）进制转10进制
    private function ABC2decimal($abc){
        $ten = 26*(strlen($abc)-1)+1;
        $len = strlen($abc);
        for($i=1;$i<=$len;$i++){
            $char = substr($abc,0-$i,1);//反向获取单个字符

            $int = ord(strtoupper($char));
            $ten += ($int-65)*pow(26,$i-1);
        }
        if($ten<10){
            return '0'.$ten;
        }else{
            return $ten;
        }
    }

    /*
    *根据 | 和 * 分解标准sku
    */
    public function splittStandardSku($sku){
        $splittedSku=array();
        $skuDepart = explode("|",$sku);
        foreach ($skuDepart as $key => $value) {
            $skuQuantityDepart = explode("*",$value);
            array_push($splittedSku, array('sku'=>$skuQuantityDepart[0],'qty'=>$skuQuantityDepart[1]==null?1:$skuQuantityDepart[1]));
        }
        return $splittedSku;
    }
}

?>