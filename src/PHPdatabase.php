<?php

namespace darkziul\PHPdatabase;


/**
 * PHPdatabase
 * @package \PHPdatabaseInterface
 * @category Library
 * @author Luiz Carlos Wagner 
 * @link [repository](https://github.com/darkziul/PHPdatabase)
 * @version 0.0.5-init
 * @license MIT
 * 
 **/
class PHPdatabase implements InterfacePHPdatabase{

	public function __construct()
	{



	}


	private function bracket(){

			achar os outros (?:\[)([^\[\]]+)(?:\])

			$func = function($matches){
			    var_dump($matches);
			    
			    if( empty(@$matches) ) return @matches[0];
			};
			preg_replace_callback(['~([^\[\]]+)(?:\[)~'], $func, 'contnet[a]');

	}
}//END class PHPdatabase