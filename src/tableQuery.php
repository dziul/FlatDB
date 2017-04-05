<?php
 
namespace darkziul;

/**
 * table Query
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class tableQuery
 {

	
	/** nome da tabela
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
	 * limite
	 * @var number
	 */
	public $limit = -1;
	/**
     * @var array
     */
    public $where = null;
	/**
	 * @var bool
	 */
	private $executed = false;
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