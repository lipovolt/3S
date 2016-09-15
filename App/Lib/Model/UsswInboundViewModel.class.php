<?php

Class UsswInboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'ussw_inbound'=>array('id','status'),
    	'ussw_inbound_item'=>array('sku'=>'sku','declare_quantity'=>'declare_quantity','_on'=>'ussw_inbound.id=ussw_inbound_item.inbound_id'),
    );


}


?>