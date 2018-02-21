<?php
/*
// J5
// Code is Poetry */
#  CRNRSTN Suite :: An Open Source PHP Class Library to configure an applications' code-base to run in multiple hosting environments.
#  Copyright (C) 2018 Jonathan 'J5' Harris.
#  VERSION :: 1.0.0
#  AUTHOR :: J5
#  URI :: http://crnrstn.jony5.com/
#  OVERVIEW :: Once CRNRSTN has been configured for your different hosting environments, seamlessly release a web application from
#              one environment to the next without having to change your code-base to account for environmentally specific parameters.
#  LICENSE :: This program is free software: you can redistribute it and/or modify
#             it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of 
#             the License, or (at your option) any later version.

#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.

#  You should have received a copy of the GNU General Public License
#  along with this program. Thandle_env_ARRAYhis license can also be downloaded from
#  my web site at (http://crnrstn.jony5.com/license.txt).  
#  If not, see <http://www.gnu.org/licenses/>

class crnrstn {

	private static $oLogger;
	public static $log_profl_ARRAY = array();
	public static $log_endpt_ARRAY = array();
	
	public $configSerial;
	public $configMatchCount = array();
	
	public $opensslSessEncryptCipher = array();
	public $opensslSessEncryptSecretKey = array();
	public $opensslSessEncryptOptions = array();
	public $sessHmac_algorithm = array();
	
	public $opensslCookieEncryptCipher = array();
	public $opensslCookieEncryptSecretKey = array();
	public $opensslCookieEncryptOptions = array();
	public $cookieHmac_algorithm = array();
	
	private static $handle_srvr_ARRAY = array();

	private static $env_detect_ARRAY = array();
	public $handle_env_ARRAY = array();
	private static $env_name_ARRAY = array();
	
	public $grant_accessIP_ARRAY = array();
	public $deny_accessIP_ARRAY = array();

	public $oMYSQLI_CONN_MGR;

	private static $envDetectRequiredCnt;
	
	public static $handle_resource_ARRAY = array();
	
	private static $serverAppKey = array();
	
	private static $env_select_ARRAY = array();
	
	private static $envMatchCount;
	private static $envChecksum;

	public function __construct($srvr_ARRAY, $serial) {
		#error_log("crnrstn.inc.php (30) Initializing config serialization with :".$serial);
		//
		// CHECK SERVER SUPER GLOBAL ARRAY FOR DATA
										
				
		//
		// INSTANTIATE LOGGER
		if(!isset(self::$oLogger)){
			self::$oLogger = new crnrstn_logging();
		}
		

		
		try{
			if(!array_key_exists('SERVER_ADDR', $srvr_ARRAY)){
				//
				// HOOOSTON...VE HAF PROBLEM!
				throw new Exception('CRNRSTN initialization error :: $_SERVER[] super global has not been passed to the crnrstn class object successfully on server '.$_SERVER['SERVER_NAME'].' ('.$_SERVER['SERVER_ADDR'].').');
			}else{	

				
				//
				// STORE SUPER GLOBAL ARRAY WITH SERVER DATA TO SUPPORT ENVIRONMENTAL DETECTION
				self::$handle_srvr_ARRAY=$srvr_ARRAY;
				
				//
				// STORE CONFIG SERIAL KEY AND INITIALIZE MATCH COUNT.
				$this->configSerial = $serial;
				$this->configMatchCount[crc32($serial)] = 0;
				
				//
				// IF EARLY ENV DETECTION DURING defineEnvResource() DUE TO SPECIFIED requiredDetectionMatches(), STORE HERE: 
				self::$serverAppKey[crc32($this->configSerial)] = "";

				
				//
				// INSTANTIATE SESSION MANAGER
				#if(!isset($this->oSESSION_MGR)){
				#	$this->oSESSION_MGR = new crnrstn_session_manager($this);
				#}
		
				//
				// INITIALIZE DATABASE CONNECTION MANAGER. [##ENHANCEMENT##]IF MySQL < 4.1.3, NEED TO USE MYSQL PROCEEDURALLY
				if(!isset($this->oMYSQLI_CONN_MGR)){
					#error_log("crnrstn.inc.php construct() (93) ********CONFIG SERIAL: ******** ".$this->configSerial." *******************");
					$this->oMYSQLI_CONN_MGR = new crnrstn_mysqli_conn_manager($this->configSerial);
				}
				
				//
				// INITIALIZE IP ADDRESS SECURITY MANAGER
				if(!isset($this->oCRNRSTN_IPSECURITY_MGR)){
					$this->oCRNRSTN_IPSECURITY_MGR = new crnrstn_ip_auth_manager(self::$handle_srvr_ARRAY['REMOTE_ADDR']);
					#error_log("crnrstn.inc.php construct() (113) ******** IP : ******** ".$this->oCRNRSTN_IPSECURITY_MGR->clientIpAddress()." *******************");
					
				}
				
				//
				// INITIALIZE CIPHER SECURITY MANAGER
				#if(!isset($this->oCRNRSTN_CIPHER_MGR)){
					#error_log("crnrstn.inc.php (100) passing in configSerial of ".$this->configSerial." to cipher_manager");
				#	$this->oCRNRSTN_CIPHER_MGR = new crnrstn_cipher_manager($this->configSerial);
				#}			
			
			
			}
		} catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('crnrstn->__construct()', LOG_EMERG, $e->getMessage());
		}
		

	}
	
	public function returnConfigSerial(){
		
		return $this->configSerial;
		
	}
	
	
	
	public function addEnvironment($key, $errorReporting){
		#error_log("crnrstn.inc.php addEnvironment (102) ********CONFIG SERIAL: ******** ".$this->configSerial." *******************");
		$this->addServerEnv(crc32($this->configSerial), crc32($key), $errorReporting);
		return true;
	}


	public function addServerEnv($configSerial, $key, $errRptProfl) {
		#error_log("crnrstn.inc.php (153) addServerEnv() ******** errRptProfl: ******** ".$errRptProfl." *******************");
		try{
			if(!isset($this->handle_env_ARRAY[$configSerial][$key])){
				$this->handle_env_ARRAY[$configSerial][$key] = $errRptProfl;
				self::$env_detect_ARRAY[$configSerial][$key] = 0;
				self::$env_name_ARRAY[$configSerial][$key] = $key;
			}else{
				//
				// 	THIS KEY HAS ALREADY BEEN INITIALIZED
				throw new Exception('CRNRSTN initialization notice :: This environmental key ('.$key.') has already been initialized.');
			}
		}catch( Exception $e ) {
			//
			// SEND THIS THROUGH THE LOGGER OBJECT
			self::$oLogger->captureNotice('crnrstn->addServerEnv()', LOG_INFO, $e->getMessage());
		}
    }
	
	public function initLogging($key, $loggingProfl=NULL, $loggingEndpoint=NULL){
		#error_log("crnrstn.inc.php initLogging() (155) ********CONFIG SERIAL: ******** ".$this->configSerial." *******************");
		if($loggingProfl!=''){
			self::$log_profl_ARRAY[crc32($this->configSerial)][crc32($key)] = $loggingProfl;
			self::$log_endpt_ARRAY[crc32($this->configSerial)][crc32($key)] = $loggingEndpoint;
		}
		
		return true;
		#error_log("crnrstn.inc.php (135) initLogging count: ".sizeof(self::$log_profl_ARRAY).", log_endpt_ARRAY: ".self::$log_endpt_ARRAY[crc32($key)]);
	}
	
	public function grantExclusiveAccess($env, $ipOrFile){
		
		#error_log("crnrstn.inc.php (182) grantExclusiveAccess env: ".$env);
		$this->grant_accessIP_ARRAY[crc32($this->configSerial)][crc32($env)] = $ipOrFile;
		
		return true;
	}
	
	public function denyAccess($env, $ipOrFile){
		$this->deny_accessIP_ARRAY[crc32($this->configSerial)][crc32($env)] = $ipOrFile;

		return true;
	}
	
	public function addDatabase($env, $host, $un=NULL, $pwd=NULL, $db=NULL, $port=NULL){
		#$env = $env.self::$configSerial;
		
		//
		// HANDLE PATH TO DATABASE CONFIG FILE (E.G. ONLY 2 PARAMS PROVIDED)
		if($db==NULL){
			#echo "FOR ENV ".$env.", CHECKING FOR FILE (".$host.")<br>";
			#error_log("crnrstn.inc.php addDatabase() (208) ******** DB host ******** ".$host." *******************");
			if(is_file($host)){
				//
				// EXTRACT DATABASE CONFIGURATION FROM FILE
				#error_log("(212) DB FILE INCLUDE...");
				include_once($host);
				
			}else{
				#echo "FOR ENV ".$env.", THE DB HOST FILE (".$host.") IS NO FILE<br>";
				#error_log("crnrstn.inc.php addDatabase() (217) FOR ENV ".$env.", THE DB HOST FILE (".$host.") IS NO FILE");
				//
				// WE COULD NOT FIND THE DATABASE CONFIGURATION FILE
				#self::$oLogger->captureNotice('crnrstn->addDatabase()', LOG_ERR, 'Could not find/interpret the database config file parameter for an addDatabase() method called in the crnrstn configuration.');			
			}

		}else{
			//
			// SEND DATABASE CONFIGURATION PARAMETERS TO THE CONNECTION MANAGER
			$this->oMYSQLI_CONN_MGR->addConnection($env, $host, $un, $pwd, $db, $port);
		}
		
		return true;
	}
	
	
	public function requiredDetectionMatches($value=''){
		//
		// HOW MANY SERVER KEYS ARE REQUIRED TO MATCH IN ORDER TO SUCCESSFULLY 
		// CONFIGURE CRNRSTN TO MATCH WITH ONE ENVIRONMENT
		if($value==''){
			//
			// WE WANT THE ENVIRONMENT WITH MOST MATCHES. DELAY ENV DETECTION UNTIL INSTANTIATION OF ENV CLASS OBJECT
			self::$envDetectRequiredCnt = NULL;
		}else{
			//
			// NON-ZERO VALUE HAS BEEN RECIEVED. THE ENV CONFIG THAT MEETS THIS REQUIREMENT FIRST IS USED FOR ENV INITIALIZATION
			self::$envDetectRequiredCnt = $value - 0;
		}
		
		return true;
	}
	
	public function get_log_profl_ARRAY(){
		return self::$log_profl_ARRAY;	
	}
	
	public function get_log_endpt_ARRAY(){
		return self::$log_endpt_ARRAY;	
	}
	
	public function defineEnvResource($env, $key, $value){
		#error_log("crnrstn.inc.php (282) defineEnvResource [".$env."][".$key."] :: ".$value);
		if(self::$serverAppKey[crc32($this->configSerial)]=="" || crc32($env)==self::$serverAppKey[crc32($this->configSerial)] || $env=="*"){
			$this->addEnvResource(crc32($this->configSerial), crc32($env), trim($key), trim($value)); 
		}
	}
	
	public function addEnvResource($configSerial, $env, $key, $value) {
		#echo "ADD to handle_resource_ARRAY[".$env."][".$key."] :: ".$value."<br>";
		#error_log("crnrstn.inc.php (267) config session param [".$env."][".$key."] :: ".$value);
		#error_log("crnrstn.inc.php (291) addEnvResource [".$configSerial."] [".$env."][".$key."] :: ".$value);
		self::$handle_resource_ARRAY[$configSerial][$env][$key] = $value;
		
		//
		// FOR FASTEST DISCOVERY, RUN ENVIRONMENTAL DETECTION IN PARALLEL WITH INITIALIZATION OF RESOURCE DEFINITIONS.
		// THIS MEANS THERE SHOULD/WOULD BE A NON-NULL / NON ZERO INTEGER PASSED TO $oCRNRSTN->requiredDetectionMatches(2) IN THE
		// CRNRSTN CONFIG FILE. OTHERWISE, WE MUST TRAVERSE ALL ENV CONFIG DEFINITIONS AND THEN TAKE BEST FIT PER SERVER SETTINGS.
		if(self::detectServerEnv($configSerial,$env, $key, $value)){
			//
			// IF NULL/ZED COUNT, HOLD OFF ON DEFINING APPLICATION ENV KEY UNTIL ALL ENV RESOURCES HAVE BEEN 
			// PROCESSED...E.G. WAIT FOR ENV INSTANTIATION OF CLASS OBJECT BEFORE DETECTING ENVIRONMENT.
			#error_log("crnrstn.inc.php (302) addEnvResource() We look for matchcount of ".self::$envDetectRequiredCnt." with [".$configSerial."] [".$env."] [".$key."] [".$value."]");
			if((self::$env_select_ARRAY[$configSerial] != "" && $env == self::$env_select_ARRAY[$configSerial]) || self::$env_select_ARRAY[$configSerial]==""){
				if(self::$envDetectRequiredCnt > 0 && self::$serverAppKey[$configSerial]==''){
					#echo "<br>SETTING SERVER APP KEY TO :: ".$env."<br>";
					#error_log("crnrstn.inc.php (306) env detect complete. setting serverAppKey for serial ".$configSerial." to ".$env);
					self::$serverAppKey[$configSerial] = $env;
				}
			}
		}
    }
	
	private static function detectServerEnv($configSerial, $env, $key, $value) { 
		//
		// CHECK THE ENVIRONMENTAL DETECTION KEYS FOR MATCHES AGAINST THE SERVER CONFIGURATION
		if(array_key_exists($key, self::$handle_srvr_ARRAY)){
			#error_log("crnrstn.inc.php (317) detectServerEnv [".$configSerial."][".$env."][".$key."] :: ".$value);
			return self::isServerKeyMatch($configSerial, $env, $key, $value);
		}else{
			return false;
		}
	}
	
	private static function isServerKeyMatch($configSerial, $env, $key, $value){
		//
		// RUN VALUE COMPARISON FOR INCOMING VALUE AND DATA FROM THE SERVERS' SUPER GLOBAL VARIABLE ARRAY
		if($value == self::$handle_srvr_ARRAY[$key]){
			//
			// INCREMENT FOR EACH MATCH. 
			#$this->configMatchCount[$configSerial]++;
			#error_log("crnrstn.inc.php (305) *******Increment count for _SERVER[]-matched-param [".$configSerial."] [".$env."] [".$key."]");
			self::$env_detect_ARRAY[$configSerial][$env]++;
		}
		
		//
		// FIRST $ENV TO REACH $envDetectRequiredCnt...YOU KNOW YOU HAVE QUALIFIED MATCH.
		#error_log("crnrstn.inc.php (366) $_SERVER key match for env: ".$env." |envDetectRequiredCnt: ".self::$envDetectRequiredCnt." |Env Match Count:  ".self::$env_detect_ARRAY[$env]);
		if(self::$env_detect_ARRAY[$configSerial][$env] >= self::$envDetectRequiredCnt && self::$envDetectRequiredCnt>0){
			//
			// WE HAVE A ENVIRONMENTAL DEFINITION WITH A SUFFICIENT NUMBER OF SUCCESSFUL MATCHES TO THE RUNNING ENVIRONMENT 
			// AS DEFINED BY THE CRNRSTN CONFIG FILE
			#error_log("crnrstn.inc.php (316) We have matchcount of ".self::$env_detect_ARRAY[$configSerial][$env]." with key [".$configSerial."] environment [".$env."]");
			self::$env_select_ARRAY[$configSerial] = $env;
			return true;
		}else{
			//
			// EVIDENCE OF A MATCH...STILL NOT SUFFICIENT
			return false;
		}
	}
	
										 // ('LOCALHOST_MAC', 'AES-192-OFB', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'sha256');
	public function initSessionEncryption($env, $encryptCipher, $encryptSecretKey, $encryptOptions, $hmac_alg){	
		#error_log("crnrstn.inc.php (342) initSessionEncryption with key [".$this->configSerial."] environment [".$env."]");
		
		$this->opensslSessEncryptCipher[crc32($this->configSerial)][crc32($env)] = $encryptCipher;
		$this->opensslSessEncryptSecretKey[crc32($this->configSerial)][crc32($env)] = $encryptSecretKey;
		$this->opensslSessEncryptOptions[crc32($this->configSerial)][crc32($env)] = $encryptOptions;
		$this->sessHmac_algorithm[crc32($this->configSerial)][crc32($env)] = $hmac_alg;
		
		return true;
	} 
	
	public function initCookieEncryption($env, $encryptCipher, $encryptSecretKey, $encryptOptions, $hmac_alg){	
		#error_log("crnrstn.inc.php (342) initSessionEncryption with key [".$this->configSerial."] environment [".$env."]");
		
		$this->opensslCookieEncryptCipher[crc32($this->configSerial)][crc32($env)] = $encryptCipher;
		$this->opensslCookieEncryptSecretKey[crc32($this->configSerial)][crc32($env)] = $encryptSecretKey;
		$this->opensslCookieEncryptOptions[crc32($this->configSerial)][crc32($env)] = $encryptOptions;
		$this->cookieHmac_algorithm[crc32($this->configSerial)][crc32($env)] = $hmac_alg;
		
		return true;
	} 
	
	public function setServerEnv(){
		#error_log("crnrstn.inc.php (353) setServerEnv() config serial ->[".$this->configSerial."] session resource key->[".$_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY']."]");
		self::$serverAppKey[crc32($this->configSerial)] = $_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY'];
		
		return $_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY'];
		
	}
	
	public function getServerEnv() {
		
		#error_log("crnrstn.inc.php (290) getServerEnv with serverAppKey: ".self::$serverAppKey);
		# self::$serverAppKey[$configSerial] = $env;
		//
		// DID WE DETERMINE ENVIRONMENT KEY THROUGH INITIALIZATION OF CRNRSTN? IF SO, THIS PARAMETER WILL BE SET. JUST USE IT.
		if(self::$serverAppKey[crc32($this->configSerial)]!=""){
			#error_log("crnrstn.inc.php (380) getServerEnv early dump of serverAppKey: [".self::$serverAppKey[crc32($this->configSerial)]."]");
			return self::$serverAppKey[crc32($this->configSerial)];
		}else{
		
			//
			// SINCE ENV NOT DETERMINED THROUGH INITIAL INITIALIZATION, NEXT CHECK FOR  
			if(!(self::$envDetectRequiredCnt > 0)){
				//
				// RETURN SERVER APPLICATION KEY BASED UPON A BEST FIT SCENARIO. FOR ANY TIES...FIRST COME FIRST SERVED.
				#error_log("crnrstn.inc.php (384) getServerEnv with serverAppKey: ".self::$serverAppKey[crc32($this->configSerial)]);
				foreach (self::$handle_resource_ARRAY as $serial=>$resource_ARRAY) {
					foreach($resource_ARRAY as $env=>$key){
						//
						// 
						#error_log("crnrstn.inc.php (396) getServerEnv ******** inside iterator ******** env_detect_ARRAY[".$serial."][".$env."]: [".self::$env_detect_ARRAY[$serial][$env]."]");
						if(self::$env_detect_ARRAY[$serial][$env]>0){
							if(self::$envMatchCount < self::$env_detect_ARRAY[$serial][$env]){
								self::$envMatchCount = self::$env_detect_ARRAY[$serial][$env];
								self::$serverAppKey[$serial] = $env;
								#error_log("crnrstn.inc.php (405) getServerEnv ******** inside iterator ******** NEW LEADER [".$serial."][".$env."]: [".self::$env_detect_ARRAY[$serial][$env]."]");
							}
						
						}
					}
				}
			}
		

			try{
				//
				// WE SHOULD HAVE THIS VALUE BY NOW. IF NULL, HOOOSTON...VE HAF PROBLEM!. $_SERVER['SERVER_NAME']
				if(self::$serverAppKey[$serial] == ""){
					throw new Exception('CRNRSTN environmental initialization error :: Environmental detection failed to match a sufficient number of parameters to your servers configuration to successfully initialize CRNRSTN on server '.self::$handle_srvr_ARRAY['SERVER_NAME'].' ('.self::$handle_srvr_ARRAY['SERVER_ADDR'].')');
				}
			
			} catch( Exception $e ) {
				//
				// SEND THIS THROUGH THE LOGGER OBJECT
				self::$oLogger->captureNotice('crnrstn->getServerEnv()', LOG_ALERT, $e->getMessage());
				
				//
				// RETURN NOTHING
				return false;
			}	
			
			#error_log("crnrstn.inc.php (413) getServerEnv() returning as selected environment key config serverAppKey: ".self::$serverAppKey[crc32($this->configSerial)]);
			return self::$serverAppKey[crc32($this->configSerial)];
		}
	}
	
	public function getHandle_resource_ARRAY(){	
		return 	self::$handle_resource_ARRAY;
		
	}

	
	public function __destruct() {

	}
}
?>