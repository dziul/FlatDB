<?php

require 'autoload.php';

use darkziul\phpDB as phpDB;



$arr = [
			'a'=>	[
				'b'=>['test','145.5m']
					]
		];


$db = new phpDB();

var_dump( $db->accessArray('a[b]', $arr) );