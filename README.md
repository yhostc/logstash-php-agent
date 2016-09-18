业务日志采集使用规范
==================================


### 采集目的
汇总业务系统的输入和输出日志，实现用户和业务服务的统一过程跟踪。

### 采集方法
加载LogStash采集类，通过如下DEMO进行日志记录。

参数解释：
+ uri: 该服务统一英文标识，格式为 "/业务名称/服务名称/接口名称"，字符串类型
+ req: 该接口的输入参数，数组类型
+ res: 该接口的输出参数，数组类型
+ tracking_id: 统一跟踪ID，该ID产生于和用户设备直接互动的项目，并通过GET方式传递到该用户请求所涉及的所有后端服务，并逐层传递。

```PHP
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
```


### 问题跟踪
在异常产生时，我们在ES系统筛选此tracking_id，即可获知上下游链条的输入和输出，对快速定位问题提供参考依据。