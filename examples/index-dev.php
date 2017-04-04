<?php

require 'autoload.php';

use darkziul\Helpers\accessArrayElement as accessArrayElement;
use darkziul\flatDB as flatDB;


// $db = new flatDB('default', 'data');
// $dbDefault = $db->table('offer');
// var_dump( $dbDefault->metadata() );


$arrY = new accessArrayElement();

$arr =[
		'link'=>'ok',
		'attr' => [
			'css' =>[
				'black' => '#000'
			]
		],
		'attrT' => [
			'css' =>[
				'green' => '###'
			]
		],
		'attrs' => [
			'css' =>[
				'red' => '???',
				'black' => 'ok'
			]
		]

];


$ok = $arrY->getArrayElement($arr, '[?][css][red]');

var_dump($ok);

// $arr = [
// 			'ok'=>[
// 				1=>['test'=>2],
// 				2=>['test'=>25454],
// 				3=>['test'=>5]
// 			]
// 		];


// $db = new flatDB();

// // var_dump($db->bracket('[b][?][link]'));
// var_dump( $db->getArrayElement($arr, '[ok][?][test]') ); //get

// // var_dump($arr[1]);