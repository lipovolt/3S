<?php

Class SzOutboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'sz_outbound'=>array('id','status'),
    	'sz_outbound_item'=>array('sku'=>'sku','quantity'=>'quantity','_on'=>'sz_outbound.id=sz_outbound_item.outbound_id'),
    );


}


?>