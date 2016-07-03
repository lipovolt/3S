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


?>