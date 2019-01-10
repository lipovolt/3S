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
        $user = M(C('db_3s_user'))->where(array(C('db_3s_user_username')=>$username))->find();
        if (!$user || $user[C('db_3s_user_password')] != $pwd){
            $this->error('账号或密码错误');
        }
        if($user[C('db_3s_user_lock')]) $this->error('用户被锁定');        

        $data = array(
                C('db_3s_user_id') => $user[C('db_3s_user_id')],
                C('db_3s_user_logintime') => time(),
                C('db_3s_user_loginip') => get_client_ip(),
            );
        M(C('db_3s_user'))->save($data);

        session(C('USER_AUTH_KEY'), $user[C('db_3s_user_id')]);
        session('username', $user[C('db_3s_user_username')]);
        session('logintime', date('Y-m-d H:i:s', $user[C('db_3s_user_logintime')]));
        session('loginip', $user[C('db_3s_user_loginip')]);

        if($user[C('DB_3S_USER_USERNAME')] == C('RBAC_SUPERADMIN')){
            SESSION(C('ADMIN_AUTH_KEY'),true);
        }
        import('ORG.Util.RBAC');
        RBAC::saveAccessList();
  
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