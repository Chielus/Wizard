<?php
class InfoscreenStops extends TPage
{	
	private $stationsNMBS;
	private $stationsMIVB;
	
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

	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
			$this->Session->open();
			$lang = $this->Session['lang'];
			$customer = $this->Session['customer'];
			$infoscreenid = $this->Session['infoscreenid'];
			
			if($customer == null) {
				// missing customer in session, redirect to login page
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else if($infoscreenid == null) {
				// missing infoscreenid in session, redirect to list
				$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
			} else {
				$this->stationsNMBS = $this->getStations('NMBS', $lang);
				$this->stationsMIVB = $this->getStations('MIVB', $lang);
					
				$data = array();				
				foreach($this->stationsNMBS as $key => $value) {
					$data[$key] = $value['name'];
				}				
        		$this->ListNMBS->DataSource = $data; 
        		$this->ListNMBS->dataBind();
				
				$data = array();				
				foreach($this->stationsMIVB as $key => $value) {
					$data[$key] = $value['name'];
				}	
				$this->ListMIVB->DataSource = $data;
				$this->ListMIVB->dataBind();
			}
    	}
	}
}
?>
