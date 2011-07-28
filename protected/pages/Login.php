<?php
// Include this to use active controls like TActiveDropDownList.
Prado::Using('System.Web.UI.ActiveControls.*');

class Login extends TPage {
	
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

   public function onLoad($param) {
    	parent::onLoad($param);
		
		// On page load we delete available customer and infoscreen information.
		// The user needs to be authenticated  again.
		$this->Session->open();
		unset($this->Session['customer']);
		unset($this->Session['infoscreenid']);	
		
		// Bind the available languages to the TActiveDropDownList.
		// Select the current active language.
    	if(!$this->Page->IsCallback && !$this->IsPostBack) {
			$this->Lang->DataSource = array("en" => "EN", "nl" => "NL", "de" => "DE", "fr" => "FR");
        	$this->Lang->dataBind();
			
			$this->Session->open();
            if(isset($this->Session['lang'])) {
            	$this->Lang->SelectedValue = $this->Session['lang'];
            }
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
	
	// Event handler for the OnSelectedIndexChanged event of the TActiveDropDownList.
	// Note that the control is an active control.
	// The page is being reloaded the change language of the page.
	public function changeLang($param) {
		$this->Session->open();	
		$this->Session['lang'] = $this->Lang->SelectedValue;
	
		$this->Response->reload();	
	}
	
    // Event handler for the OnClick event of the login button.
    public function loginButtonClicked($sender, $param) {
        if ($this->Page->IsValid) {
           	// Obtain the username and password and check them.
	    	$username = $this->Username->Text;
	    	$password = $this->Password->Text;
            
         	$this->checkCredentials($username, $password);
        }
    }
    
    private function checkCredentials($username, $password) {
      $hash = md5($password);
      $customer = new Customer($username, $hash);
	  
	  if($customer->isValid()) {
		$this->Session->open();
		$this->Session['customer'] = $customer;
		
		// Reset a possible faulty message.
		$this->Message->Text = '';
		
        if($customer->getCustomerId() == 0){
            //root login, redirect to list of customers
            $this->Response->redirect($this->Service->constructUrl('ListCustomers', null, true));
        } else {
            // The user has been authenticated, redirect to list of infoscreens.
            $this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
        }
		
	  } else {
	  	// Authentication failed, display message.
	  	$this->Message->Text = Prado::localize("Invalid credentials!");
	  }
    }

}
?>
