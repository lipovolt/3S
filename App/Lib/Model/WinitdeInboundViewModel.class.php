<?php

Class WinitdeInboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'winitde_inbound'=>array('id','status','date','receive_date'),
    	'winitde_inbound_item'=>array('sku'=>'sku','declare_quantity'=>'declare_quantity','confirmed_quantity'=>'confirmed_quantity','_on'=>'winitde_inbound.id=winitde_inbound_item.inbound_id'),
    );


}


?>