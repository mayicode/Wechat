<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/24
 * Time: 18:17
 */
class OauthAction extends Action
{
	//保存component_verify_ticket
	public function savaticket(){
//		echo 'success';
		$postStr = file_get_contents("php://input");
		//file_put_contents('text1.log',$postStr);
		//file_put_contents('text.log',json_encode($_GET)) ;
		$options = array(
			'token' => C('token'), //填写第三方的key
			'encodingaeskey' => C('encodingaeskey'), //填写第三方加密用的EncodingAESKey
			'component_appid' => C('AppID'), //填写第三方的app id
			'component_appsecret' => C('AppSecret'), //填写第三方的密钥
			'component_verify_ticket' => M('Ticket')->where('id = 1')->getField('ticket'), //component_verify_ticket
		);
		vendor('WeiXin.Mp');
		$mpObj = new Mp($options);
		$mpObj->valid();
		$infoType = $mpObj->getRev()->getInfoType();
		
		//component_verify_ticket被动获取
		if($infoType == 'component_verify_ticket'){
			$component_verify_ticket = $mpObj->getRev()->getRevComponentVerifyTicket();
			if($component_verify_ticket !=''){
				$data['old_ticket'] = M('Ticket')->where('id = 1')->getField('ticket');
				$data['ticket'] = $component_verify_ticket;
				$data['log'] = json_encode($postStr).'GET:'.json_encode($_GET);
				$data['update_time'] = date('Y-m-d H:i:s');
				M('Ticket')->where('id = 1')->save($data);
			}
			die();
		}
		
		$AuthorizerAppid = $mpObj->getRev()->getUnauthAppid();
		if(empty($AuthorizerAppid)){
			die();
		}
		//取消授权
		if($infoType == 'unauthorized'){
			//TODO::授权成功操作
		}


		//授权成功
		if($infoType == 'authorized'){
//TODO::暂无操作需求
		}
		//更新授权
		if($infoType == 'updateauthorized'){
			$AuthorizationCode = $mpObj->getRev()->getAuthorizationCode();
			$_GET['auth_code'] = $AuthorizationCode;
			$mpObj->getAuthRefreshToken();

			$authorizer_info_arr = $mpObj->getAuthAppInfo();
			if($authorizer_info_arr){
				$authorizer_info = $authorizer_info_arr['authorizer_info'];
				$updateData['authorizer_access_token'] = $mpObj->authorizer_access_token;
				$updateData['expires_in'] = $mpObj->expires_in;
				$updateData['authorizer_refresh_token'] = $mpObj->authorizer_refresh_token;
				$updateData['func_info'] = $mpObj->func_info;
				$updateData['nick_name'] = $authorizer_info['nick_name'];
				$updateData['head_img'] = $authorizer_info['head_img'];
				$updateData['service_type_info'] = $authorizer_info['service_type_info']['id'];
				$updateData['verify_type_info'] = $authorizer_info['verify_type_info']['id'];
				$updateData['user_name'] = $authorizer_info['user_name'];
				$updateData['business_info'] = json_encode($authorizer_info['business_info']);
				$updateData['alias'] = $authorizer_info['alias'];
				$updateData['qrcode_url'] = $authorizer_info['qrcode_url'];
				//TODO::公众号信息入库
			}
		}
	}
	/*
	 * 授权返回页面
	 */
	public function oauth_return(){
		//TODO::返回信息判断
		
		
		
		
		vendor('WeiXin.Mp');
		$options = array(
			'token' => C('token'), //填写第三方的key
			'encodingaeskey' => C('encodingaeskey'), //填写第三方加密用的EncodingAESKey
			'component_appid' => C('AppID'), //填写第三方的app id
			'component_appsecret' => C('AppSecret'), //填写第三方的密钥
			'component_verify_ticket' => M('Ticket')->where('id = 1')->getField('ticket'), //填写第三方的密钥
		);
		$mpObj = new Mp($options);
//		$mpObj->valid();
		$mpObj->getAuthRefreshToken();
		$authorizer_appid = $mpObj->authorizer_appid;
		if(empty($authorizer_appid)){
			$this->error('数据错误', U('/Index/index'));
		}

		//TODO::逻辑判断
		
		
		$data['authorizer_appid'] = $mpObj->authorizer_appid;
		$data['func_info'] = $mpObj->func_info;
		$data['authorizer_access_token'] = $mpObj->authorizer_access_token;
		$data['expires_in'] = $mpObj->expires_in;
		$data['authorizer_refresh_token'] = $mpObj->authorizer_refresh_token;
		//当公众号授权给开发者的权限集列表不为空的时候入库
		//TODO::公众号基本信息入库

		$authorizer_info_arr = $mpObj->getAuthAppInfo();
		if($authorizer_info_arr){
			$authorizer_info = $authorizer_info_arr['authorizer_info'];
			$updateData['authorizer_access_token'] = $mpObj->authorizer_access_token;
			$updateData['expires_in'] = $mpObj->expires_in;
			$updateData['authorizer_refresh_token'] = $mpObj->authorizer_refresh_token;
			$updateData['func_info'] = $mpObj->func_info;
			$updateData['nick_name'] = $authorizer_info['nick_name'];
			$updateData['head_img'] = $authorizer_info['head_img'];
			$updateData['service_type_info'] = $authorizer_info['service_type_info']['id'];
			$updateData['verify_type_info'] = $authorizer_info['verify_type_info']['id'];
			$updateData['user_name'] = $authorizer_info['user_name'];
			$updateData['business_info'] = json_encode($authorizer_info['business_info']);
			$updateData['alias'] = $authorizer_info['alias'];
			$updateData['qrcode_url'] = $authorizer_info['qrcode_url'];
			//TODO::公众号基本信息入库
		}

		//数据库操作
		if(!$get_authorizer_appid){
			//TODO::公众号基本信息入库
		}
		
		$this->success('授权成功！','/Index/login');
	}

	private function oauthReturnNoid(){
		vendor('WeiXin.Mp');
		$options = array(
			'token' => C('token'), //填写第三方的key
			'encodingaeskey' => C('encodingaeskey'), //填写第三方加密用的EncodingAESKey
			'component_appid' => C('AppID'), //填写第三方的app id
			'component_appsecret' => C('AppSecret'), //填写第三方的密钥
			'component_verify_ticket' => M('Ticket')->where('id = 1')->getField('ticket'), //填写第三方的密钥
		);	
		$mpObj = new Mp($options);
//		$mpObj->valid();
		$mpObj->getAuthRefreshToken();
		$authorizer_appid = $mpObj->authorizer_appid;
		
		if(empty($authorizer_appid)){
			$this->error('数据错误', U('/Index/index'));
		}

		//TODO::逻辑判断
		
		$data['authorizer_appid'] = $mpObj->authorizer_appid;
		$data['func_info'] = $mpObj->func_info;
		$data['authorizer_access_token'] = $mpObj->authorizer_access_token;
		$data['expires_in'] = $mpObj->expires_in;
		$data['authorizer_refresh_token'] = $mpObj->authorizer_refresh_token;
		//当公众号授权给开发者的权限集列表不为空的时候入库
		//TODO::入库

		$authorizer_info_arr = $mpObj->getAuthAppInfo();
		if($authorizer_info_arr){
			$authorizer_info = $authorizer_info_arr['authorizer_info'];
			$updateData['authorizer_access_token'] = $mpObj->authorizer_access_token;
			$updateData['expires_in'] = $mpObj->expires_in;
			$updateData['authorizer_refresh_token'] = $mpObj->authorizer_refresh_token;
			$updateData['func_info'] = $mpObj->func_info;
			$updateData['nick_name'] = $authorizer_info['nick_name'];
			$updateData['head_img'] = $authorizer_info['head_img'];
			$updateData['service_type_info'] = $authorizer_info['service_type_info']['id'];
			$updateData['verify_type_info'] = $authorizer_info['verify_type_info']['id'];
			$updateData['user_name'] = $authorizer_info['user_name'];
			$updateData['business_info'] = json_encode($authorizer_info['business_info']);
			$updateData['alias'] = $authorizer_info['alias'];
			$updateData['qrcode_url'] = $authorizer_info['qrcode_url'];
			//TODO::入库
		}

		session('authorizer_appid',$authorizer_appid);
		$this->success('授权成功,请注册用户绑定','/Index/register');
	}
	/*
	 * 授权页面
	 */
	public function oauth(){
		$options = array(
			'token' => C('token'), //填写第三方的key
			'encodingaeskey' => C('encodingaeskey'), //填写第三方加密用的EncodingAESKey
			'component_appid' => C('AppID'), //填写第三方的app id
			'component_appsecret' => C('AppSecret'), //填写第三方的密钥
			'component_verify_ticket' => M('Ticket')->where('id = 1')->getField('ticket'), //填写第三方的密钥
		);
		vendor('WeiXin.Mp');
		$mpObj = new Mp($options);
		$pre_auth_code = $mpObj->getPreAuthCode();

		//TODO::逻辑判断

		if (empty($pre_auth_code)) {
			$url = '';
		} elseif($jx_id) {
			$url = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=' . C('AppID') . '&pre_auth_code=' . $pre_auth_code . '&redirect_uri=' . C('oauthReturnUrl') . '?jxid=' . $jx_id;
		}else{
			$url = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=' . C('AppID') . '&pre_auth_code=' . $pre_auth_code . '&redirect_uri=' . C('oauthReturnUrl');
		}
		$this->assign('url', $url);
		$this->display('./oauth');
	}
	
}