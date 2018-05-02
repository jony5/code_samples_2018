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

/*
	In order to kill the session altogether, like to log the user out, the session id must also be unset. If a cookie 
	is used to propagate the session id (default behavior), then the session cookie must be deleted. setcookie() may be 
	used for that.
*/

class crnrstn_session_manager {

	//
	// SERIAL AND ENCRYPTION KEY FOR CRNRSTN SESSION VALUES
	public $configSerial;
	public $resourceKey;
	private static $cacheSessionParam_ARRAY = array();
	#private static $CRNRSTN_ENCRYPT_NACL;
	private static $CRNRSTN_ENCRYPT_CIPHER;
	
	private static $oLogger;
	private static $oCOOKIE_MGR;
	private static $encryptableDataTypes = array();
	private static $oSessionEnvironment;
	
	public function __construct($oENV=NULL){
		
		//
		// INSTANTIATE LOGGER
		if(!isset(self::$oLogger)){
			self::$oLogger = new crnrstn_logging();
		}
		
		if(isset($oENV)){
			self::$oSessionEnvironment = $oENV;
			
			//
			// INITIALIZE CONFIG SERIAL FOR SESSION SERIALIZATION
			$this->configSerial = self::$oSessionEnvironment->configSerial;	
			$this->resourceKey =  self::$oSessionEnvironment->returnResouceKey();	
			//
			// BEFORE SETTING SESSION NACL, NEED TO CHECK TO SEE IF MATCHES EXISTING.
			// IF DIFFERENT, NEED TO ITERATE THROUGH ALL SESSION PARAMS TO RE-ENCRYPT WITH NEW NACL
			// IF NACL CHANGES W/O UPDATE TO SESSION ENCRYPTED DATA, ALL DATA WILL BECOME GARBAGE.
			#$tmp_key = self::$oSessionEnvironment->getEnvKey();
			#error_log("crnrstn.session.inc.php (66) tmp_key: ".$tmp_key);
			#if($tmp_key!=''){
				#error_log("crnrstn.session.inc.php (44) Set session key to ".$tmp_key);
				//self::$CRNRSTN_ENCRYPT_NACL = $tmp_key;
				//$_SESSION['CRNRSTN_ENCRYPT_NACL'] = self::$CRNRSTN_ENCRYPT_NACL;
				#error_log("crnrstn.session.inc.php (70) **ALERT** Setting session key to value of: ".self::$CRNRSTN_ENCRYPT_NACL);
				//$this->setSessionKey($this, self::$CRNRSTN_ENCRYPT_NACL);
			#}
		}
		
		//
		// INITIALIZE ARRAY OF ENCRYPTABLE DATATYPES
		self::$encryptableDataTypes = array('string','integer','double','float','int');
		
		//
		// INITIALIZE CONFIG SERIAL FOR SESSION SERIALIZATION
		#$this->configSerial = self::$oSessionEnvironment->getEnvSerial();
		#error_log("************".$this->configSerial."*************");
		
		//
		// Function Source ::
		// http://php.net/manual/en/function.hash-equals.php
		// To transparently support decryption dependency with hash_equals on older versions of PHP:
		if(!function_exists('hash_equals')) {
		  function hash_equals($str1, $str2) {
			if(strlen($str1) != strlen($str2)) {
			  return false;
			} else {
			  $res = $str1 ^ $str2;
			  $ret = 0;
			  for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
			  return !$ret;
			}
		  }
		}


	}
	#$_SESSION['CRNRSTN_CONFIG_SERIAL']
	#$_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY']
	public function setSessionParam($sessParam, $val=NULL){
		#error_log("crnrstn.session.inc.php (107) Setting a session param [".$sessParam."] with value [".$val."] and config serial: [".$_SESSION['CRNRSTN_CONFIG_SERIAL']."] | Resource Key: [".$this->resourceKey."]");

		
		if(in_array(gettype($val),self::$encryptableDataTypes)){
			//
			// UPDATE THE SESSION PARAMETER WITH THE VALUE
			#self::$cacheSessionParam_ARRAY[$sessParam] = trim($val);
			#error_log("crnrstn.session.inc.php (115) Encryptable Value of val [".$val."] to be stored in session for ".$sessParam." with resource key [".$this->resourceKey."]");
			//
			// CLEAR POTENTIAL CACHE TO FORCE REFRESH
			unset(self::$cacheSessionParam_ARRAY[$sessParam]);
			$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)] = $this->sessionParamEncrypt($val);
			$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_ENCRYPT_'.crc32($sessParam)] = 1;
			$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_DTYPE_'.crc32($sessParam)] = gettype($val);
			
			#error_log("crnrstn.session.inc.php (98) Encrypt parameter ".$sessParam." in session with value: ".$val." converted to [".$_SESSION[crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])][$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]."]");
			
		}else{
			//
			// NOT ENCRYPTABLE
			#self::$cacheSessionParam_ARRAY[$sessParam] = trim($val);
			unset(self::$cacheSessionParam_ARRAY[$sessParam]);
			$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)] = $val;
			$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_ENCRYPT_'.crc32($sessParam)] = 0;
		}

		return true;
	}
	
	public function getSessionParam($sessParam){
		$this->resourceKey = $_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY'];
		#error_log("crnrstn.session.inc.php (139) getSessionParam() Getting a session param [".$sessParam."] using config serial: ".$_SESSION['CRNRSTN_CONFIG_SERIAL']." | resource key [".$this->resourceKey."][".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']."]");
		//error_log("crnrstn.session.inc.php (139) getSessionParam() Getting a session param [".$sessParam."] using config serial: ".$this->configSerial." | resource key [".$this->resourceKey."]");
		
		//
		// RETURN THE VALUE ASSIGNED TO A PARTICULAR SESSION PARAMETER
		if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)])){
			#error_log("crnrstn.session.inc.php (145) getSessionParam() THIS PARAM: ".$sessParam." | Sess Stored value: ".$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]);
			if($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_ENCRYPT_'.crc32($sessParam)]>0){
				#error_log("crnrstn.session.inc.php (147) DTYPE [".$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_DTYPE_'.crc32($sessParam)]."]");
				switch($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_DTYPE_'.crc32($sessParam)]){
					case 'string':
						if(isset(self::$cacheSessionParam_ARRAY[$sessParam])){
							#error_log("/crnrstn/ crnrstn.session.inc.php (103) Using private static array for ".$sessParam);
							return self::$cacheSessionParam_ARRAY[$sessParam];
						}else{
							#error_log("/crnrstn/ crnrstn.session.inc.php (106) Using sessionParamDecrypt for ".$sessParam);
							self::$cacheSessionParam_ARRAY[$sessParam] = trim($this->sessionParamDecrypt($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]));
							return self::$cacheSessionParam_ARRAY[$sessParam];
						}
					break;
					case 'integer':
						if(isset(self::$cacheSessionParam_ARRAY[$sessParam])){
							return (integer) self::$cacheSessionParam_ARRAY[$sessParam];
						}else{
							self::$cacheSessionParam_ARRAY[$sessParam] = trim($this->sessionParamDecrypt($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]));
							return (integer) self::$cacheSessionParam_ARRAY[$sessParam];
						}
					break;
					case 'int':
						if(isset(self::$cacheSessionParam_ARRAY[$sessParam])){
							return (int) self::$cacheSessionParam_ARRAY[$sessParam];
						}else{
							self::$cacheSessionParam_ARRAY[$sessParam] = trim($this->sessionParamDecrypt($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]));
							return (int) self::$cacheSessionParam_ARRAY[$sessParam];
						}
					break;
					case 'double':
						if(isset(self::$cacheSessionParam_ARRAY[$sessParam])){
							return (double) self::$cacheSessionParam_ARRAY[$sessParam];
						}else{
							self::$cacheSessionParam_ARRAY[$sessParam] = trim($this->sessionParamDecrypt($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]));
							return (double) self::$cacheSessionParam_ARRAY[$sessParam];
						}
					break;
					case 'float':
						if(isset(self::$cacheSessionParam_ARRAY[$sessParam])){
							return (float) self::$cacheSessionParam_ARRAY[$sessParam];
						}else{
							self::$cacheSessionParam_ARRAY[$sessParam] = trim($this->sessionParamDecrypt($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]));
							return (float) self::$cacheSessionParam_ARRAY[$sessParam];
						}
					break;
				}
			
			}else{
				//
				// NO ENCRYPTION APPLIED TO PARAM. RETURN SESSION VALUE.
				return $_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)];
			}
		}else{
			#error_log("crnrstn.session.inc.php (169) return false");
			return false;
		}
	}

	public function issetSessionParam($sessParam){
		#echo "Checking if issetSessionParam using config serial: ".$this->configSerial."<br>";
		#error_log("crnrstn.session.inc.php (206) Checking issetSessionParam on ".$sessParam." with config serial ".$_SESSION['CRNRSTN_CONFIG_SERIAL']." and resourceKey [".$this->resourceKey."]");
		#$_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)]
		if(isset($_SESSION["CRNRSTN_".crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]["CRNRSTN_".$this->resourceKey]['CRNRSTN_'.crc32($sessParam)])){
			#error_log("crnrstn.session.inc.php (130) issetSessionParam on ".$sessParam." evaluated to TRUE with value of: ".$_SESSION[$this->configSerial.'CRNRSTN'.crc32($sessParam)]);
			return true;
		}else{
			#error_log("crnrstn.session.inc.php (133) issetSessionParam on ".$sessParam." evaluated to FALSE");
			return false;
		}
	}
	
	//public function setSessionKey($oSESSION_MGR, $val){
		#echo '<br>setSessionKey() called storing value of '.$val.' with session serial '.$oSESSION_MGR->configSerial.'<br>';
	//	error_log("crnrstn.session.inc.php (219)  **************** setting session param _CRNRSTN_ENV_KEY to value of ".$val." [".$oSESSION_MGR->configSerial."]");
		#$_SESSION[$oSESSION_MGR->configSerial.'CRNRSTN'.crc32('_CRNRSTN_ENV_KEY')] = $val;
		#$oSESSION_MGR->setSessionParam('_CRNRSTN_ENV_KEY',$val);
	//}
	
	public function getSessionKey(){
		#error_log("crnrstn.session.inc.php (225) **************** getSessionKey ****************** _CRNRSTN_ENV_KEY: ".$_SESSION[$this->configSerial.'CRNRSTN'.crc32('_CRNRSTN_ENV_KEY')]);
		#error_log("crnrstn.session.inc.php (225) **************** getSessionKey ****************** CRNRSTN_RESOURCE_KEY: ".$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']);
		#echo "Getting getSessionKey with session serial ".$this->configSerial." and returning :: ".$_SESSION[$this->configSerial.'CRNRSTN'.crc32('_CRNRSTN_ENV_KEY')]."<br>";
		#return $_SESSION[$this->configSerial.'CRNRSTN'.crc32('_CRNRSTN_ENV_KEY')];
		return $_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY'];
		
		
		#return $this->getSessionParam('_CRNRSTN_ENV_KEY');
	}
	
	//public function getSessionNACL(){
//		return self::$CRNRSTN_ENCRYPT_NACL;
//	}
	
	public function setSessionIp($key, $ip){
		#$_SESSION[$this->configSerial.'CRNRSTN'.crc32($key)] = md5($ip);
		$_SESSION['CRNRSTN_'.crc32($this->configSerial).crc32($key)] = md5($ip);
	}
	
	public function getSessionIp(){
		#if(isset($_SESSION[$this->configSerial.'CRNRSTN'.crc32('SESSION_IP')])){
		if(isset($_SESSION['CRNRSTN_'.crc32($this->configSerial).crc32('SESSION_IP')])){
			#return $_SESSION[$this->configSerial.'CRNRSTN'.crc32('SESSION_IP')];
			return $_SESSION['CRNRSTN_'.crc32($this->configSerial).crc32('SESSION_IP')];
		}else{
			return false;
		}
	}

	private function sessionParamEncrypt($val){
		#error_log("crnrstn.session.inc.php (221) sessionParamEncrypt() encrypt cipher [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]."]");
		
		try{
			#if($this->issetSessionParam("_CRNRSTN_SESS_ENCRYPT_CIPHER")){
			if(isset($_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"])){
#error_log("crnrstn.env.inc.php (231) sessionParamEncrypt() _CRNRSTN_SESS_ENCRYPT_CIPHER [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]."] ");
#error_log("crnrstn.env.inc.php (232) sessionParamEncrypt() _CRNRSTN_SESS_ENCRYPT_SECRET_KEY [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"]."] ");
#error_log("crnrstn.env.inc.php (233) sessionParamEncrypt() _CRNRSTN_SESS_ENCRYPT_OPTIONS [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_OPTIONS"]."] ");
#error_log("crnrstn.env.inc.php (234) sessionParamEncrypt() _CRNRSTN_SESS_ENCRYPT_HMAC_ALG [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_HMAC_ALG"]."] ");
		
				$ivlen = openssl_cipher_iv_length($cipher=$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$ciphertext_raw = openssl_encrypt($val, $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"], $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"], $options=$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_OPTIONS"], $iv);
				$hmac = hash_hmac($_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_HMAC_ALG"], $ciphertext_raw, $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"], $as_binary=true);
				$ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
				
#				error_log("crnrstn.session.inc.php (238) sessionParamEncrypt() returning encrypted value [".$ciphertext."] ");
				return $ciphertext;
			}else{
				
				return $val;
			}

		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('session_manager->sessionParamEncrypt()', LOG_EMERG, $e->getMessage());
		}
	}
	 
	private function sessionParamDecrypt($val){
		#error_log("crnrstn.session.inc.php (278) sessionParamDecrypt() decryption attempt for Val: ".$val);
		#error_log("[".$this->getSessionParam("_CRNRSTN_SESS_ENCRYPT_CIPHER"));
		try{
			
			if(isset($_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"])){

				$c = base64_decode($val);
				$ivlen = openssl_cipher_iv_length($cipher=$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]);
				$iv = substr($c, 0, $ivlen);
				$hmac = substr($c, $ivlen, $sha2len=32);
				$ciphertext_raw = substr($c, $ivlen+$sha2len);
				$original_plaintext = openssl_decrypt($ciphertext_raw, $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"], $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"], $options=$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_OPTIONS"], $iv);
				$calcmac = hash_hmac($_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_HMAC_ALG"], $ciphertext_raw, $_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"], $as_binary=true);
				
				if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
				{
					#error_log("crnrstn.session.inc.php (294) DECRYPTED PARAM CIPHER --> [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".$this->resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]."]");
					return $original_plaintext;
				}else{
					//
					// HOOOSTON...VE HAF PROBLEM!
					#error_log("HOOOSTON...VE HAF PROBLEM!");
					throw new Exception('CRNRSTN Session Param Decrypt Notice :: Oops. Something went wrong. Hash_equals comparison failed during data decryption.');
				}
			
			}else{
				//
				// NO ENCRYPTION. RETURN VAL
				#error_log("crnrstn.session.inc.php (306) DECRYPTED PARAM (ENCRYPT IS CLEARTEXT) --> [".$val."]");
				return $val;
			}
			
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('session_manager->sessionParamDecrypt()', LOG_EMERG, $e->getMessage());
		}
	}



	public function __destruct() {
		
	}
}
?>