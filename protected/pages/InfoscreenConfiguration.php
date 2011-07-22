<?php
Prado::Using('System.Web.UI.ActiveControls.*');

class InfoscreenConfiguration extends TPage
{
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
   
    public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }

   public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
    		$this->Session->open();			
			if(!isset($this->Session['customer'])) {
				// missing customer in session, redirect to login page
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else if(!isset($this->Session['infoscreenid'])) {
				// missing infoscreenid in session, redirect to list
				$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
			} else {
				$customer = $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
				$this->Title->Data = $infoscreen->getTitle();
				$this->Motd->Data = $infoscreen->GetMotd();
				$this->Rows->Data = $infoscreen->getSettingValue("rowstoshow");
				$this->Cycle->Data = $infoscreen->getSettingValue("cycleinterval");
				
				$this->Lang->DataSource = array("EN" => "EN", "NL" => "NL", "DE" => "DE", "FR" => "FR");
        		$this->Lang->dataBind();
			}
    	}
   }

	public function fileUploaded($sender, $param)
    {
    	if($sender->HasFile) {
    		$sender->getFileType();

	        if($sender->getFileSize() == 0) {
	            $this->isFileValidator->setIsValid(false);
	        } else if(($ft != 'image/bmp') && ($ft != 'image/gif') && ($ft != 'image/jpeg') && ($ft != 'image/png') ) {
	            $this->typeFileValidator->setIsValid(false);
	        } else if($sender->getFileSize() > 2197152) {
	            $this->sizeFileValidator->setIsValid(false);
	        } else {
	            
	        }			
		}
    }

	public function saveConfiguration($sender, $param) {
		$this->Session->open();			
		if(!isset($this->Session['customer'])) {
			// missing customer in session, redirect to login page
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Session['infoscreenid'])) {
			// missing infoscreenid in session, redirect to list
			$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
		} else {
			if ($this->IsValid)  // check if input validation is successful
			{
				$customer = $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
				$infoscreen->setTitle($this->Title->Data);
				$infoscreen->setMotd($this->Motd->Data);
				$infoscreen->setSettingValue("rowstoshow", $this->Rows->Data);
				$infoscreen->setSettingValue("cycleinterval", $this->Cycle->Data);
				$infoscreen->setSettingValue("lang", $this->Lang->SelectedValue);
				$infoscreen->setSettingValue("color", $this->Color->Data);
			}
		}
	}
   
}
?>
