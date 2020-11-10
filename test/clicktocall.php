<?php
$extension = $_REQUEST['internalnum'];
$dialphonenumber = $_REQUEST['outboundnum'];

$timeout = 10;
$asterisk_ip = "10.103.1.5";

$socket = fsockopen($asterisk_ip,"5038", $errno, $errstr, $timeout);
fputs($socket, "Action: Login\r\n");
fputs($socket, "UserName: clickadmin\r\n");
fputs($socket, "Secret: asdf1234S3\r\n\r\n");

$wrets=fgets($socket,128);

echo $wrets;

fputs($socket, "Action: Originate\r\n" );
fputs($socket, "Channel: SIP/$extension\r\n" );
fputs($socket, "Exten: $dialphonenumber\r\n" );
fputs($socket, "Context: dial-outbound\r\n" ); // very important to change to your outbound context
fputs($socket, "Priority: 1\r\n" );
fputs($socket, "Async: yes\r\n\r\n" );

$wrets=fgets($socket,128);
echo $wrets;



