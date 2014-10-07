<?php

# Require all libs
require_once("./lib/Satmap/Http.php");
require_once("./lib/Satmap/Router.php");

# Set a 404 page
Satmap\Router::four_zero_four(function(){
	Satmap\Http::set_code(404);
	Satmap\Http::set_mime("application/json");
	echo json_encode(array('error' => 'Sorry, no route could be found.'));
});

# Define a get route
Satmap\Router::get('hello',function(){ 
	echo "Hello";
});

# And another
Satmap\Router::get('hello/world',function(){ 
	echo "Hello, world";
});

# Get our HTTP Request
Satmap\Http::collect();

# Route our request
Satmap\Router::route();
