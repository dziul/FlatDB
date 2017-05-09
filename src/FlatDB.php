<?php

namespace Darkziul;

use Darkziul\DotNotationArrayAccess as DNAA;
use Darkziul\Directory as directory;
use \Exception as Exception;



/**
 * anotação
 * regex para identificar string JSON simples  ::   ^\[\{.+\}\]|^\{.+\}$    pega algo como [{code}] | {code}
 */

class FlatDB
{
	/**
	 * @var array
	 */
	private $query = [
		'table' => null,
		'where' => null,
		'order' => ['by' => 'id', 'type' => 'desc'],
		'limit' => 0,
		'offset' => 0,
		'select' => null
	];

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
	protected $strDenyAccess =  '<?php return header("HTTP/1.0 404 Not Found"); exit(); //Negar navegação | Deny navigation ?>';
	/**
	 * @var number
	 */
	protected $strlenDenyAccess;
	/**
	 * @var string
	 */
	private $metaBaseName	 = '.metadata.php';

	/**
	 * armazenar conteudo para ser executado
	 * @var array
	 */
	private $prepare = [
		'method' => null,
		'id' => 0,
		'data' => null,
		'meta' => null
	];
	/**
	 * Prefixo do cache
	 * @var string
	 */
	private $cacheNameDir = '.cache';


	private static $accessArray;

	/**
	 * construtor
	 * @param string|null $dirInit  Diretorio do armazenamento dos arquivos
	 * @return type
	 */
	public function __construct($dirInit = null) {

		$this->directoryInstance = new directory();

		$nameDataDBdefault = '_data.flatdb';
		$this->db['basePath'] = is_null($dirInit) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/' . $nameDataDBdefault . '/' : $dirInit;
		$this->directoryInstance->create($this->db['basePath']);//cria o dir


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
		$dbName = $dbName . '.db';//formatar nome
		if (empty($dbPath)) return $this->db['basePath'] . $dbName . '/'; //Folder default
		else  return $dbPath . '/' . $dbName . '/';
	}



	/**
	 * Identificar a DataBase para consulta
	 * @param type|null $dbName nome da DB
	 * @param  bool $dbCreate TRUE criar diretorio DB caso nao exista
	 * @return this
	 */
	public function db($dbName = null, $dbCreate=false)
	{
		if ($dbCreate) $this->dbCreate($dbName);
		elseif (!$this->dbExists($dbName)) throw new Exception(sprintf('Nao existe a tabela "%s".', $dbName));

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
		if (!$this->db['instantiated']) throw new Exception('Nao ha database para consulta!');

		if (!$this->tableExists($name) && $create) $this->tableCreate($name);
		elseif (!$this->tableExists($name)) throw new Exception(sprintf('Nao existe a tabela "%s"', $name));

		// set query =====
		// ===============
		$this->query['table'] = $name;
		$this->query['tablePath'] = $this->getTablePath($name);
		$this->query['where'] = [];
		$this->query['order'] = ['by'=>'id', 'type'=>'desc'];
		$this->query['limit'] = 0;
		$this->query['offset'] = 0;
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
		$this->query['tablePath'] = $path;// antecipar set

		if( !$this->directoryInstance->create($path) ) throw new Exception(sprintf('Não foi possível crear o diretorio da Tabela: "%s"!', $path));
		$this->directoryInstance->create($this->getPathCache());//criar a pasta cache
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
		// $name  =  $name . '.' . $this->simplesHash($name) . '.tb';
		return $this->db['path'] . $name . '.tb/';
	}





	/**
	 * Adcionar Conteúdo
	 * @param array $array Array sera salvo. cada elemento (chave e valor) seram passados para 'caixa baixa' (lower) e retirado qualquer espaço em branco no inicio e final
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
			$meta['length'] = count($meta['indexes'])+1;
			$id = end($meta['indexes'])+1;
		}

		

		$array = $this->lowerCase($array);
		$array['id'] = $id;
		$meta['lastId'] = $id;
		$meta['indexes'][$id] = $id;


		// execute data ==========
		$this->prepareSet('insert', DNAA::create($array), $id, $meta);

		return $this;//encadeamento ====
	}

	/**
	 * Atualizar conteudo
	 * @param type $key Chave ou (array)chave::valor
	 * @param type|null $value Valor, caso $key for array, sera desconsiderado essa arg
	 * @return this
	 */
	public function update($key, $value=null)
	{
		if (!is_array($key)) $this->query['select'] = [$key => $value];
		else $this->query['select'] = $key;

		$this->query['select'] = $this->lowerCase($this->query['select']);// all lowerCase

		$this->prepareSet('update');
		return $this; //encadeamento
	}

	/**
	 * Colocar/Adicionar  Chave e valor ou apenas chave
	 * @param mixed $newContent Caso seja conjunto de chave&valor, se ja exitir a chave o valor sera mesclado com o existente.
	 * @param  bool $valueMerge TRUE Mesclar valor caso exista um valor na chave (ira converte em array, caso seja uma string o valor existente)
	 * @return this
	 */
	public function put($newContent, $valueMerge=false)
	{
		$newContent = $this->lowerCase($newContent);
		$this->prepareSet('put', $newContent, null, null, $valueMerge); //nesse caso sera salvo o conteudo a ser adicionado em 'data'
		return $this;
	}
	public function remove($keys)
	{
		
	}



	/**
	 * Deletar arquivo(item)
	 * @param number|null $id Id a ser deletado ou nao setar para ser usado no metodo WHERE 
	 * @return this
	 */
	public function delete($id=null) 
	{
		if (is_null($id)) {
			$this->prepareSet('deleteByWhere');
		}
		else $this->prepareSet('deleteById', null, (array)$id);	
		return $this; //encadeamento
	}

	/**
	 * Selecionar
	 * @param type|null $key selecionar "coluna"
	 * @return this
	 */
	public function select($key = null)
	{
		$this->query['select'] = (array)$key;

		$this->prepareSet('select');

		return $this; //encadeamento
	}



	/**
	 * Ordernar
	 * @param string $by Chave/Valor para comparacao
	 * @param string $type apenas DESC ou ASC
	 * @return this
	 */
 	public function order($type='desc', $by='id')
	{

		$this->query['order']['by'] = $by;
		$this->query['order']['type'] = $type;
		return $this;
	}


	/**
	 * Limite para consulta
	 * @param number $n Posição limite para consulta
	 * @return this
	 */
	public function limit($n)
	{
		$this->query['limit']  = $n;
		return $this;
	}
	/**
	 * Começar a partir de
	 * @param number $n posição onde tem que começar a leitura
	 * @return this
	 */
	public function offset($n)
	{
		$this->query['offset'] = $n;
		return $this;
	}
	
	/**
	 * configurar Condições  / Filtro
	 * @param array $array ARRAY da condições
	 * @return this
	 */
	public function where(array $array)
	{
		$this->query['where'] = $array;
		return $this;
	}

	/**
	 * Description
	 * @return number Quantidade
	 */
	public function length()
	{
		return $this->getMeta('length');
	}


	/**
	 * Obter todos os itens
	 * @return array
	 */
	public function all()
	{
		return $this->parseAndSelect();
	}


	/**
	 * Executar o métodos
	 * @return bool|null NULL quando nao foi executado nenhum metodo
	 */
	public function execute()
	{
		if (!$this->query['table']) throw new Exception('Nao ha tabela para consulta');
		
		$executed = false;
		if ('insert' === $this->prepareGet('method')) {
			$this->writeMeta($this->prepareGet('meta'));
			$data = $this->prepareGet('data');
			$this->write($this->getPathFile($this->prepareGet('id')), $data, false);

			$this->prepareReset(); // reseta array prepare
			$this->removeCacheAll(); //remover todos os caches
			return $data;
		}
		elseif ('update' === $this->prepareGet('method'))
		{
			return $this->parseAndUpdate();
		}
		elseif ('put' === $this->prepareGet('method')) {
			$data = $this->parseAndPut($this->prepareGet('data'), $this->prepareGet('other'));
			$this->prepareReset(); // reseta array prepare
			$this->removeCacheAll(); //remover todos os caches
			return $data;

		}
		elseif ('select' === $this->prepareGet('method')) {
			$this->prepareReset(); // reseta array prepare
			return $this->parseAndSelect();
		}
		elseif ('deleteById' === $this->prepareGet('method')) {
			$ids = $this->prepareGet('id');
			$this->prepareReset(); // reseta array prepare
			return $this->parseAndDelete($ids);
		}
		elseif ('deleteByWhere' === $this->prepareGet('method')) {
			$this->prepareReset(); // reseta array prepare
			return $this->parseAndDeleteWithCondition();
		}
		else return null;

	}



	/**
	 *Analizar  e Ordenar
	 * @param array &$array 
	 * @param array|null $order NULL utiliza a variavel ja setada 
	 * @return array Retorna array ordenada
	 */
	private function parseAndOrder(array &$array, array $order=null)
	{

		if(is_null($order)) $order = $this->query['order'];
		// ordernar padrao ===
        if ('desc' == $order['type'] && 'id' == $order['by']) krsort($array); //decrescente
        elseif ('asc' == $order['type'] && 'id' == $order['by']) ksort($array); // crescente

        elseif ('asc' == $order['type']) {
        	$func = function($a, $b) use($order) {
        		$a = DNAA::get($a, $order['by']);
        		$b = DNAA::get($b, $order['by']);
        		if ($a == $b) return 0;
        		return ($a < $b) ? -1 : 1; //asc
        		// return ($a > $b) ? -1 : 1; //desc
        	};
        	uasort($array, $func);
        }
        elseif ('desc' == $order['type']) {
        	$func = function($a, $b) use($order) {
        		$a = DNAA::get($a, $order['by']);
        		$b = DNAA::get($b, $order['by']);

        		if ($a == $b) return 0;
        		// return ($a < $b) ? -1 : 1; //asc
        		return ($a > $b) ? -1 : 1; //desc
        	};
        	uasort($array, $func);
        }

        return $array;
	}

	/**
	 * Analizar e Selecionar
	 * @return array
	 */
	private function parseAndSelect()
	{
		if (!isset($this->query['table'])) throw new Exception('Nao ha tabela para consulta');
		
        $order  	= $this->query['order'];
        $where  	= $this->query['where'];
        $select 	= $this->query['select'];
        $offset 	= $this->query['offset'];
    	$limit  	= $this->query['limit'];



    	// var_dump($this->like($where));

    	$result = [];//init result
        $cacheName =  sha1(json_encode((array)$where + (array)$select + $order));
        // BEGIN caso exista cache ===
        if ($hasCache = $this->hasCache($cacheName)) {
        	$result = $this->readCache($cacheName);
        	// return $result;
        }
        //END cao exista cache =======
        if (!$this->hasMeta()) throw new Exception('Nao ha arquivo metadata para consulta');

        $indexes = $this->getMeta('indexes');


        if (empty($indexes)) return null;//
        // $data = [];// init data
        //condicao where ====
        if ($emptyWhere = empty($where) && !$hasCache) {
        	foreach ($indexes as $id) {
        		$data = $this->read($this->getBaseNameFile($id));
        		$result[$id] = empty($select) ? $data : DNAA::get($data, $select, true);
        	}

        } elseif (!$emptyWhere && !$hasCache) {
        	if (isset($where['id'])) {
        		if (!$this->inArrayRecursive($where['id'], $indexes)) return null;
        		$indexes = (array)$where['id'];
        		unset($where['id']);	
        	}
        	foreach ($indexes as $id) {
    			$data = $this->read($this->getBaseNameFile($id));
    			// if (DNAA::exists($data, $where)) $result[$id] = empty($select) ? $data : DNAA::get($data, $select, true);
    			if (DNAA::exists($data, $where)) $result[] = empty($select) ? $data : DNAA::get($data, $select, true);
    		}
        }

        
        if (!$hasCache) {
        	$this->parseAndOrder($result, $order);//ordenar
        	$this->writeCache($cacheName, $result);//salvar cache
        }

    	if ($limit > 0)  $result = array_slice($result, $offset, $limit, true);
    	elseif ($offset > 0) $result = array_slice($result, $offset, true);
        return $result;
	}

	/**
	 * Analizar e atualizar. Suporte de update()
	 * @return bool
	 */
	private function parseAndUpdate()
	{
		$where = $this->query['where'];
		$indexes = $this->getMeta('indexes');
		$select = $this->query['select'];
		$exists = false;

		if (empty($indexes) || empty($select)) return null;

		if (empty($where)) {
			foreach ($indexes as $id) {
				$file = $this->getBaseNameFile($id);
				$data = $this->read($file);

				// var_dump(DNAA::change($data, $select));
			}
		}
		else {
			if (isset($where['id'])) {
				// var_dump($where['id'], $indexes);
				if (!$this->inArrayRecursive($where['id'], $indexes)) return null;
        		$indexes = (array)$where['id'];
        		unset($where['id']);	
        	}
        	foreach ($indexes as $id) {
        		$file = $this->getBaseNameFile($id);
    			$data = $this->read($file);
    			if (DNAA::exists($data, $where) && DNAA::change($data, $select)) {
    				$this->write($file, $data);
    				$exists = true;
    			} else $exists = false;
    		}
		}

		return $exists;
	}
	
	/**
	 * Analizar e colocar Construtor de put()
	 * @param mixed $newContent
	 * @param bool $valueMerge TRUE mesclar o valor atual com o existente. caso o existente seja string, sera convertido em array 
	 * @return bool|null
	 */
	private function parseAndPut($newContent, $valueMerge=false)
	{
		// $order   = $this->query['order'];
		$where 	 = $this->query['where'];
		$indexes = $this->getMeta('indexes');
		// $result  = [];
		$placed = false;

		if (empty($indexes)) return null;

		if (empty($where)) {
			foreach ($indexes as $id) {
				$file = $this->getBaseNameFile($id);
				$data = $this->read($file);
				// $result[$id] = DNAA::put($data, $newContent);// TRUE ira mesclar valor caso ja exista a chave
				// DNAA::put($data, $newContent, $valueMerge);// $valueMerge::TRUE ira mesclar valor caso ja exista a chave
				if (DNAA::put($data, $newContent, $valueMerge)) {
					$placed = true;
					$this->write($file, $data);
				}

				// var_dump(DNAA::put($data, $newContent, $valueMerge));
			}
		} else {
			if (isset($where['id'])) {
				if (!$this->inArrayRecursive($where['id'], $indexes)) return null;
        		$indexes = (array)$where['id'];
        		unset($where['id']);	
        	}
			foreach ($indexes as $id) {
				$file = $this->getBaseNameFile($id);
				$data = $this->read($file);

				if (DNAA::exists($data, $where) && DNAA::put($data, $newContent, $valueMerge)) {
					// $result[$id] = DNAA::put($data, $newContent);// TRUE ira mesclar valor caso ja exista a chave
					// DNAA::put($data, $newContent, $valueMerge);// TRUE ira mesclar valor caso ja exista a chave
					$placed = true;
					$this->write($file, $data);
				}
			}
		}

		// $this->parseAndOrder($result, $order);//ordenar
		// return $result;
		return $placed;
	}

	/**
	 * Analizar e deletar
	 * @param number $ids Id ou array com ids a ser deletado  
	 * @return number  Retorna quantidade deletada
	 */
	public function parseAndDelete($ids)
	{
		$meta = $this->getMeta();
		$deleted = 0;//contabilizar quantidade deletado
		foreach ($ids as $id) {
			if (!$this->fileExists($id) && !in_array($id, $meta['indexes'])) throw new Exception(sprintf('Nao foi encontrado o arquivo=id::%s', $id));
			unset($meta['indexes'][$id]);
			unlink($this->getPathFile($id));
			$meta['length']--; //-1
			$deleted++;
		}
		$meta['lastId'] = end($meta['indexes']);
		$this->writeMeta($meta);
		$this->removeCacheAll();
		return $deleted;
	}

	/**
	 * Analizar e deletar com condicao (where)
	 * @return number|null Retorna a quantidade deletada ou NULL algo errado
	 */
	private function parseAndDeleteWithCondition()
	{
		$where 	 = $this->query['where'];
		$indexes = $this->getMeta('indexes');
		$meta = $this->getMeta();
		$deleted = 0;//contabilizar quantidade deletado
		if (empty($where) || empty($indexes)) return null;

		if (isset($where['id'])) {
			if (!$this->inArrayRecursive($where['id'], $indexes)) return null;
    		$indexes = (array)$where['id'];
    		unset($where['id']);	
    	}
		foreach ($indexes as $id) {
			$file = $this->getBaseNameFile($id);
			$data = $this->read($file);
			if (DNAA::exists($data, $where)) {
				if (!$this->fileExists($id) && !in_array($id, $meta['indexes'])) throw new Exception(sprintf('Nao foi encontrado o arquivo=id::%s', $id));
				unset($meta['indexes'][$id]);
				unlink($this->getPathFile($id));
				$meta['length']--; //-1
				$deleted++;
			}
		}
		$meta['lastId'] = end($meta['indexes']);
		$this->writeMeta($meta);
		$this->removeCacheAll();
		return $deleted;
	}




	/**
	 * Gerar um simples hash
	 * @param mixed $needle 
	 * @return string
	 */
	private function simplesHash($needle)
	{
		// return $needle;
		return hash('crc32', $needle); //lower
		// if (is_numeric($needle)) return '' . ($needle+1)/3.14159265359; //number PI
		// else return $needle[2] . $needle[0] . $needle[1] . $needle[0];
		// return md5($needle);
	}
	/**
	 * Saber se tem o cache
	 * @param mixed $name 
	 * @return mixed
	 */
	private function hasCache($name)
	{
		return file_exists($this->getPathCache($name)); 
	}
	/**
	 * Escrever(salvar) o cache
	 * @param mixed $name Nome do cache
	 * @param mixed $content Conteudo a ser salvo
	 * @return bool
	 */
	private function writeCache($name, $content)
	{
		return $this->write($this->getPathCache($name), $content, false);	
	}
	/**
	 * Ler o cache
	 * @param mixed $name 
	 * @return mixed
	 */
	private function readCache($name)
	{
		return $this->read($this->getPathCache($name), false);
	}
	/**
	 * Remover todos os caches
	 * @return bool
	 */
	private function removeCacheAll()
	{
		return $this->directoryInstance->delete($this->getPathCache(), false);
	}
	/**
	 * Obter o caminho do cache
	 * @param mixed $name NULL retorna apenas o diretorio
	 * @return string Caminho completo do cache
	 */
	private function getPathCache($name=null)
	{
		$path = $this->query['tablePath'] . $this->cacheNameDir . '/';
		if (is_null($name)) return $path;
		return $path . $name . '.php';
	}
	/**
	 * Gerar o caminho do arquivo
	 * @param type $id ID
	 * @param bool $addHash Adicionar ou nao HASH ao nome do arquivo
	 * @return string retorna a string caminho montada
	 */
	private function getPathFile($id)
	{
		return $this->query['tablePath'] . $this->getBaseNameFile($id);
	}
	/**
	 * obter o nome base do arquivo
	 * @param mixed $id ID
	 * @param bool $addHash Adicionar ou nao HASH ao nome do arquivo 
	 * @return string
	 */
	private function getBaseNameFile($id)
	{
		return $this->simplesHash($id) . '.php';
	}
	/**
	 * Saber se arquivo existe
	 * @param type $nameFile Nome do Arquivo
	 * @return bool
	 */
	private function fileExists($nameFile)
	{
		return file_exists($this->getPathFile($nameFile));
	}



	/**
	 * Ler o arquivo
	 * @param string $pathOrFile  caminho ou nome do arquivo : file.php
	 * @param bool $relative Setar $path como relativo
	 * @return mixed
	 */
	private function read($pathOrFile, $relative = true)
	{
		if ($relative) $pathOrFile = $this->query['tablePath'] . $pathOrFile;

		if (!($contents = @file_get_contents($pathOrFile))) throw new Exception(sprintf('Nao foi possivel ler o arquivo: "%s"', $pathOrFile));
		 ;
		$contents = substr($contents, $this->strlenDenyAccess);
		return json_decode($contents, true);
	}

	/**
	 * Description
	 * @param string $pathOrFile caminho do arquivo ou nome do arquivo
	 * @param array $array array a ser salvo
	 * @param bool $relative  setar $path como relativo
	 * @return type
	 */
	private function write($pathOrFile,  $content, $relative = true)
	{
		if ($relative) $pathOrFile = $this->query['tablePath'] . $pathOrFile;

		if (is_array($content)) $content = json_encode($content);
		// if (is_array($content)) $content = json_encode($content, JSON_FORCE_OBJECT);

		return is_numeric(@file_put_contents($pathOrFile, $this->strDenyAccess . $content  , LOCK_EX));
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
	 * @param string|null $key Chave a retornar: lastId, length, indexes
	 * @return array
	 */
	private function getMeta($key=null)
	{
		/**
		 * [lastId]
		 * [legth]
		 * [indexes]
		 */
		if (!isset($this->query['table'])) throw new Exception('Não existe tabela para consultar');
		if (!$this->hasMeta()) return false;

		$data= $this->read($this->metaBaseName);
		return (is_null($key)) ? $data : $data[$key];
	}

	/**
	 * Saber se tem metaData
	 * @return bool
	 */
	private function hasMeta()
	{
		if (!isset($this->query['table'])) throw new Exception('Não existe tabela para consultar');
		return file_exists($this->query['tablePath'] . $this->metaBaseName);
	}

	/**
	 * Salvar o conteudo metaData
	 * @param type $array array do conteudo a ser salvo
	 * @return bool
	 */
	private function writeMeta($array)
	{
		if (!isset($this->query['table'])) throw new Exception('Não existe tabela para consultar');
		return $this->write($this->metaBaseName, $array);
	}

	/**
	 * Auxiliar para a variavel $prepare, SETA
	 * @param string $method 
	 * @param array|null $data 
	 * @param number $id 
	 * @param array|null $meta 
	 * @return void
	 */
	private function prepareSet($method, $data=null, $id=0, $meta=null, $other=null)
	{
		$this->prepare['method'] = $method;
		$this->prepare['data'] 	 = $data;
		$this->prepare['meta']   = $meta;
		$this->prepare['id']     = $id;
		$this->prepare['other']  = $other;
	}
	/**
	 * Auxiliar para a variavel $prepare, RESETA. deixa todos os valores em NULL
	 * @return void
	 */
	private function prepareReset()
	{
		$this->prepareSET(null, null, null, null);
	}
	/**
	 * Auxiliar para a variavel $prepare, OBTEM
	 * @param mixed $key Nome da chave para retornar. validos: method, data, meta, id
	 * @return array Retorna a variavel (array) $prepare
	 */
	private function prepareGet($key)
	{
		// if(!isset($this->prepare[$key])) throw new Exception(sprintf('Nao existe chave "%s" em (array) $prepare.', $key));
		return $this->prepare[$key];
	}







	/**
	 * HELPERS ===============================
	 * =======================================
	 * =======================================
	 * =======================================
	 * =======================================
	 * =======================================
	 */


	/**
	 * Executar uma função a cada elemento da array
	 * @param callable $callback Um callback ou Array de callback @example 'strtolower' ou array('strtolower', 'trim')
	 * @param array $array  Array base
	 * @param bool $alsoTheKey TRUE executa a funcao também na chaves. FALSE executa apenas no valor
	 * @return array
	 */
	private function arrayMapRecursive($callback, array $array, $alsoTheKey=false)
	{
		$callback = (array)$callback; //force to array
		$result = [];
		foreach ($array as $key => $value) {

			if(is_array($callback)) {
				foreach ($callback as $fn) {
					if ($alsoTheKey) $key = $fn($key);
				}
			}

			if (is_array($value)) $result[$key] = $this->arrayMapRecursive($callback, $value, $alsoTheKey);
			else {
				for ($i=0, $c = count($callback); $i < $c; $i++) { 
					$value = $callback[$i]($value);
				}
				$result[$key] = $value;
				
			}
		}
		return $result;
	}

	/**
	 * deixar lowerCase os elementos da array
	 * @param array $haystack A Array
	 * @return string Array Modificado
	 */
	private function lowerCase(array $haystack)
	{
		
		$fn = function($string){
			if (!is_string($string)) return $string;
			return mb_strtolower(trim($string), 'UTF-8');
		};

		return $this->arrayMapRecursive($fn, $haystack, true);
	}




	/**
	 * Checar se um valor existe em uma array (multidimensional).
	 * Procura em $haystack o valor $needle
	 * Caso $needle um conjunto de valores (array), caso na consulta um dos valores nao existir, deixa de continuar a consulta e retorna FALSE
	 * @param mixed $needle Valor a procurar. pode ser uma array @example 'ok' ou array('ok', 'list', 'news')
	 * @param type $haystack Array base
	 * @param type|bool $caseInsensitive  TRUE não diferencia maiúsculas e minúsculas
	 * @param type|bool $strict TRUE checa o tipo de $needle também
	 * @return bool
	 */
	private function inArrayRecursive($needle, array $haystack, $caseInsensitive=false, $strict=false) 
	{
		if (is_array($needle) && (bool)$needle) {
			foreach ($needle as $value) {
				if(!$this->inArrayRecursive($value, $haystack, $caseInsensitive, $strict)) return false;
			}
			return true;
		}

		foreach ($haystack as $item) {
			
			if (($strict ? $item === $needle : $item == $needle) ||
				$caseInsensitive && !$strict && !is_array($item) && strcasecmp($needle, $item) === 0 ||
				is_array($item) && $this->inArrayRecursive($needle, $item, $caseInsensitive, $strict))  return true;
		}
		return false;		
	}

}// END class
