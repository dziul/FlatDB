<?php
 
namespace darkziul;

/**
 * Helper class DB QUERY
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class flatDBtableQuery
 {

	
	/**
	 * @var string
	 */
	public $table;
	/**
	 * Caminho da tabela
	 * @var string
	 */
	public $tablePath;
	/**
	 * @var number
	 */
	public $id = 0;

	/**
	 * @var string
	 */
	public $select=null;

	/**
	 * @var number
	 */
	public $offset = 0;
	/**
     * @var array
     */
    public $where = null;
	/**
	 * @var bool
	 */
	private $execute=false;
	/**
	 * @var array
	 */
	public $order = ['key'=>'id', 'order'=>'desc'];


	

	public function __construct($name, $path='')
	{
		$this->tablePath = $path;
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