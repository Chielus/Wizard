<?php
class Login extends TPage
{
    /**
     * Event handler for the OnClick event of the login button.
     * @param TButton the button triggering the event
     * @param TEventParameter event parameter (null here)    
     */
    public function loginButtonClicked($sender, $param)
    {
        if ($this->IsValid)  // check if input validation is successful
        {
            // obtain the username and password from the textboxes
	    	$username = $this->Username->Text;
	    	$password = $this->Password->Text;
            
         	$this->checkCredentials($username, $password);
        }
    }
    
    protected function checkCredentials($username, $password)
    {
      $customer = new Customer($username, $password);
	  
	  if($customer->isValid()) {
		$this->Session->open();
		$this->Session['customer'] = $customer;
		$this->Session['lang'] = 'EN';
		
		// user has been authenticated, redirect to list of infoscreens
	  	$this->Response->redirect($this->Service->constructUrl('ListInfoscreens', null, true));
	  } else {
	  	$this->Message->Text = 'Invalid credentials!';
	  }
    }
}
?>
