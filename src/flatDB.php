<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\mkDir;
use \Exception as Exception;

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
	/**
	 * @var array
	 */
	private $metaDataCache = [];
	/**
	 * @var string
	 */
	private $baseNameMetaData = 'metaData.php';
	/**
	 * @var array
	 */
	private $indexes =[];



	public function __construct($dbName = 'default', $dataPath = null)
	{

		if(empty($dataPath)) $this->dataFolder = $dataPath = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/flatFileDB/' . $dbName .'/'; //Folder default
		else  $this->dataFolder = $dataPath . '/' .$dbName. '/';


		$this->mkdir = new mkDir();

		if( !$this->mkdir->create($this->dataFolder) ) throw new Exception(sprintf('Não foi possível crear o diretorio do DB "%s"!', $this->dataFolder));
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


	public function add(array $array)
	{

		if( $this->query->hasExecute() ) throw new Exception(sprintf('consulta já foi feita'));


		$table = $this->query->table;
		$id = 0;
		$metaData = [];//init
		
		$dirFolder = $this->dataFolder . $table;

		if( !$this->mkdir->isDir($dirFolder) )
		{
			if( !$this->mkdir->create($dirFolder) )  throw new Exception(sprintf('Não foi possível criar o diretório: %s', $dirFolder));
			else file_put_contents($this->dataFolder . $table . '/index.php', $strDenyAccess);

			$id++;  //add +1

			$metaData = [
				'lastID' => $id,
				'length' => $id,
				'indexes' => []
			];

		}else
		{
			$metaData = $this->metaData();
			$id = $metaData['lasID']++;

		}

		$array['id'] = $id;
		
	}


	/**
	 * Ler o arquivo
	 * @param string $path  caminho do arquivo
	 * @param bool $relative saber se o caminho setado em $path é relativo
	 * @return mixed
	 */
	private function read($path, $relative = true)
	{
		if ($relative) $path = $this->dataFolder . $path;

		$contents = file_get_contents($path);
		return unserialize(substr($contents, $this->strlenDenyAccess));

	}



	public function metaData()
	{
		$table = $this->query->table;

		if (!array_key_exists($table, $this->metaDataCache)) {

			$filePath = $this->dataFolder . $table .'/'. $this->baseNameMetaData;
			if ( !file_exists($filePath) ) throw new Exception(sprintf('Metadata da tabela "%s" não encontrado!', $table));

			$this->metaData[$table] = $this->read($path, false);
			
		}


		return $this->metaDataCache[$table];
	}
}