<?php

class ApplicationGlobalization extends TGlobalizationAutoDetect
{
	
	public $AvailableCultures;
	
	public function init($xml)
	{
		parent::init($xml);
		$this->Application->OnBeginRequest[] = array($this, 'beginRequest');
	}

	public function beginRequest($sender, $param)
	{
		$culture = $this->Request['lang'];
		if(null == $culture) {
			if(null !== ($cookie = $this->Request->Cookies['lang'])) {
				$culture = $cookie->getValue();
			}
		}

		if(is_string($culture))	{
			$info = new CultureInfo();
			if($info->validCulture($culture)) {
				if(!in_array($culture, explode(";", $this->AvailableCultures))) {
					$culture = "en";
				}
			} else {
				$culture = "en";
			}
		} else {
			$culture = "en";
		}
		
		$this->setCulture($culture);
		$this->Response->Cookies[] = new THttpCookie('lang', $culture);
	}
}

?>
