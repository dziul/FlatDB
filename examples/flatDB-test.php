	<?php

require 'autoload.php';

use darkziul\Helpers\accessArrayElement as accessArrayElement;
use darkziul\Helpers\accessArrayElementUseEval as accessArrayElementUseEval;
use darkziul\flatDB;

// $accessAE = new accessArrayElement();
$flatdb = new flatDB('_dataDB/');

// var_dump( $flatdb->dbExists('example') );
// var_dump( $flatdb->dbCreate('example') );
// var_dump( $flatdb->db('example') );
// var_dump( $flatdb->dbDelete('example') );

// var_dump($flatdb->db('example')->tableShow());
// var_dump($flatdb->db('example')->tableShow(true)); //json

// var_dump($flatdb->db('example')->tableCreate('default'));//create
// var_dump($flatdb->db('example')->table('default'));//instance
// var_dump($flatdb->db('example')->tableExists('default'));//exists
// var_dump($flatdb->db('example')->tableDelete('default'), $flatdb->db('example')->tableExists('default'));//delete and check
$whoArr = ['parent', 'self', 'other', 'child'];



$arrInsert = [
		'who'=> $whoArr[mt_rand(0, count($whoArr)-1)],
		'uniqid'=> uniqid(rand(),true),
		'number'=>rand(19,90),
		'group'=>[
			'a'=>substr(uniqid(rand(),true), -10),
			'b'=>substr(uniqid(rand(),true), -10),
			'c'=>substr(uniqid(rand(),true), -10),
			'i' => 2
		],
		'unid' => 15,
		'collection' => [
			'item' => [
				'use' => [
					3,
					20,
					2,
					150
				]
			]
		]
	];
// var_dump($flatdb->db('example')->table('default')->insert($arrInsert)->execute());//create
// var_dump($flatdb->db('example')->table('default')->insert($arrInsert, 'item')->execute());//create custom key


$arrAdd = [
	'type' => 'array'
];
$arrWhere = [
	'who' => 'self'
];
// var_dump($flatdb->db('example')->table('default')->add($arrAdd)->execute());//add
// var_dump($flatdb->db('example')->table('default')->add($arrAdd)->where($arrWhere)->execute());//add e filter 


// var_dump($flatdb->db('example')->table('default')->remove(2)->execute());//delete
// var_dump($flatdb->db('example')->table('default')->remove([10,8])->execute());//delete multi


// var_dump($flatdb->db('example')->table('default')->meta());//show metadata


$arr = [
	
	'main' => [
		
		[
			'id'=> uniqid(rand(),true),
			'description' => 'okokok',
			['title'=>'test'],
			['title'=>'test2']
		],
		[
			'id'=> [uniqid(rand(),true), uniqid(rand(),true), uniqid(rand(),true), 'ok', ''],
			'description' => 'okokok',
			'name' => 'pedro'
		],
		[
			'id'=> uniqid(rand(),true),
			'description' => 'okokok',
			['test1','test2','test3']
		]
	],
	'test'

];



// var_dump(accessArrayElement::get(['main.[+].description', 'main.[+].id'], $arr));
// var_dump(accessArrayElement::exist(['main.[+].description', 'main.[+].id'], $arr));
$addARR = [
	'item' => [
		'litmus v20',
		'life go go'
	]
];
// $addARR = [
// 	'item.[]' => 'litmus Master',
// 	'item.[]' => 'life or death'
// ];	
var_dump(accessArrayElement::inset(['main.[+].id' => $addARR], $arr), $arr);



// function test($key, $t=0){

// 	if($t) {
// 		return ($key === '+' || $key === '(+)' || $key == '(?)' || $key == '[?]' || $key == '[+]');
// 	} else {
// 		return (in_array($key, ['+', '(+)', '(?)', '[?]', '[+]']));
// 	}
// }

// $limit = 10000;
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	test('+',1);
// }
// $end = microtime(true);
// var_dump($end - $begin);


// // $limit = 10000;
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	test('+');	
// }
// $end = microtime(true);
// var_dump($end - $begin);


// var_dump( arrayElementExists(['collection[item]' => 'use'], $arrInsert) );
	
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$accessArrayElement->getArrayElement($arrInsert, '[collection][item][use]');
// }
// $end = microtime(true);
// var_dump($end - $begin);

// var_dump( $accessArrayElement->getArrayElement($arrInsert, '[collection][item][use]') );

// $arr = [
// 	'ok'=>['test'=>2],
// 	'ok'=>['test'=>5],
// 	'last'
// ];


// $compare = [
// 	'ok'
// ];

// var_dump(arraySearch($compare, $arrInsert ));
















// $limit = 10000;

// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$d = hash('crc32', $i);
// }
// $end = microtime(true);
// var_dump( 'METHOD 1 :: ' . ($end - $begin) );

/* FAST */
// $d = [];//init
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$string = '.0a1b2c3d4f6g7h8i9j';
// 	$d[] = str_pad((.5/($i+1)), 25, $string);
// }
// $end = microtime(true);
// var_dump('METHOD 2 :: ' . ($end - $begin) );
// /* FAST */


// $d = [];//init
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$string = (.5/($i+1)) . '.0a1b2c3d4f6g7h8i9j';

// 	$d[] = mb_substr($string, 0, 25);
// }
// $end = microtime(true);
// var_dump('METHOD 2-1 :: ' . ($end - $begin) );


// $d = [];//init
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$string = (($i+1)/3.14159265359);
// 	$d[] = ''.$string;

// 	// $string = 'example';
// 	// $d[] = @$string[5] . @$string[4] . @$string[1] . @$string[0] . @$string[3];
// }
// $end = microtime(true);
// var_dump($d,'METHOD 2-1-1 :: ' . ($end - $begin) );

// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	$d = strtolower(str_replace('=', '', base64_encode($i)));
// }
// $end = microtime(true);
// var_dump( 'METHOD 3 :: ' . ($end - $begin) );	