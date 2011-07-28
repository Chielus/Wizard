<?php

class InfoscreenConfiguration extends TPage {
	
	// On page creation the constructor checks if a language is set.
    // If so we set the globalization (internationalization) for this page.
   	public function __construct() {   		
        parent::__construct();

		$this->Session->open();
		if(isset($this->Session['lang'])) {
			$lang = $this->Session['lang'];
		} else {
			$lang = "en";
		}
		
        if(!is_null($lang)) {
            $this->getApplication()->getGlobalization()->setCulture($lang);
        }
  	}	   
   
   	// Add the available stylesheet to the page.
    public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $useragent, $matched)) {
    		$url = 'css/style.css';
			$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    		$url = 'css/style-ie.css';
			$this->Page->ClientScript->registerStyleSheetFile($url, $url);
		} else {
			$url = 'css/style.css';
			$this->Page->ClientScript->registerStyleSheetFile($url, $url);			
		}		
    }

   	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
    		$this->Session->open();			
			if(!isset($this->Session['customer'])) {
				// There customer is missing in the session, redirect to login page.
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else if(!isset($this->Session['infoscreenid'])) {
				// The infoscreenid is missing in the session and no root customer -> redirect to list.
				$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
			} else {
				$customer = $this->Session['customer']->getCustomerId() == 0 ? $this->Session['configureCustomer'] : $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
				$this->Title->Data = $infoscreen->getTitle();
				$this->Motd->Data = $infoscreen->GetMotd();
				$this->Rows->Data = $infoscreen->getSettingValue("rowstoshow") ? $infoscreen->getSettingValue("rowstoshow") : '10';
				$this->Cycle->Data = $infoscreen->getSettingValue("cycleinterval") ? $infoscreen->getSettingValue("cycleinterval") : '10';
				$this->Color->Data = $infoscreen->getSettingValue("color");
				
				$this->Lang->DataSource = array("en" => "EN", "nl" => "NL", "de" => "DE", "fr" => "FR");
        		$this->Lang->dataBind();
			}
    	}
   	}

	// Event handler for the OnClick event of the save button.
	public function saveConfiguration($sender, $param) {
		$this->Session->open();			
		if(!isset($this->Session['customer'])) {
			// There customer is missing in the session, redirect to login page.
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Session['infoscreenid'])) {
			// The infoscreenid is missing in the session, redirect to list.
			$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
		} else {
			if ($this->Page->IsValid)	{
				$customer = $this->Session['customer']->getCustomerId() == 0 ? $this->Session['configureCustomer'] : $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
				$infoscreen->setTitle($this->Title->Data);
				$infoscreen->setMotd($this->Motd->Data);
				$infoscreen->setSettingValue("rowstoshow", $this->Rows->Data);
				$infoscreen->setSettingValue("cycleinterval", $this->Cycle->Data);
				$infoscreen->setSettingValue("lang", $this->Lang->SelectedValue);
				$infoscreen->setSettingValue("color", $this->Color->Data);
				
				$this->Response->redirect($this->Service->constructUrl('InfoscreenLogo', null, true));
			}
		}
	}
   
}
?>
