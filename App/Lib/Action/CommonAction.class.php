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
}

?>