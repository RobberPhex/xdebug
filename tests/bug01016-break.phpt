--TEST--
Test for bug #1016: Support for pause-execution
--SKIPIF--
<?php if (getenv("SKIP_DBGP_TESTS")) { exit("skip Excluding DBGp tests"); } ?>
--FILE--
<?php
require 'dbgp/dbgpclient.php';

$dir = dirname(__FILE__);
putenv("XDEBUG_TEST_DIR=$dir");

$filename = dirname(__FILE__) . '/bug01016.inc';

$debugClient = new DebugClient();
$conn = $debugClient->start($filename);

$debugClient->doRead($conn);

$debugClient->sendCommand($conn, 'run', 1);

$debugClient->sendCommand($conn, 'status', 2);
$debugClient->doRead($conn, (string)2);

$debugClient->sendCommand($conn, 'break', 3);
$debugClient->doRead($conn, (string)3);

$debugClient->doRead($conn, (string)1);

$debugClient->sendCommand($conn, 'status', 4);
$debugClient->doRead($conn, (string)4);

$debugClient->sendCommand($conn, 'break', 5);
$debugClient->doRead($conn, (string)5);

$debugClient->sendCommand($conn, 'detach', 6);
$debugClient->doRead($conn, (string)6);

$debugClient->stop($conn);

?>
--EXPECTF--
<?xml version="1.0" encoding="iso-8859-1"?>
<init xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" fileuri="file://bug01016.inc" language="PHP" xdebug:language_version="" protocol_version="1.0" appid="" idekey=""><engine version=""><![CDATA[Xdebug]]></engine><author><![CDATA[Derick Rethans]]></author><url><![CDATA[https://xdebug.org]]></url><copyright><![CDATA[Copyright (c) 2002-2099 by Derick Rethans]]></copyright></init>

-> run -i 1
-> status -i 2
<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="status" transaction_id="2" status="running" reason="ok"></response>

-> break -i 3
<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="break" transaction_id="3" status="running" reason="ok"></response>

<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="run" transaction_id="1" status="break" reason="ok"><xdebug:message filename="file://bug01016.inc" lineno="%d"></xdebug:message></response>

-> status -i 4
<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="status" transaction_id="4" status="break" reason="ok"></response>

-> break -i 5
<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="break" transaction_id="5" status="break" reason="ok"><error code="5"><message><![CDATA[command is not available]]></message></error></response>

-> detach -i 6
<?xml version="1.0" encoding="iso-8859-1"?>
<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="detach" transaction_id="6" status="stopping" reason="ok"></response>
