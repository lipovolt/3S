<?php

Class WinitOutboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'winit_outbound'=>array('id','create_time'),
    	'winit_outbound_item'=>array('sku'=>'sku','quantity'=>'quantity','_on'=>'winit_outbound.id=winit_outbound_item.outbound_id'),
    );


}


?>