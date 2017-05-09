<?php
 
namespace darkziul\Helpers;

/**
 * A simple and secure helper to manipulete Array in various ways via Square bracket notation
 * @author Luiz Carlos Wagner
 * @license MIT
 **/


// FEITO teste entre EVAL e FOREACH, EVAL foi mais rapido =====
// ============================================================


// STRING colchete  key[subjey] ou [key][subkey]  


class accessArrayElementUseEvalException extends \Exception{}

class accessArrayElementUseEval
{

	/**
	 * Padronizador
	 * @var  array
	 */
	private static $pattern = ['~^([^\[\]]+)(?=\[)~','~\[([^\[\](\[\'|\[\")(\'\]|\"\])]*)\]~'];


	private static $messageNotFound = 'NOTHING_FOUND';

	// ideia de usar a array de consulta direto no construtor, mas precisa fazer o test de performace. Pois nesse modo sera necessario sempre fazer uma nova instancia da class
	// public function __construct(array $haystack)
	// {

	// }



	/**
	 *  Analizar e Redefinir STRING colchetes
	 * @param string $strSquareBracket  String do colchete  @example Pega algo como   test[group]  [test][group] 
	 * @return string
	 */
	//squareBracket = colchete
	public static function parseAndRefineStringBracket($stringBracket)
	{
			$isStrBracket = null; //
			// if(!is_string($stringBracket)) throw new Exception(['$stringBracket precisa ser string']);
			$func = function($matches) use(&$isStrBracket) {
			    // var_dump($matches);//debug
			    if (!isset($matches[1])) return $matches[0];

			    // var_dump($matches);//debug
			    $isStrBracket = true;

			    $match = $matches[1];

			    return  is_numeric($match) || empty($match) ? '['.$match.']' : '[\''.$match.'\']'; 

			};
			// return preg_replace_callback(['~(?J:\[([\'"])(?<el>.*?)\1\]|(?<el>\]?[^\[]+)|\[(?<el>(?:[^\[\]]+|(?R))*)\])~'], $func, $strSquareBracket);
			$result =  preg_replace_callback(self::$pattern, $func, $stringBracket);

			// var_dump($result);//debug
			return $isStrBracket ? $result : $isStrBracket;

	}

	/**
	 *  Acessar elemento da array
	 * @param string $needle string colchete
	 * @param array $haystack Array para consulta 
	 * @return mixed
	 */
	private static function accessArrayElement($needle, array $haystack)
	{
		$result = [];
		$isArray = is_array($needle);
		if(!$isArray) $needles = [$needle];
		else $needles = $needle;

		foreach ($needles as $key) {
					
					if ($bracket = self::parseAndRefineStringBracket($key)) {

						$result[$key] = eval("return isset(\$haystack$bracket) ? \$haystack$bracket : self::\$messageNotFound;");

				} else {
				 		$result[$key] = isset($haystack[$key]) ? $haystack[$key] : self::$messageNotFound;
				 		// $bracket = '[' . $key . ']'; // este eh apenas para caso for soltado o erro
				}

				// if(is_null($result[$key])) throw new accessArrayException(sprintf('Nao existe  Array%s', $bracket));
		}

		// var_dump($result);
		return (!$isArray) ? $result[$needle] : $result;
		
	}

	/**
	 * Pegar o elemento array
	 * @param type $needle STRING ou ARRAY de string colchete(s) @example test[main] ; array( test[main], test2[example][one], ... ) 
	 * @param array $haystack Array base para consulta
	 * @return mixed
	 */
	public static function get($needle, array $haystack)
	{
		return self::accessArrayElement($needle, $haystack);
	}


	/**
	 * adicionar key e valor
	 * @param type $needle string ou array string de colchete => valor
	 * @param array &$haystack array base para consulta 
	 * @return array Retorna $haystack
	 */
	private static function insertArrayElement($needle, array &$haystack)
	{
		// $result = false;		
		if(!is_array($needle)) $needles = [$needle];
		else $needles = $needle;

		foreach ($needles as $key => $value) {
					
			if ($bracket = self::parseAndRefineStringBracket($key)) {
				eval("return \$haystack$bracket = \$value;");
			} else {
			 		$haystack[$key] = $value;
			}
		}

		return $haystack;
		
	}

	public static function set($needle, array &$haystack)
	{
		return self::insertArrayElement($needle, $haystack);
	}


	/**
	 * Atualizar conteudo
	 * @param type $needle string ou grupo de string colchete array 
	 * @param array &$haystack arrayy base para consulta
	 * @return array|null
	 */
	public static function change($needle, array &$haystack)
	{
		foreach ($needle as $key => $value) {
			if(!self::keyExists($key, $haystack)) return null;
		}
		return self::insertArrayElement($needle, $haystack); 
	}
	

	private static function removeArrayElement($needle, array &$haystack)
	{
		// $result = false;		
		if(!is_array($needle)) $needles = [$needle];
		else $needles = $needle;

		foreach ($needles as $key) {
					
			if ($bracket = self::parseAndRefineStringBracket($key)) {
				eval("if(isset(\$haystack$bracket)) unset(\$haystack$bracket);");
			} else {
				// var_dump($key);
			 		if(isset($haystack[$key])) unset($haystack[$key]);
			}
		}

		return $haystack;
		
	}

	public static function remove($needle, array &$haystack)
	{
		return self::removeArrayElement($needle, $haystack);
	}

	/**
	 *  Checar se existe chave e valor na array base
	 * @param array $needle  KEy=>Value, stringColchete=>Value @example array('test[group]' => true) 
	 * @param array $haystack Array que sera usada para chegar @example array('test[group]' => true, 'tests[groups]' => [...])
	 * @return bool CAso exista TRUE, ao contrario FALSE
	 */
	private static function checkExistsKeyAndValue(array $needle, array $haystack)
	{

		$result = true;
		foreach ($needle as $KEY => $VALUE) {
			

				$haystackValue = self::accessArrayElement($KEY, $haystack);

				if($VALUE == $haystackValue || isset($haystackValue[$VALUE])) break;//verificar se eh igual ou caso $haystack for array, existe ou nao
				elseif (is_array($haystackValue)) {
					$result = false;
					//caso for array, sera  uma array sem KEY definido, algo como [1,2,3,4,...]
					foreach ($haystackValue as $value) {
						// var_dump($VALUE ,$value);
						if($VALUE == $value) {
							$result = true;
							break;
						}
					}
				} else {
					$result = false;
				}
				
		}

		return $result;

	}

	/**
	 *  Checar se existe a chave e valor na array base
	 * @param array $needle  KEy=>Value, stringColchete=>Value @example array('test[group]' => true) 
	 * @param array $haystack Array que sera usada para chegar @example array('test[group]' => true, 'tests[groups]' => [...])
	 * @return bool 
	 */
	public static function keyAndValueExists(array $needle, array $haystack)
	{
			return self::checkExistsKeyAndValue($needle, $haystack);
	}

	/**
	 * Checar se chave existe
	 * @param mixed $needle quando usado um grupo (ARRAY) de chaves para consulta. Caso uma dessas chaves nao existir o retorno sera false, mesmo as demais existirem 
	 * @param array $haystack Array para consulta
	 * @return bool
	 */
	public static function keyExists($needle, array $haystack)
	{
		$result = self::accessArrayElement($needle, $haystack);
		return (is_array($result) && in_array(self::$messageNotFound, $result) || $result === self::$messageNotFound)  ? false : true ;
	}

 }//END class