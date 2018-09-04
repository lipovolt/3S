<?php

/**
*递归重组节点数组为多维数组
*@param [typ] $node [要处理的节点数组]
*@param interger $pid [父级id]
*@return [typ] [description]
*/
function node_merge ($node, $access=null, $pid=0){
	$arr = array();

	foreach ($node as $key => $value) {
		if(is_array($access)){
			$value[C('DB_ACCESS')] = in_array($value[C('DB_NODE_ID')],$access)?1:0;
		}
		if($value[C('DB_NODE_PID')] == $pid){
			$value['child'] = node_merge($node,	$access, $value[C('DB_NODE_ID')]);
			$arr[] = $value;
		}
	}

	return $arr;
}

function p ($array) {
    dump($array, 1, '<pre>', 0);
}



/** * 生成pdf * @param string $html 需要生成的内容 */ 
function pdf($html='<h1 style="color:red">这是一个测试文件，生成pdf文件！</h1>'){ 
	vendor('tcpdf.tcpdf'); 
	$pdf = new \tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 
	// 设置打印模式 
	$pdf->SetCreator(PDF_CREATOR); $pdf->SetAuthor('Nicola Asuni'); $pdf->SetTitle('TCPDF Example 001'); 
	$pdf->SetSubject('TCPDF Tutorial'); 
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide'); 
	// 是否显示页眉 
	$pdf->setPrintHeader(false); 
	// 设置页眉显示的内容 
	$pdf->SetHeaderData('logo.png', 60, 'lipovolt.com', '', array(0,64,255), array(0,64,128)); 
	// 设置页眉字体 
	$pdf->setHeaderFont(Array('dejavusans', '', '12')); 
	// 页眉距离顶部的距离 
	$pdf->SetHeaderMargin('5'); 
	// 是否显示页脚 
	$pdf->setPrintFooter(true); 
	// 设置页脚显示的内容 
	$pdf->setFooterData("MADE IN CHINA"); 
	// 设置页脚的字体 
	$pdf->setFooterFont(Array('dejavusans', '', '10')); 
	// 设置页脚距离底部的距离 
	$pdf->SetFooterMargin('10'); 
	// 设置默认等宽字体 
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED); 
	// 设置行高 
	$pdf->setCellHeightRatio(1); 
	// 设置左、上、右的间距 
	$pdf->SetMargins('10', '10', '10'); 
	// 设置是否自动分页 距离底部多少距离时分页 
	$pdf->SetAutoPageBreak(TRUE, '15'); 
	// 设置图像比例因子 
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) { 
		require_once(dirname(__FILE__).'/lang/eng.php'); 
		$pdf->setLanguageArray($l); 
	} 
	$pdf->setFontSubsetting(true); 
	$pdf->AddPage(); 
	// 设置字体 
	$pdf->SetFont('stsongstdlight', '', 14, '', true); 
	$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true); 
	$pdf->Output('example_001.pdf', 'I'); 
}

?>