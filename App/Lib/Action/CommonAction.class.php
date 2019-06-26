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
            $this->assign('todoQuantity',M(C('DB_TODO'))->where(array(array(C('DB_TODO_PERSON')=>'张昱'),array(C('DB_TODO_STATUS')=>0)))->count());
        }else{
            $this->assign('todoQuantity',M(C('DB_TODO'))->where(array(array(C('DB_TODO_PERSON')=>I('session.username',0)),array(C('DB_TODO_STATUS')=>0)))->count());
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
}

?>