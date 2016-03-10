<?php

namespace Mepatek\UserManager\Mapper;

use Nette;

class AbstractMapper extends Nette\Object
{

	/**
	 * Helper : array or \Traversable to string
	 *
	 * @param array | \Traversable $values
	 * @param string               $delimiter
	 *
	 * @return string
	 */
	protected function traversableToString($values, $delimiter = ", ")
	{
		$str = "";
		foreach ($values as $name => $value) {
			if (is_array($value) or is_object($value)) {
				$value = serialize($value);
			}
			$str .= ($str ? $delimiter : "") . "$name:$value";
		}
		return $str;
	}

}