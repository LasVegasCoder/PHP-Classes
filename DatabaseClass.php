<?php
/*#! This Class is used to connect to any database engine, mysql, SQL, Oracle, InformxSQL and more
 * @Description	: 	DatabaseClass could be used to establish connection in a singleton pattern to connect to any Database Engine, 
 * 					extending PDO Object
 * @Return		: 	It return PDO::FETCH_ASSOC as an array, if false or 0, is passed to the DatabaseClass::runQuery third parameter or Fetch() since if true or 1 is passed.
 * @doInsert	:   It does insert data to database and return "data inserted" on success or NULL on failure.  Message could be customized as well.
 * @doRegister	: 	Function does registeration, insert array of data into database and return message upon success or failure.
 * @Author		: 	Prince Adeyemi
 * @License		: 	Private...  I nearly lost my life, so contact me before using any of this code or get ready for court for violation of everything and Intellectual Property!
 * @Contact		:	Email:  YourVegasPrince@gmail.com, Facebook: facebook.com/MyVegasPrince
 */
//DEFINED('DATABASE_CONNECT') or DIE('YOU ARE NOT AUTHORIZE TO USE THIS DIRECT. YOUR CONNECTING INFORMATION WAS LOGGED.');

if(!class_exists('DatabaseClass')){


	Class DatabaseClass extends PDO  {
		private static $_link = NULL;
		
		/* In a sigleton, it is a good practice to make it's __Construct private so it is not callable. */
		public function __Construct(){
			//echo 'DatabaseClass called';
			
		}
		
		/* Connect to database function */
		public static function _Connect( $dbengine = 'mysql', $host='127.0.0.1', $dbuser, $dbpass, $dbname = NULL  ){
			
			if(is_null( DatabaseClass::$_link )){
				/* Setup PDO Database Singleton Object */
				try{
					$link = new PDO ("$dbengine:host=$host;dbname=$dbname", $dbuser, $dbpass );
					$link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
					$link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
					$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					
					DatabaseClass::$_link = $link;
					if(DatabaseClass::$_link ){
						echo "Ashley you're freeking Connected! <br >";
						exit;
					}
				}
				catch( PDOException $e) {
					//throw new PDOException( $e->getMessage(), (int) $e->getCode() );
					echo 'Database Error MSG: ' . $e->getMessage() .'<br >Database Error Code: ' . $e->getCode() .'<br >';
				}
					
				
			}else {
				// Database is still active, nothing to do;
			}
			
			return DatabaseClass::$_link;
		}

		/* Query function */
		public static function runQuery($query, $params=array(), $all=false){
			
			//echo "runing Query : $query";
			$stm = DatabaseClass::$_link->prepare($query);
			
			if(!is_array($params)){
				echo "<p style='color:red; font-size:1.5em;'>Parameter must be an Array! Check parameter passed to the runQuery function.</p>";
				DatabaseClass::$_link = NULL;
				exit();
			}
			
			// echo "<pre>" . PRINT_R($params, 1) . "</pre>";
			//echo count($params);
			foreach( $params as $field => $value ){
				$stm->bindValue( $field, $value);	
			}			
			
			$bool = $stm->execute();
			if($bool){
				$result = (($all)? $stm->fetch() : $stm->fetchAll());
			}else {
				$result=NULL;
			}
			DatabaseClass::$_link = NULL;
			
			return $result;
		}
		
		
		/* Registration function */
		public static function doRegister($query, $params=array()){
			
			// echo "runing Query : $query";
			$stm = DatabaseClass::$_link->prepare($query);
			
			if(!is_array($params)){
				echo "<p style='color:red; font-size:1.5em;'>Parameter must be an Array! Check parameter passed to the doRegister function.</p>";
				DatabaseClass::$_link = NULL;
				exit();
			}
			
			foreach( $params as $field => $value ){
				$stm->bindValue( $field, $value);	
			}			
			
			$bool = $stm->execute();
			if($bool){

				if( $db->lastinsertid() > 0 ){
					// To do : send an email with an activation link to activate new account.
					// DatabaseClass::doActivation( $user_id );
					
					$message =  "<p style='color:green'>Registered Successfully! <br >Check your email to activate your account.</p>";
				}else {
					$message =  "<p style='color:red'>Registration failed!.<br > Check your input and try again.</p>";
				}
					
				DatabaseClass::$_link = NULL;
			}else {
				$message = "<p style='color:red'>Weird error occured! <br >Data not inserted.</p>";
			}
			return $message ;			
		}
		
		
		/* Registration function */
		public static function doInsert($query, $params=array()){
			
			//echo "runing Query : $query";
			$stm = DatabaseClass::$_link->prepare($query);
			
			if(!is_array($params)){
				echo "<p style='color:red; font-size:1.5em;'>Parameter must be an Array! Check parameter passed to the doRegister function.</p>";
				DatabaseClass::$_link = NULL;
				exit();
			}
			
			foreach( $params as $field => $value ){
				$stm->bindValue( $field, $value);	
			}			
			
			$bool = $stm->execute();
			if($bool){

				if( $db->lastinsertid() > 0 ){
					$message =  "<p style='color:green'>Data Successfully inserted into database.</p>";
				}else {
					$message =  "<p style='color:red'>Data NOT inserted!.<br > Check your input data and try again.</p>";
				}
					
				DatabaseClass::$_link = NULL;
			}else {
				$message = "<p style='color:red'>Weird error occured! <br >Data not inserted.</p>";
			}
			return $message ;			
		}		
		
		
	} //end of DatabaseClass
	
} // end of DatabaseClass class checking
