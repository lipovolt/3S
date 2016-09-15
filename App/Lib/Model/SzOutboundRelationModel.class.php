<?php

Class SzOutboundRelationModel extends RelationModel{

	//定义主表名称
	Protected $tableName = 'sz_outbound';

	//定义关联关系
	public $_link = array(
	    		'sz_outbound_item'=> array(  
	     				'mapping_type'=> self::HAS_MANY,
                     	'foreign_key'=>'id',
                     	'parent_key'=>'outbound_id',
                     	'mapping_name'=>'items'
                     	),

    );


}


?>