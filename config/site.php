<?php

return [
	'title' => 'AsiaNet',
	'subtitle' => 'FMS',
	'company' => 'Asia Net',
	'description' => 'Asia Net',
	'version' => '1.0',
	'year' => '2022',
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
