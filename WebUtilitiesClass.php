<?php 
if(!class_exists('WebUtilities')){
	
	Class WebUtilities extends PDO{
		public function __Construct(){
			//echo 'WebUtilities called <br >';
			$this->_run();
		}
		
		private function _run(){
			
			WebUtilities::delete(10);
			
		}
		
		
		public static function delete( $short_code_id ){
			
			//echo "Deleting $short_code_id ";
			
		}
		
		
		
	} // end of class
}

 new WebUtilities;
 
 ?>