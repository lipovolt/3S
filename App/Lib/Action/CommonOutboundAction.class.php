<?php

class CommonOutboundAction extends CommonAction{
    public function outboundItemsPriceUp($account, $outboundItemArr){
        foreach ($outboundItemArr as $key => $value) {
            $this->outboundItemPriceUp($account,$value['sku']);
        }
    }

    public function outboundItemPriceUp($account,$sku){
        $stable = M($this->getSalePlanTableName($account));        
        $sr=$stable->where(array(C('DB_USSW_SALE_PLAN_SKU')=>$sku))->find();
        if($sr[C('DB_USSW_SALE_PLAN_STATUS')]==1 && strtotime($sr[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')])<(time()-60*60*3)){
            $metaMap[C('DB_USSW_SALE_PLAN_METADATA_ID')] = array('eq',1);
            $pcr = M(C('DB_USSW_SALE_PLAN_METADATA'))->where($metaMap)->getField(C('DB_USSW_SALE_PLAN_METADATA_PCR'));
            $country = $this->getItemLocationCountry($account);
            $ptable = M(C('DB_PRODUCT'));
            $rtable = M(C('DB_RESTOCK'));
            $p = $ptable->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
            $pweight = $p[C('DB_PRODUCT_PWEIGHT')]<1?$p[C('DB_PRODUCT_WEIGHT')]:$p[C('DB_PRODUCT_PWEIGHT')];
            if($country=='us'){
                $pToCountry = $p[C('D_PRODUCT_TOUS')];
            }elseif($country=='de'){
                $pToCountry = $p[C('D_PRODUCT_TODE')];
            }else{
                $pToCountry = null;
            }
            $startDate = date('Y-m-d H:i:s');
            $endDate = date('Y-m-d H:i:s',time()-60*60*24*60);
            $rmap[C('DB_RESTOCK_SHIPPING_DATE')] = array('between',array($startDate,$endDate));
            $rmap[C('DB_RESTOCK_TRANSPORT')] = array('eq','海运');
            $rmap[C('DB_RESTOCK_STATUS')] = array('eq','已发货');
            $rmap[C('DB_RESTOCK_SKU')] = array('eq',$this->fbaSkuToStandardSku($sku));
            if($country=='us'){
               $rmap[C('DB_RESTOCK_WAREHOUSE')] = array('in',array('万邑通美西','美自建仓'));
            }elseif($country=='de'){
                $rmap[C('DB_RESTOCK_WAREHOUSE')] = array('eq',array('万邑通德国'));
            }
            $seaToCountryIn2Month = $rtable->where($rmap)->find();
            $profitRate = ($sr[C('DB_USSW_SALE_PLAN_PRICE')]-$sr[C('DB_USSW_SALE_PLAN_COST')])/$sr[C('DB_USSW_SALE_PLAN_PRICE')];

            $storage=M($this->getStorageTableName($account))->where(array(C('DB_USSTORAGE_SKU')=>$this->fbaSkuToStandardSku($sku)))->find();
            $aiquantity = $storage[C('DB_USSTORAGE_AINVENTORY')] + $storage[C('DB_USSTORAGE_IINVENTORY')] ;

            /* 
                检测每天上传的美国仓出库单。
                1.  利润率低于百分之12  涨价
                2.  利润率12-18之间
                    a.  中国发货竞争激烈的产品，不调价 售价低于8美元，重量低于200g视为竞争激烈产品
                    b.  空运试算，发的是海运的重货不调价 检查上次发货的运输方式， 两个月内有海运的货，不涨价。
                    c.  高价值产品调价 大于13美元的货视为高价值产品
                    d.  库存量大的产品不调价。


                检测每天上传的德国仓出库单。
                1.  利润率低于百分之12  涨价
                2.  利润率12-18之间 涨价
                    a.  库存量大的产品不调价

            */
            if($profitRate<0.15 || (($country=='cn' || $country=='us') && $profitRate<=0.18)){
                $sr[C('DB_USSW_SALE_PLAN_PRICE')] = $sr[C('DB_USSW_SALE_PLAN_PRICE')]*(1+$pcr/100);
                $sr[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
                $stable->save($sr);
            }elseif($country=='de' && $profitRate>=0.15 && $profitRate<0.18){
                $winitDeAction = A('Sale/WinitDeSale');
                if($this->isFBASku($sku)){
                    $saleQuantity = $winitDeAction->calWinitDeSaleQuantity($account,$this->fbaSkuToStandardSku($sku),date('Y-m-d H:i:s',time()-60*60*24*15),date('Y-m-d H:i:s',time()));

                }else{
                    $saleQuantity = $winitDeAction->calWinitDeSaleQuantity($account,$sku,date('Y-m-d H:i:s',time()-60*60*24*15),date('Y-m-d H:i:s',time()));
                }
                
                if($aiquantity>0 && $aiquantity<10*$saleQuantity){
                    $sr[C('DB_USSW_SALE_PLAN_PRICE')] = $sr[C('DB_USSW_SALE_PLAN_PRICE')]+$sr[C('DB_USSW_SALE_PLAN_PRICE')]*$pcr/100;
                    $sr[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
                    $stable->save($sr);
                }
                
            }elseif($country=='us' && $profitRate>=0.18 && $profitRate<0.25){
                $ggsAction = A('Sale/GgsUsswSale');
                if($this->isFBASku($sku)){
                    $saleQuantity = $ggsAction->calUsFBASaleQuantity($account,$sku,date('Y-m-d H:i:s',time()-60*60*24*5),date('Y-m-d H:i:s',time()));

                }else{
                    $saleQuantity = $ggsAction->calUsswSaleQuantity($account,$sku,date('Y-m-d H:i:s',time()-60*60*24*5),date('Y-m-d H:i:s',time()));
                }
                if($aiquantity>0 && $aiquantity<12*$saleQuantity){

                    if($sr[C('DB_USSW_SALE_PLAN_PRICE')]>13){
                        $sr[C('DB_USSW_SALE_PLAN_PRICE')] = $sr[C('DB_USSW_SALE_PLAN_PRICE')]+$sr[C('DB_USSW_SALE_PLAN_PRICE')]*$pcr/100;
                        $sr[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
                        $stable->save($sr);
                    }elseif(($sr[C('DB_USSW_SALE_PLAN_PRICE')]>8 || $pweight>200) && (($pToCountry=='空运' && $seaToCountryIn2Month==null)|| $pToCountry=='海运')){
                        $sr[C('DB_USSW_SALE_PLAN_PRICE')] = $sr[C('DB_USSW_SALE_PLAN_PRICE')]+$sr[C('DB_USSW_SALE_PLAN_PRICE')]*$pcr/100;
                        $sr[C('DB_USSW_SALE_PLAN_LAST_MODIFY_DATE')] = date('Y-m-d H:i:s',time());
                        $stable->save($sr);
                    }
                }
            }
        }
    }

    //Return the sale plan table name according to the account
    public function getSalePlanTableName($account){
        switch ($account) {
            case 'greatgoodshop':
                return C('DB_USSW_SALE_PLAN');
                break;
            case 'lipovolt':
                return C('DB_USSW_SALE_PLAN2');
                break;
            case 'g-lipovolt':
                return C('DB_USSW_SALE_PLAN3');
                break;
            case 'blackfive':
                return C('DB_USSW_SALE_PLAN4');
                break;
            case 'yzhan-816':
                return C('DB_YZHAN_816_PL_SALE_PLAN');
                break;
            case 'rc-helicar':
                return C('DB_RC_DE_SALE_PLAN');
                break;
            case 'shangsitech@qq.com':
                return C('DB_WINIT_DE_AMAZON_SALE_PLAN');
                break;
            default:
                return null;
                break;
        }
    }

    public function getItemLocationCountry($account){
        switch ($account) {
            case 'greatgoodshop':
                return 'us';
                break;
            case 'lipovolt':
                return 'us';
                break;
            case 'g-lipovolt':
                return 'us';
                break;
            case 'blackfive':
                return 'us';
                break;
            case 'yzhan-816':
                return 'de';
                break;
            case 'rc-helicar':
                return 'cn';
                break;
            case 'shangsitech@qq.com':
                return 'de';
                break;
            default:
                return null;
                break;
        }
    }

    //Return the storage table name according to the account
    public function getStorageTableName($account){
        switch ($account) {
            case 'greatgoodshop':
                return C('DB_USSTORAGE');
                break;
            case 'vtkg5755':
                return C('DB_SZSTORAGE');
                break;
            case 'lipovolt':
                return C('DB_USSTORAGE');
                break;
            case 'g-lipovolt':
                return C('DB_USSTORAGE');
                break;
            case 'blackfive':
                return C('DB_USSTORAGE');
                break;
            case 'yzhan-816':
                return C('DB_WINIT_DE_STORAGE');
                break;
            case 'rc-helicar':
                return C('DB_SZSTORAGE');
                break;
            case 'shangsitech@qq.com':
                return C('DB_WINIT_DE_STORAGE');
                break;
            default:
                return null;
                break;
        }
    }
}

?>