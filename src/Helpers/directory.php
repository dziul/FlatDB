<?php
 
namespace darkziul\Helpers;

/**
 * HELPER make directory class
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

class Directory
{

 	public function __construct(){}


 	public function has($dir)
 	{
 		return is_dir($dir);
 	}


	/**
	 * Avaliar e Criar o Diretorio
	 * @param string $dir caminho do diretorio @example scard/data/dir/
	 * @param bool $recursive ativar modo recursivo de criação
	 * @param number $mode permissão do arquivo, padrão: 07777
	 * @return bool  NULL quando $dir já for diretorio /ou já existe
	 */
	public function create($dir, $recursive=false, $mode=0777)
	{
		if( $this->has($dir) ) return true; 

        $dirParent = dirname($dir); // dir parent
        
        $return = null; //default
        if($recursive) $return =  $this->create($dirParent, $recursive, $mode); //ativar o modo recursivo de criação

        if(!$recursive  &&  is_writable($dirParent) || $return && is_writable($dirParent))
        {

                mkdir($dir);
                return chmod($dir, $mode); //forçar a permissão
        }

        return false;
	}

	/**
	 * Deleta o diretorio junto todas as pasta/arquivos
	 * @param string $dir caminho do diretorio @example scard/data/dir/
	 * @param $removeMySelf TRUE remove todos os subdiretorios,arquivos e o proprio dir setado em $dir
	 * @return null|bool  NULL quando $dir não for um diretorio
	 */
	public function delete($dir, $removeMySelf=true)
	{

		if(is_dir($dir))
		{


			$recursiveDirIte = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
			$Iterator = new \RecursiveIteratorIterator($recursiveDirIte, \RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($Iterator as $file) {
				$fn = $file->isFile() ? 'unlink' : 'rmdir';

				$fn($file->getPathname());
			}
			
			return ($removeMySelf) ? rmdir($dir) : true;
		}

		return null;
	}



	/**
	 * Retorna todas as pastas do Diretorio [Nao as subsPastas]
	 * @param string $dir caminho do dir  @example data/example/
	 * @return array  retorna todos os nomes de pasta existente no diretorio setado em $dir  @example $dir:'data/example/'   out: ['entry-1', 'entry-2']
	 */
	public function showFolders($dir)
	{
		$outArr = [];//init
		$recursiveDirIte = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);

		foreach ($recursiveDirIte as $item)
		{
			if ($item->isDir()) $outArr[] = $item->getFilename();
		}
		$outArr['length'] = count($outArr); 
		return $outArr;

	}

}
