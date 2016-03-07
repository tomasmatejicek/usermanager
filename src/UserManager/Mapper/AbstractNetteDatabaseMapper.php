<?php

namespace Mepatek\UserManager\Mapper;

use Nette;


class AbstractNetteDatabaseMapper extends AbstractMapper
{
	/**
	 * Find entities by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 * @return array
	 */
	public function findBy(array $values, $order=null, $limit=null, $offset=null)
	{
		$selection = $this->selectionBy( $values, $order, $limit, $offset );
		$retArray = array();
		foreach ($selection as $row) {
			$retArray[] = $this->dataToItem($row);
		}
		return $retArray;
	}

	/**
	 * Count entities by $values (key=>value)
	 *
	 * @param array $values
	 * @return integer
	 */
	public function countBy(array $values)
	{
		return $this->selectionBy($values)->count();
	}

	/**
	 * Sum column (property) by $values (key=>value)
	 *
	 * @param array $values
	 * @param $column
	 * @return integer
	 */
	public function sumBy(array $values, $column)
	{
		return $this->selectionBy($values)->sum($this->translatePropertyToColumnSQL($column));
	}

	/**
	 * Helper for findBy, countBy
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 * @return Nette\Database\Table\Selection
	 */
	private function selectionBy(array $values, $order = null, $limit = null, $offset = null)
	{
		// for MSSQL - ORDER MUST BE SET FOR OFFSET
		if ($order === null or (is_array($order) and count($order) == 0) ) {
			// set order by ID
			$order = array("id" => "ASC");
		}
		$selection = $this->getTable();
		// compose Where
		foreach ($values as $key => $value) {
			// translate property name to SQL column name
			$keyTranslate = $this->translatePropertyToColumnSQL($key);
			$selection->where($keyTranslate, $value);
		}
		// compose Order
		if ($order !== null) {
			$orderString = "";
			foreach ($order as $column => $ascdesc) {
				// translate properties to SQL column name
				$columnTranslate = $this->translatePropertyToColumnSQL($column);
				$orderString = ($orderString ? "," : "") . $columnTranslate . (strtolower($ascdesc) == "desc" ? " DESC" : "");
			}
			if ($orderString) {
				$selection->order($orderString);
			}
		}
		// compose Limit
		if ($limit !== null) {
			if ($offset !== null) {
				$selection->limit((int)$limit, (int)$offset);
			} else {
				$selection->limit((int)$limit);
			}
		}
		return $selection;
	}


	/**
	 * Translate property name in string to SQl column name
	 * @param $string
	 * @return string
	 */
	private function translatePropertyToColumnSQL($string)
	{
		return strtr( $string, $this->mapItemPropertySQLNames() );
	}

}