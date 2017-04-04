<?php
 
namespace darkziul;

/**
 * flat DB Interface
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 interface flatDBInterface{

 	public function insert(array $arr, $id=null);

 	public function remove($id);

 	public function update($id);

 	/**
 	 * Selecionar
 	 * @param string|array $id @example (string)'nome' | (array)['nome', 'idade']
 	 * @return type
 	 */
 	public function select($key=null);

 	/**
 	 * Condições | Filtro
 	 * @param array $arr 
 	 * @return type
 	 */
 	public function where(array $arr);


 	public function execute();


 	public function offset(number $n);

 	public function limit(number $n);
 	

 	// public function metaData();

 	// public function indexes();



 }//END interface