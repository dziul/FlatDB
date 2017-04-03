<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\mkDir;

class flatDB
{
	/**
	 * @var string
	 */
	private  $dataFolder;
	/**
	 * @var obj
	 */
	private $mkdir;
	/**
	 * @var string
	 */
	private $strDenyAccess =  '<?php return header("HTTP/1.0 404 Not Found"); exit(); //Negar navegação | Deny navigation ?>';
	/**
	 * @var number
	 */
	private $strlenDenyAccess;



	public function __construct($dbName = 'default', $dataPath = null)
	{

		if(empty($dataPath)) $this->dataFolder = $dataPath = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/flatFileDB/' . $dbName .'/'; //Folder default
		else  $this->dataFolder = $dataPath . '/' .$dbName. '/';


		$this->mkdir = new mkDir();
		if( $this->mkdir->create($this->dataFolder) ) throw new Exception("Não foi possível crear o diretorio ({$this->dataFolder}) para a Tabala $dbName");
		else file_put_contents($this->dataFolder . 'index.php', $this->strDenyAccess);
		

		$this->strlenDenyAccess = strlen($this->strDenyAccess); //calcular o tamanho da string


	}


	/**
	 * Setar Tabela
	 * @param string $name  Nome da Tabela
	 * @return this
	 */
	public function table($name)
	{
		$this->query = new DBQuery($name);
		return $this;
	}

	/**
	 * Saber se existe a tabela
	 * @param string $name nome da tabela a ser consultada
	 * @return bool
	 */
	public function tableExists(string $name)
	{
		return is_dir($this->dataFolder. $name);
	}


	public function set(array $array)
	{
		return $this;
	}
}