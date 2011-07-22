<?php

class Success extends TPage {

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

	// css
	public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }
	
	// what to do on page load
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

}

?>
