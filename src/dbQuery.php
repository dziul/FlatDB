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
	public $id = 0;
	/**
	 * @var string
	 */
	public $table;
	/**
	 * @var bool
	 */
	private $execute=false;
	/**
	 * @var array
	 */
	public $order = ['key'=>'id', 'order'=>'desc'];


	

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