<?php namespace Premmerce\WooCommercePinterest\Model;

use wpdb;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;

abstract class AbstractModel {

	const TYPE_RESULTS = 1;

	const TYPE_ROW = 2;

	const TYPE_COLUMN = 3;

	const TYPE_VAR = 4;

	/**
	 * WPDB instance
	 *
	 * @var wpdb
	 */
	protected $db;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * WHERE query part
	 *
	 * @var string
	 */
	protected $where = '';

	/**
	 * SQL OFFSET number
	 *
	 * @var int
	 */
	private $offset;

	/**
	 * SQL LIMIT number
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * SQL ORDER
	 *
	 * @var string
	 */
	private $order;

	/**
	 * SQL DISTINCT
	 *
	 * @var string
	 */
	private $distinct;

	public function __construct() {
		global $wpdb;
		$this->db    = $wpdb;
		$this->table = $this->db->prefix . $this->table;
	}


	/**
	 * Run query and get results
	 *
	 * @param $fields
	 *
	 * @param int $returnType
	 *
	 * @return mixed
	 */
	public function get( $fields = null, $returnType = self::TYPE_RESULTS) {
		$sql = $this->getSql($fields);

		$this->reset();

		switch ($returnType) {
			case self::TYPE_ROW:
				return $this->db->get_row($sql, ARRAY_A);
			case self::TYPE_COLUMN:
				return $this->db->get_col($sql);
			case self::TYPE_VAR:
				return $this->db->get_var($sql);
			case self::TYPE_RESULTS:
			default:
				return $this->db->get_results($sql, ARRAY_A);
		}
	}


	/**
	 * Find
	 *
	 * @param int $id
	 *
	 * @return null|array
	 */
	public function find( $id) {
		$result = $this->where(array('id' => $id))->get();

		if (count($result) > 0) {
			return $result[0];
		}
	}

	/**
	 * Create new row
	 *
	 * @param array $data
	 *
	 * @return int
	 *
	 * @throws PinterestModelException
	 */
	public function create( array $data) {
		$result = $this->db->insert($this->table, $this->sanitize($data));

		$id = $this->db->insert_id;


		if (false === $result || ! $id) {
			throw new PinterestModelException('Database request failed. Error from wpdb: ' . $this->db->last_error);
		}


		return $id;
	}

	/**
	 * Add or replace existing row
	 *
	 * @param array $data
	 *
	 * @return int|null
	 */
	public function replace( array $data) {
		$result = $this->db->replace($this->table, $this->sanitize($data));

		return false === $result ? null : $result;
	}

	/**
	 * Add or replace multiple rows in DB
	 *
	 * @param array $fields Array represents table fields.
	 *                      Keys is field name, values is data type placeholder (%d, %f, %s)
	 * @param array $data Array represents table data structure.
	 *                      Each item represents single row, where keys is column names, and values - it's data
	 *
	 * @throws PinterestModelException
	 */
	public function replaceMultiple( array $fields, array $data) {
		$valuesString         = $this->prepareReplaceMultipleValuesString($fields, $data);
		$fieldsString         = implode(', ', array_keys($fields));
		$onDuplicateKeyUpdate = $this->prepareOnDuplicateKeyUpdate($fields);

		$sql = "INSERT INTO {$this->table} ({$fieldsString}) VALUES {$valuesString} {$onDuplicateKeyUpdate}";

		$result = $this->db->query($sql);

		if (false === $result) {
			throw new PinterestModelException($this->db->last_error);
		}
	}

	/**
	 * Prepare replace multiple query part
	 *
	 * @param $fields
	 * @param $data
	 *
	 * @return string
	 */
	protected function prepareReplaceMultipleValuesString( $fields, $data) {
		$parts = array();

		foreach ($data as $dataRow) {

			$sortedDataRow    = $this->sortDataRow(array_keys($fields), $dataRow);
			$sanitizedDataRow = $this->sanitize($sortedDataRow);

			$parts[] = '(' . $this->db->prepare(implode(', ', $fields), $sanitizedDataRow) . ')';
		}

		return implode(', ', $parts);
	}

	/**
	 * Sort data row
	 *
	 * @param array $fieldsOrder
	 * @param array $dataRow
	 *
	 * @return array
	 */
	protected function sortDataRow( array $fieldsOrder, array $dataRow) {
		$sortedDataRow = array();

		foreach ($fieldsOrder as $fieldName) {
			$sortedDataRow[$fieldName] = isset($dataRow[$fieldName]) ? $dataRow[$fieldName] : '';
		}

		return $sortedDataRow;
	}

	/**
	 * Prepare ON DUPLICATE KEY UPDATE query part
	 *
	 * @param array $fields
	 *
	 * @return string
	 */
	private function prepareOnDuplicateKeyUpdate( array $fields) {
		$fieldsToUpdateOnDuplicate = array_keys($fields);
		unset($fieldsToUpdateOnDuplicate['id']);

		$onDuplicateKeyUpdateParts = array();

		foreach ($fieldsToUpdateOnDuplicate as $field) {
			$onDuplicateKeyUpdateParts[] =  " {$field}=values({$field})";
		}

		$onDuplicateKeyUpdate = implode(', ', $onDuplicateKeyUpdateParts);

		return $onDuplicateKeyUpdate ? " ON DUPLICATE KEY UPDATE {$onDuplicateKeyUpdate}" : '';
	}

	/**
	 * Update row in DB
	 *
	 * @param int $id
	 * @param array $data
	 *
	 * @return false|int
	 */
	public function update( $id, array $data) {
		return $this->updateWhere(array('id' => $id), $data);
	}

	/**
	 * Update with WHERE condition
	 *
	 * @param $where
	 * @param $data
	 *
	 * @return false|int
	 */
	public function updateWhere( $where, $data) {
		return $this->db->update($this->table, $this->sanitize($data), $where);
	}

	/**
	 * Delete single item
	 *
	 * @param int $id
	 *
	 * @return false|int
	 */
	public function deleteSingleById( $id) {
		return $this->db->delete($this->table, array('id' => $id));
	}

	/**
	 * Delete previously filtered rows
	 *
	 * @return false|int
	 */
	public function deleteFiltered() {
		$sql = "DELETE FROM {$this->table} {$this->where}";

		$this->reset();

		return $this->db->query($sql);
	}

	/**
	 * Delete multiple items
	 *
	 * @param array $ids
	 *
	 * @return false|int
	 *
	 * @todo: check and delete. Looks like this method not used now
	 */
	public function remove( $ids) {
		$placeholders = $this->generatePlaceholders($ids, '%d');

		$sql = $this->db->prepare("DELETE FROM {$this->table} WHERE id IN {$placeholders}", $ids);

		return $this->db->query($sql);
	}

	/**
	 * Add IN clause to query
	 *
	 * @param $column
	 * @param array $values
	 *
	 * @return $this
	 *
	 * @throws PinterestModelException
	 */
	public function in( $column, array $values) {
		$this->where .= $this->formatInNotIn($column, $values, 'IN');

		return $this;
	}

	/**
	 * Add NOT IN clause to query
	 *
	 * @param $column
	 * @param array $values
	 *
	 * @return $this
	 *
	 * @throws PinterestModelException
	 */
	public function notIn( $column, array $values) {
		$this->where .= $this->formatInNotIn($column, $values, 'NOT IN');

		return $this;
	}

	/**
	 * Prepare NOT IN query part
	 *
	 * @param string $column
	 * @param array $values
	 * @param int $operator 0 for NOT IN, 1 for IN
	 *
	 * @return string
	 *
	 * @throws PinterestModelException
	 */
	private function formatInNotIn( $column, array $values, $operator) {
		$operatorsWhiteList = array(
			'IN',
			'NOT IN'
		);

		if (! in_array($operator, $operatorsWhiteList, true)) {
			throw new PinterestModelException("Unknown sql operator \"{$operator}\" passed instead of one of allowed IN and NOT IN");
		}

		$whereString = $this->getWherePartTemplate();
		$values      = $values ? $values : array('');
		$query       = $whereString . $column . " {$operator} " . $this->generatePlaceholders($values);

		return $this->db->prepare($query, $values) . ' ';

	}

	/**
	 * Add WHERE part to query
	 *
	 * @param array $columns
	 *
	 * @param string $separator
	 *
	 * @return $this
	 */
	public function where( $columns, $separator = '=') {
		if (count($columns)) {
			$where = $this->implodeKeys($columns, ' ' . $separator . ' %s', ' AND ');

			$whereString = $this->getWherePartTemplate();

			$this->where .= $this->db->prepare($whereString . $where, $columns) . ' ';
		}

		return $this;
	}

	/**
	 * Add DISTINCT keyword to query
	 *
	 * @return $this
	 */
	public function distinct() {
		$this->distinct = ' DISTINCT ';

		return $this;
	}

	/**
	 * Add OFFSET to query
	 *
	 * @param int $offset
	 *
	 * @return $this
	 */
	public function offset( $offset) {
		$this->offset = $offset;

		return $this;
	}

	/**
	 * Add LIMIT to query
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit( $limit) {
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Add ORDER BY to query
	 *
	 * @param string $field
	 * @param string $type
	 *
	 * @return AbstractModel
	 */
	public function orderBy( $field = 'id', $type = 'DESC') {
		if ($field) {
			$this->order = " ORDER BY {$field} {$type} ";
		}

		return $this;
	}

	/**
	 * Return generated SQL code
	 *
	 * @param array|null $fields
	 *
	 * @return string
	 */
	public function getSql( $fields = null) {
		$sql = $this->select($fields) . ' FROM ' . $this->table . $this->where . $this->order . $this->limitOffset();

		return $sql;
	}


	/**
	 * Add SELECT to query
	 *
	 * @param string|array $fields
	 *
	 * @return string
	 */
	protected function select( $fields) {
		if (is_array($fields)) {
			$fields = implode(', ', $fields);
		} elseif (is_null($fields)) {
			$fields = '*';
		}


		return ' SELECT ' . $this->distinct . $fields . ' ';
	}

	/**
	 * Append LIMIT and OFFSET to query string
	 *
	 * @return string
	 */
	protected function limitOffset() {
		$offsetLimit = '';

		if ($this->limit > 0) {
			$offsetLimit .= " LIMIT {$this->limit} ";
		}
		if ($this->offset > 0) {
			$offsetLimit .= " OFFSET {$this->offset} ";
		}

		return $offsetLimit;
	}

	/**
	 * Sanitize rule and fill generated fields
	 *
	 * @param $array
	 *
	 * @return array
	 */
	protected function sanitize( array $array) {
		$data = array();
		foreach ($array as $key => $value) {
			$data[$key] = $this->sanitizeField($key, $value);
		}

		return $data;
	}


	/**
	 * Sanitize single field
	 *
	 * @param string $fieldName
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function sanitizeField( $fieldName, $value) {
		return esc_sql($value);
	}

	/**
	 * Reset built query
	 *
	 * @return $this
	 */
	protected function reset() {
		$this->where    = null;
		$this->offset   = null;
		$this->limit    = null;
		$this->order    = null;
		$this->distinct = null;

		return $this;
	}

	/**
	 * Implode keys
	 *
	 * @param array $columns
	 * @param string $separator
	 * @param string $and
	 *
	 * @return string
	 */
	protected function implodeKeys( $columns, $separator, $and) {
		return implode("{$separator}{$and}", array_keys($columns)) . $separator;
	}

	/**
	 * Generate placeholders to use in prepare()
	 *
	 * @param array $values
	 * @param string $placeholder
	 *
	 * @return string
	 */
	protected function generatePlaceholders( array $values, $placeholder = '%s') {
		return '(' . implode(', ', array_fill(0, count($values), $placeholder)) . ')';
	}

	/**
	 * Return template to build WHERE clause for query
	 *
	 * @return string
	 */
	private function getWherePartTemplate() {
		if (! $this->where) {
			$wherePartTemplate = ' WHERE ';
		} else {
			$wherePartTemplate = ' AND ';
		}

		return $wherePartTemplate;
	}
}
