<?php

class ListInfoscreens extends TPage {

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
	private function getInfoscreens($customer) {
		$data = array();
		
		$infoscreenids = $customer->getInfoscreenIds();
		foreach($infoscreenids as $id) {
			$infoscreen = $customer->getInfoscreen($id);
			array_push($data, array("id" => $id, "title" => $infoscreen->getTitle(), "motd" => $infoscreen->getMotd()));
		}
		
		return $data;
	}

	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack && !$this->IsCallBack) {			
    		$this->Session->open();
			if(!isset($this->Session['customer'])) {
				// There customer is missing in the session, redirect to login page.
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else {
			    $customer;
			    if($this->Session['customer']->getCustomerId() == 0){
			        $db = new Db();
			        $customer = $db->getCustomer($this->Request['cid']);
                    $this->Session->open();
                    $this->Session['configureCustomer'] = $customer;
                    $this->addButton->Visible = "True";
		        } else {
                    $customer = $this->Session['customer'];
                }
				$infoscreens = $this->getInfoscreens($customer);               
				if(empty($infoscreens)) {
					$this->Empty->Data = Prado::localize("There are no infoscreens available to configure!");
					$this->Empty->Style = "";
				} else {
					// Bind the available infoscreens to the our grid.
        			$this->DataGrid->DataSource = $infoscreens;
        			$this->DataGrid->dataBind();
				}
			}
    	}
	}
	
	// Event handler for the OnClick event of the configure button.
	public function configureInfoscreen($sender, $param) {
		$infoscreenid = $param->getCommandParameter();
		
		$this->Session->open();
		$this->Session['infoscreenid'] = $infoscreenid;
		
		$this->Response->redirect($this->Service->constructUrl('InfoscreenStops', null, true));
	}
    
    // Event handler for the OnClick event of the configure button.
    public function addInfoscreen($sender, $param) {
        $db = new Db();
        $customerid = $this->Request['cid']; // from request because only root can add infoscreens
        $infoscreenid = $db->insertInfoscreen($customerid);
        if($infoscreenid == -1){
            throw new Exception("Failed inserting Infoscreen");
        } else {
            $this->Session->open();
            $this->Session['infoscreenid'] = $infoscreenid;
            $this->Response->redirect($this->Service->constructUrl('InfoscreenStops', null, true));
        }
    }

}

?>
