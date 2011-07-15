<?php

class ListInfoscreens extends TPage {
	
	protected function getInfoscreens($customer) {
		$data = array();
		
		$infoscreenids = $customer->getInfoscreenIds();
		foreach($infoscreenids as $id) {
			$infoscreen = $this->Session['customer']->getInfoscreen($id);
			array_push($data, array("id" => $id, "title" => $infoscreen->getTitle(), "motd" => $infoscreen->getMotd()));
		}
		
		return $data;
	}

	public function onLoad($param) {
    	parent::onLoad($param);
    	if(!$this->IsPostBack) {
    		$this->Session->open();
			
			if(!isset($this->Session['customer'])) {
				// missing customer in session, redirect to login page
				$this->Response->redirect($this->Service->constructUrl('Login', null, true));
			} else {
				$customer = $this->Session['customer'];					
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