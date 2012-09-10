<?php

class dayData
{	
	private $datas;

	public $date;
	public $week_day;
	
	public $name1;   //7-9
	public $name2;   //9-13
	public $name3;   //13-17 
	public $name4;   //17-19 
	public $name5;   //19-21
	
	public $value1;   //7-9
	public $value2;   //9-13
	public $value3;   //13-17 
	public $value4;   //17-19 
	public $value5;   //19-21
	

//	var $V = '%2FwEPDwUJNjc5NDQ1NzQ3D2QWAgIDD2QWBAIFDw9kFgIeB29uZm9jdXMFRCByZXR1cm4gdGhpcy52YWx1ZT0odGhpcy52YWx1ZT09J%2Bivt%2Bi%2Bk%2BWFpemqjOivgeeggSc%2FJyc6dGhpcy52YWx1ZSk7ZAIJDw8WBB4ISW1hZ2VVcmwFCmltYWdlLmFzcHgeB0VuYWJsZWRnZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFBmJ1dHRvbgUMSW1hZ2VCdXR0b24xI3JDOoS4RMTp2IxB%2FEhxzSnJYOE%3D';
//	var $V = '%2FwEPDwUJNjc5NDQ1NzQ3D2QWAgIDD2QWBAIFDw9kFgIeB29uZm9jdXMFRCByZXR1cm4gdGhpcy52YWx1ZT0odGhpcy52YWx1ZT09J%2Bivt%2Bi%2Bk%2BWFpemqjOivgeeggSc%2FJyc6dGhpcy52YWx1ZSk7ZAIJDw8WBB4ISW1hZ2VVcmwFCmltYWdlLmFzcHgeB0VuYWJsZWRnZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFBmJ1dHRvbgUMSW1hZ2VCdXR0b24xvDGinkJQFwqCOt403p9RULvgTR0=';
//	var $E = '%2FwEWBgKFp4KXDwLEhISACwKd%2B7q4BwLR8dTXDgLz9r7ABALSwpnTCK8xw6SaQH%2BvCtSfth%2BQrUxEro2k';
//	var $E = '%2FwEWBgLrtriPAgLEhISACwKd%2B7q4BwLR8dTXDgLz9r7ABALSwpnTCETwYUZENJYVWtSAvgRFPePhM5Mp';
	
	public function __construct($data = "", $view = "", $event="")
    {
		$this->datas = $data;
		$this->R = urlencode("散段");
		$this->V = urlencode($view);
		$this->E = urlencode($event);
		
		$this->init();
    }
	
	public function isLeft()
	{
		$total = $this->value1 + $this->value2 + $this->value3 + $this->value4 + $this->value5;
		
		if($total > 0)
			return true;
		else
			return false;
	}
	
	public function init()
	{
		$items =  explode("\r\n", $this->datas);

		$this->getDateData($items[1]);
		
		$this->name1 = $this->getNameData($items[2]);
		$this->name2 = $this->getNameData($items[4]);
		$this->name3 = $this->getNameData($items[6]);
		$this->name4 = $this->getNameData($items[8]);
		$this->name5 = $this->getNameData($items[10]);
		
		$this->value1 = $this->getValueData($items[2]);
		$this->value2 = $this->getValueData($items[4]);
		$this->value3 = $this->getValueData($items[6]);
		$this->value4 = $this->getValueData($items[8]);
		$this->value5 = $this->getValueData($items[10]);
	}
	
	private function getDateData($dateLine = "")
	{
		$len = strlen($dateLine);

		if($len > 13)
		{
			$dateLine = substr($dateLine, strpos($dateLine, ">")+1);
			$dateLine = substr($dateLine, 0, strpos($dateLine, "<"));

			$index = strpos($dateLine, "(");
			if($index != false)
			{
				$this->date = substr($dateLine, 0, $index);
				$index2 = strpos($dateLine, ")");
				if($index2 != false)
				{
					$this->week_day = substr($dateLine, $index+1, $index2);
				}
			}	
		}
	}
	
	private function getValueData($valueLine = "")
	{
		$v = 'value="';
		$str = substr($valueLine, strpos($valueLine, $v) + strlen($v));
		$str = substr($str, 0, strpos($str, '"'));
		return $str;
	}
	
	private function getNameData($valueLine = "")
	{
		$v = 'name="';
		$str = substr($valueLine, strpos($valueLine, $v) + strlen($v));
		$str = substr($str, 0, strpos($str, '"'));
		return $str;
	}
	
	public function makePostdata($select = 1)
	{
		if($select == 1)
		{
			$this->selectName = urlencode($this->name1);
			$this->selectValue = $this->value1;
		}
		elseif($select == 2)
		{
			$this->selectName = urlencode($this->name2);
			$this->selectValue = $this->value2;
		}
		elseif($select == 3)
		{
			$this->selectName = urlencode($this->name3);
			$this->selectValue = $this->value3;
		}
		elseif($select == 4)
		{
			$this->selectName = urlencode($this->name4);
			$this->selectValue = $this->value4;
		}
		elseif($select == 5)
		{
			$this->selectName = urlencode($this->name5);
			$this->selectValue = $this->value5;
		}
	
		return "__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE={$this->V}&RadioButtonList1={$this->R}&{$this->selectName}={$this->selectValue}&__EVENTVALIDATION={$this->E}";
	}
}

class dfss
{
    var $cookieFile = './var/www/dfss/cookie.tmp';
    var $loginUrl = 'http://114.251.109.196:8080/XYYC21DR1.aspx';
	var $captchaUrl = 'http://114.251.109.196:8080/image.aspx';
	var $yuecheUrl = 'http://114.251.109.196:8080/aspx/car/XYYC22.aspx';
	
    var $header = array(
			'Cookie' => 'ASP.NET_SessionId=350tjia5tuff0zex1g0rsv55; CheckCode=08200; ImageV=08200',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Encoding' => 'gzip,deflate',
            'Accept-Language' => 'zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
            'Accept-Charset' => 'GBK,utf-8;q=0.7,*;q=0.3',
            'Cache-Control' => 'max-age=0',
            'Connection' => 'keep-alive',
			'Referer' => 'http://114.251.109.196:8080/XYYC21DR1.aspx',
        //    'Content-Length' => '510',
            'Content-Type' => 'application/x-www-form-urlencoded'
		);
		

//	var $V = '%2FwEPDwUJNjc5NDQ1NzQ3D2QWAgIDD2QWBAIFDw9kFgIeB29uZm9jdXMFRCByZXR1cm4gdGhpcy52YWx1ZT0odGhpcy52YWx1ZT09J%2Bivt%2Bi%2Bk%2BWFpemqjOivgeeggSc%2FJyc6dGhpcy52YWx1ZSk7ZAIJDw8WBB4ISW1hZ2VVcmwFCmltYWdlLmFzcHgeB0VuYWJsZWRnZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFBmJ1dHRvbgUMSW1hZ2VCdXR0b24xI3JDOoS4RMTp2IxB%2FEhxzSnJYOE%3D';
	var $V = '%2FwEPDwUJNjc5NDQ1NzQ3D2QWAgIDD2QWBAIFDw9kFgIeB29uZm9jdXMFRCByZXR1cm4gdGhpcy52YWx1ZT0odGhpcy52YWx1ZT09J%2Bivt%2Bi%2Bk%2BWFpemqjOivgeeggSc%2FJyc6dGhpcy52YWx1ZSk7ZAIJDw8WBB4ISW1hZ2VVcmwFCmltYWdlLmFzcHgeB0VuYWJsZWRnZGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFBmJ1dHRvbgUMSW1hZ2VCdXR0b24xvDGinkJQFwqCOt403p9RULvgTR0=';
//	var $E = '%2FwEWBgKFp4KXDwLEhISACwKd%2B7q4BwLR8dTXDgLz9r7ABALSwpnTCK8xw6SaQH%2BvCtSfth%2BQrUxEro2k';
	var $E = '%2FwEWBgLrtriPAgLEhISACwKd%2B7q4BwLR8dTXDgLz9r7ABALSwpnTCETwYUZENJYVWtSAvgRFPePhM5Mp';
		
	protected $ch; //curl
	protected $c; //captcha_code
	
	
    public function __construct($u = '11046685', $p = '02240')
    {
        $this->u = $u; //username
        $this->p = $p; //password
		
		$this->yuecheIndex = 1;
		
    }
	
	public function quit()
	{
		fclose($this->log_file);
	}
	
	public function init()
	{
		$this->ch = curl_init();
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0');
		curl_setopt($this->ch, CURLOPT_COOKIESESSION, 1);
		//get cookie info
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieFile);
	}
	
	public function login()
	{

		if($this->decaptcha())
		{
			$handle = fopen('php://stdin', 'r');  
			echo "[php://stdin]请输入验证码：";  
			$line = fgets($handle);
			$this->c = substr($line, 0, 5);

			return $this->do_login();

		}
		else
		{
			echo "Get captcha failed, retry...";
			exit;
		}
	
		return false;

	}
	
	public function makePostData()
	{
		$this->postData = "__VIEWSTATE={$this->V}&txtname={$this->u}&txtpwd={$this->p}&yanzheng={$this->c}&button.x=0&button.y=0&__EVENTVALIDATION={$this->E}";
	}
	
	public function decaptcha()
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->captchaUrl);
		
		curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
		

        $img = curl_exec($this->ch);

		if($img == null)
		{
			return false;
		}

		$write = @fopen("test.jpg","w");
		fwrite($write,$img);
		fclose($write);

		return true;
	}
	
    public function do_login()
    {
		$this->makePostData();

        curl_setopt($this->ch, CURLOPT_URL, $this->loginUrl);
		
		
		//发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
		curl_setopt($this->ch, CURLOPT_POST, 1);

		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postData);
		

        $html = curl_exec($this->ch);

		if ($html == NULL) { 
			echo "Login failed";
        }
		else
		{
			$write = @fopen("test.htm","w");
			fwrite($write,$html);
			fclose($write);
		}
		echo "Login success\n";
        return true;
    }
	
	public function getDataList()
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->yuecheUrl);
		
		curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
		

        $html = curl_exec($this->ch);
		
		$write = @fopen("dataList.html","w");
		fwrite($write,$html);
		fclose($write);

		$this->parseDataList($html);
	}
	public function log($log)
	{
		fwrite($this->log_file,$log);
	}
	
	public function parseDataList($str)
	{
	
		$EVENTVALIDATION = '<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="';
		$event = substr($str, strpos($str, $EVENTVALIDATION) + strlen($EVENTVALIDATION));
		$event = substr($event, 0, strpos($event, '"'));
		$this->log($event."\n");

		
		echo "EVENT=".$event."\n";
		
		$VIEWSTATE = '<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="';
		$view = substr($str, strpos($str, $VIEWSTATE) + strlen($VIEWSTATE));
		$view = substr($view, 0, strpos($view, '"'));
		$this->log($view."\n");
		
		echo "VIEW=".$view."\n";
		
		$alltree = explode('</tr><tr align="center" style="color', $str);

		$tree = array_slice($alltree, 1, 7);

		$this->alldatas = array(new dayData($tree[0],$view,$event), new dayData($tree[1],$view,$event), new dayData($tree[2],$view,$event),
			new dayData($tree[3],$view,$event), new dayData($tree[4],$view,$event), new dayData($tree[5],$view,$event), new dayData($tree[6],$view,$event));
		
		
		$item = $this->alldatas[6];
		echo $item->date."\n";
	}
	
	public function do_yueche($postD = "")
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->yuecheUrl);
		
	//	curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
		
		//发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
		curl_setopt($this->ch, CURLOPT_POST, 1);

		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postD);
		

        $html = curl_exec($this->ch);

		if ($html == NULL) { 
			echo "yueche failed";
        }
		else
		{
			$write = @fopen("result".$this->yuecheIndex.".htm","w");
			fwrite($write,$html);
			fclose($write);
		}

		$this->yuecheIndex++;
	}
	
	public function yueche()
	{
		$i = 0;
		foreach($this->alldatas as $item)
		{

			echo $item->date.$i."TT\n";
			
			$this->log("HHHH".$item->makePostdata(1))."\n";
			echo "HHHH".$item->makePostdata(1)."\n";
			if($item->isLeft())
			{
			echo "isLeft"."\n";
				if($item->value1 > 0)
					$this->do_yueche($item->makePostdata(1));
				if($item->value2 > 0)
					$this->do_yueche($item->makePostdata(2));
				if($item->value3 > 0)
					$this->do_yueche($item->makePostdata(3));
				if($item->value4 > 0)
					$this->do_yueche($item->makePostdata(4));
				if($item->value5 > 0)
					$this->do_yueche($item->makePostdata(5));
				
			}
			
			$i++;
		}
	}
}

echo "test";

$dfss = new dfss('11046685', '02240');

$dfss->init();

if($dfss->login())
{

	$dfss->getDataList();
	
	$dfss->yueche();
	
	$dfss->quit();
    exit;

}

?>