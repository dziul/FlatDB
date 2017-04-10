<?php
 
namespace darkziul\Helpers;

/**
 * A simple and secure helper to manipulete Array in various ways via dot notation
 * @author Luiz Carlos Wagner
 * @license MIT
 **/


// FEITO teste entre EVAL e FOREACH, EVAL foi mais rapido =====
// ============================================================


// STRING colchete  key[subjey] ou [key][subkey]  


class accessArrayException extends \Exception{}

class accessArrayElement
{
	/**
	 * esse metodo eh mais rapido do que o bracket notation
	 */


	private static $notFoundMessage = 'NOT_FOUND';

	/**
	 * Definir o operador
	 * @param string $stringNotNotation String do not notation @example key.subkey.main.one 
	 * @return array
	 */
	public static function defineOperator($stringNotNotation)
	{
		return explode('.', $stringNotNotation);
	}


	/**
	 * Obter o valor procurado em $array por $string
	 * @package self::findArrayElement
	 * @param string $dotNotation  String contendo o padrao DOT NOTATION @example master.people.name 
	 * @param array $array Array a ser consultado
	 * @return mixed  valor encontrado em $array por $string ou NULL caso ao contrario
	 */
	public static function  get($dotNotation, array $array)
	{
		return self::findArrayElement($dotNotation, $array);	
	}


	public static function exists($needle, array $array)
	{
		// var_dump($needle[0],$needle, isset($needle[0]) && array_key_exists(0, $needle));
		return self::findArrayElement($needle, $array, true);
	}


	/**
	 * Econtrar a partir da chave
	 * @param type $needle String/Array das chaves a ser buscada
	 * @param array $array Array a ser consultado
	 * @param type|null $methodExists TRUE ativa o metodo Exists, sua saida sera BOOL, FALSE percorre normalmente
	 * @return array|bool  Array dos elementos encontrados ou BOOL caso o argumento $methodExists for TRUE
	 */
	private static function findArrayElementKey($needle, array $array, $methodExists=true)
	{
		$hasValue;
		$result = [];
		$array = $array;

		$isArray = is_array($needle);
		if (!$isArray) {
			$needle = [$needle];
		}
 		
		foreach ($needle as $key) {

			$content = self::parseAndValiteKey(self::defineOperator($key), $array);
			
			$result[] = $content;

			if($methodExists) {
				if (empty($content)) return false;
				// if ($content == null) return false;
			}
		}
		if ($methodExists) return true;
		return $result;
	}


	/**
	 * Analizar e validar a chave
	 * Procurar em $array por $keys
	 * @param array $needle Array contendo as chaves a ser procurada e percorridas 
	 * @param array $haystack Array de entrada
	 * @param string $method Defini o metodo a ser usado
	 * @return mixed  Retorna o valor da chave analizada e validada ou NULL caso ao contrario
	 */
	private static function parseAndValiteKey(array $keys, array $array, $method='get')
	{
		$newArr = [];//init
		foreach ($keys as $key) {
			array_shift($keys);
			$before = $array;
			//+ determina que o que vier imediatamente antes dele deve aparecer 1 ou mais vezes na expressão.
			if ($key === '+') {
				foreach ($before as $k => $v) {
					if(is_array($v)) {
						$newArr[$k] = self::parseAndValiteKey($keys, $v);
						// var_dump($newArr[$k]);
						if(is_null($newArr[$k])) unset($newArr[$k]);
					}
				}
				return $newArr;
			}

			if (!is_array($array) || !isset($array[$key])) return null;
			$array = $array[$key];
		}



		return $array;

	}






 }//END class