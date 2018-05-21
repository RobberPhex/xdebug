--TEST--
Bug #60523 (PHP Errors are not reported in browsers using built-in SAPI)
--INI--
display_errors=1
--FILE--
<?php
include "php_cli_server.inc";
php_cli_server_start('require("syntax_error.php");');
$dir = realpath(dirname(__FILE__));

file_put_contents($dir . "/syntax_error.php", "<?php non_exists_function(); ?>");

list($host, $port) = explode(':', PHP_CLI_SERVER_ADDRESS);
$port = intval($port)?:80;
$output = '';

$fp = fsockopen($host, $port, $errno, $errstr, 0.5);
if (!$fp) {
  die("connect failed");
}

if(fwrite($fp, <<<HEADER
GET /index.php HTTP/1.1
Host: {$host}


HEADER
)) {
	while (!feof($fp)) {
		$output .= fgets($fp);
	}
}
echo $output;
@unlink($dir . "/syntax_error.php");
fclose($fp);
?>
--EXPECTF--
HTTP/1.0 500 Internal Server Error
Host: %s
Date: %s
Connection: close
X-Powered-By: PHP/%s
Content-type: text/html; charset=UTF-8

<br />
<font size='1'><table class='xdebug-error xe-uncaught-exception' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Fatal error: Uncaught Error: Call to undefined function non_exists_function() in %s/syntax_error.php on line <i>1</i></th></tr>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Error: Call to undefined function non_exists_function() in %s/syntax_error.php on line <i>1</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>%f</td><td bgcolor='#eeeeec' align='right'>354128</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='%s/index.php' bgcolor='#eeeeec'>.../index.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>%f</td><td bgcolor='#eeeeec' align='right'>354752</td><td bgcolor='#eeeeec'>require( <font color='#00bb00'>'%s/syntax_error.php'</font> )</td><td title='%s/index.php' bgcolor='#eeeeec'>.../index.php<b>:</b>1</td></tr>
</table></font>