<?php
class InfoscreenStops extends TPage
{
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

	public function onPreRenderComplete($param) {
		parent::onPreRenderComplete($param);

		$url = 'css/style.css';
 		$this->Page->ClientScript->registerStyleSheetFile($url, $url);
    }

	// register a prado rendered componentid so we can access this easily using javascript
	// for some reason a randered prado component has an id slightly modified
	// than the one specified
	private function registerScriptClientId($component){
		$cs = $this->Page->ClientScript;
		if (!$cs->isHeadScriptRegistered('ClientID')){
			$cs->registerHeadScript('ClientID', 'ClientID = {};');
		}
		if (!$cs->isHeadScriptRegistered('ClientID.'.$component->ID)){
			$cs->registerHeadScript('ClientID.'.$component->ID, 'ClientID.'.$component->ID.' = \''.$component->ClientID.'\';');
		}
	}
	
	public function getLang() {
		$this->Session->open();
		return $this->Session['lang'];
	}
	
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
	
	protected function getStations($system, $lang) {
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
	    	if(!$this->IsPostBack) {			
				$this->Session->open();			
				if(!isset($this->Session['customer'])) {
					// missing customer in session, redirect to login page
					$this->Response->redirect($this->Service->constructUrl('Login', null, true));
				} else if(!isset($this->Session['infoscreenid'])) {
					// missing infoscreenid in session, redirect to list
					$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
				} else {
					$this->HiddenStations->Value = "";
				}
	    	}
	}
	
	public function saveStops($sender, $param) {
		$this->Session->open();			
		if(!isset($this->Session['customer'])) {
			// missing customer in session, redirect to login page
			$this->Response->redirect($this->Service->constructUrl('Login', null, true));
		} else if(!isset($this->Session['infoscreenid'])) {
			// missing infoscreenid in session, redirect to list
			$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
		} else {
			$customer = $this->Session['customer'];
			$infoscreenid = $this->Session['infoscreenid'];
			$infoscreen = $customer->getInfoscreen($infoscreenid);	
						
			$infoscreen->removeAllStations();
			$stations = explode(";", $this->HiddenStations->Value);
			foreach($stations as $id) {
				$infoscreen->addStation($id);
			}

			$this->Response->redirect($this->Service->constructUrl('InfoscreenConfiguration', null, true));
		}		
	}
}
?>
