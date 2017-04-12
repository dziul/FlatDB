	<?php

require 'autoload.php';

use darkziul\Helpers\dotNotationArrayAccess as Arr;
// use darkziul\Helpers\accessArrayElementUseEval as accessArrayElementUseEval;
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


$arr = [
	'main' => [
		[
			'category' => [
					'code' => microtime(true),
					'name' => 'Lima',
					'tag' => ['ok2','ok5','ok784']
				],
			'city' => 'rio de janeiro'
		],
		[
			'category' => [
					'code' => 'm__' . microtime(true),
					'name' => 'Pedro',
					'tag' => ['not2784','not7845','not54', 'sub'=>'true']
				],
			'city' => 'Sao Paulo'
		],
		'name_sub' => 'falsiane',
		'Hola World'
]
];


// begin GET accessArray ====
// ==========================
// var_dump(Arr::get($arr, ['main.[+].category', 'main.[+].[+].code'], true)); //get com o nome da KEY
// var_dump(Arr::get($arr, ['main.[+].category', 'main.[+].[+].code'])); //get grupo
// var_dump(Arr::get($arr, 'main.[+].category')); //get elemet
// var_dump(Arr::get($arr)); //get all
// ==========================
//end GET accessArray =======


// begin SET accessArray ====
// ==========================
var_dump(Arr::set($arr, ['main.[+].body'=>'coconut'])); //set key=>value
// ==========================
//end SET accessArray =======





// $limit = 10000;
// $begin = microtime(true);
// for ($i=0; $i < $limit; $i++) { 
// 	Arr::get($arr, 'main.[+].category');
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
