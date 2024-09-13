<?php

return [
	'routes' => [
		['name' => 'share#create', 'url' => '/new', 'verb' => 'POST'],
		['name' => 'share#updateById', 'url' => '/update-by-id', 'verb' => 'PUT'],
		['name' => 'share#updateByToken', 'url' => '/update-by-token', 'verb' => 'PUT'],
		['name' => 'settings#fetch', 'url' => '/settings', 'verb' => 'GET'],
		['name' => 'settings#save', 'url' => '/settings/save', 'verb' => 'POST'],
	]
];
