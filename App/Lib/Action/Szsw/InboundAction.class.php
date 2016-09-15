<?php

class InboundAction extends CommonAction{

    public function simpleInbound(){
      $this->display();
    }

    public function inbound(){
        if(IS_POST){
          $str = explode('_', I('post.sku','','htmlspecialchars'));
          if($this->checkSku(I('post.sku','','htmlspecialchars')) || $str[0]=='tmp'){
            $szstorage = M(C('DB_SZSTORAGE'));
            $szstorage->starttrans();
            
            $where[C('DB_SZSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
            if(I('post.position','','htmlspecialchars') != ''){
                $where[C('DB_SZSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
            }
            $row = $szstorage->where($where)->find();
            if($row!==null && $row!==false){
                $data[C('DB_SZSTORAGE_CINVENTORY')] = $row[C('DB_SZSTORAGE_CINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                $data[C('DB_SZSTORAGE_AINVENTORY')] = $row[C('DB_SZSTORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                $result = $szstorage->where(array(C('DB_SZSTORAGE_ID')=>$row[C('DB_SZSTORAGE_ID')]))->save($data);
                $szstorage->commit();
                if(false !== $result and 0!== $result){
                    $this->success('更新库存成功！');
                }
                else{
                    $this->error('更新库存失败！');
                }
            }else{
                $data[C('DB_SZSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                if(I('post.position','','htmlspecialchars') != ''){
                  $where[C('DB_SZSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                }
                $data[C('DB_SZSTORAGE_CINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                $data[C('DB_SZSTORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                $result = $szstorage->add($data);
                $szstorage->commit();
                if($result){
                    $this->success('新产品入库成功！');
                }
                else{
                    $this->error('新产品入库失败！');
                }
            }
          }else{
              $this->error('产品编码不在产品列表，请检查');
          }
            
        } 
    }

    private function checkSku($sku){
        $result = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
        if($result != ''){
            return true;
        }
        return false;
    }

}

?>