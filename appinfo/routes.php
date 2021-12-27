<?php

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'share#create', 'url' => '/new', 'verb' => 'POST'],
        ['name' => 'share#update', 'url' => '/update', 'verb' => 'POST'],
        ['name' => 'settings#fetch', 'url' => '/settings', 'verb' => 'GET'],
        ['name' => 'settings#save', 'url' => '/settings/save', 'verb' => 'POST'],
	]
];
