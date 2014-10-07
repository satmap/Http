<?php

namespace Satmap;

class Router extends Http{

	static public $routes = [];
	static public $four = [];
	
	static public function get($path, $function){
		if(!isset(self::$routes[$path])){
			self::$routes[$path] = array('method' => 'get', 'callback' => $function);
		}
	}
	
	static public function four_zero_four($function){
		self::$four = array('method' => 'all', 'callback' => $function);
	}
	
	static public function route(){
		$path = implode("/",parent::path(2));
		$method = parent::method();
		
		if(isset(self::$routes[$path])){
			$route = self::$routes[$path];
			if($route['method'] == $method || $route['method'] == 'all'){
				$route['callback']();
			} else {
				self::no_route();
			}
		} else {
			self::no_route();
		}
	}
	
	static public function no_route(){
		$callback = self::$four;
		return $callback['callback']();
	}
}