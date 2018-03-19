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
#  If not, see <http://www.gnu.org/licenses/><strong></strong>

class crnrstn_environmentals {
	public $configSerial;
	
	private static $oLogger;
	
	public $log_profl_ARRAY = array();
	public $log_endpt_ARRAY = array();
	
	private static $resourceKey;
	
	public $oCRNRSTN_IPSECURITY_MGR;
	public $oSESSION_MGR;
	public $oCOOKIE_MGR;
	public $oMYSQLI_CONN_MGR;
	public $oHTTP_MGR;
	
	private static $sess_env_param_ARRAY = array();
	
	private static $requestProtocol;
	
	#private static $srvrSessionShadow = array();		// FALLBACK PARAMETER FOR SESSION-LESS CONNECTIONS
		
	public function __construct($oCRNRSTN,$instanceType=NULL) {

		$this->configSerial = $oCRNRSTN->configSerial;
		$this->log_profl_ARRAY = $oCRNRSTN->get_log_profl_ARRAY();
		$this->log_endpt_ARRAY = $oCRNRSTN->get_log_endpt_ARRAY();
		
		#error_log("crnrstn.env.inc.php (62) COOKIE MGR INSTANTIATION");
		$this->oCOOKIE_MGR = new crnrstn_cookie_manager($this);
		$this->oHTTP_MGR = new crnrstn_http_manager();
		
		#self::$serverAppKey[$configSerial] = $env;		
		
		if(!($instanceType=='simple_configcheck')){
			
			//
			// INSTANTIATE LOGGER
			if(!isset(self::$oLogger)){
				self::$oLogger = new crnrstn_logging();
			}
			
			
			try{
					//
					//	DETERMINE KEY DESIGNATING THE RUNNING ENVIRONMENT, WHERE KEY = CRC32(env key)
					self::$resourceKey = $oCRNRSTN->getServerEnv();
					#error_log("crnrstn.env.inc.php __construct() (65) Did we successfully determine ENV KEY from crnrstn :: ".self::$resourceKey);
					#print_r("crnrstn.env.inc.php __construct() (66) Did we successfully determine ENV KEY from crnrstn :: ".self::$resourceKey);
					if(self::$resourceKey==""){
						//
						// WE DON'T HAVE THE ENVIRONMENT DETECTED. THROW EXCEPTION.
						// HOOOSTON...VE HAF PROBLEM!
						throw new Exception('CRNRSTN environmental configuration error :: unable to detect environment on server '.$_SERVER['SERVER_NAME'].' ('.$_SERVER['SERVER_ADDR'].').');
					}else{
						
						//
						// INSTANTIATE ENVIRONMENTAL IP ACCESS AUTHORIZATION MANAGEMENT CLASS OBJECT AND CLONE FROM CRNRSTN
						//if(!isset($this->oCRNRSTN_IPSECURITY_MGR)){
						//	error_log("crnrstn.env.inc.php (75) __construct :: clientIpAddress: ".$oCRNRSTN->oCRNRSTN_IPSECURITY_MGR->clientIpAddress());
						//	$this->oCRNRSTN_IPSECURITY_MGR = new crnrstn_ip_auth_manager($oCRNRSTN->oCRNRSTN_IPSECURITY_MGR->clientIpAddress());
						//}
									
						//if(!isset($this->oSESSION_MGR)){
						$this->oSESSION_MGR = new crnrstn_session_manager($this);
						//}
						
						
						$this->oCRNRSTN_IPSECURITY_MGR = clone $oCRNRSTN->oCRNRSTN_IPSECURITY_MGR;
						unset($oCRNRSTN->oCRNRSTN_IPSECURITY_MGR);
						#error_log("crnrstn.env.inc.php (83) constructor()  [".$this->oCRNRSTN_IPSECURITY_MGR->clientIpAddress()."]");
						//					
						// WE HAVE SELECTED ENVIRONMENT KEY. INITIALIZE. CONFIG KEY AND ENV KEY.
						// FLASH CONFIG KEY AND ENV KEY TO SESSION.
						$this->initRuntimeConfig();
						
						//
						// INITIALIZE ERROR REPORTING FOR THIS ENVIRONMENT
						$this->initializeErrorReporting($oCRNRSTN);						
						
						//
						// INITIALIZE ENVIRONMENTAL LOGGING BEHAVIOR
						$this->initEnvLoggingProfile();
						
						//
						// INITIALIZE IP ADDRESS RESTRICTIONS from grantExclusiveAccess()
						if(isset($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							$this->initExclusiveAccess($oCRNRSTN);
						}
																		
						//
						// INITIALIZE IP ADDRESS RESTRICTIONS from denyAccess()
						if(isset($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							$this->initDenyAccess($oCRNRSTN);
						}
						
						//
						// INITIALIZE DATABASE
						$this->oMYSQLI_CONN_MGR = clone $oCRNRSTN->oMYSQLI_CONN_MGR;
						$this->oMYSQLI_CONN_MGR->setEnvironment($this);
						
						
						//
						// INITIALIZE SESSION ENCRYPTION
						#error_log("crnrsgtn.env.inc.php (113) constructor config serial [".$this->configSerial."] resource key [".self::$resourceKey."]");
						if(isset($oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey])){
							#error_log("crnrsgtn.env.inc.php (116) constructor WE HAVE SESSION ENCRYPT CONFIG DATA...[".$oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey]."]");
							$this->initSessionEncryption($oCRNRSTN);
						}
					
						//
						// INITIALIZE COOKIE ENCRYPTION
						if(isset($oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey])){
							#error_log("crnrsgtn.env.inc.php (116) constructor WE HAVE COOKIE ENCRYPT CONFIG DATA...[".$oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey]."]");
							$this->initCookieEncryption($oCRNRSTN);
							
						}
						
						//
						// BEFORE ALLOCATING ADDITIONAL MEMORY RESOURCES, PROCESS IP AUTHENTICATION
						if(isset($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]) || isset($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							#error_log("crnrstn.env.inc.php (133) constructor ****** WE HAVE IP RESTRICTIONS TO PROCESS *****");
							if(!$this->oCRNRSTN_IPSECURITY_MGR->authorizeEnvAccess($this, self::$resourceKey)){
							
								//
								// WE COULD PERHAPS USE A MORE GRACEFUL WAY TO TRANSITION TO ERR...BUT THIS WORKS
								// THE METHOD returnSrvrRespStatus() CONTAINS SOME CUSTOM HTML FOR OUTPUT IF YOU WANT TO TWEAK ITS DESIGN

								#error_log("crnrstn.env.inc.php (196) NO ACCESS FOR YOU :: 403 :: ".self::$resourceKey);
								$this->returnSrvrRespStatus(403);
								#error_log("crnrstn.env.inc.php (198) session_destroying...");
								#session_destroy();
								exit();
							}
						}
						
						//
						// FLASH CRNRSTN CONFIG DEFINED ENV RESOURCES FOR THE DETECTED ENV TO SESSION MEMORY
						$this->initEnvResources($oCRNRSTN);
						
						
					}
			
				} catch( Exception $e ) {
				//
				// SEND THIS THROUGH THE LOGGER OBJECT
				self::$oLogger->captureNotice('oENV->__construct()', LOG_ALERT, $e->getMessage());
				
				
			}
			#if(!($this->oSESSION_MGR->issetSessionParam('_CRNRSTN_ENV_KEY'))){
			#error_log("crnrstn.env.inc.php (83) DO I HAVE configSERIAL:".$this->oSESSION_MGR->configSerial);
//			if(!(isset($_SESSION[$this->oSESSION_MGR->configSerial.'CRNRSTN'.crc32('_CRNRSTN_ENV_KEY')]))){ 
//
//			
//			}else{
//				
//				//
//				// WE TOOK WHAT WE NEEDED FROM oCRNRSTN. FREE RESOURCES HELD BY UNNECESSARY/REDUNDANT CONFIGURATION PARAMETERS.
//				unset($oCRNRSTN);
//			}
		}else{
			//
			// THIS IS A SIMPLE CONFIG CHECK.
			
			
		}
	}
	
	public function isConfigured($oCRNRSTN){
		//
		// INSTANTIATE SESSION MANAGER
		if(!isset($this->oSESSION_MGR)){
			$this->oSESSION_MGR = new crnrstn_session_manager($this);
		}
		
		//
		// DO WE HAVE SESSION DATA IN LINE WITH THE CONFIGURATION SERIALIZATION
		if(isset($_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY'])){
			#error_log("crnrstn.env.inc.php (202) isConfigured() Session is set. [".$this->configSerial."]");
			#$tmp_configSerial = $_SESSION['CRNRSTN_CONFIG_SERIAL'];
			#$tmp_resourceKey = $_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY'];
			//
			// SESSION IS SET
			try{
					//
					//	DETERMINE KEY DESIGNATING THE RUNNING ENVIRONMENT, WHERE KEY = CRC32(env key)
					$oCRNRSTN->setServerEnv();
					self::$resourceKey = $oCRNRSTN->getServerEnv();
					#self::$resourceKey = $_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY'];
					#error_log("crnrstn.env.inc.php isConfigured() (211) Did we successfully determine ENV KEY from session :: ".self::$resourceKey);
					#print_r("crnrstn.env.inc.php __construct() (66) Did we successfully determine ENV KEY from crnrstn :: ".self::$resourceKey);
					if(self::$resourceKey==""){
						//
						// WE DON'T HAVE THE ENVIRONMENT DETECTED. THROW EXCEPTION.
						// HOOOSTON...VE HAF PROBLEM!
						//throw new Exception('CRNRSTN environmental configuration error :: unable to detect environment on server '.$_SERVER['SERVER_NAME'].' ('.$_SERVER['SERVER_ADDR'].').');
						#error_log("crnrstn.env.inc.php (219) isConfigured() Session is NOT set.");
						return false;
					}else{
						
						$this->oCRNRSTN_IPSECURITY_MGR = clone $oCRNRSTN->oCRNRSTN_IPSECURITY_MGR;
						unset($oCRNRSTN->oCRNRSTN_IPSECURITY_MGR);
						#error_log("crnrstn.env.inc.php (226) isConfigured() IP->  [".$this->oCRNRSTN_IPSECURITY_MGR->clientIpAddress()."]");
						//					
						// WE HAVE SELECTED ENVIRONMENT KEY. INITIALIZE. CONFIG KEY AND ENV KEY.
						// FLASH CONFIG KEY AND ENV KEY TO SESSION.
						$this->initRuntimeConfig();
						
						//
						// INITIALIZE ERROR REPORTING FOR THIS ENVIRONMENT
						$this->initializeErrorReporting($oCRNRSTN);						
						
						//
						// INITIALIZE ENVIRONMENTAL LOGGING BEHAVIOR
						//$this->initEnvLoggingProfile();
						
						//
						// INITIALIZE IP ADDRESS RESTRICTIONS from grantExclusiveAccess()
						if(isset($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							$this->initExclusiveAccess($oCRNRSTN);
						}
																		
						//
						// INITIALIZE IP ADDRESS RESTRICTIONS from denyAccess()
						if(isset($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							$this->initDenyAccess($oCRNRSTN);
						}
						
						//
						// INITIALIZE DATABASE
						$this->oMYSQLI_CONN_MGR = clone $oCRNRSTN->oMYSQLI_CONN_MGR;
						$this->oMYSQLI_CONN_MGR->setEnvironment($this);
						
						//
						// INITIALIZE SESSION ENCRYPTION
						//error_log("crnrsgtn.env.inc.php (259) isConfigured() [".$this->configSerial."] resource key [".self::$resourceKey."]");
						//if(isset($oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey])){
						//	error_log("crnrsgtn.env.inc.php (277) is configured WE HAVE SESSION ENCRYPT CONFIG DATA...[".$oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey]."]");
						//	$this->initSessionEncryption($oCRNRSTN);
						//}
					
						//
						// INITIALIZE COOKIE ENCRYPTION
						//if(isset($oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey])){
						//	error_log("crnrsgtn.env.inc.php (283) is configured WE HAVE COOKIE ENCRYPT CONFIG DATA...[".$oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey]."]");
						//	$this->initCookieEncryption($oCRNRSTN);
							
						//}
						
						//
						// BEFORE ALLOCATING ADDITIONAL MEMORY RESOURCES, PROCESS IP AUTHENTICATION
						if(isset($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]) || isset($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
							#error_log("crnrstn.env.inc.php (276) is configured ****** WE HAVE IP RESTRICTIONS TO PROCESS *****");
							if(!$this->oCRNRSTN_IPSECURITY_MGR->authorizeEnvAccess($this, self::$resourceKey)){
							
								//
								// WE COULD PERHAPS USE A MORE GRACEFUL WAY TO TRANSITION TO ERR...BUT THIS WORKS
								// THE METHOD returnSrvrRespStatus() CONTAINS SOME CUSTOM HTML FOR OUTPUT IF YOU WANT TO TWEAK ITS DESIGN

								#error_log("crnrstn.env.inc.php (196) NO ACCESS FOR YOU :: 403 :: ".self::$resourceKey);
								$this->returnSrvrRespStatus(403);
								#error_log("crnrstn.env.inc.php (198) session_destroying...");
								#session_destroy();
								exit();
							}
						}
						
						//
						// FLASH CRNRSTN CONFIG DEFINED ENV RESOURCES FOR THE DETECTED ENV TO SESSION MEMORY
						//$this->initEnvResources($oCRNRSTN);
						//error_log("crnrstn.env.inc.php (295) isConfigured() Session is set.[".$this->configSerial."][".self::$resourceKey."]");
						
						//
						// INSTANTIATE COOKIE MANAGER
//						if(!isset($this->oCOOKIE_MGR)){
//							$this->oCOOKIE_MGR = new crnrstn_cookie_manager($this);
//						}
//						
//						//
//						// INSTANTIATE HTTP MANAGER
//						if(!isset($this->oHTTP_MGR)){
//							$this->oHTTP_MGR = new crnrstn_http_manager();
//						}
						return true;
						
	
					
					}
			
				} catch( Exception $e ) {
				//
				// SEND THIS THROUGH THE LOGGER OBJECT
				self::$oLogger->captureNotice('oENV->isConfigured()', LOG_ALERT, $e->getMessage());
				
				
			}
			
			
		}else{
			//
			// NO SESSION SET
			#error_log("crnrstn.env.inc.php (330) isConfigured() Session is NOT set.");
			return false;
		}
		
		//
		// IF SESSION PARAMS EXISTS FOR THIS CONFIG SERIAL RETURN TRUE.
		
		
		
	}
	
	public function getEnvKey(){
		#error_log("crnrstn.env.inc.php (345) resourceKey: ".self::$resourceKey);
			return self::$resourceKey;
	}
	
	public function getEnvSerial(){
		return 	$this->configSerial;
	}
	
	
	private function initEnvLoggingProfile(){
		#error_log("crnrstn.env.inc.php (206) initEnvLoggingProfile() config serial: [".$this->configSerial."]  resource key [".self::$resourceKey."]");
		//
		// INITIALIZE SESSION PARAMS FOR LOGGING FUNCTIONALITY 
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_LOG_PROFILE", $this->log_profl_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_LOG_ENDPOINT", $this->log_endpt_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
		
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_LOG_PROFILE"] = $this->log_profl_ARRAY[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_LOG_ENDPOINT"] = $this->log_endpt_ARRAY[crc32($this->configSerial)][self::$resourceKey];
		
		#$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]['_CRNRSTN_LOG_PROFILE'] = $this->log_profl_ARRAY[crc32($this->configSerial)][self::$resourceKey];
		#$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]['_CRNRSTN_LOG_ENDPOINT'] = $this->log_endpt_ARRAY[crc32($this->configSerial)][self::$resourceKey];
		#self::$srvrSessionShadow["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]['LOG_PROFILE'] = $this->log_profl_ARRAY[crc32($this->configSerial)][self::$resourceKey];
		#self::$srvrSessionShadow["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]['LOG_ENDPOINT'] = $this->log_endpt_ARRAY[crc32($this->configSerial)][self::$resourceKey];
						
		#error_log("crnrstn.env.inc.php (214) log_profl_ARRAY: ".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_LOG_PROFILE"]);
		#print_r("crnrstn.env.inc.php (215) log_profl_ARRAY: ".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_LOG_PROFILE"]);	
		
	}
	
	
	private function initRuntimeConfig(){
		//
		// INITIALIZE CONFIG AND ENV KEYS.
		$_SESSION['CRNRSTN_CONFIG_SERIAL'] = $this->configSerial;
		$_SESSION['CRNRSTN_'.crc32($this->configSerial)]['CRNRSTN_RESOURCE_KEY'] = self::$resourceKey;		
		#error_log("crnrstn.env.inc.php (373) initRuntimeConfig() -- configSerial [".$this->configSerial."]  resourceKey [".self::$resourceKey."]");
		
		#$_SESSION['CRNRSTN_'.crc32($_SESSION['CRNRSTN_CONFIG_SERIAL'])]['CRNRSTN_RESOURCE_KEY']
	}
	
	private function initializeErrorReporting($oCRNRSTN){
		#error_log("crnrstn.env.inc.php (367) initializeErrorReporting() config serial: [".$this->configSerial."]  key: [".self::$resourceKey."] value:  [".$oCRNRSTN->handle_env_ARRAY[crc32($this->configSerial)][self::$resourceKey]."]");
		error_reporting($oCRNRSTN->handle_env_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
	}
	
	private function initExclusiveAccess($oCRNRSTN){
		//
		// PROCESS IP ADDRESS ACCESS AND RESTRICTION FOR SELECTED ENVIRONMENT
		#$this->grant_accessIP_ARRAY =  $oCRNRSTN->grant_accessIP_ARRAY;
		#error_log("crnrstn.env.inc.php (243) initExclusiveAccess() [".$oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]."]");
		if(is_file($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
			#error_log("crnrstn.env.inc.php (63) Processing grant exclusive access include file :: ".$this->grant_accessIP_ARRAY[self::$resourceKey]);
			//
			// EXTRACT ACCESS-BY-IP AUTHORIZATION PROFILE FROM FILE
			#error_log("crnrstn.env.inc.php (248) initExclusiveAccess() we have a file!!");
			include_once($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				
		}else{
			// 
			// CHECK FOR NULL. IF NULL, DO NOTHING.
			if($oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey] != ""){
			
				#error_log("crnrstn.env.inc.php (256) We have IPs to process for env ".self::$resourceKey." and NOT an include file...see: ".$oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				#print_r("crnrstn.env.inc.php (257) We have IPs to process for env ".self::$resourceKey." and NOT an include file...see: ".$oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				$this->oCRNRSTN_IPSECURITY_MGR->grantAccessWKey(self::$resourceKey, $oCRNRSTN->grant_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
			}else{
					//
					// NO IP ADDRESSES PROVIDED. NOTHING TO DO HERE.
					#error_log("crnrstn.env.inc.php (256) initExclusiveAccess() Nothing to do with IP here as initExclusiveAccess not configured for this environment. ");
					#print_r("crnrstn.env.inc.php (257) initExclusiveAccess() Nothing to do with IP here as initExclusiveAccess not configured for this environment. ");
					
			}
		}
	}
	
	private function initDenyAccess($oCRNRSTN){
		#$this->deny_accessIP_ARRAY =  $oCRNRSTN->deny_accessIP_ARRAY;

		#error_log("crnrstn.env.inc.php (271) initDenyAccess() config serial [".$this->configSerial."] or [".crc32($this->configSerial)."]  resource key [".self::$resourceKey."] deny array [".$oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]."]");
		if(is_file($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey])){
			#error_log("crnrstn.env.inc.php (266) initDenyAccess() Processing deny access include file :: ".$oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
			//
			// EXTRACT ACCESS-BY-IP AUTHORIZATION PROFILE FROM FILE
			#error_log("crnrstn.env.inc.php (269) initDenyAccess() we have include file for IP deny access.");
			include_once($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				
		}else{
			if($oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey] != ""){
				//
				// 
				#error_log("crnrstn.env.inc.php (275) initDenyAccess() We have DENIAL IPs to process and NOT an include file...see: ".$oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				#print_r("crnrstn.env.inc.php (276) initDenyAccess() We have DENIAL IPs to process and NOT an include file...see: ".$oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
				#error_log("crnrstn.env.inc.php (149) we have IPs for IP deny access.");
				$this->oCRNRSTN_IPSECURITY_MGR->denyAccessWKey(self::$resourceKey, $oCRNRSTN->deny_accessIP_ARRAY[crc32($this->configSerial)][self::$resourceKey]);
			}else{
				//
				// NO IP ADDRESSES PROVIDED. NOTHING TO DO HERE.
				#error_log("crnrstn.env.inc.php (283) initDenyAccess() Nothing to do with IP here as initExclusiveAccess not configured for this environment. ");
				#print_r("crnrstn.env.inc.php (284) initDenyAccess() Nothing to do with IP here as initExclusiveAccess not configured for this environment. ");
				
			}		
		}		
	}
	
	
	public function returnResouceKey(){
		//
		// RETURN RESOURCE KEY FOR DETECTED ENVIRONMENT
		return 	self::$resourceKey;
	}
	
	public function initSessionEncryption($oCRNRSTN){
		
		//
		// TRANSFER SESSION ENCRYPT PARAMS TO SESSION
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_SESS_ENCRYPT_CIPHER", $oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_SESS_ENCRYPT_SECRET_KEY", $oCRNRSTN->opensslSessEncryptSecretKey[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_SESS_ENCRYPT_OPTIONS", $oCRNRSTN->opensslSessEncryptOptions[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_SESS_ENCRYPT_HMAC_ALG", $oCRNRSTN->sessHmac_algorithm[crc32($this->configSerial)][self::$resourceKey]);		
		
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"] = $oCRNRSTN->opensslSessEncryptCipher[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"] = $oCRNRSTN->opensslSessEncryptSecretKey[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_OPTIONS"] = $oCRNRSTN->opensslSessEncryptOptions[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_HMAC_ALG"] = $oCRNRSTN->sessHmac_algorithm[crc32($this->configSerial)][self::$resourceKey];
		
		#error_log("crnrstn.env.inc.php (308) initSessionEncryption() _CRNRSTN_SESS_ENCRYPT_CIPHER [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_CIPHER"]."] ");
		#error_log("crnrstn.env.inc.php (309) initSessionEncryption() _CRNRSTN_SESS_ENCRYPT_SECRET_KEY [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_SECRET_KEY"]."] ");
		#error_log("crnrstn.env.inc.php (310) initSessionEncryption() _CRNRSTN_SESS_ENCRYPT_OPTIONS [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_OPTIONS"]."] ");
		#error_log("crnrstn.env.inc.php (311) initSessionEncryption() _CRNRSTN_SESS_ENCRYPT_HMAC_ALG [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_SESS_ENCRYPT_HMAC_ALG"]."] ");
		
	}
	
	public function initCookieEncryption($oCRNRSTN){
		
		//
		// TRANSFER COOKIE ENCRYPT PARAMS TO SESSION
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_COOKIE_ENCRYPT_CIPHER", $oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY", $oCRNRSTN->opensslCookieEncryptSecretKey[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_COOKIE_ENCRYPT_OPTIONS", $oCRNRSTN->opensslCookieEncryptOptions[crc32($this->configSerial)][self::$resourceKey]);
		#$this->oSESSION_MGR->setSessionParam("_CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG", $oCRNRSTN->cookieHmac_algorithm[crc32($this->configSerial)][self::$resourceKey]);		
		
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"] = $oCRNRSTN->opensslCookieEncryptCipher[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"] = $oCRNRSTN->opensslCookieEncryptSecretKey[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_OPTIONS"] = $oCRNRSTN->opensslCookieEncryptOptions[crc32($this->configSerial)][self::$resourceKey];
		$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG"] = $oCRNRSTN->cookieHmac_algorithm[crc32($this->configSerial)][self::$resourceKey];
		
		#error_log("crnrstn.env.inc.php (365) initCookieEncryption() _CRNRSTN_COOKIE_ENCRYPT_CIPHER [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_CIPHER"]."] ");
		#error_log("crnrstn.env.inc.php (366) initCookieEncryption() _CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_SECRET_KEY"]."] ");
		#error_log("crnrstn.env.inc.php (367) initCookieEncryption() _CRNRSTN_COOKIE_ENCRYPT_OPTIONS [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_OPTIONS"]."] ");
		#error_log("crnrstn.env.inc.php (368) initCookieEncryption() _CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG [".$_SESSION["CRNRSTN_".crc32($this->configSerial)]["CRNRSTN_".self::$resourceKey]["_CRNRSTN_COOKIE_ENCRYPT_HMAC_ALG"]."] ");
		
	}
	
	private function initEnvResources($oCRNRSTN){
		//
		// ITERATE THROUGH handle_resource_ARRAY TO EXTRACT ENV SPECIFIC USER DEFINED PARAMS
		// TRANSFER DATA (JUST FOR THE RUNNING ENV) FROM oCRNRSTN RESOURCE ARRAY TO oENV RESOURCE ARRAY
		#self::$handle_resource_ARRAY[$configSerial][$env][$key] 
		# $this->configSerial
		#error_log("crnrstn.env.inc.php (497) initEnvResources() getSessionKey [".$this->oSESSION_MGR->getSessionKey()."]");
		$this->getHandle_resource_ARRAY = $oCRNRSTN->getHandle_resource_ARRAY();
		foreach($this->getHandle_resource_ARRAY[crc32($this->configSerial)][$this->oSESSION_MGR->getSessionKey()] as $key=>$value){
			#self::set($key, $value);
			//error_log("crnrstn.env.inc.php (503) initEnvResources() key [".$key."] value [".$value."]");
			$this->oSESSION_MGR->setSessionParam($key, $value);
		}
		
		//
		// INITIALIZE oENV CLASS OBJECT WITH ANY WILDCARDS
		if(isset($this->getHandle_resource_ARRAY[crc32($this->configSerial)][crc32('*')])){
			foreach($this->getHandle_resource_ARRAY[crc32($this->configSerial)][crc32('*')] as $key=>$value){
				#self::set($key, $value);
				//error_log("crnrstn.env.inc.php (512) initEnvResources(*) key [".$key."] value [".$value."]");
				$this->oSESSION_MGR->setSessionParam($key, $value);
			}
		}

		
		//
		// TEST RETRIEVAL 
		#error_log("crnrstn.env.inc.php (395) initEnvResources() MAILER_FROM_EMAIL [".$this->oSESSION_MGR->getSessionParam("MAILER_FROM_EMAIL")."]");
		
	}
	
	public function returnSrvrRespStatus($errorCode){
		//
		// http://php.net/manual/en/function.http-response-code.php
		// Source: Wikipedia "List_of_HTTP_status_codes"
		$http_status_codes = array(100 => "Continue", 101 => "Switching Protocols", 102 => "Processing", 200 => "OK", 201 => "Created", 202 => "Accepted", 203 => "Non-Authoritative Information", 204 => "No Content", 205 => "Reset Content", 206 => "Partial Content", 207 => "Multi-Status", 300 => "Multiple Choices", 301 => "Moved Permanently", 302 => "Found", 303 => "See Other", 304 => "Not Modified", 305 => "Use Proxy", 306 => "(Unused)", 307 => "Temporary Redirect", 308 => "Permanent Redirect", 400 => "Bad Request", 401 => "Unauthorized", 402 => "Payment Required", 403 => "Forbidden", 404 => "Not Found", 405 => "Method Not Allowed", 406 => "Not Acceptable", 407 => "Proxy Authentication Required", 408 => "Request Timeout", 409 => "Conflict", 410 => "Gone", 411 => "Length Required", 412 => "Precondition Failed", 413 => "Request Entity Too Large", 414 => "Request-URI Too Long", 415 => "Unsupported Media Type", 416 => "Requested Range Not Satisfiable", 417 => "Expectation Failed", 418 => "I'm a teapot", 419 => "Authentication Timeout", 420 => "Enhance Your Calm", 422 => "Unprocessable Entity", 423 => "Locked", 424 => "Failed Dependency", 424 => "Method Failure", 425 => "Unordered Collection", 426 => "Upgrade Required", 428 => "Precondition Required", 429 => "Too Many Requests", 431 => "Request Header Fields Too Large", 444 => "No Response", 449 => "Retry With", 450 => "Blocked by Windows Parental Controls", 451 => "Unavailable For Legal Reasons", 494 => "Request Header Too Large", 495 => "Cert Error", 496 => "No Cert", 497 => "HTTP to HTTPS", 499 => "Client Closed Request", 500 => "Internal Server Error", 501 => "Not Implemented", 502 => "Bad Gateway", 503 => "Service Unavailable", 504 => "Gateway Timeout", 505 => "HTTP Version Not Supported", 506 => "Variant Also Negotiates", 507 => "Insufficient Storage", 508 => "Loop Detected", 509 => "Bandwidth Limit Exceeded", 510 => "Not Extended", 511 => "Network Authentication Required", 598 => "Network read timeout error", 599 => "Network connect timeout error");
		
		#http_response_code($errorCode);
		#session_destroy();
		header('HTTP/1.1 '.$errorCode.' '.$http_status_codes[$errorCode]);
		
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>'.$errorCode.' '.$http_status_codes[$errorCode].'</title>
<style type="text/css">
<!--
body{margin:0;font-size:.7em;font-family:Verdana, Arial, Helvetica, sans-serif;background:#EEEEEE;}
fieldset{padding:0 15px 10px 15px;} 
h1{font-size:2.4em;margin:0;color:#FFF;}
h2{font-size:1.7em;margin:0;color:#CC0000;} 
h3{font-size:1.2em;margin:10px 0 0 0;color:#000000;} 
#header{width:96%;margin:0 0 0 0;padding:6px 2% 6px 2%;font-family:"trebuchet MS", Verdana, sans-serif;color:#FFF;
background-color:#555555;}
#content{margin:0 0 0 2%;position:relative;}
.content-container{background:#FFF;width:96%;margin-top:8px;padding:10px;position:relative;}
-->
</style>
</head>
<body>
<div id="header"><h1>Server Error</h1></div>
<div id="content">
 <div class="content-container"><fieldset>
  <h2>'.$errorCode.' '.$http_status_codes[$errorCode].'</h2>
 </fieldset></div>
</div>
</body>
</html>';
		
		exit();
	}
	
	
	public function getEnvParam($paramName){
		
		if(!isset(self::$sess_env_param_ARRAY[$paramName])){
			self::$sess_env_param_ARRAY[$paramName] = $this->pullFromSession($paramName);
		}
		
		return self::$sess_env_param_ARRAY[$paramName];
	}
	
	private function pullFromSession($key) {

		//
		// PULL FROM SESSION CACHE
		return $this->oSESSION_MGR->getSessionParam($key);
	}


	public function openssl_get_cipher_methods(){
		# method source :: http://php.net/manual/en/function.openssl-encrypt.php
		$ciphers             = openssl_get_cipher_methods();
		$ciphers_and_aliases = openssl_get_cipher_methods(true);
		$cipher_aliases      = array_diff($ciphers_and_aliases, $ciphers);
		
		//ECB mode should be avoided
		$ciphers = array_filter( $ciphers, function($n) { return stripos($n,"ecb")===FALSE; } );
		
		//At least as early as Aug 2016, Openssl declared the following weak: RC2, RC4, DES, 3DES, MD5 based
		$ciphers = array_filter( $ciphers, function($c) { return stripos($c,"des")===FALSE; } );
		$ciphers = array_filter( $ciphers, function($c) { return stripos($c,"rc2")===FALSE; } );
		$ciphers = array_filter( $ciphers, function($c) { return stripos($c,"rc4")===FALSE; } );
		$ciphers = array_filter( $ciphers, function($c) { return stripos($c,"md5")===FALSE; } );
		$cipher_aliases = array_filter($cipher_aliases,function($c) { return stripos($c,"des")===FALSE; } );
		$cipher_aliases = array_filter($cipher_aliases,function($c) { return stripos($c,"rc2")===FALSE; } );
		$mergedCiphers = array_merge($ciphers,$cipher_aliases);
		
		
		return $mergedCiphers;
		
	}
	
	//
	// RETURN HTTP/S PATH OF CURRENT SCRIPT
	public function currentLocation(){
		if($_SERVER['HTTPS']){
			self::$requestProtocol='https://';
		}else{
			self::$requestProtocol='http://';
		}
		
		return self::$requestProtocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	
	public function __destruct() {
		
	}
}
?>