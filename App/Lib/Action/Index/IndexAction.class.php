<?php

class IndexAction extends Action {
    public function index(){
        $this->display();
    }

    public function login(){
    	if(!IS_POST) _404('页面不存在', U('index'));

        if(I('code','','md5') != session('verify')){
            $this->error('验证码错误');
        }

        $username = I('username');
        $pwd = I('password','','md5');
        $user = M('user')->where(array('username'=>$username))->find();
        if (!$user || $user['password'] != $pwd){
            $this->error('账号或密码错误');
        }
        if($user['lock']) $this->error('用户被锁定');

        $data = array(
                'id' => $user['id'],
                'logintime' => time(),
                'loginip' => get_client_ip(),
            );
        M('user')->save($data);

        session('uid', $user['id']);
        session('username', $user['username']);
        session('logintime', date('Y-m-d H:i:s', $user['logintime']));
        session('loginip', $user['loginip']);
        $this->redirect(U('Product/Product/productInfo','','',1));

    }

    public function verify(){
    	import('ORG.Util.Image');
    	Image::buildImageVerify(1,1,'png');
    }

    public function logout(){
        session_unset();
        session_destroy();
        $this->display('index');
    }

    
}

?>