<?php

require 'autoload.php';

use darkziul\Helpers\accessArrayElement as accessArrayElement;
use darkziul\flatDB;


$flatdb = new flatDB('_dataDB/');

// var_dump( $flatdb->dbCreate('example') );
// var_dump( $flatdb->db('example') );
// var_dump( $flatdb->dbExists('example') );
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
			'c'=>substr(uniqid(rand(),true), -10)
		]
	];
// var_dump($flatdb->db('example')->table('default')->insert($arrInsert)->execute());//create


$arrAdd = [
	'who' => 'self'
];
$arrWhere = [
	
]
// var_dump($flatdb->db('example')->table('default')->add($arrAdd)->execute());//add
// // var_dump($flatdb->db('example')->table('default')->add($arrAdd)->execute());//add se tiver 
