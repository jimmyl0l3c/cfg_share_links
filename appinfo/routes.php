<?php

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'share#create', 'url' => '/new', 'verb' => 'POST'],
        ['name' => 'share#update', 'url' => '/update', 'verb' => 'POST'],
	]
];
