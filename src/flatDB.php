<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\directory;
use \Exception as Exception;


class flatDB
{
	/**
	 * @var obj
	 */
	private $query;

	/**
	 * var responsabel pelas info do db
	 * @var obj
	 */
	private $db;

	
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
	 * @var string
	 */
	private $metaBaseName	 = 'meta.php';

	/**
	 * armazenar conteudo para ser executado
	 * @var array
	 * 
	 * [action]
	 * [id]
	 * [meta]
	 * 
	 */
	private $execute = [];


	/**
	 * construtor
	 * @param string|null $dirInit  caminho do diretiro
	 * @return type
	 */
	public function __construct($dirInit = null) {

		$this->directoryInstance = new directory();

		$this->db = new dbQuery();//instanciar class que guarda info do DB	



		$this->db->basePath = is_null($dirInit) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/_data_flatDB/' : $dirInit;
		$this->directoryInstance->create($this->db->basePath);//cria o dir


		$this->strlenDenyAccess = strlen($this->strDenyAccess); //calcular o tamanho da string
	}


	/**
	 * Retorna nome formatado
	 * @param type $dbName 
	 * @return type
	 */
	private function getDbName($dbName)
	{
		return 'db_'.$dbName;
	}

	/**
	 * Gerar o caminho do diretorio do banco
	 * @param string $dbPath 
	 * @param string $dbName 
	 * @return string  caminho construído
	 */
	private function getDbPath($dbName, $dbPath=null)
	{
		if (empty($dbName)) $dbName = $this->db->name; //pegaNameDefault

		$dbName = $this->getDbName($dbName);//formatar nome

		if (empty($dbPath)) return $this->db->basePath . $dbName .'/'; //Folder default
		else  return $dbPath . '/' .$dbName. '/';
	}



	public function db($dbName = null)
	{
		if (!$this->dbExists($dbName)) throw new Exception(sprintf('Nao existe a tabela "%s".', $dbName));

		$this->db->instantiated = true; // set instantiated
		$this->db->name = $dbName;//set name
		$this->db->path =  $this->getDbPath($dbName);//set path
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
		return is_numeric( file_put_contents($dbPath . 'index.php', $this->strDenyAccess) );		
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
			return $this->directoryInstance->delete($this->getDbPath($dbName));
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
	 * Mostrar todas as tabelas do DB atual
	 * @param bool $jsonOUT TRUE: saida sera em string JSON 
	 * @return string|array  Default eh Array
	 */
	public function tableShow($jsonOUT = false)
	{
		if (!$this->db->instantiated) throw new Exception('Nao ha nenhum DB setado!');

		$data = $this->directoryInstance->showFolders($this->db->path);

		if ($jsonOUT) return json_encode($data);//string json
		return $data;//array
	}



	/**
	 * Setar Tabela
	 * @param string $name  Nome da Tabela
	 * @param  bool $create TRUE: cria a tabela caso não exista | Create case does not exist
	 * @return this
	 */
	public function table($name, $create=false)
	{
		if (!$this->db->instantiated) throw new Exception('Nao existe DataBase selecionado!');

		// $this->who = 'table';
		if(!$this->tableExists($name) && $create) $this->tableCreate($name);
		elseif (!$this->tableExists($name)) throw new Exception(sprintf('Nao existe a tabela "%s"', $name));
		

		$this->query = new tableQuery($name, $this->getTablePath($name));

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
		return is_numeric(file_put_contents($path . 'index.php', $this->strDenyAccess));
	}

	/**
	 * Deletar tabela
	 * @param type $name 
	 * @return null|bool NULL: caso nao existe tabela
	 */
	public function tableDelete($name)
	{

		if ($this->tableExists($name)) {
			return $this->directoryInstance->delete($this->getTablePath($name));
		}

		return null;
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
	 * Gerar o caminho para tabela
	 * @param string $name  nome da tabela
	 * @return string CAminho completo da tabela
	 */
	private function getTablePath($name)
	{
		$prefix  =  'tb_';
		return $this->db->path . $prefix . $name .'/';
	}





	/**
	 * Adcionar conteudo
	 * @param array $array 
	 * @return array
	 */
	public function insert(array $array)
	{
		if (!isset($this->query->table)) throw new Exception('Nao ha tabela para consulta');


		$id = 1;
		$meta = [];//
		if (!$this->hasMeta()) {

			$meta['last_id'] = $id;
			$meta['length'] = $id;
		} else {
			$meta = $this->getMeta();
			$meta['last_id']++;
			$meta['length']++;

			$id = $meta['last_id'];
		}

		$meta['indexes'][$id] = $id;
		$array['id'] = $id;


		$this->writeMeta($meta);
		$this->write($this->getPathFile($id), $array, false);

		//encadeamento
		return $this->meta();
	}


	public function execute()
	{

	}

	/**
	 * Gerar o caminho do arquivo
	 * @param type $id ID
	 * @return string retorna a string caminho montada
	 */
	private function getPathFile($id)
	{
		
		return $this->query->tablePath . '/_input-' . $id . '.php';
	}


	/**
	 * Ler o arquivo
	 * @param string $PathOrFile  caminho ou nome do arquivo
	 * @param bool $relative Setar $path como relativo
	 * @return mixed
	 */
	private function read($PathOrFile, $relative = true)
	{
		if ($relative) $PathOrFile = $this->query->tablePath . $PathOrFile;

		$contents = file_get_contents($PathOrFile);
		return json_decode(substr($contents, $this->strlenDenyAccess), true);
	}

	/**
	 * Description
	 * @param string $PathOrFile caminho do arquivo ou nome do arquivo
	 * @param array $array array a ser salvo
	 * @param bool $relative  setar $path como relativo
	 * @return type
	 */
	private function write($PathOrFile,  array $array, $relative = true)
	{
		if ($relative) $PathOrFile = $this->query->tablePath . $PathOrFile;

		return file_put_contents($PathOrFile, $this->strDenyAccess . json_encode($array, JSON_FORCE_OBJECT) , LOCK_EX);
	}


	private function selectedKey()
	{
		
	}



	/**
	 * Coletar infor do metaData
	 * @param bool $outJSON TRUE: retorna o string JSON
	 * @return string|array
	 */
	public function meta($outJSON=false)
	{
		return ($outJSON) ? json_encode($this->getMeta()) : $this->getMeta();
	}
	/**
	 * Gerar informacoes da tabela 
	 * @return array
	 */
	private function getMeta()
	{
		if (!isset($this->query->table)) throw new Exception('Não existe tabela para consultar');
		if (!$this->hasMeta()) return false;
		return $this->read($this->metaBaseName);
	}

	/**
	 * Saber se tem metaData
	 * @return bool
	 */
	private function hasMeta()
	{
		if (!isset($this->query->table)) throw new Exception('Não existe tabela para consultar');
		return file_exists($this->query->tablePath . $this->metaBaseName);
	}

	/**
	 * Salvar o conteudo metaData
	 * @param type $array array do conteudo a ser salvo
	 * @return bool
	 */
	private function writeMeta($array)
	{
		if (!isset($this->query->table)) throw new Exception('Não existe tabela para consultar');
		return $this->write($this->metaBaseName, $array);
	}


}// END class