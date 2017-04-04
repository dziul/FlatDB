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
	 * @var array
	 */
	private $metaCache = [];

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


	public function tableShow($)
	{

	}



	/**
	 * Setar Tabela
	 * @param string $name  Nome da Tabela
	 * @param  bool $create TRUE: cria a tabela caso não exista | Create case does not exist
	 * @return this
	 */
	public function table($name, $create=false)
	{
		if (!$this->hasDB) throw new Exception('Nao existe DataBase selecionado!');

		// $this->who = 'table';
		if(!$this->tableExists($name) && $create) $this->tableCreate($name);
		elseif (!$this->tableExists($name)) throw new Exception(sprintf('Nao existe a tabela %s', $name));
		

		$this->query = new flatDBtableQuery($name, $this->getTablePath($name));

		return $this;
	}

	/**
	 * Criar tabela
	 * @param string $name  Nome da tabela
	 * @return bool
	 */
	public function tableCreate($name)
	{

		$path = $this->getTablePath($name);
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
	 * Pegar todos os nomes das tabelas referente a DB
	 * @param bool $outJSON TRUE: saida em JSON 
	 * @return array|string  STRING: strJSON
	 */
	private function showTables($outJSON=false)
	{
		$result = $this->directoryInstance->showFolders($this->dbFolder); //return array
		if ($outJSON) return json_encode($result);
		return $result;
	}

	/**
	 * Gerar o caminho para tabela
	 * @param string $name  nome da tabela
	 * @return string CAminho completo da tabela
	 */
	private function getTablePath(string $name)
	{
		return $this->dbFolder . $nome .'/';
	}




	/**
	 * Gerar o caminho do arquivo
	 * @param type $id ID
	 * @return string retorna a string caminho montada
	 */
	private function getPathFile($id)
	{
		
		return query->tablePath . 'row_' . $id . '.php';
	}


	/**
	 * Adcionar conteudo
	 * @param array $array 
	 * @return array
	 */
	public function insert(array $array)
	{

		
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
	private function write($path,  array $array, $relative = true)
	{
		if ($relative) $path = $this->dbFolder . $path;

		return file_put_contents($path, $this->strDenyAccess . json_encode($array, JSON_FORCE_OBJECT) , LOCK_EX);
	}


	private function selectedKey()
	{
		
	}


	/**
	 * Gerar informacoes da tabela 
	 * @return array
	 */
	public function metaData()
	{
		if (!isset($this->query->table)) throw new Exception('Não existe tabela para consultar');
		

		$table = $this->query->table;

		if (!array_key_exists($table, $this->metaCache)) {

			$filePath = $this->dbFolder . $table .'/meta.php';
			if ( !file_exists($filePath) ) throw new Exception(sprintf('Metadata da tabela "%s" não encontrado!', $table));

			$this->metaCache[$table] = $this->read($filePath, false);
			
		}

		return $this->metaCache[$table];
	}

}// END class