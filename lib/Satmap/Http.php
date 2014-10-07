<?php

namespace Satmap;

class Http {
	
	/* A place to store our $_SERVER var for work */
	public static $server;
	
	/* Comparison constants for methods */
	const GET = 'get';
	const POST = 'post';
	const PUT = 'put';
	const DELETE = 'delete';
	const PATCH = 'patch';
	
	/* An array of all valid status codes */
	public static $status_codes = [
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		204 => 'No Content',
		301 => 'Moved Permanently',
		302 => 'Found',
		304 => 'Not Modified',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		409 => 'Conflict',
		418 => 'I\'m A Teapot',
		420 => 'Rate Limited',
		429 => 'Rate Limited',
		500 => 'Server Error',
		502 => 'Bad Gateway',
		503 => 'Unavailable',
		504 => 'Gateway Timeout'
	];
	
	/* An array of all valid http methods */
	public static $methods = ['post','get','put','patch','delete'];
	
	/* An array of json mine types */
	public static $json_mimes = ['application/json','text/json','text/javascript'];
	
	/* An array for url form */
	public static $form_mimes = ['application/x-www-form-urlencoded'];
	
	/* Pass our SERVER to the class to work with */
	public function __construct(){
		self::$server = $_SERVER;
	}	
	
	public static function collect(){
		self::$server = $_SERVER;
	}
	
	/* Get and validate our method */
	public static function method(){
		return isset(self::$server['REQUEST_METHOD']) ? (in_array(strtolower(self::$server['REQUEST_METHOD']), self::$methods) ? strtolower(self::$server['REQUEST_METHOD']) : false) : false;
	}
	
	/* Check if request is secured */
	public static function secure(){
		if(self::$server['SERVER_PORT'] == 443 && self::$server['HTTPS'] !== ''){ return true; }
		else { return false; }
	}
	
	/* Get an array of query string */
	public static function query(){
		$query = self::$server['QUERY_STRING'];
		if(strlen($query) > 0){
			parse_str($query,$array);
			return $array;
		} else { return false; }
	}
	
	/* Get an array of our path 
	   $offset will trip the array by that many keys
	   this useful for treating subfolder as root
	*/
	public static function path($offset = null){
		// Get our uri from the server
		$uri = self::$server['REQUEST_URI'];
		
		// Remove our query.
		if(self::query()){
			$uri = str_replace("?".self::$server['QUERY_STRING'],"",$uri);
		}
		
		// Remove a trailing slash
		$length = strlen($uri);
		if($uri[$length-1] == "/"){ 
			$uri = substr($uri,0,-1);
		}
		
		// Remove a preceding slash
		if($uri[0] == "/"){
			$uri = substr($uri,1,$length);
		}
		
		// Split our string
		$uri = explode("/",$uri);
		
		// Process our offset
		if($offset){
			for($i = 0; $i < $offset; $i++){
				unset($uri[$i]);
			}
		}
		
		// Return our array
		return $uri;
	}
	
	/* Get post raw body */
	public static function full_body(){
		$body = file_get_contents('php://input');
		return strlen($body) > 0 ? $body : false;
	}
	
	/* Gets a processed body */
	public static function body(){
	
		$body = self::full_body();
		
		if($body && self::is_json()){
			return json_decode($body);
		} else if($body && self::is_form()){
			parse_str($body,$array);
			return $array;
		} else { 
			return $body;
		}
	}
	
	/* Get our mime type */
	public static function mime(){
		$mime = (isset(self::$server['CONTENT_TYPE']) ? self::$server['CONTENT_TYPE'] : "");
		return strlen($mime) ? $mime : false;
	}
	
	/* Checks if our body is JSON */
	public static function is_json(){
		return self::mime() && in_array(self::mime(),self::$json_mimes) ? true : false;
	}
	
	/* Checks if our body is FROM */
	public static function is_form(){
		return self::mime() && in_array(self::mime(),self::$form_mimes) ? true : false;
	}
	
	/* Set a header code for response */
	public static function set_code($code){
		return http_response_code($code);
	}
	
	/* Set a header code for response */
	public static function set_mime($type){
		return header('Content-Type: '.$type);
	}
}