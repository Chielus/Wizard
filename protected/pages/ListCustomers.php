<?php

class ListCustomers extends TPage {

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
	
	// Retreive the infoscreen of the current user from the database and return them.
	private function getCustomers() {
		$data = array();
		
        $db = new Db();
		$customers = $db->getCustomers();
		foreach($customers as $id => $customer) {
			array_push($data, array("id" => $id, "username" => $customer->getUsername(), "#infoscreens" => count($customer->getInfoscreenIds())));
		}
		
		return $data;
	}

	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack && !$this->IsCallBack) {			
    		$this->Session->open();
			if(!isset($this->Session['customer']) || $this->Session['customer']->getCustomerId() != 0) {
				// redirect to login page if no root customer.
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else {
				$customers = $this->getCustomers();
				if(empty($customers)) {
					$this->Empty->Data = Prado::localize("There are no customers available to configure!");
					$this->Empty->Style = "";
				} else {
					// Bind the available customers to the our grid.
        			$this->DataGrid->DataSource = $customers;
        			$this->DataGrid->dataBind();
				}
			}
    	}
	}
	
	// Event handler for the OnClick event of the configure button.
	public function configureCustomer($sender, $param) {
		$customerid = $param->getCommandParameter();
		
		$this->Response->redirect($this->Service->constructUrl('CustomerConfiguration', array("id" => $customerid), true));
	}
    
    // Event handler for the OnClick event of the configureInfoScreens button.
    public function configureInfoScreens($sender, $param) {
        $customerid = $param->getCommandParameter();
        
        $this->Session->open();
        
        $this->Response->redirect($this->Service->constructUrl('ListInfoscreens', array("cid" => $customerid), true));
    }

}

?>
