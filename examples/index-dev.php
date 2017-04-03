<?php

require 'autoload.php';

use darkziul\flatDB as flatDB;


$db = new flatDB('default', 'data');
$dbDefault = $db->table('offer');
var_dump( $dbDefault->metadata() );

// $arr = [
// 			'ok'=>[
// 				1=>['test'=>2],
// 				2=>['test'=>25454],
// 				3=>['test'=>5]
// 			]
// 		];


// $db = new phpDB();

// // var_dump($db->bracket('[b][?][link]'));
// var_dump( $db->getArrayElement($arr, '[ok][?][test]') ); //get

// // var_dump($arr[1]);