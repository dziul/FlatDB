<?php
 
namespace darkziul;

/**
 * dbQuery
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class dbQuery
 {

	
	/**
	 * Nome do DB
	 * @var string
	 */
	public $name;
	/**
	 * Caminho do DB
	 * @var string
	 */
	public $path;
	/**
	 * Caminho base do DB.  Algo como dirname()
	 * @var string
	 */
	public $basePath;
	/**
	 * identificar se o DB foi instanciado
	 * @var bool
	 */
	public $instantiated = false;

	public function __construct($path='', $name=null)
	{
		if (empty($name)) $this->name = '_data.flatdb';
		$this->path = $path;
	}
}