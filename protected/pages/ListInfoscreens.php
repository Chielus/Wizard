<?php

class ListInfoscreens extends TPage {
	
	protected function getInfoscreens($customer) {
		$data = array();
		
		$infoscreenids = $customer->getInfoscreenIds();
		foreach($infoscreenids as $id) {
			$infoscreen = new Infoscreen($id);
			array_push($data, array("id" => $id, "infotext" => $infoscreen->getInfoText(), "motd" => $infoscreen->getMotd()));
		}
		
		return $data;
	}

	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
			$this->Session->open();
			$customer = $this->Session['customer'];
			
			if($customer == null) {
				// missing customer in session, redirect to login page
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else {										
        		$this->DataGrid->DataSource = $this->getInfoscreens($customer);
        		$this->DataGrid->dataBind();
			}
    	}
	}
	
	public function configureInfoscreen($sender, $param) {
		$infoscreenid = $param->getCommandParameter();
		
		$this->Session->open();
		$this->Session['infoscreenid'] = $infoscreenid;
		
		$this->Response->redirect($this->Service->constructUrl('InfoscreenStops', null, true));
	}

}

?>