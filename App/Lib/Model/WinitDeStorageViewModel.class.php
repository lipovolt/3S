<?php

Class WinitDeStorageViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'winit_de_storage'=>array('id','position','sku','attribute','cinventory','ainventory','oinventory','iinventory','csales','remark','sale_status','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', 'weight'=>'weight', 'price'=>'price', '_on'=>'winit_de_storage.sku=product.sku'),
    );


}


?>