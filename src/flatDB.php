<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\directory;
use \Exception as Exception;

// $D = new($dirDB);
// $D->db('dbFF') //

class flatDB
{
	/**
	 * @var obj
	 */
	private $query;

	/**
	 * Saber se o DB foi ativo /oou em uso
	 * @var bool
	 */
	private $hasDB = false;

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
	private $dbBasePath;

	/**
	 * Nome do DB padrão 
	 * @var string 
	 */
	private $dbNameDefault = '_dataDB';


	/**
	 * construtor
	 * @param string|null $dbPath  caminho do diretiro
	 * @return type
	 */
	public function __construct($dbPath = null) {

		$this->directoryInstance = new directory();

		$this->dbBasePath = empty($dbPath) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/_data_flatDB/' : $dbPath;
		$this->strlenDenyAccess = strlen($this->strDenyAccess); //calcular o tamanho da string
	}


	/**
	 * Gerar o caminho do diretorio do banco
	 * @param string $dbPath 
	 * @param string $dbName 
	 * @return string  caminho construído
	 */
	private function getDbPath($dbName, $dbPath=null)
	{
		if (empty($dbName)) $dbName = $this->dbNameDefault;
		if (empty($dbPath)) return $this->dbBasePath . $dbName .'/'; //Folder default
		else  return $dbPath . '/' .$dbName. '/';
	}



	public function db($dbName = null)
	{
		if (!$this->dbExists($dbName)) throw new Exception(sprintf('Nao existe a tabela "%s".', $dbName));
		$this->hasDB = true;//saber se o DB esta sendo usado	
		// $this->who = 'db'; //indentificar para utilizar no encadeamento
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
		$dbPath = $this->getDbPath($dbName);
		if( !$this->directoryInstance->create($dbPath) ) throw new Exception(sprintf('Não foi possível crear o diretorio do DB: "%s"!', $dbPath));
		return file_put_contents($dbPath . 'index.php', $this->strDenyAccess);		
	}


	/**
	 * Deletar DB
	 * @param string $dbName 
	 * @param string|null $dbPath caminho do diretorio @example data/example/dir/
	 * @return bool|null  NULL quando $dir não for um diretorio
	 */
	public function dbDelete($dbName = null)
	{
		
		if ($this->dbExists($dbName))	{
			$dir = $this->getDbPath($dbName);
			return $this->directoryInstance->delete($this->dbExists($dir));
		}

		return null;
	}



	/**
	 * Identificar se DB existe
	 * @param string $dbName 
	 * @param type|null $dbPath 
	 * @return type
	 */
	public function dbExists($dbName = null)
	{
		$dir = $this->getDbPath($dbName);
		return is_dir($dir);
	}



	/**
	 * Setar Tabela
	 * @param string $name  Nome da Tabela
	 * @return this
	 */
	public function table($name)
	{
		if (!$this->hasDB) throw new Exception('Nao existe DataBase selecionado!');

		// $this->who = 'table';
		if (!$this->tableExists($name)) throw new Exception(sprintf('Nao existe a tabela %s', $name));
		

		$this->query = new flatDBtableQuery($name);
		$this->query->path = $this->getTablePath($name);

		return $this;
	}

	public function tableCreate($name)
	{

		$path = $getTablePath($name);
		if( !$this->directoryInstance->create($path) ) throw new Exception(sprintf('Não foi possível crear o diretorio da Tabela: "%s"!', $path));
		return file_put_contents($dbPath . 'index.php', $this->strDenyAccess);
	}


	/**
	 * Saber se existe a tabela
	 * @param string $name nome da tabela a ser consultada
	 * @return bool
	 */
	public function tableExists($name)
	{
		return is_dir($this->getTablePath($name));
	}

	/**
	 * Gerar o diretorio Tabela
	 * @param string $name  nome da tabela
	 * @return string CAminho completo da tabela
	 */
	private function getTablePath(string $name)
	{
		return $this->dbFolder . $nome .'/';
	}


	/**
	 * Pegar todos os nomes das tabelas referente a DB
	 * @param bool $outJSON TRUE: saida sera em JSON 
	 * @return array|string
	 */
	public function allNameTable($outJSON=false)
	{
		$result = $this->directoryInstance->allNameFolders($this->dbFolder); //return array
		if ($outJSON) return json_encode($result);
		return $result;
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