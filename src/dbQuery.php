<?php
 
namespace darkziul;

/**
 * Helper class DB QUERY
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class DBQuery
 {

	/**
	 * @var number
	 */
	private $id = 0;
	/**
	 * @var string
	 */
	private $table;
	/**
	 * @var bool
	 */
	private $execute=false;
	/**
	 * @var array
	 */
	private $order = ['key'=>'id', 'order'=>'desc'];

	public function __construct($name)
	{
		$this->table = $name;
	}

	private function execute()
	{
		$this->executed = true;
	}

	private function hasExecute()
	{
		return $this->executed;
	}
}