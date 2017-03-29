<?php

namespace darkziul;

/**
 * PHPdatabase
 * @package phpDB
 * @category Library
 * @author Luiz Carlos Wagner 
 * @link [repository](https://github.com/darkziul/phpDB)
 * @version 0.0.5-init
 * @license MIT
 * 
 **/
class phpDB implements phpDBInterface{

	public function __construct()
	{



	}



 	public function add(){}
 	public function attach(){}

 	public function change(){}
 	public function remove(){}

 	public function find(){}


 	public function save(){}

 	public function all(){}
 	public function column(){}




 	//squareBracket = colchete
	public function squareBracket( $strSquareBracket )
	{

			

			$func = function($matches){
			    // var_dump($matches);//debug
			    
			    if( empty(@$matches[1]) ) return $matches[0];

			    $match = $matches[1];
			    return  is_numeric($match) ? '['.$match.']' : '[\''.$match.'\']'; 

			};
			return preg_replace_callback(['~([^\[\]]+)(?=\[)~', '~(?:\[)([^\[\](\[\')]+)(?:\])~'], $func, $strSquareBracket);

	}

	//access array Element
	public function accessArray( $strSquareBracket, $array)
	{
		$squreBracket = $this->squareBracket($strSquareBracket);

		return eval("return \$array$squreBracket;");
	}


}//END class PHPdatabase