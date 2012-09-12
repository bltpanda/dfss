<?php
/**
*执行类，程序入口
*整个程序以一定时间间隔持续刷新预约单，一直到设定的时间段检测到有可预约状态，提交预约单
*程序中调整hasFreeTime方法，可以设定一天几个预约时间段的优先级
*@author bitpanda
*/

	include("configure.php");
	include("utils.php");

	$isSuccess = false;
	
	$configure = new configure();
	$LOG = new Log();
	
	$dfss = new dfss($LOG,$configure);

	while(!$isSuccess)
	{
		if(!$dfss->isLogin)
			$dfss->login();
		if(!$dfss->isLogin)
		{
			$LOG->log("Relogin failed,sleep 1s");
			sleep(1);
			continue;
		}
		
		$dfss->accessToData();
		$LOG->log("打开约车页面");
		while(!$isSuccess && $dfss->isLogin)
		{
			$LOG->log("刷新约车页面");
			if($dfss->refreshData())
			{
				$LOG->log("判断有无空闲时间段");
				if($dfss->hasFreeTime())
				{
					$LOG->log("提交表单");
					if($dfss->postData())
					{
						$LOG->log("约车成功，退出");
						$isSuccess = true;
						break;
					}
					else
					{
						$LOG->log("约车失败，POSTERROR");	
					}
				}
				$LOG->log("无空闲时间段,3s后刷新");
			//	sleep(3);
			}
			else
			{
				//$dfss->isLogin = false;
				$LOG->log("Refresh data error,sleep 5s");
				sleep(3);
				continue;
			}
		}
		sleep(3);
	}
	
class dfss
{	
	var $isLogin = false;

	public function __construct($log = "", $configure="")
    {
        $this->isLogin = false;
		$this->configure = $configure;
		$this->LOG = $log;
		$this->init();
    }
	
	public function init()
	{
		$this->ch = curl_init();
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->configure->header);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0');
		curl_setopt($this->ch, CURLOPT_COOKIESESSION, 1);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 5);
		//get cookie info
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->configure->cookieFile);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->configure->cookieFile);
	}
	
	public function login()
	{
		$this->LOG->log("login....");
		$post = $this->getLoginParameters();
		
		if($post != null)
		{
			curl_setopt($this->ch, CURLOPT_URL, $this->configure->loginUrl);
		
			//发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
			
			$loginResult = curl_exec($this->ch);

			if ($loginResult == NULL) { 
				$this->LOG->log("连接网络失败");
			}
			else
			{
				$this->LOG->saveFile($loginResult, $this->configure->login_result_html);
				$analyze = new analyze($loginResult);
				$this->isLogin = $analyze->isLoginSuccess();
			}
		}
	}
	
	public function getLoginParameters()
	{
		$u = $this->configure->username;
		$p = $this->configure->password;
		
		//连接登陆页面获取表单元素，构造post参数
		curl_setopt($this->ch, CURLOPT_URL, $this->configure->loginUrl);
		curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
		
        $loginHtml = curl_exec($this->ch);
		
		//获取验证码图片
		curl_setopt($this->ch, CURLOPT_URL, $this->configure->captchaUrl);
		curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
		
        $captchaImg = curl_exec($this->ch);
		
		if($loginHtml == null || $captchaImg == null)
		{
			$this->LOG->log("连接网络失败");
			return null;
		}
		else
		{
			$this->LOG->log("连接网络成功");
			$this->LOG->saveFile($loginHtml, $this->configure->login_html);
			$this->LOG->saveFile($captchaImg, $this->configure->captcha_png);
			
			$analyze = new analyze($loginHtml);
			
			$viewState = urlencode($analyze->getViewState());
			$eventValidation = urlencode($analyze->getEventValidation());
			$c = $this->decaptcha();

			return "__VIEWSTATE={$viewState}".
				"&txtname={$u}".
				"&txtpwd={$p}".
				"&yanzheng={$c}".
				"&button.x=0&button.y=0".
				"&__EVENTVALIDATION={$eventValidation}";
		}

	}
	
	public function decaptcha()
	{
	
		exec("{$this->configure->tesseract_path} {$this->configure->captcha_png} {$this->configure->decode_path}");
	
		$handle = fopen($this->configure->decode_path.'.txt', 'r'); 

		if($handle != null)
		{		
			$line = fgets($handle);
			$this->LOG->log("自动识别验证码：".substr($line, 0, 5));
			return substr($line, 0, 5);
		}	
		exec($this->configure->captcha_png);
		$handle = fopen('php://stdin', 'r');  
		echo "请输入验证码：";  
		$line = fgets($handle);
		$this->LOG->log("手动输入验证码：".substr($line, 0, 5));
		return substr($line, 0, 5);
	}
	
	public function accessToData()
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->configure->yuecheUrl);
		curl_setopt($this->ch, CURLOPT_HTTPGET, 1);

        $reserveHtml = curl_exec($this->ch);
		if($reserveHtml == null)
		{
			$this->LOG->log("约车页面打开失败");
			$this->isLogin = false;
		}
		else
		{
			$this->LOG->saveFile($reserveHtml, $this->configure->yueche_html);
			$analyze = new analyze($reserveHtml);
			if($analyze->isOpenReserveSuccess())
			{
				$this->reserve_V = urlencode($analyze->getViewState());
				$this->reserve_E = urlencode($analyze->getEventValidation());
			}
			else
			{
				$this->LOG->log("约车页面出错：".$analyze->getErrorMsg());
				$this->isLogin = false;
			}
		}
	}
	
	public function refreshData()
	{
		$post = "__EVENTTARGET="
			."&__EVENTARGUMENT="
			."&__LASTFOCUS="
			."&__VIEWSTATE={$this->reserve_V}"
			."&RadioButtonList1={$this->configure->refreshField}"
			."&btnRefresh={$this->configure->refreshField}"
			."&__EVENTVALIDATION={$this->reserve_E}";

		curl_setopt($this->ch, CURLOPT_URL, $this->configure->yuecheUrl);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);

        $refreshHtml = curl_exec($this->ch);	
		
		if($refreshHtml == null)
		{
			$this->LOG->log("刷新约车表单时失败");
			$this->isLogin = false;
			return false;
		}
		else
		{
			$this->LOG->saveFile($refreshHtml, $this->configure->refresh_html);
			$analyze = new analyze($refreshHtml);
			if($analyze->isOpenReserveSuccess())
			{
				$this->reserve_V = urlencode($analyze->getViewState());
				$this->reserve_E = urlencode($analyze->getEventValidation());
				
				$this->reserveArray = $analyze->getReserveArray();
				
				return true;
			}
			else
			{
				$this->LOG->log("刷新约车页面出错：".$analyze->getErrorMsg());
				$this->isLogin = false;
				return false;
			}
		}
	}
	
	public function hasFreeTime()
	{
		$dateList = $this->configure->dateList;
		$dateInfoList = $this->reserveArray;
		
		$this->LOG->log("进入判断：");
		
		foreach($dateInfoList as $dateInfo)
		{
			print_r($dateInfo);
			
			foreach($dateList as $date)
			{
				if(strcmp("{$dateInfo->date}", "{$date}")==0)
				{
					$this->LOG->log($dateInfo->date." ".$date);
					if($dateInfo->isLeft())
					{
						$this->LOG->log($dateInfo->date."有剩余");
						if($dateInfo->value1 > 0)
						{
							$this->selectValue = urlencode($dateInfo->value1);
							$this->selectName = urlencode($dateInfo->name1);
						}
						elseif($dateInfo->value2 > 0)
						{
							$this->selectValue = urlencode($dateInfo->value2);
							$this->selectName = urlencode($dateInfo->name2);
						}
						elseif($dateInfo->value3 > 0)
						{
							$this->selectValue = urlencode($dateInfo->value3);
							$this->selectName = urlencode($dateInfo->name3);
						}
						elseif($dateInfo->value4 > 0)
						{
							$this->selectValue = urlencode($dateInfo->value4);
							$this->selectName = urlencode($dateInfo->name4);
						}
						elseif($dateInfo->value5 > 0)
						{
							$this->selectValue = urlencode($dateInfo->value5);
							$this->selectName = urlencode($dateInfo->name5);
							
							return false;
						}

						return true;
					}

				}
			}
		}
	
		return false;
	}
	
	public function postData()
	{
		$post = "__EVENTTARGET="
			."&__EVENTARGUMENT="
			."&__LASTFOCUS="
			."&__VIEWSTATE={$this->reserve_V}"
			."&RadioButtonList1={$this->configure->postField}"
			."&{$this->selectName}={$this->selectValue}"
			."&__EVENTVALIDATION={$this->reserve_E}";

		curl_setopt($this->ch, CURLOPT_URL, $this->configure->yuecheUrl);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);

        $postResultHtml = curl_exec($this->ch);	
		
		if($postResultHtml == null)
		{
			$this->LOG->log("提交表单时失败，页面为空");
			return false;
		}
		else
		{
			$this->LOG->saveFile($postResultHtml, $this->configure->yueche_result_html);
			$analyze = new analyze($postResultHtml);
			if($analyze->isReserveSuccess())
			{
	//			$this->reserve_V = urlencode($analyze->getViewState());
	//			$this->reserve_E = urlencode($analyze->getEventValidation());
				
	//			$this->reserveArray = $analyze->getReserveArray();
				return true;
			}
			else
			{
				$this->LOG->log("提交表单出错：".$analyze->getErrorMsg());
				return false;
			}
		}
	}
	
}
	
?>