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


 	public function isDir($dir)
 	{
 		return is_dir($dir);
 	}


	/**
	 * Avaliar e Criar o Diretorio
	 * @param string $dir diretorio a ser analizado e criado
	 * @param bool $recursive ativar modo recursivo de criação
	 * @param number $mode permissão do arquivo, padrão: 07777
	 * @return bool
	 */
	public function create($dir, $recursive=false, $mode=0777)
	{
		if( $this->isDir($dir) ) return true; // Caso $dir for diretorio | Case $dir for directory

        $dirParent = dirname($dir); // dir parent
        
        $return = null; //default
        if($recursive) $return =  $this->set($dirParent, $recursive, $mode); //ativar o modo recursivo de criação

        if(!$recursive  &&  is_writable($dirParent) || $return && is_writable($dirParent))
        {

                mkdir($dir);
                return chmod($dir, $mode);
        }

        return false;
	}

}