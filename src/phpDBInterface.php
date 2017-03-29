<?php
 
namespace darkziul;

/**
 * phpDB Interface
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 interface phpDBInterface{

 	public function add();
 	public function attach();

 	public function change();
 	public function remove();

 	public function find();


 	public function save();

 	public function all();
 	public function column();




 }//END interface