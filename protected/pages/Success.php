<?php

class Success extends TPage {

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

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }
	
	// Check what to do on page load.
	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
    		$this->Session->open();			
			if(!isset($this->Session['customer'])) {
				// There customer is missing in the session, redirect to login page.
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else if($this->Session['customer']->getCustomerId() == 0) {
                // root customer -> Back hyperlink should link to ListCustomers page
                $this->Back->Text = Prado::localize("<- Back to list of customers.");
                $this->Back->NavigateUrl = "index.php?page=ListCustomers";
            } 
            
    	}
   	}

}

?>
