<?php

/* 
// J5
// Code is Poetry */
if ( ! session_id() ) @ session_start();

//
// CRNRSTN CLASS INCLUDES ::
require('./_crnrstn.root.inc.php');
require($CRNRSTN_ROOT.'/_crnrstn/class/crnrstn/crnrstn.inc.php');						// CRNRSTN
require($CRNRSTN_ROOT.'/_crnrstn/class/logging/crnrstn.log.inc.php');					// LOGGING
require($CRNRSTN_ROOT.'/_crnrstn/class/environmentals/crnrstn.env.inc.php');			// ENVIRONMENTALS
require($CRNRSTN_ROOT.'/_crnrstn/class/security/crnrstn.ipauthmgr.inc.php');			// SECURITY
require($CRNRSTN_ROOT.'/_crnrstn/class/database/mysqli/crnrstn.mysqli.inc.php');		// DATABASE
#require($CRNRSTN_ROOT.'/_crnrstn/class/soa/nusoap/nusoap.php');						// NUSOAP (3RD PARTY CLIENT/SERVER SOAP)
#require($CRNRSTN_ROOT.'/_crnrstn/class/soa/nusoap/class.wsdlcache.php');				// NUSOAP (3RD PARTY CLIENT/SERVER SOAP)
#require($CRNRSTN_ROOT.'/_crnrstn/class/soa/crnrstn.soap.inc.php');						// SOAP MANAGEMENT
require($CRNRSTN_ROOT.'/_crnrstn/class/session/crnrstn.session.inc.php');				// SESSION MANAGEMENT
require($CRNRSTN_ROOT.'/_crnrstn/class/session/crnrstn.cookie.inc.php');				// COOKIE MANAGEMENT
require($CRNRSTN_ROOT.'/_crnrstn/class/session/crnrstn.http.inc.php');					// HTTP MANAGEMENT

//
// INSTANTIATE AN INSTANCE OF CRNRSTN BY PASSING SERVER SUPER GLOBAL AND A SERIALIZATION KEY FOR THIS CONFIG FILE.
$oCRNRSTN = new crnrstn($_SERVER,'RrI5nh3');

##
# REFERENCE OF ERROR LEVEL CONSTANTS
# http://php.net/error-reporting
/*
The error level constants are always available as part of the PHP core.
; E_ALL             - All errors and warnings (includes E_STRICT as of PHP 6.0.0)
; E_ERROR           - fatal run-time errors
; E_RECOVERABLE_ERROR  - almost fatal run-time errors
; E_WARNING         - run-time warnings (non-fatal errors)
; E_PARSE           - compile-time parse errors
; E_NOTICE          - run-time notices (these are warnings which often result
;                     from a bug in your code, but it's possible that it was
;                     intentional (e.g., using an uninitialized variable and
;                     relying on the fact it's automatically initialized to an
;                     empty string)
; E_STRICT          - run-time notices, enable to have PHP suggest changes
;                     to your code which will ensure the best interoperability
;                     and forward compatibility of your code
; E_CORE_ERROR      - fatal errors that occur during PHP's initial startup
; E_CORE_WARNING    - warnings (non-fatal errors) that occur during PHP's
;                     initial startup
; E_COMPILE_ERROR   - fatal compile-time errors
; E_COMPILE_WARNING - compile-time warnings (non-fatal errors)
; E_USER_ERROR      - user-generated error message
; E_USER_WARNING    - user-generated warning message
; E_USER_NOTICE     - user-generated notice message
; E_DEPRECATED      - warn about code that will not work in future versions
;                     of PHP
; E_USER_DEPRECATED - user-generated deprecation warnings

; Common Values for error reporting:
;   	E_ALL (Show all errors, warnings and notices including coding standards.)
;   	E_ALL & ~E_NOTICE  (Show all errors, except for notices)
;   	E_ALL & ~E_NOTICE & ~E_STRICT  (Show all errors, except for notices and coding standards warnings.)
;   	E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR  (Show only errors)
;
; Default Value: E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED
; Development Value: E_ALL
; Production Value: E_ALL & ~E_DEPRECATED & ~E_STRICT
*/

# INITIALIZE A KEY + ERROR REPORTING FOR EACH APPLICATION DEV/HOSTING ENVIRONMENT ::
# PARAMETER 1 [environment-key] = A KEY TO REPRESENT EACH ENVIRONMENT THAT WILL RUN THIS INSTANTIATION OF CRNRSTN
# PARAMETER 2 [error-reporting-constants] = ERROR REPORTING PROFILE
#
#$oCRNRSTN->addEnvironment([environment-key], [error-reporting-constants]);
//$oCRNRSTN->addEnvironment('LOCALHOST_PC', E_ERROR);
$oCRNRSTN->addEnvironment('LOCALHOST_PC', E_ALL & ~E_NOTICE & ~E_STRICT);
$oCRNRSTN->addEnvironment('PROD_HOST24', E_ALL & ~E_NOTICE & ~E_STRICT);
$oCRNRSTN->addEnvironment('LOCALHOST_MAC2', E_ALL);
#print_r("<br>Local host PC checksum [".crc32("LOCALHOST_PC")."]<br>");
#print_r("Prod host24 checksum [".crc32("PROD_HOST24")."]<br> ");
#print_r("Local host mac checksum [".crc32("LOCALHOST_MAC2")."]<br>");


//
// INITIALIZE LOGGING FUNCTIONALITY FOR EACH ENVIRONMENT. IF NULL OR UNDEFINED, WILL LOG TO SCREEN.
# $oCRNRSTN->initLogging([environment-key], [logging-constant], [additional-logging-parameters]);
#
# CRNRSTN LOGGING CONSTANTS FOR INITIALIZATION = [DEFAULT,SCREEN, EMAIL or FILE]
# e.g. LOGGING TO SCREEN
# $oCRNRSTN->initLogging('000WEBHOSTJONY5', 'SCREEN');

# e.g. LOGGING TO EMAIL
# $oCRNRSTN->initLogging('000WEBHOSTJONY5', 'EMAIL', 'email_one@address.com, email_two@address.com, email_n@address.com');

# e.g. LOGGING TO FILE
# $oCRNRSTN->initLogging('000WEBHOSTJONY5', 'DEFAULT');					// SYSTEM DEFAULT FILE LOGGING
# $oCRNRSTN->initLogging('000WEBHOSTJONY5', 'FILE', '/var/logFolder');	// INCLUDE PATH TO DIRECTORY FOR CUSTOM LOG FILE

$oCRNRSTN->initLogging('PROD_HOST24', 'DEFAULT');												// SYSTEM DEFAULT ERROR LOGGING
$oCRNRSTN->initLogging('000WEBHOSTJONY5', 'SCREEN');											// OUTPUT LOG INFO TO SCREEN
#$oCRNRSTN->initLogging('LOCALHOST_PC', 'EMAIL','email1@domain.com,email2@domain.com');			// EMAIL LOG INFO TO LIST OF COMMA DELIMITED EMAIL ACCOUNTS
#$oCRNRSTN->initLogging('LOCALHOST_MAC2', 'FILE','/var/log/customlogs/crnrstnlogs');			// PATH TO FOLDER & FILE WHERE LOG DATA WILL BE APPENDEED
$oCRNRSTN->initLogging('LOCALHOST_MAC2', 'SCREEN');


//
// INITIALIZE SECURITY PROTOCOLS FOR EXCLUSIVE RESOURCE ACCESS. 2 FORMATS.
# FORMAT 1. PASS IN ENVIRONMENT KEY AND PATH TO CONFIGURED CRNRSTN IP AUTHENTICATION MANAGER CONFIG FILE ON THE SERVER.
# $oCRNRSTN->grantExclusiveAccess([environment-key], [path-to-db-configuration-file]);
$oCRNRSTN->grantExclusiveAccess('LOCALHOST_PC', 'C://DATA_GOVT_SURVEILLANCE//_wwwroot//xampp//htdocs//crnrstn//config.ipauthmgr.secure//_crnrstn.ipauthmgr.config.inc.php');
$oCRNRSTN->grantExclusiveAccess('LOCALHOST_MAC2', '/var/www/html/woodford/config.ipauthmgr.secure/grantexclusiveaccess/_crnrstn.ipauthmgr.config.inc.php');
$oCRNRSTN->grantExclusiveAccess('PROD_HOST24', '/home/jony5com/woodford.jony5.com/config.ipauthmgr.secure/grantexclusiveaccess/_crnrstn.ipauthmgr.config.inc.php');


# FORMAT 2. PASS IN ENVIRONMENT KEY AND IP ADDRESS (OR COMMA DELIMITED LIST OF IPv4 or IPv6 (testing needed) IPs)
# $oCRNRSTN->grantExclusiveAccess([environment-key], [comma-delimited-list-of-IPs]);
#$oCRNRSTN->grantExclusiveAccess('LOCALHOST_MAC2','192.168.172.*,192.168.173.*,192.168.174.3');
#$oCRNRSTN->grantExclusiveAccess('LOCALHOST_PC','127.*');
#$oCRNRSTN->grantExclusiveAccess('LOCALHOST_PC','127.0.0.1, 127.*, 130.51.10.*');
#$oCRNRSTN->grantExclusiveAccess('LOCALHOST_PC','127.0.0.1, 130.*, 130.51.10.*, FE80::230:80FF:FEF3:4701');

//
// INITIALIZE SECURITY PROTOCOLS FOR RESOURCE DENIAL. 2 FORMATS.
# FORMAT 1. PASS IN ENVIRONMENT KEY AND PATH TO A CONFIG FILE ON THE SERVER.
#$oCRNRSTN->denyAccess([environment-key], [path-to-ip-authorization-configuration-file]);
$oCRNRSTN->denyAccess('LOCALHOST_PC', 'C://DATA_GOVT_SURVEILLANCE//_wwwroot//xampp//htdocs//jony5.com//_crnrstn//config.ipauthmgr.secure//_crnrstn.ipauthmgr.config.inc.php');
#$oCRNRSTN->denyAccess('LOCALHOST_MAC2', '/var/www/html/woodford/config.ipauthmgr.secure/denyaccess/_crnrstn.ipauthmgr.config.inc.php');

# FORMAT 2. PASS IN ENVIRONMENT KEY AND IP ADDRESS (OR COMMA DELIMITED LIST OF IPv4 or IPv6 (testing needed) IPs)
#$oCRNRSTN->denyAccess('PROD_HOST24','172.16.110.1');
#$oCRNRSTN->denyAccess('LOCALHOST_MAC2','172.16.110.1');
$oCRNRSTN->denyAccess('LOCALHOST_PC','127.0.0.10, 127.0.0.2, 127.0.0.3, 127.0.0.4, 127.0.0.5');

//
// INITIALIZE DATABASE FUNCTIONALITY FOR EACH ENVIRONMENT. 2 WAYS TO USE THIS METHOD.
#METHOD ONE# $oCRNRSTN->addDatabase([environment-key], [path-to-db-configuration-file]);
$oCRNRSTN->addDatabase('LOCALHOST_PC', 'C://DATA_GOVT_SURVEILLANCE//_wwwroot//xampp//htdocs//crnrstn//config.database.secure//_crnrstn.db.config.inc.php');
$oCRNRSTN->addDatabase('LOCALHOST_MAC2', '/var/www/html/woodford/config.database.secure/_crnrstn.db.config.inc.php');
$oCRNRSTN->addDatabase('PROD_HOST24', '/home/jony5com/woodford.jony5.com/config.database.secure/_crnrstn.db.config.inc.php');

#METHOD TWO# $oCRNRSTN->addDatabase([environment-key], [db-host], [db-user-name], [db-user-pswd], [db-database-name], [optional-db-port]);
//$oCRNRSTN->addDatabase('000WEBHOSTJONY5', 'mx.localhost.com', 'crnrstn_assets', '222222222222222', 'db_crnrstn_assets', 80);
//$oCRNRSTN->addDatabase('000WEBHOSTJONY5', 'mx.localhost.com', 'crnrstn_posts', '33333333333333', 'db_crnrstn_posts', 80);
//$oCRNRSTN->addDatabase('000WEBHOSTJONY5', 'mx.localhost.com', 'crnrstn_demo', '44444444444444', 'db_crnrstn_demo', 80);
#$oCRNRSTN->addDatabase('000WEBHOSTJONY5', 'mx.localhost.com', 'crnrstn_users', '1111111111111', 'db_crnrstn_users', 3306);
//
//$oCRNRSTN->addDatabase('LOCALHOST_PC', '127.0.0.4', 'crnrstn_demo3_un', 'FZZ88X3EU5s8vFAC', 'crnrstn_demo3', 80);
//$oCRNRSTN->addDatabase('LOCALHOST_PC', '127.0.0.3', 'crnrstn_demo2_un', 'PwdBNBvuFHrwMqCS', 'crnrstn_demo2', 80);
//$oCRNRSTN->addDatabase('LOCALHOST_PC', '127.0.0.2', 'crnrstn_demo4_un', 'G36NQtqFXYWcVXpA', 'crnrstn_demo4', 80);
#$oCRNRSTN->addDatabase('LOCALHOST_PC', 'localhost', 'crnrstn_demo', 'aXNTPxGPeLRwYzTS', 'crnrstn_demo', 3306);

//
// INITIALIZATION FOR ENCRYPTION :: CRNRSTN SESSION DATA :: ADVANCED CONFIGURATION PARAMETERS
/*
To configure any of your SERVER environments to hide persistent CRNRSTN configuration session data behind a layer of encryption, 
run $oCRNRSTN->initSessionEncryption(x,x,x,..)...as defined below...specifying the environmental key for 
each environment where encryption is desired. CAUTION: This feature will increase server load. CAUTION: CRNRSTN applies a combination 
of encryption cipher and HMAC keyed hash value data manipulationas and comparisons to store and verify CRNRSTN session data. Some 
encryption-cipher / HMAC-algoirthm combinations will not be compatible with CRNRSTN due to how they are 
applied to the data when encryption is initialized...so please test your encryption configuration before applying to production environment.

*Note that the available cipher methods can differ between your dev server and your production server! They will depend on the installation 
and compilation options used for OpenSSL in your machine(s).

$oCRNRSTN->initSessionEncryption([environment-key] -> Specify one of your previously defined addEnvironment() environment keys , 
								   [openssl-encryption-cipher] -> For a list of recommended and available openssl cipher methods...run $oCRNRSTN->openssl_get_cipher_methods(), 
								   [openssl-encryption-key] -> specify an encryption key to be used by the CRNRSTN encryption layer for encryptable session data, 
								   [openssl-encryption-options] -> a bitwise disjunction of the flags OPENSSL_RAW_DATA and OPENSSL_ZERO_PADDING, 
								   [hmac-algorithm] -> Specify the algorithm to be used by CRNRSTN when using the HMAC library to generate a keyed hash value. For a list 
								   					   of available algorithms run hash_algos(). CAUTION :: Some hash_algos returned methods will NOT be compatible
													   with hash_hmac() which CRNRSTN uses in validating its decryption. And certain openssl encryption cipher / hash_algos 
													   algorithm combinations will not be compatible. Please test the compatibility of your desired encryption cipher and 
													   hmac algoritm in each environment...especially before releasing to production code base. 
									);
*/
$oCRNRSTN->initSessionEncryption('LOCALHOST_PC', 'AES-192-OFB', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'sha256');
$oCRNRSTN->initSessionEncryption('LOCALHOST_MAC2', 'AES-192-OFB', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'sha256');
$oCRNRSTN->initSessionEncryption('PROD_HOST24', 'AES-256-CTR', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'ripemd256');

//
// INITIALIZATION FOR ENCRYPTION :: CRNRSTN COOKIE DATA :: ADVANCED CONFIGURATION PARAMETERS
/*
CAUTION. If cookie encryption is enabled and then changed some time later. It is possible for clients to have cookie data that was
encrypted with a "no-longer-in-production" encryption cipher or HMAC algorithm...and hence be unreadable to the application. Developer
needs to take this into consideration and plan for use case where cookie data is unreadable...with graceful degradation or cookie reset.
*/
#$oCRNRSTN->initCookieEncryption([environment-key], [openssl-encryption-cipher], [openssl-encryption-key], [openssl-encryption-options], [hmac-algorithm])
$oCRNRSTN->initCookieEncryption('LOCALHOST_MAC2', 'AES-256-CTR', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'gost');
$oCRNRSTN->initCookieEncryption('LOCALHOST_PC', 'AES-192-OFB', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'sha256');
$oCRNRSTN->initCookieEncryption('PROD_HOST24', 'AES-256-CTR', 'this-Is-the-encryption-key', OPENSSL_RAW_DATA, 'ripemd256');

//
// TO ACHIEVE SLIGHT OPTIMIZATION AT FIRST RUNTIME, PASS AN APPROPRIATE INTEGER VALUE TO requiredDetectionMatches(). ONLY AND PRECISELY WHEN THAT QUANTITY OF PROVIDED $_SERVER PARAMETERS MATCH FOR ANY GIVEN 
// DEFINED ENVIRONMENT'S defineEnvResource() KEYS, WILL THE THE DETECTION SCRIPT STOP PROCESSING ANY FURTHER defineEnvResource() KEYS AND SELECT THE QUALIFYING ENVIRONMENT PROFILE.
$oCRNRSTN->requiredDetectionMatches(5);
$oENV = new crnrstn_environmentals($oCRNRSTN,'simple_configcheck');
 
if(!$oENV->isConfigured($oCRNRSTN)){
#	error_log('_crnrstn.config.inc.php (238) :: I AM NOT CONFIGURED IN SESSION. DEFINING RESOURCE KEYS NEXT.');
	unset($oENV);
	//
	// FOR EACH ENVIRONMENT ABOVE, DEFINE RELEVANT CORE SERVER CONFIG SETTINGS + ADD ANY CUSTOM KEYS/VALUES OF YOUR OWN
	# # # # # # # #
	# RESOURCE KEY DEFINITIONS FOR CORE AND CUSTOM ATTRIBUTES THAT CAN BE USED FOR EACH ENVIRONMENT RUNNING YOUR APPLICATION ::
	# * HERE ARE EXAMPLES OF CORE/RESERVED SERVER SUPER GLOBAL PARAMETERS. 
	# * SUPER GLOBAL SERVER VALUES FROM THESE DEFINITIONS WILL BE USED TO CONFIGURE CRNRSTN TO ITS ENV PER REAL-TIME SERVER SETTINGS
	#___{KEY}__<-- custom or $_SERVER[] value_______{EXAMPLE OF CONTENT}________________
	# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 
	# DOCUMENT_ROOT									(e.g. 'C:\\[path]\\[to]\\[site-root]\\[folder]\\' or '/var/www/')
	# SERVER_NAME									(e.g. 'localhost' or 'stage.mydomain.com' or 'mydomain.com')
	# REMOTE_ADDR									(e.g. '127.0.0.1' or '265.121.2.110')
	# SERVER_SIGNATURE
	# ...{ANY OTHER $_SERVER[] SUPER GLOBAL PARAMETER MAY BE USED TO SUPPORT CRNRSTN SERVER DETECTION}...
	# 
	# For a more complete list of available super global array parameters, please see :: 
	# http://php.net/manual/en/reserved.variables.server.php
	#
	# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 
	# EXAMPLES OF ADDITIONAL AND CUSTOM KEYS/VALUES THAT ONE MAY DECIDE TO INITIALIZE THROUGH CRNRSTN
	# DOMAIN									(e.g. 'www.domain.com' or 'localhost')
	# SSL_ENABLED								(i.e. true or false/NULL)
	# ROOT_PATH_CLIENT_HTTP						(e.g. NULL for hosting off root {DOMAIN} or '{DOMAIN}/[your-folder]/[your-folder]/')
	#
	# So...for example, here is how one would programatically add/define keys to initialize their crnrstn 
	# application where, $oCRNRSTN->defineEnvResource([environment-key], [resource-key], [value]) ::
	# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 
	# $oCRNRSTN->defineEnvResource('STAGE_TESTING_WhAtEvEr', 'SSL_ENABLED', false);
	# $oCRNRSTN->defineEnvResource('PRODUCTION_ZEDS_DEAD', 'ABSPATH', '/home/a8537844/public_html/');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_AN_OLD_MACINTOSH', 'YoUr-custom-resource-key', 'value-for-key');
	# 
	# or, for example...adding some keys (wordpress specific) to run a wordpress blog on top of crnrstn
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'DATABASE_TABLE_PREFIX', 'wp_');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'DATABASE_CHARSET', 'utf8');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'DATABASE_COLLATE', '');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'AUTH_KEY', 				'p19~FS%rRR4C,U8Is3?GsL%T=x2HWO~7PY^NVg}%G0e41:VRDx$u$B75nBDo[z%-');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SECURE_AUTH_KEY', 		'V4L<.%eLV2Ni_~02FT#7bwjuBxAOsbq/q6{RhL)ox^^u8Xy(6[|%+S9v5$rXpD39');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'LOGGED_IN_KEY', 		'$O##gm<>u[WdJCD-pnh)5hNIqAyc3=is<WV%*k]3O%F^p%-l3@$//8?wmHyJ^gY#');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'NONCE_KEY', 			'Pv2bduy|>4}zP2L-<aJNBza?!#bLf{!wc1$1k$;cs=fFO~u}DI2K;TN:nn8Qm{U0');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'AUTH_SALT', 			'OvEKNG`wc0*o8uguR8f^<RrInhDluX0^J:<3@mV:<:LA-V8eaeBr/~DDnJ#,={_1');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SECURE_AUTH_SALT', 		'%lGQeL#3p9o;lhgsQ1UF_A_`*K-V+y}b8M.lL`!/G4$_,JCidTBz.!Xzy`)[/D*&');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'LOGGED_IN_SALT', 		'*<2 .FW;wq;4gYUkz5Q*7-OClKjrC^ZIDM3IQ|1NS|z>LDfuq$?h!L-=C:-.v,0Y');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'NONCE_SALT', 			'Ai=LA9lW:, @DO6-j)kg}]h}9P)4XUrEXTn{/.Hp]gDw$:V%6@^VP;rs0Lp)%n80');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'WPLANG', '');
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'WP_DEBUG', false);
	# $oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'ABSPATH', 'C:\\DATA_GOVT_SURVEILLANCE\\_wwwroot\\xampp\\htdocs\\jony5.com\\');
	# # # # # # # #
	
	

	//
	// BEGIN CONFIG FOR NEXT ENVIRONMENT
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'DOMAIN', 'woodford.jony5.com');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SERVER_NAME', 'woodford.jony5.com');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SERVER_ADDR', '212.1.211.13');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SERVER_PORT', '80');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SERVER_PROTOCOL', 'HTTP/1.1');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SSL_ENABLED', false);
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'DOCUMENT_ROOT', '/home/jony5com/woodford.jony5.com'); # VALUE FOR YOUR SERVER['DOCUMENT_ROOT']
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'DOCUMENT_ROOT_DIR', '');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'ROOT_PATH_CLIENT_HTTP', 'http://woodford.jony5.com/');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'ROOT_PATH_CLIENT_HTTP_DIR', '');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'SOA_NAMESPACE', 'http://services.crnrstn.jony5.com/soap/services');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'WSDL_URI_MGMT', 'http://services.crnrstn.jony5.com/soa/crnrstnmgmt/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'WSDL_URI', 'http://services.crnrstn.jony5.com/soa/crnrstn/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'WSDL_CACHE_TTL','80');	
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'MAILER_FROM_EMAIL', 'noreply_crnrstn@crnrstn.jony5.com');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'MAILER_FROM_NAME', 'CRNRSTN Suite :: Community Mailer');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'MAILER_AUTHKEY', 'Pv2bduy|>4}zP2L-<aJNBza?!#bLf{!wc1$1k$;cs=fFO~u}D');
	$oCRNRSTN->defineEnvResource('PROD_HOST24', 'APP_NAME', 'crnrstn');
			
	//
	// BEGIN CONFIG FOR NEXT ENVIRONMENT
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'DOMAIN', '172.16.110.130');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SERVER_NAME', '172.16.110.130');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SERVER_ADDR', '172.16.110.130');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SERVER_PORT', '80');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SERVER_PROTOCOL', 'HTTP/1.1');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SSL_ENABLED', false);
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'DOCUMENT_ROOT', '/var/www/html'); # VALUE FOR YOUR SERVER['DOCUMENT_ROOT']
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'DOCUMENT_ROOT_DIR', '/woodford');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'ROOT_PATH_CLIENT_HTTP', 'http://172.16.110.130/');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'ROOT_PATH_CLIENT_HTTP_DIR', 'woodford/');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'SOA_NAMESPACE', 'http://172.16.110.130/soap/services');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'WSDL_URI_MGMT', 'http://172.16.110.130/services/soa/crnrstnmgmt/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'WSDL_URI', 'http://172.16.110.130/services/soa/crnrstn/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'WSDL_CACHE_TTL','80');	
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'MAILER_FROM_EMAIL', 'noreply_crnrstn@crnrstn.jony5.com');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'MAILER_FROM_NAME', 'CRNRSTN Suite :: Community Mailer');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'MAILER_AUTHKEY', 'Pv2bduy|>4}zP2L-<aJNBza?!#bLf{!wc1$1k$;cs=fFO~u}D');
	$oCRNRSTN->defineEnvResource('LOCALHOST_MAC2', 'APP_NAME', 'crnrstn');


	//
	// BEGIN CONFIG FOR NEXT ENVIRONMENT
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'DOMAIN', '127.0.0.1');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SERVER_NAME', '127.0.0.1');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SERVER_ADDR', '127.0.0.1');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SERVER_PORT', '80');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SERVER_PROTOCOL', 'HTTP/1.1');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SSL_ENABLED', false);
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'DOCUMENT_ROOT', 'C:/DATA_GOVT_SURVEILLANCE/_wwwroot/xampp/htdocs/'); # VALUE FOR YOUR SERVER['DOCUMENT_ROOT']
	
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'ROOT_PATH_CLIENT_HTTP', 'http://127.0.0.1/');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'ROOT_PATH_CLIENT_HTTP_DIR', '');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'SOA_NAMESPACE', 'http://127.0.0.1/soap/services');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'WSDL_URI_MGMT', 'http://127.0.0.1/services/soa/crnrstnmgmt/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'WSDL_URI', 'http://127.0.0.1/services/soa/crnrstn/1.0.0/wsdl/index.php?wsdl');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'WSDL_CACHE_TTL','80');	
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'MAILER_FROM_EMAIL', 'noreply_crnrstn@crnrstn.jony5.com');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'MAILER_FROM_NAME', 'CRNRSTN Suite :: Community Mailer');
	$oCRNRSTN->defineEnvResource('LOCALHOST_PC', 'MAILER_AUTHKEY', 'Pv2bduy|>4}zP2L-<aJNBza?!#bLf{!wc1$1k$;cs=fFO~u}D');

	

	//
	// FOR ALL ENVIRONMENTS :: AS DESIGNATED BY PASSING '*' AS ENV KEY PARAMETER
	$oCRNRSTN->defineEnvResource('*','NUSOAP_USECURL','0');
	$oCRNRSTN->defineEnvResource('*','PAGE_INDEXSIZE','3');
	$oCRNRSTN->defineEnvResource('*','SEARCHPAGE_INDEXSIZE','15');
	$oCRNRSTN->defineEnvResource('*','USERPROFILE_EXTERNALURI','3');
	$oCRNRSTN->defineEnvResource('*','AUTOSUGGEST_RESULT_MAX','10');
	$oCRNRSTN->defineEnvResource('*','DATA_MODE','XML|XML|SOAP');		# [XML,SOAP] [NAV|CONTENT|COMMENT]
	
	//
	// INSTANTIATE ENVIRONMENTAL CLASS BASED ON ABOVE DEFINED CRNRSTN CONFIGURATION 
	$oENV = new crnrstn_environmentals($oCRNRSTN);
	unset($oCRNRSTN);

}else{
	unset($oCRNRSTN);
}

# # # # # #
# # # # # #
# # # # # #
# # # # # # 	END OF CRNRSTN CONFIG

?>