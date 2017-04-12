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
	 * Converte dot em array
	 * @param string $dot String not notation @example main.category.name ;  main.[+].name 
	 * @return array
	 */
	public static function dotToArray($dot)
	{
		return explode('.', $dot);
	}


	public static function exists(array $array, $key, $value)
	{


		self::getOrange($array, self::dotToArray($key));

	}
	/**
	 * Obter
	 * @param array $array Array base
	 * @param type|null $keys chave/chaves de busca
	 * @param type|bool $getKeyName TRUE o resultado terá o nome da chave <code>['main.code' => 15]</code>, FALSE não mostrrá o nome <code>[0 => 15]</code>
	 * @return Mi
	 */
	public static function get(array $array, $keys=null, $getKeyName=false)
	{
		if (!isset($keys)) return $array;
		$keys = (array) $keys;
		$count = count($keys);
		$result = [];
		foreach ($keys as $key) {
			if ($getKeyName) $result[$key] = self::__get__($array, self::dotToArray($key));
			else $result[] = self::getOrange($array, self::dotToArray($key));
			
		}


		return  (($count-1) || $getKeyName) ?  $result : $result[0];
		// return array_filter($result);
	}

	/**
	 * Inserir conteudo
	 * @param type &$array 
	 * @param type $keys 
	 * @param type|null $value 
	 * @param type|bool $stringIgnore TRUE Caso o seletor for string irá convete o valor em array e add o elemento de $value, Força a adição em string. FALSE igora string
	 * @return type
	 */
	public static function set(&$array, $keys, $value=null, $stringIgnore=false)
	{

		if (is_array($keys)) {
			foreach ($keys as $key => $value) {
				$data = self::set($array, $key, $value);
			}
		} else {
			$data = self::setOrange($array, self::dotToArray($keys), $value, $stringIgnore,false);
		}

		return $data;

	}

	private static function setOrange( array &$array, array $keys, $value=null, $stringIgnore=false, $notSetExists=true)
	{

		$count = count($keys)-1;

		for ($i=0; $i < $count; $i++) { 
			$index = $keys[$i];


			if ($notSetExists && isset($array[$index])) return false;


			if ($index === '[+]') {
				$keysNew = array_slice($keys, $i+1); // pula a o proximo valor/chave
				foreach ($array as $_k => $_v) {

					if (is_array($_v)) {

						self::setOrange($array[$_k], $keysNew, $value);	

					} elseif ($stringIgnore) {

						$array[$_k] = [];
						$array[$_k][] = $_v;
						self::setOrange($array[$_k], $keysNew, $value);	

					}
				}
				return $array;
			}

			//caso não exista ou não seja uma array cria um vazio para dar continuidade
			if (!isset($array[$index]) || !is_array($array)) {
				$array[$index] = [];
			}

			$array = &$array[$index];
		}


		$array[$keys[$i]] = $value;
		return $array;

	}

	/**
	 * "Laranja" de get()
	 * @param array $array 
	 * @param type $key 
	 * @param type|bool $recursive 
	 * @return type
	 */
	private static function getOrange(array $array, $key, $recursive=false, $valueCompare=null)
	{


		foreach ($key as $index => $k) {
			// array_shift($key);

			if ($k === '[+]') {
				$pos = array_slice($key, $index+1);
				$result = [];

				foreach ($array as $_v) {
					if(is_array($_v)) {
						$data = self::getOrange($_v, $pos, true);
						$result[] = (is_array($data) && isset($data[0])) ? $data[0] : $data;
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