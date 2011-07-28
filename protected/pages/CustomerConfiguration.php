<?php

class CustomerConfiguration extends TPage {
	
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
			if(!isset($this->Session['customer']) || $this->Session['customer']->getCustomerId() != 0) {
				// redirect to login page.
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else if(!isset($this->Request['id'])) {
				// The customerid is missing in the request, create a new customer
				
			} else {
			    $db = new Db();
			    $customers = $db->getCustomers();
			    $customer = $customers[$this->Request['id']];
				
				$this->Username->Data = $customer->getUsername();
			}
    	}
   	}

	// Event handler for the OnClick event of the save button.
	public function saveCustomer($sender, $param) {
		$this->Session->open();			
		if(!isset($this->Session['customer']) || $this->Session['customer']->getCustomerId() != 0) {
			// redirect to login page.
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Request['id'])) {
			// The customerid is missing in the request, create a new customer
			if ($this->Page->IsValid)    {
                $db = new Db();
                
                $db->insertCustomer($this->Username->Data,md5($this->NewPassword->Data));
                
                $this->Response->redirect($this->Service->constructUrl('Success', null, true));
            }
		} else {
			if ($this->Page->IsValid)	{
				$id = $this->Request['id'];
                $db = new Db();
                $customer = $db->getCustomer($id);
                
                if($this->Username->Data != $customer->getUsername()){
                    $customer->setUsername($this->Username->Data);
                }
                if($this->NewPassword->Data != ""){
                    $customer->setPassword($this->NewPassword->Data);
                }
                

				$this->Response->redirect($this->Service->constructUrl('Success', null, true));
			}
		}
	}
   
}
?>
