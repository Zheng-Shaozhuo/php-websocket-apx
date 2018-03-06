<?php
    $sw = new swoole_websocket_server('0.0.0.0', 9502);
	
	// 设置链接属性
    $sw->set(array(
    		'daemonize' => false,		// 是否守护进程, 即是否后台运行
    		'max_request' => 10000,		// 最大连接数量
    		'dispatch_mode' => 2,		// 派发模式
    		'debug_mode' => 1,
			//心跳检测
    		'heartbeat_checkk_interval' => 5,
    		'heartbeat_idle_time' => 600
    ));
    
	// 监听websocket长连接建立
    $sw->on('open', function(swoole_websocket_server $server, $request) {
    	echo "func open tick [$request->fd].\n";
    	$server->push($request->fd, "hello, welcome to chatroom \n");
    });
    
	// 监听websocket消息事件
    $sw->on('message', function(swoole_websocket_server $server, $frame) {
    	echo "func message tick [$frame->fd]:[$frame->data]:[$frame->opcode]:[$frame->finish], receive msg.\n";
    	$msg = 'No.' . $frame->fd . " (" . date('Y-m-d H:i:s') . ") : {$frame->data}\n";
    	
    	global $sw;
		// 遍历当前所有链接, 群发推送
		foreach($sw->connections as $fd) {
    		$server->push($fd, $msg);
    	}
    });
    
	// 监听websocket链接关闭事件
    $sw->on('close', function($serv, $fd){
    	echo "client [$fd] is closed.\n";
    	$serv->close($fd);
    });
    
    $sw->start();
    
