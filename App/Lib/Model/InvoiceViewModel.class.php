<?php

Class InvoiceViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'restock'=>array('id','sku','quantity','_type'=>'LEFT'),
    	'product'=>array('ename'=>'ename','price'=>'price', '_on'=>'restock.sku=product.sku'),
    );


}


?>