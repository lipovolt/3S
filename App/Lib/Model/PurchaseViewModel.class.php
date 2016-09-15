<?php

Class PurchaseViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'purchase'=>array('id','status'),
    	'purchase_item'=>array('sku'=>'sku','purchase_quantity'=>'purchase_quantity','warehouse'=>'warehouse','_on'=>'purchase.id=purchase_item.purchase_id'),
    );


}


?>