<?php

	If(!class_exists('WebController')){
		class WebController{
			
			/* private variables */
			private $_error_message = array();
			/* public variables */
			
			/* utilities */
			
			/* constants */
			
			public function __Construct(){
				//echo "Hello contstruct";
				//$this->run();
				
				//echo 'Construct <br />';
				
				
				$logindata = array(
					'username' => 'prince',
					'userpass' => '12345',
					'userhash' => 'sjdkidsiwjijfwe'
				);
				
				$this->debug($logindata);
			}
			
			private function run(){
				echo 'Hello world!';
				
				//exit();
			}
			
			private function login( $login_data = array() ){
				
				debug($login_data);
				
				
			}
			
			
			private function register( $register_data = array() ){
				
				
			}
			
			private function logout( $logout_data = array() ){
				
				
			}			
			
			/*Debug Data */
			public function debug( $data = array() ){
				if(empty($data) || count($data)==0){
				   $this->_error_message = array(
				   	'error_code' => -1,
					'error_message' => 'Data cannot be empty'   
				   );
				}
				print '<pre>' . print_r( $data, true )  . '</pre>';
				//exit();
			}
			
		} //end of class WebController
	}

	
	new WebController();
?>
