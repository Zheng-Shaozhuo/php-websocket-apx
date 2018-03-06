<?php
    $sw = new swoole_websocket_server('0.0.0.0', 9502);
    $sw->set(array(
    		'daemonize' => false,
    		'max_request' => 10000,
    		'dispatch_mode' => 2,
    		'debug_mode' => 1,
    		'heartbeat_checkk_interval' => 5,
    		'heartbeat_idle_time' => 600
    ));
    
    $sw->on('open', function($sw, $request) {
    	echo "func open tick [$request->fd].\n";
    	$sw->push($request->fd, "hello, welcome to chatroom \n");
    });
    
    $sw->on('message', function($sw, $request) {
    	echo "func message tick [$request->fd], receive msg.\n";
    	$msg = 'from ' . $request->fd . " : {$request->data}\n";
    	
    	$start_fd = 0;
    	while (true) {
    		$conn_list = $sw->connection_list($start_fd, 100);
    		var_dump($conn_list, count($conn_list));
    		
    		if ($conn_list == false || count($conn_list) == 0) {
    			echo "finish\n";
    			return ;
    		}
    		
    		$start_fd = end($conn_list);
    		foreach($conn_list as $fd) {
    			$sw->push($fd, $msg);
    		}
    	};
    });
    
    $sw->on('close', function($sw, $fd){
    	echo "client [$fd] is closed.\n";
    	$sw->close($fd);
    });
    
    $sw->start();
    
