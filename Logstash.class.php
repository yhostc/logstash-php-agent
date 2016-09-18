<?php
// +----------------------------------------------------------------------
// | Wecook LogStash Tracking
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.wecook.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yhostc <yhostc@gmail.com>
// +----------------------------------------------------------------------

class Logstash
{
	/**
	 * REDIS REDIS服务器地址
	 * @var String
	 */
	private $REDIS_HOST	= '127.0.0.1';
	
	/**
	 * Redis 端口地址
	 * @var Int
	 */
	private $REDIS_PORT	= '6379';
	
	/**
	 * Redis 验证信息
	 * @var string
	 */
	private $REDIS_AUTH = '';

	/**
	 * 日志索引名称
	 * @var String
	 */
	private $LOGSTASH_INDEX = 'logstash';
	
	/**
	 * 日志索引类型
	 * @var String
	 */
	private $LOGSTASH_TYPE = '';
	
	
	public function __construct($options=array()){
		$this->REDIS_HOST = @$options['REDIS_HOST'] ? $options['REDIS_HOST'] : $this->REDIS_HOST;
		$this->REDIS_PORT = @$options['REDIS_PORT'] ? $options['REDIS_PORT'] : $this->REDIS_PORT;
		$this->REDIS_AUTH = @$options['REDIS_AUTH'] ? $options['REDIS_AUTH'] : $this->REDIS_AUTH;

		$this->LOGSTASH_TYPE  = $options['LOGSTASH_TYPE']  ? $options['LOGSTASH_TYPE']  : $this->LOGSTASH_TYPE;
	}
	
	/**
	 * 日志记录
	 * @param string $uri 			请求URI
	 * @param array $req			请求信息
	 * @param array $res 			返回信息
	 * @param string $tracking_id	请求跟踪ID
	 */
	public function write($uri='', $req=array(), $res=array(), $tracking_id=''){

		// 整理消息日志协议
		$logdata = array(
			'type'			=> $this->LOGSTASH_TYPE,		// 日志类型
			'tracking_id'   => $tracking_id, 				// 唯一请求标识
			'@uri'			=> (string)$uri,				// 请求URI
			'@req'          => (array)$this->check($req),	// 请求结果
			'@res'          => (array)$this->check($res),	// 响应结果
			'@timestamp'	=> date('c', time())			// 请求时间
		);

		$redis = new \Redis(); 
		$redis->connect($this->REDIS_HOST, $this->REDIS_PORT);
		$redis->auth($this->REDIS_AUTH);
	
		// 放进redis队列
		$idx = $redis->lpush($this->LOGSTASH_INDEX, @json_encode($logdata, JSON_UNESCAPED_UNICODE));

		echo "-> [".($idx? "SUCCESS" : "FAIL")."] ".date('Y-m-d H:i:s')." TYPE:{$this->LOGSTASH_TYPE} URI:{$uri} TRACKING:{$tracking_id} IDX:{$idx}\r\n";
	}

	/**
	 * 返回有效的两级数组，多级数组将被忽视
	 * @param  array $params
	 * @return array
	 */
	private function check($params){
		$data = array();
		foreach ($params as $key => $value) {
			if(is_array($value)){
				$value = @json_encode($value, JSON_UNESCAPED_UNICODE);
			}
			$data[$key] = $value;
		}
		return (array)$data;
	}

}