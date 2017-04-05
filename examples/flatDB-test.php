<?php

require 'autoload.php';

use darkziul\Helpers\accessArrayElement as accessArrayElement;
use darkziul\flatDB;


$flatdb = new flatDB('_dataDB/');

// var_dump( $flatdb->dbCreate('ok') );
// var_dump( $flatdb->db('ok') );
// var_dump( $flatdb->dbExists('ok') );
// var_dump( $flatdb->dbDelete('ok') );

// var_dump($flatdb->db('ok')->tableShow());
// var_dump($flatdb->db('ok')->tableShow(true)); //json

// var_dump($flatdb->db('ok')->tableCreate('analu'));//create
// var_dump($flatdb->db('ok')->table('analu'));//instance
// var_dump($flatdb->db('ok')->tableExists('analu'));//exists
var_dump($flatdb->db('ok')->tableDelete('analu'), $flatdb->db('ok')->tableExists('analu'));//delete