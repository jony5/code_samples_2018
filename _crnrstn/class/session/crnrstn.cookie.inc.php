<?php
/*
// J5
// Code is Poetry */
#  CRNRSTN Suite :: An Open Source PHP Class Library to facilitate the execution of an application's code-base across multiple hosting environments.
#  Copyright (C) 2018 Jonathan 'J5' Harris.
#  VERSION :: 1.0.0
#  AUTHOR :: J5
#  URI :: http://crnrstn.jony5.com/
#  OVERVIEW :: Once CRNRSTN has been configured for your different hosting environments from localhost through to production, seamlessly 
#		   	   release a web application from one environment to the next without having to change your code-base to account for 
#			   environmentally specific parameters. Configure the profiles of each running environment to account for all of your 
#			   application's environmentally specific parameters; and do this all from one place with the CRNRSTN Suite ::
#  LICENSE :: This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public 
#			  License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.

#  You should have received a copy of the GNU General Public License
#  along with this program. Thandle_env_ARRAYhis license can also be downloaded from
#  my web site at (http://crnrstn.jony5.com/license.txt).  
#  If not, see <http://www.gnu.org/licenses/>

class crnrstn_cookie_manager {
	
	public static $cookie_ARRAY = array();
	public $configSerial;
	public static $tmp_cookie_name;
	public static $cookieValue_Encrypted;
	public static $cookieName_Encrypted;
	private static $cookieName_ChecksumSeed = 'CRNRSTN';				// SEED CHARS VALID FOR COOKIE NAME
	public static $thisCookieCrawler_ARRAY = array();
	private static $oLogger;
	
	public function __construct($name=NULL,$value=NULL,$expire=NULL,$path=NULL,$domain=NULL,$secure=NULL,$httponly=NULL) {

		//
		// INSTANTIATE LOGGER
		if(!isset(self::$oLogger)){
			self::$oLogger = new crnrstn_logging();
		}
		
		
		//
		// IF WE HAVE COOKIE NAME, ADD THE COOKIE
		if(isset($name)){
			error_log("cookie.inc.php (56) NAME IS set to ".$name);
			//
			// Because the expire argument is integer, it cannot be skipped with an empty string, use a zero (0) instead.
			if(!isset($expire)){
				$expire=0;
			}
			
			//
			// CHECK FOR INITIALIZATION OF COOKIE ENCRYPTION IN THIS SESSION
			if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
			
				error_log("/cookie.inc.php/ (67) COOKIE ENCRYPT NEEDED IN CONSTRUCTOR...");
				//
				// SET THE COOKIE
				#error_log("(139)[".$name."] [".$value."]");
				self::$cookieValue_Encrypted = $this->cookieParamEncrypt($value);
				self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
								
				error_log("crnrstn.cookie.inc.php (112)[".self::$cookieName_Encrypted."][".self::$cookieValue_Encrypted."]");
				return setcookie (self::$cookieName_Encrypted,self::$cookieValue_Encrypted,$expire,$path,$domain,$secure,$httponly);
				
			}else{
				
				
				error_log("/cookie.inc.php/ (80) COOKIE ENCRYPT NOT NEEDED IN CONSTRUCTOR...");
					
				//
				// SET THE COOKIE
				return setcookie ($name,$value,$expire,$path,$domain,$secure,$httponly);
			}

			//
			// SET THE COOKIE
			setcookie ($name,$value,$expire,$path,$domain,$secure,$httponly);
			
		}
	}
	
	##
	## METHOD NOTES/IDEAS
	# - addCookie ([cookie-name],[optional-cookie-value],[optional-cookie-expire],[optional-cookie-path],[optional-cookie-domain],[optional-cookie-secure],[optional-cookie-httponly])
	# - addRawCookie ([cookie-name],[optional-cookie-value],[optional-cookie-expire],[optional-cookie-path],[optional-cookie-domain],[optional-cookie-secure],[optional-cookie-httponly])
	#	* If output exists prior to calling this function, setcookie() will fail and return FALSE. 
	#	* Because the expire argument is integer, it cannot be skipped with an empty string, 
	#	  use a zero (0) instead. [http://www.php.net]
	#	* Like other headers, cookies must be sent before any output from your script (this is a protocol 
	#	  restriction). This requires that you place calls to this function prior to any output, including <html> 
	#	  and <head> tags as well as any whitespace. [http://www.php.net]
	#	* Consider integrating an output buffer into your cookie management. What are the performance 
	#	  implications (e.g. higher server cache requirements)...if any?
	# 	* ob_start() :: Some web servers (e.g. Apache) change the working directory of a script when calling the callback 
	#	  function. You can change it back by e.g. chdir(dirname($_SERVER['SCRIPT_FILENAME'])) in the callback function. [http://www.php.net]
	#	* If you wish to assign multiple values to a single cookie, just add [] to the cookie name. [http://www.php.net]
	#	* The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch. In other 
	#	  words, you'll most likely set this with the time() function plus the number of seconds before you want 
	#	  it to expire. Or you might use mktime(). time()+60*60*24*30 will set the cookie to expire in 30 days. If set 
	#	  to 0, or omitted, the cookie will expire at the end of the session (when the browser closes). [http://www.php.net]
	
	public function addCookie($name,$value=NULL,$expire=NULL,$path=NULL,$domain=NULL,$secure=NULL,$httponly=NULL){
		try{
			if(isset($name)){
				//
				// Because the expire argument is integer, it cannot be skipped with an empty string, use a zero (0) instead.
				if(!isset($expire)){
					$expire=0;
				}
				
				//
				// CHECK FOR INITIALIZATION OF COOKIE ENCRYPTION IN THIS SESSION
				if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
				
					error_log("/cookie.inc.php/ (105)	COOKIE ENCRYPT NEEDED");
					//
					// SET THE COOKIE
					#error_log("(139)[".$name."] [".$value."]");
					self::$cookieValue_Encrypted = $this->cookieParamEncrypt($value);
					self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
									
					error_log("crnrstn.cookie.inc.php (112)[".self::$cookieName_Encrypted."][".self::$cookieValue_Encrypted."]");
					return setcookie (self::$cookieName_Encrypted,self::$cookieValue_Encrypted,$expire,$path,$domain,$secure,$httponly);
					
				}else{
					
					
					error_log("/cookie.inc.php/ (109)	COOKIE ENCRYPT NOT NEEDED");
						
					//
					// SET THE COOKIE
					return setcookie ($name,$value,$expire,$path,$domain,$secure,$httponly);
				}
				
			}else{
				//
				// HOOOSTON...VE HAF PROBLEM!
				throw new Exception('CRNRSTN Cookie Management Notice :: A cookie failed to be initialized due to missing NAME parameter.');
			}
			
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('cookie_manager->addCookie()', LOG_NOTICE, $e->getMessage());
		}
	}
	
	public function addRawCookie($name,$value=NULL,$expire=NULL,$path=NULL,$domain=NULL,$secure=NULL,$httponly=NULL){
		try{
			if(isset($name)){
				//
				// Because the expire argument is integer, it cannot be skipped with an empty string, use a zero (0) instead.
				if(!isset($expire)){
					$expire=0;
				}
				
				if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
					
					self::$cookieValue_Encrypted = $this->cookieParamEncrypt($value);
					self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
					
					setrawcookie (self::$cookieName_Encrypted,self::$cookieValue_Encrypted,$expire,$path,$domain,$secure,$httponly);
					
				}else{
					//
					// SET THE RAW COOKIE. CLEAR TEXT
					setrawcookie ($name,$value,$expire,$path,$domain,$secure,$httponly);
				
				}
				
				
			}else{
				//
				// HOOOSTON...VE HAF PROBLEM!
				throw new Exception('CRNRSTN Cookie Management Notice :: A raw cookie failed to be initialized due to missing NAME parameter.');
			}
			
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('cookie_manager->addRawCookie()', LOG_NOTICE, $e->getMessage());
		}
	}
	
	# - deleteCookie([cookie-name])
	# 	* When deleting a cookie you should assure that the expiration date is in the past, to trigger the 
	#	  removal mechanism in your browser. 
	#   * Test this with multi-dimen arrays
	public function deleteCookie($name,$path=NULL){
		//
		// CHECK FOR REQUIRED INFORMATION
		try{
			if(isset($name)){
				//
				// OK TO ATTEMPT DELETION OF COOKIE
				// CHECK FOR COOPKIE ENCRYPTION LAYER
				if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
					//
					// OK TO ATTEMPT DELETION OF COOKIE
					error_log("cookie.inc.php (210) DELETE ENCRYPTED COOKIE....");
					self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
					#setcookie (self::$cookieName_Encrypted,'', time() - 3600,$path);
					setcookie (self::$cookieName_Encrypted,'', 1 ,$path);
				}else{
				
					//
					// NO COOKIE ENCRYPTION. SET COOKIE. 
					error_log("cookie.inc.php (218) DELETE CLEAR TEXT COOKIE....".$name);
					#setcookie ($name,'', time() - 3600,$path);
					setcookie ($name,'', 1,$path);
					
				}
			}else{
				//
				// HOOOSTON...VE HAF PROBLEM!
				throw new Exception('CRNRSTN Cookie Management Notice :: Failed to delete cookie due to missing NAME parameter.');
			}
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('cookie_manager->deleteCookie()', LOG_NOTICE, $e->getMessage());
		}
	}
	
	# - getCookie([cookie-name])
	public function getCookie($name){
		//
		// CHECK FOR REQUIRED INFORMATION
		try{
			if(isset($name)){
				//
				// OK TO ATTEMPT TO GET COOKIE
				// CHECK FOR INITIALIZATION OF COOKIE ENCRYPTION IN THIS SESSION
				if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
					#error_log("cookie.inc.php (248) COOKIE DECRYPTION NEEDED....");
					self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
					#error_log("cookie.inc.php (250) COOKIE LOOKING FOR....".self::$cookieName_Encrypted);
					if(isset($_COOKIE[self::$cookieName_Encrypted])){
						#error_log("cookie.inc.php (252) *****WE HAVE ENCRYPTED COOKIE...*****");
						self::$cookieValue_Encrypted = $_COOKIE[self::$cookieName_Encrypted];
						return trim($this->cookieParamDecrypt(self::$cookieValue_Encrypted));
					}else{
						//
						// $_COOKIE NOT SET WITH THIS PARAMTER NAME. IS THE CALLING SCRIPT AT A $path THAT PROVIDES VISIBILITY TO THE COOKIE FOR WHICH YOU ARE SEARCHING?
						return false;
					}
				
				}else{
					//
					// NO ENCRYPTION. RETURN COOKIE.
					error_log("cookie.inc.php (265) NO COOKIE DECRYPTION NEEDED. Getting cookie...".$name);
					return $_COOKIE[$name];
				}

			}else{
				//
				// HOOOSTON...VE HAF PROBLEM!
				throw new Exception('CRNRSTN Cookie Management Notice :: Failed to get cookie due to missing NAME parameter.');
			}
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('cookie_manager->getCookie()', LOG_ERR, $e->getMessage());
		}
	}
	
//	public function getEncryptedCookie($name){
//		//
//		// CHECK FOR REQUIRED INFORMATION
//		try{
//			if(isset($name)){
//				//
//				// OK TO ATTEMPT TO GET COOKIE
//				self::$cookieName_Encrypted = self::$cookieName_ChecksumSeed.crc32($name);
//				if($_COOKIE[self::$cookieName_Encrypted]){
//					self::$cookieValue_Encrypted = $_COOKIE[self::$cookieName_Encrypted];
//					return trim($this->cookieParamDecrypt(self::$cookieValue_Encrypted));
//				}else{
//				
//					return false;
//				}
//			}else{
//				//
//				// HOOOSTON...VE HAF PROBLEM!
//				throw new Exception('CRNRSTN Cookie Management Notice :: Failed to get cookie (encrypted) due to missing NAME parameter.');
//			}
//		}catch( Exception $e ) {
//			//
//			// SEND THIS THROUGH THE LOGGER OBJECT
//			self::$oLogger->captureNotice('cookie_manager->getEncryptedCookie()', LOG_NOTICE, $e->getMessage());
//		}
//	}	
	

	private function cookieParamEncrypt($val){
		
		try{
			#if($this->issetSessionParam("_CRNRSTN_SESS_ENCRYPT_CIPHER")){
			#error_log("crnrstn.session.inc.php (320) _SESSION['CRNRSTN_CONFIG_SERIAL'] [".$_SESSION['CRNRSTN_CONFIG_SERIAL']."] and configSerial [".$this->configSerial."]");
			if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){
		
				$ivlen = openssl_cipher_iv_length($cipher=$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"]);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$ciphertext_raw = openssl_encrypt($val, $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"], $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"], $options=$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_OPTIONS"], $iv);
				$hmac = hash_hmac($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG"], $ciphertext_raw, $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"], $as_binary=true);
				$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
				
#				error_log("crnrstn.session.inc.php (238) sessionParamEncrypt() returning encrypted value [".$ciphertext."] ");
				return $ciphertext;
			}else{
				#error_log("crnrstn.session.inc.php (335) cookieParamEncrypt() _CRNRSTN_COOKIE_ENCRYPT_CIPHER not set in session...");
				return $val;
			}

		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('session_manager->cookieParamEncrypt()', LOG_EMERG, $e->getMessage());
		}
		



	}
	 
	private function cookieParamDecrypt($val){
	
		#error_log("crnrstn.session.inc.php (356) cookieParamDecrypt() decryption attempt for Val: ".$val);
		#error_log("[".$this->getSessionParam("_CRNRSTN_SESS_ENCRYPT_CIPHER"));
		try{
			
			if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"])){

				$c = base64_decode($val);
				$ivlen = openssl_cipher_iv_length($cipher=$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"]);
				$iv = substr($c, 0, $ivlen);
				$hmac = substr($c, $ivlen, $sha2len=32);
				$ciphertext_raw = substr($c, $ivlen+$sha2len);
				$original_plaintext = openssl_decrypt($ciphertext_raw, $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"], $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"], $options=$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_OPTIONS"], $iv);
				$calcmac = hash_hmac($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG"], $ciphertext_raw, $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"], $as_binary=true);
				
				if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
				{
					return $original_plaintext;
				}else{
					//
					// HOOOSTON...VE HAF PROBLEM!
					#error_log("HOOOSTON...VE HAF PROBLEM!");
					throw new Exception('CRNRSTN Cookie Param Decrypt Notice :: Oops. Something went wrong. Hash_equals comparison failed during data decryption.');
				}
			
			}else{
				//
				// NO ENCRYPTION. RETURN VAL
				#error_log("crnrstn.session.inc.php (308) DECRYPTED PARAM (ENCRYPT IS CLEARTEXT) --> [".$val."]");
				return $val;
			}
			
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('session_manager->cookieParamDecrypt()', LOG_CRIT, $e->getMessage());
		}

	}	
	
	
	public function deleteAllCookies($path=NULL){
		
		//
		// LETS TRY WORKING WITH A HANDLE.
		#self::$cookie_ARRAY=$_COOKIE;
		self::$cookie_ARRAY=array_keys($_COOKIE);
		error_log("cookie.inc.php (388) sizeof cookie_ARRAY->".sizeof(self::$cookie_ARRAY));
		#$cookiesSet = array_keys($_COOKIE);
		for ($x=0;$x<count(self::$cookie_ARRAY);$x++){
			error_log("cookie.inc.php (391) cookie name->".self::$cookie_ARRAY[$x]);
			setcookie(self::$cookie_ARRAY[$x],"",time()-1,$path);
		}
		
//		for($i=0;$i<sizeof(self::$cookie_ARRAY);$i++){
//			self::$thisCookieCrawler_ARRAY = each(self::$cookie_ARRAY);
//			 
//			//
//			// 	CLEAR OUT ALL NON-MULTIDIMENSAIONAL ARRAY COOKIES
//			if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']]) && isset(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']])){
//				error_log("cookie.inc.php (401) cookie name: ".self::$thisCookieCrawler_ARRAY['key']);
//				$this->deleteCookie(self::$thisCookieCrawler_ARRAY['key']);
//				
//			}else{
//				
//				//
//				// WE HAVE AT LEAST ONE 1-DIMENSION ARRAY TO TRAVERSE FROM THE $_COOKIE[] GLOBAL
//				foreach (self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']] as $tmp_cookie_name=>$tmp_cookie_value) {
//					#echo "[".$tmp_cookie_name."]";
//					
//					//
//					// 	EXPIRE SIMPLE COOKIES
//					if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name])){
//						self::$tmp_cookie_name = self::$thisCookieCrawler_ARRAY['key']."[".$tmp_cookie_name."]";
//						error_log("cookie.inc.php (415) cookie name: ".self::$tmp_cookie_name);
//						$this->deleteCookie(self::$tmp_cookie_name);
//					}else{
//						foreach (self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name] as $tmp_cookie_name_dim2=>$tmp_cookie_value_dim2) {
//							#echo "--[".$tmp_cookie_name_dim2."]<br>";
//							
//							//
//							// 	EXPIRE 1 DIMENSAIONAL ARRAY COOKIES
//							if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2])){
//								self::$tmp_cookie_name = self::$thisCookieCrawler_ARRAY['key']."[".$tmp_cookie_name."][".$tmp_cookie_name_dim2."]";
//								error_log("cookie.inc.php (425) cookie name: ".self::$tmp_cookie_name);
//								$this->deleteCookie(self::$tmp_cookie_name);
//							}else{
//								foreach (self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2] as $tmp_cookie_name_dim3=>$tmp_cookie_value_dim3) {
//									#echo "--[[[[".$tmp_cookie_name_dim3."]<br>";
//									
//									//
//									// 	EXPIRE 2 DIMENSAIONAL ARRAY COOKIES
//									if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2][$tmp_cookie_name_dim3])){
//										self::$tmp_cookie_name = self::$thisCookieCrawler_ARRAY['key']."[".$tmp_cookie_name."][".$tmp_cookie_name_dim2."][".$tmp_cookie_name_dim3."]";
//										error_log("cookie.inc.php (435) cookie name: ".self::$tmp_cookie_name);
//										$this->deleteCookie(self::$tmp_cookie_name);
//									}else{
//										foreach (self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2] as $tmp_cookie_name_dim3=>$tmp_cookie_value_dim3) {
//											#echo "--[[[[".$tmp_cookie_name_dim3."]<br>";	
//											
//											//
//											// 	EXPIRE 3 DIMENSAIONAL ARRAY COOKIES
//											if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2][$tmp_cookie_name_dim3])){
//												self::$tmp_cookie_name = self::$thisCookieCrawler_ARRAY['key']."[".$tmp_cookie_name."][".$tmp_cookie_name_dim2."][".$tmp_cookie_name_dim3."]";
//												error_log("cookie.inc.php (445) cookie name: ".self::$tmp_cookie_name);
//												$this->deleteCookie(self::$tmp_cookie_name);
//											}else{
//												foreach (self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2][$tmp_cookie_name_dim3] as $tmp_cookie_name_dim4=>$tmp_cookie_value_dim4) {
//													#echo "--[[[[".$tmp_cookie_name_dim4."]<br>";
//				
//													//
//													// 	EXPIRE 4 DIMENSAIONAL ARRAY COOKIES
//													if(!is_array(self::$cookie_ARRAY[self::$thisCookieCrawler_ARRAY['key']][$tmp_cookie_name][$tmp_cookie_name_dim2][$tmp_cookie_name_dim3][$tmp_cookie_name_dim4])){
//														self::$tmp_cookie_name = self::$thisCookieCrawler_ARRAY['key']."[".$tmp_cookie_name."][".$tmp_cookie_name_dim2."][".$tmp_cookie_name_dim3."][".$tmp_cookie_name_dim4."]";
//														error_log("cookie.inc.php (445) cookie name: ".self::$tmp_cookie_name);
//														$this->deleteCookie(self::$tmp_cookie_name);
//													}
//												}
//											}
//										}
//									}
//								}
//							}
//						}
//					}
//				}
//			}
//		}
//		
		return true;
	}
	
	public function getAllCookies(){
		return $_COOKIE;
	}
	
	public function __destruct() {

	}
}

?>