<?php
class Login extends TPage
{
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


   public function onLoad($param) {
    	parent::onLoad($param);
		
		$this->Session->open();
		unset($this->Session['customer']);
		unset($this->Session['lang']);	
		
    	if(!$this->IsPostBack) {	
			$this->Lang->DataSource = array("en" => "EN", "nl" => "NL", "de" => "DE", "fr" => "FR");
        	$this->Lang->dataBind();			
    	}
   }

	
    public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }
	
    /**
     * Event handler for the OnClick event of the login button.
     * @param TButton the button triggering the event
     * @param TEventParameter event parameter (null here)    
     */
    public function loginButtonClicked($sender, $param) {
        if ($this->IsValid)  // check if input validation is successful
        {
            	// obtain the username and password from the textboxes
	    	$username = $this->Username->Text;
	    	$password = $this->Password->Text;
            
         	$this->checkCredentials($username, $password);
        }
    }
    
    protected function checkCredentials($username, $password)
    {
      $hash = md5($password);
      $customer = new Customer($username, $hash);
	  
	  if($customer->isValid()) {
		$this->Session->open();
		$this->Session['customer'] = $customer;
		$this->Session['lang'] = $this->Lang->Data;
		
		// reset faulty message
		$this->Message->Text = '';
		
		// internationalization
		$globalization = $this->getApplication()->getGlobalization();
		$globalization->setCulture($this->Lang->Data);
		
		// user has been authenticated, redirect to list of infoscreens
	  	$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
	  } else {
	  	$this->Message->Text = Prado::localize("Invalid credentials!");
	  }
    }

}
?>
