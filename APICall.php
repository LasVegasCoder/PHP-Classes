<?php

	/*
		Name:		Simple API Caller 
		Desc:		API Caller Utility allows your application to make call to any api and return result.
		Version:	V1.0
		Date:		3/11/2017
		Author:		Prince Adeyemi
		Contact:	prince@vegasnewspaper.com
		Facebook:	fb.com/YourVegasPrince
		
		Usage:
		Assuming you want to call Amazon;
		
		$api = new PrinceAPICaller();  
		$result = $api->_sendRequest('http://domain.com/path/to/endpoint');
		
		print_r( $result );
		
		To pass DATA to your endpoint.
		
			$data = array( 'username' => 'MyUsername', 'password' => 'Mypassword' );
		
			$api->_sendRequest('http://amazon.com/whatever/path/api/or/webpage', $data );
			
		To use a GET method, specify 'GET' e.g.
			$api = new PrinceAPICaller('GET');
			$result = $prince->_sendRequest('http://google.com');
			
			print_r( $result );
			
		SSL cannot protect user's DATA that's intercepted on local network such as free Wifi
		
		I added encryption options, this way you can encrypt your data from your app before sending it to your server for process.
		
		For example: When user submit a form from your app or webpage, for a stronger security,
		we can now encrypt the whole form data, or part of the submitted DATA before sending it to our server for processing.
		
		e.g: Your user submits data such as: First, Last, Credit Card Information, D.O.B, and other vital information from your website or app.
		Such information is susceptible to sniffers on the same network, i.e a hacker sitting at a Starbucks may sniffing network may sniff 
		any DATA submitted by any client that's connected to the same Starbucks network.
		
		ATTACKER     ---------------------------  ROUTER/WiFi ----------------------------- USERS
		Attacker is connected to a network such as free wifi (Starbucks for example), attacker lauches sniffers, or ARP Poison attack
		which forwards users data to attacker before the attacker reroute such data to the real ROUTER after with copy of data dumped on his local device
		for later reviewing.
		
		As you can see, SSL cannot protect user's DATA intercepted on local network.
		
		To mitigate this type of attack, I prefer to encrypt user's DATA before leaving the webpage, or application. i.e before it get
		sent over the network; therefore FIRSTNAME will look like $Zmlyc3RuYW1l221.2c3RuYW1l if ever sniffed. As you can see that this data is 
		useless to the attacker if sniffed over the network because the attacker MUST know the password to decrypt it.
		
		Server side:
		Server received the encrypted DATA save it to database or use our known secret to decrypt it, then process it fast and display 
		the decypted data to user:
		
		SERVER RECEIVED $Zmlyc3RuYW1l221.2c3RuYW1l ------SAVE|DECRYPT it to FIRSTNAME and process.
		
		Server side can also encrypt the data and send it down to your app or webpage, of course the app will decrypt the data
		process it and displays it to users.
		
		This is an added security to ensure that your user's data is totally encrypted, before sent over tcp either using ssl or not.
		
		For example:
		$DataToEncrypt = array(
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'payment' => array(
				'paymentid' => $paymentID,
				'creditcard' => $creditcard,
				'expiredate' => $expire,
				'billing' => $billing
			),
			'status' => $status
		);
		
		$EncryptedDATA = EncryptIt( $DataToEncrypt, null, 'myGreatSecreteKey' );
		print_r($EncryptedDATA); 
		OUTPUT: $c3Nkam5ka3dlZm9wZmtvcGYgd2Yga2ZsYyBhIGNsa2FldiBtIHZha2xhdmF2dmE=
		
		Now you can send $EncryptedDATA to your app/web server and use 'myGreatSecreteKey' to Decrypt it from the server before processing.
		
		So on your server:
		//Receive form submission
		  $incomingData = $_REQUEST['form_data'];
		  
		// Decrypt form submission if neccessary or save it to database  
		  $Decrypted = DecryptIt( $incomingData, null, 'myGreatSecreteKey' );
		  print_r(  $Decrypted );
		  OUTPUT: Real Data.
		  
	This is very useful when you are concerned about network sniffing, hacking, etc.  Even if attacker get your encrypted data, 
	it is completely useless unless he/she know your 'secret key' to decrypt the encrypted data.
	*/

	
	if( !class_exists( 'PrinceAPICaller' ) )
	{
		class PrinceAPICaller
		{
			private $_ch;
			private $_error;
			private $_result;
			private $_cookieFile;
			private $_method;
			
			private $_ENC_METHOD 	= "AES-256-CBC";
			private $_ENC_KEY 	= "MySecretKey12345";
			private $_ENC_IV	= "mySecretemySecret";
			
			//Begins
			
			public function __Construct( $reqType='post' )
			{
				$this->_error = array( 
					'code' => '0', 
					'errorMessage' => '');
					
				$this->_ch = null;
				$this->_result = '';	
				$this->_cookieFile = '/tmp/apiCallerCookies.txt';
				
				$this->_method  = ( isset( $reqType ) && !empty( $reqType ) ) ? strtolower( $reqType ) : 'post' ;
			}
			
			public function _sendRequest( $endpoint, $data=null )
			{
				// safe check
				$endpoint = ( isset( $endpoint ) && !empty( $endpoint ) ) ? $endpoint : '';
				$postData = ( isset( $data ) && !empty( $data ) ) ? http_build_query( $data ) : null;
				
				
				if( empty( $endpoint ) )
				{
					$this->_error = array(
						'code' => 1 ,
						'errorMessage' => "Uh oh, I don't have telepathy to know your endpoint!");
						
					return $this->_error;
				}
				
				// check if curl is installed
				if( !function_exists( 'curl_init' ) )
				{
					$this->_error = array(
						'code' => 2,
						'errorMessage' => 'Curl not installed, please install curl');
					
					return $this->_error;
				}
				
				$this->_ch = ( $this->_ch == null ) ? curl_init() : $this->_ch;
				
				if( $this->_method == 'post' )
				{
					curl_setopt( $this->_ch, CURLOPT_URL, $endpoint );
					curl_setopt( $this->_ch, CURLOPT_COOKIEJAR, $this->_cookieFile);
					curl_setopt( $this->_ch, CURLOPT_FOLLOWLOCATION, true ) ;
					curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $this->_ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)" );
					curl_setopt( $this->_ch, CURLOPT_POST, true );
					curl_setopt( $this->_ch, CURLOPT_POSTFIELDS, $postData );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYHOST, FALSE );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYPEER, FALSE );
						
					$this->_result	= curl_exec( $this->_ch );
					
					if( curl_error( $this->_ch ) )
					{
						return curl_error( $this->_ch );
					}
					curl_close( $this->_ch );
					
						return $this->_result;
					
				} 
				else
				if( $this->_method == 'get' )
				{	
					$endpoint = str_replace(' ', "%20", $endpoint);
					
					curl_setopt( $this->_ch, CURLOPT_URL, $endpoint );
					curl_setopt( $this->_ch, CURLOPT_COOKIEJAR, $this->_cookieFile);
					curl_setopt( $this->_ch, CURLOPT_FOLLOWLOCATION, true );
					curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt( $this->_ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYHOST, FALSE );
					curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYPEER, FALSE );
						
					$this->_result	= curl_exec( $this->_ch );
					
					if( curl_error( $this->_ch ) )
					{
						return curl_error( $this->_ch );
					}
					curl_close( $this->_ch);
					
					return $this->_result ;
				}
				else {
					$this->_error = array(
						'code' => 3,
						'errorMessage' => 'Invalid request, POST | GET allowed ');
					
					return $this->_error;
				}
				
			}// end of _sendRequest
			
			public function EncryptIt( $DataToEncrypt, $Method = null, $SecretKey=null, $iv = "mySecretemySecre" )
			{
				$Method = ( $Method !== null )  ? $Method : $this->_ENC_METHOD;
				$SecretKey = ( $SecretKey !== null )  ? $SecretKey : $this->_ENC_KEY;
				$iv = ( $iv !== null )  ? $iv : $this->_ENC_IV;
				
				$Encrypted = openssl_encrypt( $DataToEncrypt, $Method, $SecretKey, 0, $iv ); 
				if( $Encrypted )
				{
					return $Encrypted;
				}
			}


			public function DecryptIt( $EncryptedData, $Method = null, $SecretKey=null, $iv = null )
			{
				$Method = ( $Method !== null )  ? $Method : $this->_ENC_METHOD;
				$SecretKey = ( $SecretKey !== null )  ? $SecretKey : $this->_ENC_KEY;
				$iv = ( $iv !== null )  ? $iv : $this->_ENC_IV;
				
				$Decrypted = openssl_decrypt( $EncryptedData, $Method, $SecretKey, 0, $iv );
				
				if( $Decrypted )
				{
					return $Decrypted;
				}
			}		
			
		} // end of class
	} //end of class checking
?>
