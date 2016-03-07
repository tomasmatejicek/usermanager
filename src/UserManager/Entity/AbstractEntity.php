<?php

namespace Mepatek\UserManager\Entity;

use Nette\Utils\Strings,
	Nette\Utils\DateTime;


/**
 * Class AbstractEntity
 * @package Mepatek\UserManager\Entity
 */
abstract class AbstractEntity extends \Nette\Object implements \ArrayAccess, \IteratorAggregate
{
	/**
	* Truncate value to $max character and return string or null
	* @param string $value
	* @param integer $max
	* @return string|null
	*/
	protected function StringTruncate($value, $max=255)
	{
		if ($value) {
			return (string)Strings::truncate($value, $max, "");
		} else {
			return null;
		}
	}

	/**
	* Correct value rowguid
	* @param string $rowguid
	* @return string|null
	*/
	protected function Rowguid($rowguid)
	{
		// is correct uuid ?
		if (Strings::match($rowguid, '/^\{{0,1}[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}\}{0,1}$/i')) {
			return (string)$rowguid;
		} else {
			return null;
		}
	}

	/**
	 * Correct value Datetime
	 * @param \Nette\Utils\Datetime|integer|string|null $DateTime
	 * @return \Nette\Utils\Datetime|null
	 */
	protected function DateTime($DateTime)
	{
		if ($DateTime===null) {
			return null;
		} else {
			return DateTime::from($DateTime);
		}
	}

	/**
	 * Correct value Money
	 * @param float|integer|string|null $Money
	 * @return float|null
	 */
	protected function Money($Money)
	{
		if ($Money===null) {
			return null;
		} else {
			return (float)$Money;
		}
	}

	/**
	 * Correct value Float
	 * @param float|integer|string|null $Float
	 * @return float|null
	 */
	protected function Float($Float)
	{
		if ($Float===null) {
			return null;
		} else {
			return (float)$Float;
		}
	}


	/**
	 * ArrayAccess offsetSet
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
	}

	/*
	 * ArrayAccess offsetExists
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->$offset);
	}

	/**
	 * ArrayAccess offsetUnset
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
	}

	/**
	 * ArrayAccess offsetGet
	 * @param mixed $offset
	 * @return null
	 */
	public function offsetGet($offset) {
		return isset($this->$offset) ? $this->$offset : null;
	}

	/**
	 * Traversable interface
	 * @return ArrayIterator
	 */
	public function getIterator() {
		$array = get_object_vars($this);
		return new \ArrayIterator($array);
	}

}
