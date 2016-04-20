<?php

class RestockAction extends CommonAction{
	public function index(){
		$restock = M(C('DB_RESTOCK'))->select();
		$this->assign('restock',$restock);
		$this->display();
	}

	public function export(){
		$xlsName  = "restock";
        $xlsCell  = array(
	        array(C('DB_RESTOCK_ID'),'补货编号'),
	        array(C('DB_RESTOCK_MANAGER'),'产品经理'),
	        array(C('DB_RESTOCK_SKU'),'产品编码'),
	        array(C('DB_RESTOCK_QUANTITY'),'数量'),
	        array(C('DB_RESTOCK_WAREHOUSE'),'仓库'),
	        array(C('DB_RESTOCK_TRANSPORT'),'运输方式'),
	        array(C('DB_RESTOCK_STATUS'),'状态'),
	        array(C('DB_RESTOCK_REMARK'),'备注')  
	        );
        $xlsModel = M(C('DB_RESTOCK'));
     	$manager = session(C('DB_3S_USER_USERNAME'));
        $xlsData  = $xlsModel->where(array(C('DB_RESTOCK_MANAGER')=>$manager))->select();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
	}
	
	public function exportExcel($expTitle,$expCellName,$expTableData){
		$fileName = $expTitle.date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
		$cellNum = count($expCellName);
		$dataNum = count($expTableData);
		vendor("PHPExcel.PHPExcel");

		$objPHPExcel = new PHPExcel();
		$cellName = array('A','B','C','D','E','F','G','H');

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