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
	 * @package self::parserAndConfigure
	 * @param string $dotNotation  String/Grupo contendo o padrao DOT NOTATION @example master.people.name ; array('master.people.name', 'master.city', ...) 
	 * @param array $array Array a ser consultado
	 * @return mixed  valor encontrado em $array por $string ou NULL caso ao contrario
	 */
	public static function  get($dotNotation, array $array)
	{
		return self::parserAndConfigure($dotNotation, $array);	
	}


	public static function inset(array $keyAndValue, array &$array)
	{
		return self::parserAndConfigure($keyAndValue, $array, 'inset');
	}

	/**
	 * Verificar se existe chave /ou chave=>value
	 * @param type $needle "chave"/ou grupo de "chave" de chaves a ser verificado;  "chave:value"/ou grupo de "chave:value" a ser verificado; ou misto @example 'main.name'; array('main.name', 'main.type'); array('main.name'=>'Antonio', 'main.type'=>'orange'); array('main.name', 'main.type'=>'orange')
	 * @param array $array  Array base
	 * @return bool TRUE caso exista doas as chaves setadas, FALSE caso ao contrario
	 */
	public static function exist($needle, array $array)
	{
		// var_dump($needle[0],$needle, isset($needle[0]) && array_key_exists(0, $needle));
		return self::parserAndConfigure($needle, $array, 'exist');
	}


	/**
	 * Analizar e configurar, usando algum metodo
	 * @param type $needle String/Array das chaves a ser buscada
	 * @param array $array Array a ser consultado
	 * @param string $method Definir o tipo de metodo a ser usado; Default get. Aceito: get; exists; update; remove; add
	 * @return array|bool  Array dos elementos encontrados ou BOOL caso o argumento $methodExists for TRUE
	 */
	private static function parserAndConfigure($needle, array $array, $method='get')
	{
		$hasValue;
		$result = [];
		if (!is_array($needle)) {
			$needle = [$needle];
		}
 		
		foreach ($needle as $key => $value) {

			if (is_numeric($key)) { //caso key foir numero, significa que não foi setado o value
				$k = $value;
				$hasValue = false;
			} else {
				$k = $key;
				$hasValue = true;
			}

			$content = self::parseAndGenerate(self::defineOperator($k), $array, $method, $value);
			// var_dump($content);//debug

			if ($method == 'get') $result[] = $content;
			elseif ($method == 'exist') {
				if (empty($content)) return false;
				elseif ($hasValue) {
					if (!is_array($content) || is_array($content) && !self::inArray($value, $content)) return  false;
				}
			}
				elseif ($method == 'inset') {}
			elseif ($method == 'unset') {}
			elseif ($method == 'change') {}

		}


		// outset ===
		// ==========
		if ($method == 'get') {
			return array_filter($result);
		}
		elseif ($method == 'exist') {
			return true;
		}
		elseif ($method == 'inset') {
			return $content;
		}
		elseif ($method == 'unset') {

		}
		elseif ($method == 'change') {

		}

	}





	/**
	 * Analizar e validar a chave
	 * Procurar em $array por $keys
	 * @param array $needle Array contendo as chaves a ser procurada e percorridas 
	 * @param array $haystack Array de entrada
	 * @param string $method Defini o metodo a ser usado
	 * @param string $value Argumento apenas usado para passar o valor de $keys, para os metodos change; unset; set
	 * @return mixed  Retorna o valor da chave analizada e validada ou NULL caso ao contrario
	 */
	private static function parseAndGenerate(array $keys, array $array, $method='get', $value='')
	{
		$newArr = [];//init
		foreach ($keys as $key) {
			array_shift($keys);
			$before = $array;
			//+ determina que o que vier imediatamente antes dele deve aparecer 1 ou mais vezes na expressão.
			if ($key == '[?]' || $key == '[]' || $key == '[+]') { // este modo é mais rapido que in_array()
				foreach ($before as $k => $v) {
					// var_dump($value);
					if(is_array($v)) {
						$newArr[] = self::parseAndGenerate($keys, $v, $method, $value);
						// if(empty(var)($newArr[$k])) unset($newArr[$k]);
					}
				}
				// var_dump($newArr);//
				return array_filter($newArr);
			}


			if ($method === 'inset') {

				if (empty($value)) throw new accessArrayException('method::inset, Nao ha Valor para ser adicionado');
				if(is_array($array) && !isset($array[$key])	) {
					$array[$key] = $value;
					continue;
				}
				
			}
			elseif (!is_array($array) || !isset($array[$key])) return null;
			// var_dump($key);
			$array = $array[$key];
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