<?php namespace NinjaDB;

trait ModelTrait {

	/**
	 * @param string $table
	 * @usage ninjaDB()->table('post')
	 * @return $this
	 */
	public function table($table) {
		$this->selected_table = $this->db->prefix.$table;
		return $this;
	}

	/**
	 * @param array $selects
	 * @usage ninjaDB()->select([])
	 * @return $this
	 */
	public function select($selects) {
		if(is_array($selects)) {
			if(isset($this->statements['selects'])) {
				$this->statements['selects'] = array_merge($this->statements['selects'], $selects);
			} else {
				$this->statements['selects'] = $selects;
			}
		} else {
			$this->statements['selects'][] = $selects;
		}

		return $this;
	}

    /**
     * @param array $selects
     *
     * @usage ninjaDB()->selectDistinct([])
     *
     * @return $this
     */
    public function selectDistinct($selects) {
        $this->select($selects);

        $this->statements['distinct'] = true;

        return $this;
    }

	/**
	 * @param string|array $key
	 * @param string $operator
	 * @param null $value
	 * @usage ninjaDB()->where('id', 1)
	 * @usage ninjaDB()->where('id', '>', 1)
	 * @return $this
	 */
	public function where($key, $operator = '=', $value = null) {

		if(is_array($key)) {
			foreach ($key as $field) {
				$this->whereHandler($field[0], $field[1], $field[2]);
			}
			return $this;
		}

		if (func_num_args() == 2) {
			$value = $operator;
			$operator = '=';
		}

		$this->whereHandler($key, $operator, $value);
		return $this;
	}

	/**
	 * @param string $key
	 * @param string $operator
	 * @param null $value
	 * @param bool $isSearch
	 * @usage ninjaDB()->orWhere('id', 1)
	 * @usage ninjaDB()->orWhere('id', '>', 1)
	 *
	 * @return $this
	 */
	public function orWhere($key, $operator = '=', $value = null, $isSearch = false) {

		if(is_array($key)) {
			foreach ($key as $field) {
				$this->whereHandler($field[0], $field[1], $field[2], 'OR', $isSearch);
			}
			return $this;
		}

		if (func_num_args() == 2) {
			$value = $operator;
			$operator = '=';
		}

		$this->whereHandler($key, $operator, $value, 'OR', $isSearch);
		return $this;
	}

    /**
     * @param       $key
     * @param array $values
     *
     * @return $this
     */
    public function whereIn($key, $values)
    {
        return $this->where($key, 'IN', $values);
    }


    /**
     * @param string|array $key
     * @usage ninjaDB()->whereNotNull('id')
     * @return $this
     */
    public function whereNotNull($key) {
        $this->whereHandler($key, 'IS NOT', 'NULL');
        return $this;
    }

    /**
     * @param string|array $key
     * @usage ninjaDB()->whereNull('id')
     * @return $this
     */
    public function whereNull($key) {
        $this->whereHandler($key, 'IS', 'NULL');
        return $this;
    }


    /**
	 * @param $string
	 * @param bool|array $columns
	 * @usage ninjaDB()->search('search_string')
	 * @usage ninjaDB()->search('search_string', ['post_content', 'post_title'])
	 * @return $this
	 */
	public function search($string, $columns = false) {
		if(!$columns) {
			$columns = $this->db->get_col("DESC {$this->selected_table}", 0);
		}
		foreach ($columns as $column) {
			$this->orWhere($column, 'LIKE', '%'.$string.'%', true);
		}
		return $this;
	}

	/**
	 * @param string $key
	 * @param null|string $operator
	 * @param null|string|integer|mixed $value
	 * @param string $joiner
	 * @param bool $is_search
	 * @return $this
	 */
	public function whereHandler($key, $operator = null, $value = null, $joiner = 'AND', $is_search = false)
	{
		$this->statements['wheres'][] = compact('key', 'operator', 'value', 'joiner', 'is_search');
		return $this;
	}

	/**
	 * @param integer $limit
	 * ninjaDB()->limit(5)
	 * @return $this
	 */
	public function limit($limit) {
		$this->statements['limit'] = 'LIMIT ' . intval($limit);
		return $this;
	}

	/**
	 * @param integer $offset
	 * ninjaDB()->offset(10')
	 * @return $this
	 */
	public function offset($offset) {
		$this->statements['offset'] = 'OFFSET ' . intval($offset);
		return $this;
	}

	/**
	 * @param integer $take
	 * ninjaDB()->take(5)
	 * @return $this
	 */
	public function take($take) {
		return $this->limit($take);
	}

	/**
	 * @param integer $skip
	 * ninjaDB()->skip(5)
	 * @return $this
	 */
	public function skip($skip) {
		return $this->offset($skip);
	}

	/**
	 * @param string $field
	 * @param string $type
	 * ninjaDB()->orderBy('id', 'ASC')
	 * @return $this
	 */
	public function orderBy($field, $type = 'DESC') {
		$this->statements['order_by'] = 'ORDER BY '. $field . ' ' .$type;
		return $this;
	}

	/**
	 * @description: Get sql from builder
	 * @param string $type
	 *
	 * @return string
	 * @internal param $ : $type
	 */
	public function getSQL($type = 'select') {
		$wheres = '';
		if( isset($this->statements['wheres']) ) {
			$wheres = $this->handleWheres($this->statements['wheres']);
		}
		$typeArray = array();
		if($type == 'select') {
			$typeArray = array(
				'SELECT'.(isset($this->statements['distinct']) ? ' DISTINCT' : ''),
				isset($this->statements['selects']) ? implode(', ', $this->statements['selects']) : '*',
				'FROM'
			);
		} else if($type == 'delete') {
			$typeArray = array(
				'DELETE',
				'FROM'
			);
		}

		$sqlArray = array(
			$this->selected_table,
			$wheres,
			isset($this->statements['order_by']) ? $this->statements['order_by'] : '',
			isset($this->statements['limit']) ? $this->statements['limit'] : '',
			isset($this->statements['offset']) ? $this->statements['offset'] : '',
		);

		$sqlArray = array_merge($typeArray, $sqlArray);

		return $this->concatSQLArray($sqlArray);
	}

	/**
	 * @param string $type
	 * @param bool | string $field
	 * @description: Get prepared sql statement
	 * @return bool|string
	 */
	public function getSqlStatement( $type = 'query', $field = false ) {
		$statement = false;
		if($type == 'query') {
			return $this->prepare('select');
		}
		else if($type == 'count' ) {
			$oldStatements = $this->statements;
			unset($this->statements['order_by']);
			unset($this->statements['limit']);
			unset($this->statements['offset']);
			$this->select(['COUNT(*)']);
			$statement = $this->prepare();
			$this->setLastQueryInfo();
			$this->statements = $oldStatements;
		}
		else if ($type == 'max') {
			$oldStatements = $this->statements;
			$this->orderBy($field, 'DESC');
			$this->select(['MAX('.$field.')']);
			$statement = $this->prepare();
			$this->setLastQueryInfo();
			$this->statements = $oldStatements;
		}
		elseif ($type == 'min') {
			$oldStatements = $this->statements;
			$this->orderBy($field, 'ASC');
			$this->select(['MIN('.$field.')']);
			$statement = $this->prepare();
			$this->setLastQueryInfo();
			$this->statements = $oldStatements;
		}
		else if ($type == 'avg') {
			$oldStatements = $this->statements;
			unset($this->statements['order_by']);
			$this->select(['AVG('.$field.')']);
			$statement = $this->prepare();
			$this->setLastQueryInfo();
			$this->statements = $oldStatements;
		}
		else if ($type == 'sum') {
			$oldStatements = $this->statements;
			unset($this->statements['order_by']);
			$this->select(['SUM('.$field.')']);
			$statement = $this->prepare();
			$this->setLastQueryInfo();
			$this->statements = $oldStatements;
		}
		return $statement;
	}

	/**
	 * GET SQL
	 * @return string
	 */
	public function prepare($type = 'select') {
		$sql = $this->getSQL($type);
		if(count($this->bindings)) {
			$sql = $this->db->prepare( $sql, $this->bindings );
		}
		return $sql;
	}

	/**
	 * @param array $sqlArray
	 * @description: convert array into string for SQL
	 * @return string
	 */
	public function concatSQLArray($sqlArray) {
		$str = '';
		foreach ($sqlArray as $piece) {
			$str = trim($str) . ' ' . trim($piece);
		}
		return trim($str);
	}

	/**
	 * @param array $wheres
	 * @param bool $joinWhere
	 * handle wheres and set bindings
	 * @return mixed|string
	 */
	public function handleWheres($wheres, $joinWhere = true) {
		$criteria = '';
		$bindings = array();
		$searchWheres = array();
		foreach ($wheres as $statement) {
			// find wheres in search

			if($statement['is_search']) {
				$searchWheres[] = array(
					'joiner' => $statement['joiner'],
					'key' => $statement['key'],
					'value' => $statement['value'],
					'operator' => $statement['operator'],
					'is_search' => false
				);
			} elseif ($statement['operator'] == 'IN') {
                $values = is_array($statement['value']) ? $statement['value'] : array($statement['value']);

                $criteria .= $statement['joiner'].' '.$statement['key'].' '.$statement['operator'].' ('
                             .implode(', ', array_fill(0, count($values), '%s')).')';

                $this->bindings = array_merge($this->bindings, $values);
            } else if($statement['value'] == 'NULL') {
                $criteria .= $statement['joiner'] . ' ' . $statement['key'] . ' ' . $statement['operator'] . ' ' . 'NULL';
            } else {
				$valuePlaceholder = $this->getValuePlaceholder($statement['value']);
				$criteria .= $statement['joiner'] . ' ' . $statement['key'] . ' ' . $statement['operator'] . ' '
				             . $valuePlaceholder . ' ';
				$this->bindings[] = $statement['value'];
			}
		}

		if(count($searchWheres)) {
			$criteria .= 'AND ( '.$this->handleWheres($searchWheres, false).' ) ';
		}
		$criteria = preg_replace('/^(\s?AND ?|\s?OR ?)|\s$/i', '', $criteria);

		if($criteria) {
			if($joinWhere) {
				return 'WHERE '.$criteria;
			}
			return $criteria;
		}
		return '';
	}

	/**
	 * @param string | float | integer $value
	 * @description: detect value type and return mysql binding placeholder
	 * @return string
	 */
	public function getValuePlaceholder($value) {
		// For wheres
		$valuePlaceholder = '%s';
		if(is_int($value)) {
			$valuePlaceholder = '%d';
		} else if(is_float($value)) {
			$valuePlaceholder = '%f';
		}
		return $valuePlaceholder;
	}

	/**
	 * Set SQL QUERY info if WP_DEBUG is true
	 * @return void
	 */
	public function setLastQueryInfo() {
		if(defined('WP_DEBUG') && WP_DEBUG) {
			$this->lastQueryInfo = array(
				'table' => $this->selected_table,
				'statements' => $this->statements,
				'bindings' => $this->bindings,
//				'sql' => $this->getSQL(),
//				'sql_statement' => $this->getSqlStatement(),
				'outputType' => $this->outputType
			);
		}
	}

	/**
	 * @description: get last query info
	 * @return array
	 */
	public function getLastQueryInfo() {
		return $this->lastQueryInfo;
	}

	/**
	 * @description: Get Selected table name
	 * @return string
	 */
	public function getSelectedTable() {
		return $this->selected_table;
	}


	/**
	 * Reset the query object
	 * @return void
	 */
	public function reset() {
		$this->outputType = 'OBJECT';
		$this->statements = array();
		$this->bindings = array();
	}

	/**
	 * @param string $errorMessage
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function throwError($errorMessage = 'Something is wrong') {
		if(WP_DEBUG) {
			throw new \Exception($errorMessage);
		}
		return false;
	}
}
