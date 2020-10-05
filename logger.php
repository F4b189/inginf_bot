<?php
/**
* This file contains all the logger code
* Precision of logging is defined by the constant LOG_LVL (from 0 to 7)
* 0 => No log
* 7 => Log every single update and method (use with caution)
*
* Intended use in a function (where $request is the result of the method):
* if (LOG_LVL > prefixed_level) {
* 	sendLog(__FUNCTION__, $request);
* }
*
* @todo Define other log levels
* @todo Define aliases for the log levels
*
* No libraries are used in this project.
*
* @author		Giorgio Pais
* @author		Giulio Coa
* @author		Simone Cosimo
* @author		Luca Zaccaria
* @author		Alessio Bincoletto
* @author		Marco Smorti
*
* @copyright	2020- Giorgio Pais <info@politoinginf.it>
*
* @license		https://choosealicense.com/licenses/gpl-3.0/
*/

define('LOG_LVL', 7);

/**
* Send the log to the log channel
*
* @param string $function Name of the function/update
* @param string $request JSON returned from method/update
*
* @return mixed Message sent in the channel
*/
function sendLog($function, $request) {
	/**
	* Decode the request
	*
	* json_decode() Convert the JSON string to a PHP object
	*/
	$request = json_decode($request, TRUE);
	
	$reply = "<b>$function</b>:\n\n";

	/**
	* If a description is set, appends it to the reply string
	*
	* empty() check if the argument is empty
	* 	''
	* 	""
	* 	'0'
	* 	"0"
	* 	0
	* 	0.0
	* 	NULL
	* 	FALSE
	* 	[]
	* 	array()
	*/
	$description = empty($request['description']) === FALSE ? "\n\nℹ️ <i>" . $request['description'] . '</i> ℹ️' : '';

	// Check if there are no errors or if $request is an incoming update
	if($request['ok'] == TRUE || $function == 'UPDATE') {
		/**
		* If $request has been generated by a function, extracts the message from the field "result"
		* otherwise it gets it directly from the result of the UPDATE
		*
		* empty() check if the argument is empty
		* 	''
		* 	""
		* 	'0'
		* 	"0"
		* 	0
		* 	0.0
		* 	NULL
		* 	FALSE
		* 	[]
		* 	array()
		*/
		$msg = empty($request['result']) === FALSE ? $request['result'] : $request;

		// Appends a pretty-printed message to $reply
		$reply .= json_encode($msg, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	// If there are some errors getting the results
	} else if($request['ok'] == FALSE) {
		//If an error_code is set, appends it to the reply message
		$error_code = empty($request['error_code']) === FALSE ? "\n\n<b>❌ Error code:" . $request['error_code'] . "❌</b>" : "";
		$reply = "<b>Result not present!</b>" . $description . $error_code;
	}
	// Sends a message to the debug channel (chatid of the channel declared in private.php file, not present in the repo)
	return sendMessage(LOG_CHANNEL, $reply);
}

// Warning: sends every single update and every method result to the debug channel
if (LOG_LVL > 6) {
	sendLog('UPDATE', $content);
}
