<?php
 
namespace darkziul;

/**
 * acess Array Element
 * @author Luiz Carlos Wagner
 * @license MIT
 **/

 class acessArrayElement{

 	/**
 	 * @var string
 	 **/
 	// private static $delimiter = '.'; //default



	//squareBracket = colchete
	public function bracket( $strSquareBracket )
	{

			

			$func = function($matches){
			    var_dump($matches);//debug
			    // return;
			    if( empty(@$matches[1]) ) return $matches[0];

			    $match = $matches[1];
			    return  is_numeric($match) ? '['.$match.']' : '["'.$match.'"]'; 

			};
			// return preg_replace_callback(['~(?J:\[([\'"])(?<el>.*?)\1\]|(?<el>\]?[^\[]+)|\[(?<el>(?:[^\[\]]+|(?R))*)\])~'], $func, $strSquareBracket);
			return preg_replace_callback(['~^(?<master>[^\[\]]+)(?=\[)~', '~\[([^\[\](\[\'|\[\")(\'\]|\"\])]*)\]~'], $func, $strSquareBracket);

	}




	public function getArrayElement(array $array, $keys)
	{

		return $this->arrayElementMultidimencional($array, $this->buildAccessKey($keys), 'get');
	}

	private function buildAccessKey($squareBracket)
	{

		// return $this;//debug
		$keys = [];//base para guarda
		$func = function($matches) use(&$keys)
		{
			// var_dump($matches);//debug
			if( isset($matches[1]) ) $keys[] = trim($matches[1]);
			return $matches[0];
		};
		preg_replace_callback(['~\[([^\[\](\[\'|\[\")(\'\]|\"\])]*)\]~'], $func, $squareBracket);

		if( !count($keys) ) throw new Exception("Not build access key");
		
		return $keys;

	}

	private function arrayElementMultidimencional(array $array, $keys, $mode='get', $value='', $doneRecursive=false  )
	{
		/**
		 * 
		 * $mode
		 *  get :: default
		 *  set
		 *  unset
		 * 
		 **/
		
		foreach ($keys as $key)
		{
			array_shift($keys);//remove o primeiro
			$before = $array; // save content array|content
			
			// BEGIN access Multidimencional
			// $mode :: get
			if('?' === $key) 
			{
				$ownerlessArr = [];//init
				// $varTEMP = '{{abc@@##%%__TEMP__%%##@@abc}}';
				foreach($before as $KEY => $VALUE)
				{

					if( is_array($VALUE) && !empty($VALUE) )
					{

						$data = $this->arrayElementMultidimencional($VALUE, $keys, false, false, true);
						if( $doneRecursive && is_array($data) && isset($data[0]) ) $data = $data[0];

						if( !empty($data)  ) $ownerlessArr[] = $data;
					}  

					// var_dump($ownerlessArr);//debug
				}
				return $ownerlessArr;
			}
			if('set' == $mode && '' == $key)
			{
				var_dump($key);
			}
			// END access Multidimencional


			if( !is_array($array) || !isset($array[$key]) ) return null;
			$array = $array[$key];
		}

		return $array;
	}

	//access array Element via dot notation
	// public function accessArrayElement( $index, array $array)
	// {
	// 	return $this->getArrayElement(explode(self::$delimiter, $index), $array);
	// }

	// public function accessArrayElement( array $array, $index)
	// {

	// 	$index = explode(self::$delimiter, $index);

	// 	var_dump($index);

	// 	// return $this->getArrayElement($array, $index);
	// 	return $this->getArrayElementRecursive($array, $index);
	// }

	// private function getArrayElement(array $array, array $keys)
	// {
	// 	foreach ($keys as $key)
	// 	{
	// 		if( !is_array($array) || !isset($array[$key]) ) return null;
	// 		$array = $array[$key]; 	
	// 	}
	// 	return $array;
	// }

	

	private function callUserFuncAcessArray()
	{
		call_user_func_array($func, $paramArr);
	}

 }//END class

 class acessArrayException extends \Exception{}