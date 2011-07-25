<?php
class InfoscreenStops extends TPage {
	// Selected station information is saved in a hidden text field.
	// This is needed because the full list of stations in a TListBox isn't 
	// accessible on the server side. By adding a selected station (e.g. ;Torhout;)
	// to this text field and removing stations (e.g Lichtervelde) from this text
	// field it's always up to date.
	// An example of the value of the hidden text field at any point can be:
	// ;;Torhout;Brugge;;Roeselare;;Deinze;;;
	// This string can easily be exploded to get the needed data.
	
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

	// Register a prado rendered component in <head> so it's accessible using javascript.
	// For some reason a rendered prado component has an id slightly modified compared to its original.
	private function registerScriptClientId($component){
		$cs = $this->Page->ClientScript;
		if (!$cs->isHeadScriptRegistered('ClientID')){
			$cs->registerHeadScript('ClientID', 'ClientID = {};');
		}
		if (!$cs->isHeadScriptRegistered('ClientID.'.$component->ID)){
			$cs->registerHeadScript('ClientID.'.$component->ID, 'ClientID.'.$component->ID.' = \''.$component->ClientID.'\';');
		}
	}
	
	// Get the language.
	// Used in javascript to fetch the station names in the right language.
	public function getLang() {
		$this->Session->open();
		return $this->Session['lang'];
	}
	
	// Fetch station information from the iRail API.
	private function curl_download($url) { 
	    if (!function_exists('curl_init')){
	        die('Sorry cURL is not installed!');
	    }	 

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Infoscreen/1.0");
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	 
	    $output = curl_exec($ch);
	 
	    curl_close($ch);
	 
	    return $output;
	}
	
	// Retreive stations.
	private function getStations($system, $lang) {
		$data = array();
		
		$base = 'http://api.irail.be/stations/';
    	$params = array('system' => $system, 'format' => 'json', 'lang' => $lang);
    	$url = $base . '?' . http_build_query($params);
		
		$json = json_decode($this->curl_download($url));		
		foreach($json->station as $stop) {
			$temp = array();
			$temp['standardname'] = $stop->standardname;
			$temp['lat'] = $stop->locationY;
			$temp['long'] = $stop->locationX;
			$temp['name'] = $stop->name;
      		$data[$stop->id] = $temp;
    	}    	
		
		return $data;
	}

	// Register controls for clientside scripting.
	public function onPreRender($param){
		parent::onPreRender($param);
	
		$this->registerScriptClientId($this->ListNMBS);
		$this->registerScriptClientId($this->ListMIVB);
		$this->registerScriptClientId($this->ListDeLijn);
		
		$this->registerScriptClientId($this->SelectedNMBS);
		$this->registerScriptClientId($this->SelectedMIVB);
		$this->registerScriptClientId($this->SelectedDeLijn);

		$this->registerScriptClientId($this->HiddenStations);
	}

	public function onLoad($param) {
	    	parent::onLoad($param);
	    	if(!$this->IsPostBack && !$this->IsCallBack) {
	    		// It is possible that the session expires during configuration.
    			// Therefore we always check the customer and infoscreenid in the session.
    			// If redirection is needed we do so.
					
				$this->Session->open();			
				if(!isset($this->Session['customer'])) {
					// There customer is missing in the session, redirect to login page.
					$this->Response->redirect($this->Service->constructUrl('Login', null, true));
				} else if(!isset($this->Session['infoscreenid'])) {
					// The infoscreenid is missing in the session, redirect to list.
					$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
				} else {
					$this->HiddenStations->Value = "";
				}
	    	}
	}
	
	// Event handler for the OnClick event of the save button.
	public function saveStops($sender, $param) {
		// It is possible that the session expires during configuration.
    	// Therefore we always check the customer and infoscreenid in the session.
    	// If redirection is needed we do so.
		
		$this->Session->open();			
		if(!isset($this->Session['customer'])) {
			// There customer is missing in the session, redirect to login page.
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Session['infoscreenid'])) {
			// The infoscreenid is missing in the session, redirect to list.
			$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
		} else {
			// Get needed information from session.
			$customer = $this->Session['customer'];
			$infoscreenid = $this->Session['infoscreenid'];
			$infoscreen = $customer->getInfoscreen($infoscreenid);	
			
			// Change the infoscreen data in the database using the provided model.
			// We retreive the selected stations from our hidden text field (see top of this page).
			$infoscreen->removeAllStations();
			$stations = explode(";", $this->HiddenStations->Value);
			foreach($stations as $id) {
				$infoscreen->addStation($id);
			}

			// Redirect to the next step of the configuration.
			$this->Response->redirect($this->Service->constructUrl('InfoscreenConfiguration', null, true));
		}		
	}
}
?>
