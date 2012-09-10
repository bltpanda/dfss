<?php
/**
*约车信息配置脚本
*运行前需先配置好以下参数：
*username/password  用户名密码
*dateList 约车日期，可多选
*/
class configure
{	
	public function __construct()
    {
		$this->username = "";
		$this->password = "";

		$this->postField = urlencode("散段");
		$this->refreshField = urlencode("刷新");
		
		$this->cookieFile = './var/www/dfss/cookie.tmp';
		$this->loginUrl = 'http://114.251.109.196:8080/XYYC21DR1.aspx';
		$this->captchaUrl = 'http://114.251.109.196:8080/image.aspx';
		$this->yuecheUrl = 'http://114.251.109.196:8080/aspx/car/XYYC22.aspx';

		$this->login_html = '.\file\login.html';
		$this->login_result_html = '.\file\login_result.html';
		$this->captcha_png = '.\file\captcha.jpg';
		$this->yueche_html = '.\file\yueche.html';
		$this->refresh_html = '.\file\refresh.html';
		$this->yueche_result_html = '.\file\yuehce_result.html';
		
		$this->tesseract_path = 'D:/GreenPro/Tesseract-ocr/tesseract.exe';
		$this->decode_path = '.\file\decode';
		
		$this->dateList = array(
			"2012-06-09",
			"2012-06-10"
		);
		
		$this->header = array(
	//		'Host' => 'http://114.251.109.196:8080',
	//		'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0',
			'Cookie' => 'ASP.NET_SessionId=350tjia5tuff0zex1g0rsv55; CheckCode=08200; ImageV=08200',
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Encoding' => 'gzip,deflate',
			'Accept-Language' => 'zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
			'Accept-Charset' => 'GBK,utf-8;q=0.7,*;q=0.3',
			'Cache-Control' => 'max-age=0',
			'Connection' => 'keep-alive',
			'Referer' => 'http://114.251.109.196:8080/web/XYYC21DR1.aspx',
			//    'Content-Length' => '510',
			'Content-Type' => 'application/x-www-form-urlencoded'
		);
	}
}
?>