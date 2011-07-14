<?php

class Infoscreen {
	
	// configuration values which are hardcoded for each infoscreen:
	// infoscreenid, customerid, infotext and message of the day,
	// all other configuration values are specified in a dynamic 'array' of settings,
	// all stations are specified in a dynamic 'array'
	
	private $infoscreenid;
	private $customerid;
	private $infotext;
	private $motd;
	
	private $settings;
	private $stations;
	
	function __construct($infoscreenid) {
		$this->infoscreenid = $infoscreenid;
		
		$db = new Db();
		$infoscreen = $db->getInfoscreen($this->infoscreenid);
		$this->customerid = $infoscreen['customerid'];
		$this->infotext = $infoscreen['infotext'];
		$this->motd = $infoscreen['motd'];
		
		$this->settings = new Settings($this->infoscreenid);
		$this->stations = new Stations($this->infoscreenid);
	}
	
	public function getCustomerId() {
		return $this->customerid;
	}
	
	public function getInfoText() {
		return $this->infotext;
	}
	
	public function getMotd() {
		return $this->motd;
	}
	
	// STATIONS
	
	public function getStationIds() {
		return $this->stations->getStationIds();
	}
	
	public function addStation($stationid) {
		$this->stations->addStation($stationid);
	}
	
	public function removeStation($stationid) {
		$this->stations->removeStation($stationid);
	}
	
	// SETTINGS
	
	public function getSettingValue($key) {
		return $this->settings->getValue($key);
	}
	
	public function setSettingValue($key, $value) {
		$this->settings->setValue($key, $value);
	}
	
	public function deleteSettingValue($key) {
		$this->settings->deleteValue($key);
	}
	
}

?>