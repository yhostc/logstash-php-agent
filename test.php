<?php

require("Logstash.class.php");


$options = [
	'REDIS_HOST'	=> '192.168.3.13',
	'REDIS_PORT'	=> 6379,
	'LOGSTASH_TYPE'	=> 'wecook-service-app'
];
$log = new Logstash($options);



$req = ['wid'=>'111', 'channel'=>'xiaomi'];
$res = [
	'took'	=> 2.63, 
	'data'	=> [['aaa'=>time(), 'bbb'=>'222'],['aaa'=>'333', 'bbb'=>'444']]
];

$log->write('app/device/register', $req, $res, md5(time()));

?>