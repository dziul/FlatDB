<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\directory;
use \Exception as Exception;

class flatDB
{
	/**
	 * @var string
	 */
	private  $dbFolder;

	
	/**
	 * @var obj
	 */
	private $directoryInstance;
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


	/**
	 * @var string
	 */
	private $dbBaseDir;

	/**
	 * Nome do DB padrão 
	 * @var string 
	 */
	private $dbNameDefault = '_dataDB';


	/**
	 * construtor
	 * @param string|null $dbDir  caminho do diretiro
	 * @return type
	 */
	public function __construct($dbDir = null) {

		$this->directoryInstance = new directory();

		$this->dbBaseDir = empty($dbDir) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/_data_flatDB/' : $dbDir;
		$this->strlenDenyAccess = strlen($this->strDenyAccess); //calcular o tamanho da string
	}


	/**
	 * Gerar o caminho do diretorio do banco
	 * @param string $dbPath 
	 * @param string $dbName 
	 * @return string  caminho construído
	 */
	private function setDirDataBase(string $dbName, $dbPath=null)
	{
		if (empty($dbPath)) return $this->dbBaseDir . $dbName .'/'; //Folder default
		else  return $dbPath . '/' .$dbName. '/';
	}



	public function db($dbName = null)
	{
		if (empty($dbName)) $dbName = $this->dbNameDefault;
		$this->dbFolder = $this->setDirDataBase($dbName);

		//Encadeamento | chaining
		return $this;
	}
	/**
	 * Criar o DB
	 * @param string|null $dbName Nome do DB
	 * @return bool
	 */
	public function dbCreate($dbName = null)
	{
		if (empty($dbName)) $dbName = $this->dbNameDefault;

		$dbDir = $this->setDirDataBase($dbName);
		

		if( !$this->directoryInstance->create($dbDir) ) throw new Exception(sprintf('Não foi possível crear o diretorio do DB: "%s"!', $dbDir));
		return file_put_contents($dbDir . 'index.php', $this->strDenyAccess);		
	}


	/**
	 * Deletar DB
	 * @param string $dbName 
	 * @param string|null $dbDir caminho do diretorio @example data/example/dir/
	 * @return bool|null  NULL quando $dir não for um diretorio
	 */
	public function dbDelete($dbName = null)
	{
		if (empty($dbName)) $dbName = $this->dbNameDefault;

		$dir = $this->setDirDataBase($dbName);
		if (is_dir($dir))	{
			return $this->directoryInstance->delete($dir);
		}

		return null;
	}

	/**
	 * Identificar se DB existe
	 * @param string $dbName 
	 * @param type|null $dbDir 
	 * @return type
	 */
	public function dbExists($dbName = null)
	{
		if (empty($dbName)) $dbName = $this->dbNameDefault;
		
		$dir = $this->setDirDataBase($dbName);
		return is_dir($dir);
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
		return is_dir($this->dbFolder . $name . '/');
	}

	public function allTable()
	{
		$this->dbFolder
	}

	/**
	 * Setar o caminho do arquivo
	 * @param string $path Nome do caminho
	 * @param type $id ID
	 * @return string retorna a string caminho montada
	 */
	private function setPathFileName(string $path, $id)
	{
		return $path . '/row_' . $id . '.php';
	}


	/**
	 * Adcionar conteudo
	 * @param array $array 
	 * @return array
	 */
	public function insert(array $array)
	{

		if( $this->query->hasExecute() ) throw new Exception(sprintf('consulta já foi feita'));


		$table = $this->query->table;
		$id = 0;
		$metaData = [];//init
		
		$dirFolder = $this->dbFolder . $table;

		if( !$this->directoryInstance->has($dirFolder) )
		{
			if( !$this->directoryInstance->create($dirFolder) )  throw new Exception(sprintf('Não foi possível criar o diretório: %s', $dirFolder));
			else file_put_contents($this->dbFolder . $table . '/index.php', $strDenyAccess);

			$id++;  //add +1

			$metaData = [
				'lastID' => 0,
				'length' => 0,
				'indexes' => []
			];

		}else
		{
			$metaData = $this->metaData();
			$id = $metaData['lastID']++;
		}

		$array['id'] = $id;
		

		$this->put($this->setPathFileName($table, $id));//colocar/escrever

		$metaData['lastID'] = $id;

		if (array_key_exists($table, $this->indexes)) {
			foreach ($this->indexes[$table] as $key => $value) {
				if ( $array[$index] ) 
			}
		}
	}


	/**
	 * Ler o arquivo
	 * @param string $path  caminho do arquivo
	 * @param bool $relative Setar $path como relativo
	 * @return mixed
	 */
	private function read($path, $relative = true)
	{
		if ($relative) $path = $this->dbFolder . $path;

		$contents = file_get_contents($path);
		return json_decode(substr($contents, $this->strlenDenyAccess), true);

	}

	/**
	 * Description
	 * @param string $path caminho do arquivo 
	 * @param array $array array a ser salvo
	 * @param bool $relative  setar $path como relativo
	 * @return type
	 */
	private function put($path,  array $array, $relative = true)
	{
		if ($relative) $path = $this->dbFolder . $path;

		return file_put_contents($path, $this->strDenyAccess . json_encode($array) , LOCK_EX);
	}


	private function selectFields($selector, $content)
	{

	}


	/**
	 * Gerar informacoes da tabela 
	 * @return array
	 */
	public function metaData()
	{
		$table = $this->query->table;

		if (!array_key_exists($table, $this->metaDataCache)) {

			$filePath = $this->dbFolder . $table .'/'. $this->baseNameMetaData;
			if ( !file_exists($filePath) ) throw new Exception(sprintf('Metadata da tabela "%s" não encontrado!', $table));

			$this->metaData[$table] = $this->read($filePath, false);
			
		}

		return $this->metaDataCache[$table];
	}

}// END class