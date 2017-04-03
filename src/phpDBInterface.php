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

 	/**
 	 * 
 	 * string :: 1  ::  '[product][tv]'  Delete todas as Tvs
 	 * array :: [1,2]  ::  ['[product][tv]', '[product][pc]']  deleta todas as TVs e PCs
 	 * mode where :: [1=>value, 2]   ::   ['[product][tv]'=>'samsung', '[product][pc]']  :: deleta todas as TVs Samsung e PCs
 	 * 
 	 * */
 	public function remove();


 	/**
 	 *  string :: 1 :: '[nome]'
 	 * array :: [1,2] ['[nome]', '[money]']
 	 * mode where array :: [1=>value, 2]  ::  ['[nome]'=>'pedro', '[money]']
 	 * 
 	 * */
 	public function find();


 	public function save();

 	public function all();
 	public function column();




 }//END interface