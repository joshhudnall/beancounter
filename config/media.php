<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Available sizes
	|--------------------------------------------------------------------------
	|
	| Probably should be listed largest to smallest, with specialty sizes first.
	| This is the order that fallback images will be chosen if the one requested is unavailable.
	|
	*/

	'sizes' => [
    'm' => [
      'length' => 1200,
      'aspectRatio' => 'source',
    ],
    's' => [
      'length' => 600,
      'aspectRatio' => 'source',
    ],
    'xs' => [
      'length' => 250,
      'aspectRatio' => 'source',
    ],
    'tl' => [
      'length' => 600,
      'aspectRatio' => '1:1',
    ],
    't' => [
      'length' => 300,
      'aspectRatio' => '1:1',
    ],
  ],


];
