<?php
class InfoscreenConfiguration extends TPage
{
   
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
				$customer = $this->Session['customer'];
				$infoscreenid = $this->Session['infoscreenid'];
				$infoscreen = $customer->getInfoscreen($infoscreenid);
				
				$this->Title->Data = $infoscreen->getTitle();
				$this->Motd->Data = $infoscreen->GetMotd();
				$this->Rows->Data = $infoscreen->getSettingValue("rowstoshow");
				$this->Cycle->Data = $infoscreen->getSettingValue("cycleinterval");
				
				$this->Lang->DataSource = array("EN" => "EN", "NL" => "NL", "DE" => "DE", "FR" => "FR");
        		$this->Lang->dataBind();
			}
    	}
	}

	public function saveConfiguration() {
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
			
			$infoscreen->setTitle($this->Title->Data);
			$infoscreen->setMotd($this->Motd->Data);
			$infoscreen->setSettingValue("rowstoshow", $this->Rows->Data);
			$infoscreen->setSettingValue("cycleinterval", $this->Cycle->Data);
			$infoscreen->setSettingValue("lang", $this->Lang->SelectedValue);
		}
	}
   
}
?>
