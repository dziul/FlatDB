<?php

namespace darkziul;

use darkziul\Helpers\accessArrayElement;
use darkziul\Helpers\directory;
use \Exception as Exception;



/**
 * anotação
 * regex para identificar string JSON simples  ::   ^\[\{.+\}\]|^\{.+\}$    pega algo como [{code}] | {code}
 */

class flatDB
{
	/**
	 * @var array
	 */
	private $query = [];

	/**
	 * var responsabel pelas info do db
	 * @var array
	 */
	private $db = [];

	
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
	private $metaBaseName	 = '__meta.php';

	/**
	 * armazenar conteudo para ser executado
	 * @var array
	 * 
	 * [method]
	 * [id]
	 * [content]
	 * [meta]
	 * 
	 */
	private $execute = ['method' => null, 'id' => null, 'content' => null, 'meta' => null];
	private $executeBackup;
	/**
	 * Prefixo do cache
	 * @var string
	 */
	private $prefixCache = '0.cache.';


	/**
	 * construtor
	 * @param string|null $dirInit  Diretorio do armazenamento dos arquivos
	 * @return type
	 */
	public function __construct($dirInit = null) {

		$this->executeBackup = $this->execute;
		$this->directoryInstance = new directory();


		$nameDataDBdefault = '__data.flatdb';
		$this->db['basePath'] = is_null($dirInit) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/' . $nameDataDBdefault . '.storage/' : $dirInit;
		$this->directoryInstance->create($this->db['basePath']);//cria o dir


		$this->strlenDenyAccess = strlen($this->strDenyAccess); //calcular o tamanho da string
	}


	/**
	 * Retorna nome formatado
	 * @param type $dbName 
	 * @return type
	 */
	private function getDbName($dbName)
	{
		return $dbName  . '.' . $this->simplesHash($dbName) . '.db';
	}

	/**
	 * Gerar o caminho do diretorio do banco
	 * @param string $dbPath 
	 * @param string $dbName 
	 * @return string  caminho construído
	 */
	private function getDbPath($dbName, $dbPath=null)
	{
		// if (empty($dbName)) $dbName = $this->db['name']; //pegaNameDefault
		$dbName = $this->getDbName($dbName);//formatar nome

		if (empty($dbPath)) return $this->db['basePath'] . $dbName . '/'; //Folder default
		else  return $dbPath . '/' . $dbName . '/';
	}



	/**
	 * Identificar a DataBase para consulta
	 * @param type|null $dbName nome da DB
	 * @return this
	 */
	public function db($dbName = null)
	{
		if (!$this->dbExists($dbName)) throw new Exception(sprintf('Nao existe a tabela "%s".', $dbName));

		// $this->db['basePath']
		$this->db['instantiated'] = true; // set instantiated
		$this->db['name'] = $dbName;//set name
		$this->db['path'] =  $this->getDbPath($dbName);//set path

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

		if(strlen(''.$dbName) < 3 ) throw new Exception(sprintf('"%s" precisa ter no minimo 3 caracteres', $dbName));
		$dbPath = $this->getDbPath($dbName);
		if( !$this->directoryInstance->create($dbPath) ) throw new Exception(sprintf('Não foi possível crear o diretorio do DB: "%s"!', $dbPath));
		// return $this->write($dbPath . 'index.php', '', false);//criar um index apenas com o codigo de 404
		return true;//criar um index apenas com o codigo de 404
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
		if (!$this->db['instantiated']) throw new Exception('Nao ha nenhum DB setado!');

		$data = $this->directoryInstance->showFolders($this->db['path']);

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
		if (!$this->db['instantiated']) throw new Exception('Nao existe DataBase selecionado!');

		if (!$this->tableExists($name) && $create) $this->tableCreate($name);
		elseif (!$this->tableExists($name)) throw new Exception(sprintf('Nao existe a tabela "%s"', $name));
		

		// set query =====
		// ===============
		$this->query['name'] = $name;
		$this->query['tablePath'] = $this->getTablePath($name);
		$this->query['where'] = null;
		$this->query['order'] = ['key'=>'id', 'sort'=>'DESC'];
		$this->query['limit'] = -1;
		$this->query['offset'] = 0;
		$this->query['id'] = null;
		$this->query['select'] = null;


		return $this;
	}

	/**
	 * Criar tabela
	 * @param string $name  Nome da tabela
	 * @return bool
	 */
	public function tableCreate($name)
	{
		if(strlen(''.$name) < 3 ) throw new Exception(sprintf('"%s" precisa ter no minimo 3 caracteres', $name));
		

		$path = $this->getTablePath($name);
		if( !$this->directoryInstance->create($path) ) throw new Exception(sprintf('Não foi possível crear o diretorio da Tabela: "%s"!', $path));
		// return $this->write($path . 'index.php','',false); //criar um index apenas com o codigo de 404
		return true;
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
		$name  =  $name . '.' . $this->simplesHash($name) . '.tb';
		return $this->db['path'] . $name . '/';
	}





	/**
	 * Adcionar conteudo
	 * @param array $array 
	 * @param  mixed $nameID Caso seja necessario ADICIONAR um ID personalizado.
	 * @return this
	 */
	public function insert(array $array)
	{
		$id = 1;
		$meta = [];

		if (!$this->hasMeta()) {
			$meta['length'] = $id;
		} else {
			$meta = $this->getMeta();
			$meta['length'] = $meta['indexes']++;
			$id = end($meta['indexes']);
		}

		$array['id'] = $id;
		$meta['lastID'] = $id;


		// execute data ==========
		$this->execute['method'] = 'insert';
		$this->execute['data'] 	 = $array;
		$this->execute['meta']   = $meta;
		$this->execute['id']     = $id;



	}
	// public function insert(array $array, $nameID=null)
	// {
	// 	if (!empty($nameID) && strlen(''.$nameID) < 3 ) throw new Exception(sprintf('"%s" precisa ter no minimo 3 caracteres', $nameID));
	// 	if (!isset($this->query->table)) throw new Exception('Nao ha tabela para consulta');


	// 	$id = 1;
	// 	$meta = [];//
	// 	if (!$this->hasMeta()) {
	// 		$meta['last_id'] = !empty($nameID) ? $nameID : $id;
	// 		$meta['length'] = $id;
	// 	} else {


	// 		$meta = $this->getMeta();
	// 		$indexesFlip = array_flip($meta['indexes']);
	// 		$meta['length'] = count($meta['indexes'])+1;
	// 		$id = end($indexesFlip)+1;
	// 	}


	// 	// caso $nameID ja tenha sido setado em metaDATA executa o outset "precoce"
	// 	if ($this->hasMeta() && !empty($nameID) && in_array($nameID, $meta['indexes'])) return $this;

	// 	$meta['indexes'][$id] = !empty($nameID) ? $nameID : $id;
	// 	$meta['indexes_flip'] = array_flip($meta['indexes']); // adicionar o indexes inversos
	// 	$meta['last_id'] = end($meta['indexes']);
	// 	$array['id'] = $meta['indexes'][$id];

		
	// 	$this->execute['meta'] = $meta; //set execute ==
	// 	$this->execute['id'] = !empty($nameID) ? $nameID : $id;
	// 	$this->execute['content'] = $array;
	// 	$this->execute['method'] = 'insert';


	// 	//encadeamento
	// 	return $this;
	// }



	public function add(array $array)
	{

		
		// $this->execute['meta'] = null; //set execute ==
		// $this->execute['id'] = $id;
		$this->execute['content'] = $array;
		$this->execute['method'] = 'add';

		return $this;
	}

	public function remove($id=null) 
	{

		$this->execute['meta'] = $this->getMeta(); //set execute ==
		$this->execute['content'] = null;

		if (is_null($id)) {

			$this->execute['id'] = null;
			$this->execute['method'] = 'removeByWhere';

		} else {

			$this->execute['id'] = is_array($id) ? $id: [$id];
			$this->execute['method'] = 'removeByID';
		}

		return $this;
	}


	/**
	 * Executar o métodos
	 * @return bool|null NULL quando nao foi executado nenhum metodo
	 */
	public function execute()
	{
		$executed = false;
		if ('insert' === $this->execute['method']) {
			$this->writeMeta($this->execute['meta']);
			$this->write($this->getPathFile($this->execute['id']), $this->execute['content'], false);

			$executed = true; // executado
		}

		elseif ('add' === $this->execute['method']) {

			
			// $this->execute['id'] = $id;
			$contentAdd = $this->execute['content']; // conteudo para adicionar
			$whereCompare = $this->query->where;
			var_dump($contentAdd, $whereCompare);


			$executed = true; // executado
		}

		elseif ('removeByID' === $this->execute['method']) {
		

			foreach ($this->execute['id'] as $id) {

				if (!$this->fileExists($id)) throw new Exception(sprintf('Nao foi encontrado arquivo ID::%s', $id));
				

				if (in_array($id, $this->execute['meta']['indexes'])) {

					$key = $this->execute['meta']['indexes_flip'][$id];
					unset($this->execute['meta']['indexes'][$key]);
					unlink($this->getPathFile($id));
					$this->execute['meta']['length']--; // igual a $e -= $e
				} else {
					throw new Exception(sprintf('ID::%s nao foi encontrado no metaData::indexes', $id));
				}


			}

			// $this->execute['meta']['length'] = count($this->execute['meta']['indexes']);
			$this->execute['meta']['last_id'] = end($this->execute['meta']['indexes']); // pegar ultima chave
			$this->writeMeta($this->execute['meta']);//salvar novos dados
			
			$executed = true;//executado metodo, finalizar
		}


		if ($executed) {
			$this->execute = $this->executeBackup;//reset var
			$this->removeCache();// remove todo os cache 
			return true;//
		}
		else return null;

	}


	private function findAll()
	{
		if (!isset($this->query->table)) throw new Exception('Nao ha tabela para consulta');
		
		// $table  = $this->query->table;
		$tablePath  = $this->query->tablePath;
        $order  = $this->query->order;
        $limit  = $this->query->limit;
        $offset = $this->query->offset;
        $where  = $this->query->where;
        $select = $this->query->select;


        $hash=  sha1(json_encode($order+$where+$select) . $limit . $offset);
        $cacheName = $this->prefixCache . $hash;
        $cachePath = $this->getPathFile($cacheName);


        if ($this->fileExists($cacheName)) return $this->read($cachePath, false);

        if (!$this->hasMeta()) throw new Exception('Nao ha arquivo metaData para consulta');

        $meta = $this->getMeta();

        if (empty($meta['indexes'])) return null;//

        if ('DESC' == $order['type']) krsort($meta['indexes']);
        else ksort($meta['indexes']);


	}




	private function parser($content, $compare=null, $method=null)
	{

		
	}


	/**
	 * Gerar o caminho do arquivo
	 * @param type $id ID
	 * @return string retorna a string caminho montada
	 */
	private function getPathFile($id)
	{
		return $this->query->tablePath . $this->simplesHash($id)  . 'k' . $id . '.php';
	}

	private function fileExists($nameFile)
	{
		return file_exists($this->getPathFile($nameFile));
	}


	/**
	 * Ordernar
	 * @param string $key
	 * @param string $type DESC | ASC
	 * @return this
	 */
 	public function order($key=null, $type='DESC')
	{

		$this->query->order['key'] = empty($key) ? 'id' : $key;
		$this->query->order['type'] = empty($type) ? 'DESC' : strtoupper($type);

		return $this;
	}
	/**
	 * setar limite para consulta
	 * @param number $n Posição limite para consulta
	 * @return this
	 */
	public function limit($n)
	{
		$this->query->limit = $n;
		return $this;
	}
	/**
	 * Começar a partir de
	 * @param number $n posição onde tem que começar a leitura
	 * @return this
	 */
	public function offset($n)
	{
		$this->query->offset = $n;
		return $this;
	}
	/**
	 * configurar Condições  / Filtro
	 * @param array $array ARRAY da condições
	 * @return this
	 */
	public function where(array $array)
	{
		$this->query->where = $array;
		return $this;
	}


	/**
	 * Gerar um simples hash
	 * @param string $string 
	 * @return string
	 */
	private function simplesHash($string)
	{
		// return hash('crc32', $string); //lower
		// return strtolower(str_replace('=', '', base64_encode($string))); //lower
		// return is_numeric($string) ? $string/.5 . '.' . $string+$string; //fast
		// return  str_pad($string, 24, 'a0b1c2d3e4f5g6900');//alternative
		if (is_numeric($string)) return '' . ($string+1)/3.14159265359; //number PI
		else return $string[2] . $string[0] . $string[1] . $string[0];
	}

	private function removeCache()
	{
		$pattern = $this->prefixCache . '*';
		foreach (glob($this->query->tablePath . $pattern) as $file) {
            unlink($file);
        }

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
		$contents = substr($contents, $this->strlenDenyAccess);

		return json_decode($contents, true);
	}

	/**
	 * Description
	 * @param string $PathOrFile caminho do arquivo ou nome do arquivo
	 * @param array $array array a ser salvo
	 * @param bool $relative  setar $path como relativo
	 * @return type
	 */
	private function write($PathOrFile,  $content, $relative = true)
	{
		if ($relative) $PathOrFile = $this->query->tablePath . $PathOrFile;

		if (is_array($content)) $content = json_encode($content);
		// if (is_array($content)) $content = json_encode($content, JSON_FORCE_OBJECT);

		return is_numeric(file_put_contents($PathOrFile, $this->strDenyAccess . $content  , LOCK_EX));
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
		/**
		 * [last_id]
		 * [legth]
		 * [indexes]
		 */
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