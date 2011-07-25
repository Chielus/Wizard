<?php
Prado::Using('System.Web.UI.ActiveControls.*');

class InfoscreenLogo extends TPage {
	
	// Temporary folder for file uploads.
	// Make sure this folder is writable and accessible by the server.
	// TODO: Change directory so the Infoscreen module can access it.
	private $temp = "/tmp/wizard/";
	
	private $fileLogo;
	
	// On page creation the constructor checks if a language is set.
    // If so we set the globalization (internationalization) for this page.
	public function __construct() {   		
        parent::__construct();
		
		// Check if our temp dir already exists.
		if(!is_dir($this->temp)) {
			mkdir($this->temp);
		}

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

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }
	
	// Upload the image to the temporary directory.
	// Display the image in the TActiveImage control.
	public function fileUploaded($sender, $param) {
		$this->Session->open();
		
		if($sender->HasFile) {
			$type = $sender->FileType;

			if(($type != 'image/bmp') && ($type != 'image/gif') && ($type != 'image/jpeg') && ($type != 'image/png') ) {
	            unset($this->Session['fileLogo']);
				$this->Status->Text = "Invalid file type.";
	        } else if($sender->FileSize > (1024 * 500)) {
	            unset($this->Session['fileLogo']);
				$this->Status->Text = "File size is too big.";
	        } else {
	        	if($this->Logo->saveAs($this->temp . $this->Logo->FileName)) {				
					$this->Display->ImageUrl = $this->Page->publishFilePath('/tmp/wizard/' . $sender->FileName);
					$this->Display->Style = "display: block";
					
					$this->Session['fileLogo'] = $sender->FileName;
					$this->Status->Text = "File OK!";
				} else {
					unset($this->Session['fileLogo']);
					$this->Status->Text = "Error: couldn't save file.";
				}
	        }		
		} else {
			unset($this->Session['fileLogo']);
			$this->Status->Text = "No file selected.";
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
				// The infoscreenid is missing in the session, redirect to list.
				$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
			} 
    	}
   	}
	
	// Event handler for the OnClick event of the save button.
	public function submitLogo($sender, $param) {
		$this->Session->open();			
		if(!isset($this->Session['customer'])) {
			// There customer is missing in the session, redirect to login page.
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Session['infoscreenid'])) {
			// The infoscreenid is missing in the session, redirect to list.
			$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
		} else {
			if(isset($this->Session['fileLogo'])) {
				$customer = $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
			    $infoscreen->setSettingValue("logo", $this->Session['fileLogo']);				
				$this->Response->redirect($this->Service->constructUrl('Success', null, true));
				
				// Reset the file value in case the user returns to his page.
				// Otherwise the file would still be available.
				unset($this->Session['fileLogo']);
			} else {
				$this->Status->Text = "No file selected.";
			}
		}
	}
	
}

?>