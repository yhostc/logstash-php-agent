<?php

require("Logstash.class.php");

// 模拟参数
$uri = 'app/device/register';
$req = ['wid'=>'111', 'channel'=>'xiaomi'];
$res = [
	'took'	=> 2.63, 
	'data'	=> [['aaa'=>time(), 'bbb'=>'222'],['aaa'=>'333', 'bbb'=>'444']]
];
$tracking_id = @$_GET['tracking_id'] ? $_GET['tracking_id'] : md5(time());

// 记录日志
$options = [
	'REDIS_HOST'	=> '192.168.3.13',
	'REDIS_PORT'	=> 6379,
	'LOGSTASH_TYPE'	=> 'wecook-service-app'
];
$log = new Logstash($options);
$log->write($uri, $req, $res, $tracking_id);
?>