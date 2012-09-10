<?php

class Log
{
	public function __construct()
    {
        $this->write = @fopen('.\file\log.txt',"w");

    }
	
	public function log($msg = "")
	{
		fwrite($this->write,$msg.'\r\n');	
		echo $msg."\n";
	}
	
	public function close()
	{
		fclose($this->write);
	}
	
	public function saveFile($data="", $path="")
	{
		$write = @fopen($path,"w");
		fwrite($write,$data);
		fclose($write);
	}
}

class analyze
{
	public function __construct($html = "")
    {
        $this->html = $html;
    }

	public function getEventValidation()
	{
		$EVENTVALIDATION = '<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="';
		$event = substr($this->html, strpos($this->html, $EVENTVALIDATION) + strlen($EVENTVALIDATION));
		$event = substr($event, 0, strpos($event, '"'));
	
		return $event;	
	}
	
	public function getViewState()
	{
		$VIEWSTATE = '<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="';
		$view = substr($this->html, strpos($this->html, $VIEWSTATE) + strlen($VIEWSTATE));
		$view = substr($view, 0, strpos($view, '"'));
		return $view;	
	}

	public function isLoginSuccess()
	{
		return true;
	}
	
	public function isOpenReserveSuccess()
	{
		$MESSAGE = '<span id="lblMessage" style="display:inline-block;color:Red;font-size:11pt;font-weight:normal;width:360px;">';
		$this->msg = substr($this->html, strpos($this->html, $MESSAGE) + strlen($MESSAGE));
		$this->msg = substr($this->msg, 0, strpos($this->msg, '<'));
		
		$allData = explode('</tr><tr align="center" style="color', $this->html);
		
		if(count($allData) < 7)
		{
			return false;
		}
		else
		{
			$postList = array_slice($allData, 1, 7);
			$this->reserveArray = array(new dayData($postList[0]), new dayData($postList[1]), new dayData($postList[2]),
				new dayData($postList[3]), new dayData($postList[4]), new dayData($postList[5]), new dayData($postList[6]));
				
			return true;
		}
	}
	
	public function getErrorMsg()
	{
		return $this->msg;
	}
	
	public function getReserveArray()
	{
		return $this->reserveArray;
	}
	
	public function isReserveSuccess()
	{
		return true;
	}
}

class dayData
{	
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
	
	public $isReserve1;
	public $isReserve2;
	public $isReserve3;
	public $isReserve4;
	public $isReserve5;
	
	public function __construct($data = "")
    {
		$postDatas = $data;
		
		$items =  explode("\r\n", $postDatas);

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
	
	public function isLeft()
	{
		$total = $this->value1 + $this->value2 + $this->value3 + $this->value4 + $this->value5;
		
		echo $total.'\n';
		
		if($total > 0)
			return true;
		else
			return false;
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
		$color_green = '#33FF99';  //表示未预约
		$color_red = '#FF0066';    //表示已预约过
	
		$style = 'style="background-color:';
		$color = substr($valueLine, strpos($valueLine, $style) + strlen($style));
		$color = substr($color, 0, strpos($color, ';'));
		if(strcmp("{$color}", "{$color_red}") == 0)
			return '0';
			
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
}

?>