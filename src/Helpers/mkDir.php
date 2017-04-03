<?php
 
namespace darkziul\Helpers;

/**
 * HELPER make directory class
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class mkDir
 {

 	public function __construct(){}


	/**
	 * Avaliar e Criar o Diretorio
	 * @param string $dir diretorio a ser analizado e criado
	 * @param bool $recursive ativar modo recursivo de criação
	 * @param number $mode permissão do arquivo, padrão: 07777
	 * @return bool
	 */
	private function create($dir, $recursive=false, $mode=0777)
	{
		if( is_dir($dir) ) return true; // Caso $dir for diretorio | Case $dir for directory


            $dirParent = dirname($dir); // dir parent
            
            $return = null; //default
            if($recursive) $return =  $this->set($dirParent, $recursive, $mode); //ativar o modo recursivo de criação

            if(!$recursive  &&  is_writable($parentPath) || $return && is_writable($parentPath))
            {

                    mkdir($dir);
                    return chmod($dir, $mode);
            }

            return false;
	}

}