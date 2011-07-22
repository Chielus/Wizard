<?php
Prado::Using('System.Web.UI.ActiveControls.*');

class InfoscreenLogo extends TPage {
	
	// temporary folder for file uploads
	// make sure this folder is writable and accessible by the server
	// TODO: change directory so Infoscreen module can access it
	private $temp = "/tmp/wizard/";
	
	// i18n
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
   
   //css
    public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }
	
	// upload file to $temp and display
	public function fileUploaded($sender,$param) {
		if($this->Logo->HasFile) {
			$type = $this->Logo->FileType;

			if(($type != 'image/bmp') && ($type != 'image/gif') && ($type != 'image/jpeg') && ($type != 'image/png') ) {
	            $this->typeFileValidator->setIsValid(false);
	        } else if($this->Logo->FileSize > (1024 * 500)) {
	            $this->sizeFileValidator->setIsValid(false);
	        } else {
	        	$this->Logo->saveAs($this->temp . $this->Logo->FileName);
				
				$this->Display->ImageUrl = $this->Page->publishFilePath('/tmp/wizard/' . $this->Logo->FileName);
				$this->Display->Style = "display: block";				
	        }		
		} else {
			$this->isFileValidator->setIsValid(false);
		}	
    }
	
	// missing customer -> login
	// missing infoscreendi -> list
	// else OK
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
				// OK
			}
    	}
   	}
	
	public function submitLogo($sender, $param) {
		$customer = $this->Session['customer'];
		$infoscreenid = $this->Session['infoscreenid'];
		$infoscreen = $customer->getInfoscreen($infoscreenid);
		
	    $infoscreen->setSettingValue("logo", $this->Logo->FileName);				
		$this->Response->redirect($this->Service->constructUrl('Success', null, true));
	}
	
}

?>