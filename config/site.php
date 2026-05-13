<?php

return [
	'title' => 'QIFESS',
	'subtitle' => 'FMS',
	'company' => 'QIFESS',
	'description' => 'QIFESS',
	'version' => '1.0',
	'year' => '2025',
	'email' => '-',
	'phone' => '-',
	'address' => 'Address',
	'web' => config('app.url'),

	// TEMPLATING -------------------------------------------------------
    'template' => 'themes.architectui.',
    'mobile-template' => 'themes.onsen.',
	'view' => 'src',

    'asianet_api_url' => env('ASIANET_API_URL'),
    'asianet_api_user' => env('ASIANET_API_USER'),
    'asianet_api_password' => env('ASIANET_API_PASSWORD'),
];
