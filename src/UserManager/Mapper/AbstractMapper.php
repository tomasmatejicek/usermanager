<?php

namespace Mepatek\UserManager\Mapper;

use Nette,
	App\Model\Logger;

class AbstractMapper extends Nette\Object
{
	/** @var \App\Model\Logger */
	protected $logger;

	/** ************ LOG functions ************************ */
	/**
	 * @param string $mapper
	 * @param array | \Traversable $oldvalues
	 * @param string $addMessage additional message
	 */
	protected function logDelete($mapper, $oldvalues=array(), $addMessage="")
	{
		$msg = "Delete ($mapper)\nDeleted values:" . $this->traversableToString($oldvalues, ", ") . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			array(
				"function" => substr($mapper,-50)
			)
		);
	}

	/**
	 * @param string $mapper
	 * @param array | \Traversable $oldvalues
	 * @param array | \Traversable $newvalues
	 * @param string $addMessage additional message
	 */
	protected function logSave($mapper, $oldvalues=array(), $newvalues=array(), $addMessage="")
	{
		$msg = "Save ($mapper)\nOld values:" . $this->traversableToString($oldvalues, ", ") . "\nNew values:" . $this->traversableToString($newvalues, ", ") . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			array(
				"function" => substr($mapper,-50)
			)
		);
	}

	/**
	 * @param string $mapper
	 * @param array | \Traversable $newvalues
	 * @param string $addMessage additional message
	 */
	protected function logInsert($mapper, $newvalues=array(), $addMessage="")
	{
		$msg = "New ($mapper)\nNew values:" . $this->traversableToString($newvalues, ", ") . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			array(
				"function" => substr($mapper,-50)
			)
		);
	}


	/**
	 * Helper : array or \Traversable to string
	 * @param array | \Traversable $values
	 * @param string $delimiter
	 * @return string
	 */
	private function traversableToString($values, $delimiter=", ")
	{
		$str = "";
		foreach ($values as $name=>$value) {
			$str .= ($str ? $delimiter : "") . "$name:$value";
		}
		return $str;
	}

	/**
	 * Log message - helper for logInsert/logSave/logDelete
	 * @param $msg
	 * @param array $context
	 */
	protected function log($msg, $context)
	{
		if ($this->logger) {
			$this->logger->info($msg, $context);
		}
	}
}