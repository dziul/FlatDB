<?php
 
namespace darkziul\Helpers;

/**
 * A simple and secure helper to manipulete Array in various ways via dot notation
 * PHP dot notation array access
 * @author Luiz Carlos Wagner
 * @license MIT
 **/


// FEITO teste entre EVAL e FOREACH, EVAL foi mais rapido =====
// ============================================================


// STRING colchete  key[subjey] ou [key][subkey]  


class arrayAccessException extends \Exception{}

class dotNotationArrayAccess
{
	/**
	 * esse metodo eh mais rapido do que o bracket notation
	 */

	private static $notFoundMessage = 'NOT_FOUND';

	/**
	 * Definir o operador
	 * @param string $dot String not notation @example main.category.name ;  main.[+].name 
	 * @return array
	 */
	public static function dot($dot)
	{
		return explode('.', $dot);
	}



	public static function get(array $array, $keys=null)
	{
		if (!isset($keys)) return $array;
		$keys = (array) $keys;
		$count = count($keys);
		$result = [];
		foreach ($keys as $key) {
			$result[] = self::__get__($array, self::dot($key));
		}

		return ($count-1) ? $result : $result[0];
	}

	private static function __get__(array $array, $key, $recursive=false)
	{
		
		foreach ($key as $index => $k) {
			// array_shift($key);

			if ($k === '[+]') {
				$pos = array_slice($key, $index+1);
				$result = [];
				foreach ($array as $_v) {
					if(is_array($_v)) {
						$result[] = self::__get__($_v, $pos);
					}

				}
				// var_dump($recursive, $result);
				return array_filter($result);
			}

			// var_dump(@$array);

			if(!is_array($array) || !isset($array[$k])) return null;
			$array  =  $array[$k];
			// var_dump($array); 
		}

		return $array;

	}


	/**
	 * Alternativa multidimencional|Recursivo da função in_array.
	 * Checar se o valor existe na array
	 * @param string|array $needle valor a ser procurado, pode ser um grupo (ARRAY) de string. @example 'value' ou array('value', 'value2', 'value3', '...')
	 * @param type $haystack Array a ser consultada
	 * @param type|bool $strict TRUE ativa a comparação FORÇADA, checa também o tipo de $needle em $haystack
	 * @return bool TRUE caso seja encontrado, FALSE caso ao contrário
	 */
	private static function inArray($needle, $haystack, $strict=false, &$result=0) 
	{
		if (is_array($needle)) {
			foreach ($needle as $value) {
				if(self::inArray($value, $haystack, $strict, $result)) $result++;
				else $result--;
				// var_dump($result);//debug
			}
			return $result === count($needle);
		}

		foreach ($haystack as $element) {
			if (($strict ? $element === $needle : $element == $needle) || (is_array($element) && self::inArray($needle, $element, $strict)) )  return true;
		}
		return false;		
	}

 }//END class