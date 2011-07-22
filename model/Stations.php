<?php
    
class Stations {
	
	private $infoscreenid;
	private $stationids;
	
	function __construct($infoscreenid) {
		$this->infoscreenid = $infoscreenid;
		
		$db = new Db();
		$this->stationids = $db->getStationIds($infoscreenid);
	}
	
	// return array of stationids
	// format: array(type = (..., ..., ...), type = (..., ..., ...), ...)
	public function getStationIds() {
		return $this->stationids;
	}	

	// add station or throw exception
	public function addStation($stationid) {
		$found = false;
		foreach($this->stationids as $stations) {
			if(in_array($stationid, $stations)) {
				$found = true;
			}
		}

		if($found) {
			throw new Exception("This station has already been set.");			
		} else {
			$db = new Db();
			if(!$db->insertStation($this->infoscreenid, $stationid)) {
				throw new Exception("Adding station failed.");
			} else {
				$this->stationids = $db->getStationIds($this->infoscreenid);
			}
		}		
	}
	
	// remove station or throw exception
	public function removeStation($stationid) {
		$found = false;
		foreach($this->stationids as $stations) {			
			if(in_array($stationid, $stations)) {
				$found = true;
			}
		}

		if($found) {
			$db = new Db();
			if(!$db->deleteStation($this->infoscreenid, $stationid)) {
				throw new Exception("Station removal failed.");
			} else {
				$this->stationids = $db->getStationIds($this->infoscreenid);
			}
		} else {
			throw new Exception("This station hasn't been set yet.");
		}		
	}
	
}
    
?>
