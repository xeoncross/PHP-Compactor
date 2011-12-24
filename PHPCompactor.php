<?php
/**
 * PHP Code Compactor
 *
 * Do not use this to speed up your PHP by compacting it. I will come for you.
 * Use a *real* bytecode cache: http://en.wikipedia.org/wiki/List_of_PHP_accelerators
 *
 * Instead use this to get an idea of the TRUE size of projects by comparing
 * the actual number of characters required to run that class - without long
 * variable names, comments, or other added "fluff".
 *
 * @copyright	Copyright (C) 2011-2012 David Pennington
 * @author		David Pennington <http://xeoncross.com>
 * @license		http://www.opensource.org/licenses/mit-license.php
 * @package		PHPCompactor
 */
class PHPCompactor
{
	// Array of all parser tokens (incase a method needs them)
	public $tokens;

	// Convert constant names to int values. Not cross-platform (or cross-version) safe!
	//public $convert_constants = TRUE;

	/**
	 * Load the given PHP code or token array for parsing
	 *
	 * @param string|object $class to compress and obfuscate
	 */
	public function __construct()
	{
		// Load all parser tokens incase a child class wants them
		for ($i = 100; $i < 500; $i++)
		{
			if(($name = @token_name($i)) == 'UNKNOWN') continue;
			$this->tokens[$i] = $name;
		}
	}


	/**
	 * Obfuscate all functions by replacing long variable names
	 *
	 * @param string $source containing functions/classes
	 * @return string
	 */
	function obfuscate($code)
	{
		// This regex requires that functions are indented with tabs
		$regex = '/\n\s*(\w+ ){0,2}function \w+.+?\n\t?}/is';

		// We need to obfuscate and compress each function/method
		return preg_replace_callback($regex, array($this, 'variable_replace'), $code);
	}


	/**
	 * Compress the function code by replacing variables
	 *
	 * @param string $source of function/method
	 * @return string
	 */
	function variable_replace($source)
	{
		$output = '';
		$letters = range('a', 'z');

		// Tokenize the method code so we can compress it correctly (then remove php tag)
		$tokens = array_slice(token_get_all("<?php ". $source[0]), 1);
		$variables = array();

		foreach($tokens as $c)
		{
			if(is_array($c))
			{
				// Do not replace $this with a short name!
				if($c[0] === T_VARIABLE AND $c[1] !== '$this')
				{
					if( ! isset($variables[$c[1]]))
					{
						// The first item of the difference is the value we use
						$result = array_diff($letters, $variables);
						$variables[$c[1]] = array_shift($result);
					}
					$c[1] = '$' . $variables[$c[1]];

				}
				$output .= $c[1];
			}
			else
			{
				$output .= $c;
			}
		}

		return $output;
	}


	/**
	 * Remove unneeded code tokens such as comments and whitespace.
	 *
	 * @param string $code to mimimize
	 * @return string
	 */
	public function minimize($code)
	{
		$remove = array_flip(array(
			T_END_HEREDOC,
			//T_PRIVATE,
			//T_PUBLIC,
			//T_PROTECTED,
			T_WHITESPACE,	// "\t \r\n"
			T_COMMENT,		// // or #, and /* */ in PHP 5
			T_DOC_COMMENT,	// /** Docblock
			T_BAD_CHARACTER,// anything below ASCII 32 except \t, \n and \r
			//T_OPEN_TAG	// < ?php open tag
		));

		$replace = array(
			T_PRINT => 'echo',
			T_LOGICAL_AND => '&&',
			T_LOGICAL_OR => '||',
			T_BOOL_CAST => '(bool)',
			T_INT_CAST => '(int)',
		);

		$add_space_before = array_flip(array(
			T_AS,
		));

		$add_space_after = array_flip(array(
			T_CLASS,
			T_CLONE,
			T_CONST,
			T_FINAL,
			T_FUNCTION,
			T_INSTANCEOF,
			T_NAMESPACE,
			T_NEW,
			T_THROW,
			T_STATIC,
			T_PUBLIC,
			T_PROTECTED,
			T_PRIVATE,
			T_USE
		));

		$add_space = array_flip(array(
			T_EXTENDS,
			T_IMPLEMENTS,
			T_INTERFACE
		));

		$tokens = token_get_all($code);

		foreach($tokens as $id => $token)
		{
			// Control characters
			if( ! is_array($token)) continue;

			list($code, $string, $line) = $token;

			// Might be able to *shrink* some stuff
			if(isset($replace[$code]))
			{
				$tokens[$id] = array($code, $replace[$code], $line);
				continue;
			}

			// Remove some stuff
			if(isset($remove[$code]))
			{
				unset($tokens[$id]);
				continue;
			}

			// "function my_function()" = T_FUNCTION then T_WHITESPACE then T_STRING
			if(isset($add_space[$code]))
			{
				$tokens[$id] = array($code, ' ' . $string . ' ', $line);
			}

			if(isset($add_space_before[$code]))
			{
				$tokens[$id] = array($code, ' ' . $string, $line);
			}

			if(isset($add_space_after[$code]))
			{
				$tokens[$id] = array($code, $string . ' ', $line);
			}

			// Look ahead for returnfunction() vs return$variables
			if($code == T_RETURN)
			{
				// Is there a function two places ahead?
				if(isset($tokens[$id + 2][0]))
				{
					$next = $tokens[$id + 2];
					if($next[0] == T_STRING)
					{
						$tokens[$id] = array($code, $string . ' ', $line);
					}
				}
			}
		}

		$output = '';
		foreach($tokens as $id => $token)
		{
			// Control characters
			if( ! is_array($token))
			{
				$output .= $token;
				continue;
			}

			$output .= $token[1];
		}

		return $output;
	}

}
