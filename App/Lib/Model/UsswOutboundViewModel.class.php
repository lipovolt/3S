<?php

Class UsswOutboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'ussw_outbound'=>array('id','create_time'),
    	'ussw_outbound_item'=>array('sku'=>'sku','quantity'=>'quantity','_on'=>'ussw_outbound.id=ussw_outbound_item.outbound_id'),
    );


}


?>