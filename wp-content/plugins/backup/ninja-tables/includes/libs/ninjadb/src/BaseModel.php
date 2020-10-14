<?php namespace NinjaDB;

class BaseModel
	{
		// import Modal Trait
		use ModelTrait;

		/**
		 * @var string
		 */
		private $selected_table;

		/**
		 * @var string
		 */
		protected $table;
		/**
		 * @var $wpdb
		 */
		private $db;
		/**
		 * @var array
		 */
		protected $statements = array();
		/**
		 * @var array
		 */
		protected $bindings = array();
		/**
		 * @var array
		 */
		private $lastQueryInfo = array();
		/**
		 * @var string
		 */
		protected $outputType = 'OBJECT';

		/**
		 * BaseModel constructor.
		 */
		public function __construct($table = false) {
			global $wpdb;
			$this->db = $wpdb;
			if($table) {
				$this->selected_table = $wpdb->prefix.$table;
			} else {
				$this->selected_table = $wpdb->prefix.$this->table;
			}
			
		}

		/**
		 * @return array|null|object
		 */
		public function get() {
			$result = $this->db->get_results( $this->getSqlStatement(), $this->outputType );
			$this->setLastQueryInfo();
			$this->reset();
			return $result;
		}

	/**
	 * @param string $column
	 * @param mixed $value
	 *
	 * @return array|null
	 */
	public function findAll($column, $value) {
			$this->where($column, $value);
			return $this->get();
		}

		/**
		 * @return array|null|object
		 */
		public function pluck() {
			$selects = func_get_args();
			$this->select($selects);
			return $this->get();
		}

		/**
		 * @return null|integer
		 */
		public function count() {
			$countSql =$this->getSqlStatement('count');
			$count = $this->db->get_var( $countSql );
			return $count;
		}

		/**
		 * @param string $field
		 *
		 * @return null|integer
		 */
		public function max($field) {
			$count = $this->db->get_var( $this->getSqlStatement('max', $field));
			return $count;
		}

		/**
		 * @param string $field
		 *
		 * @return null|integer
		 */
		public function min($field) {
			$count = $this->db->get_var( $this->getSqlStatement('min', $field));
			return $count;
		}

		/**
		 * @param $field
		 *
		 * @return null|float
		 */
		public function avg($field) {
			$count = $this->db->get_var( $this->getSqlStatement('avg', $field));
			return $count;
		}

		/**
		 * @param $field
		 *
		 * @return null|integer|float
		 */
		public function sum($field) {
			$count = $this->db->get_var( $this->getSqlStatement('sum', $field));
			return $count;
		}

		/**
		 * @param $value
		 * @param string $filed
		 *
		 * @return bool|mixed
		 */
		public function find($value, $filed = 'id') {
			$this->where($filed, $value);
			return $this->first();
		}

		/**
		 * @return bool|mixed
		 */
		public function first() {
			$this->limit(1);
			$result = $this->get();
			if($result)
				return $result[0];
			return false;
		}

		/**
		 * @param array $data
		 * @param bool|array $format
		 *
		 * @return int
		 */
		public function insert($data, $format = false) {
			$this->db->insert($this->selected_table, $data, $format);
			$insertId = $this->db->insert_id;
			$this->reset();
			return $insertId;
		}

		/**
		 * @param array $datas
		 * @param bool|array $format
		 *
		 * @return array
		 */
		public function batch_insert($datas, $format = false) {
			$insertIds = array();
			foreach ($datas as $data) {
				$insertIds[] = $this->insert($data, $format);
			}
			return $insertIds;
		}

		/**
		 * @param array $data
		 *
		 * @return bool|false|int
		 */
		public function update($data) {
			if(isset($this->statements['wheres']) && count($this->statements['wheres'])) {

				$whereArray = array();
				$bindings = array();
				foreach ($this->statements['wheres'] as $where) {
					$whereArray[$where['key']] = $where['value'];
					$bindings[] = $this->getValuePlaceholder($where['value']);
				}

				$result = $this->db->update($this->selected_table, $data, $whereArray, null, $bindings);
				$this->reset();
				if(false === $result) {
					return $this->throwError($this->db->last_error);
				}
				return $result;
			} else {
				return $this->throwError(__("Where clause does not exist, Please add where clause first", 'ninjadb'));
			}
		}

		/**
		 * @param bool|integer $indexID
		 * @param string $filed
		 *
		 * @return bool|false|int
		 */
		public function delete($indexID = false, $filed = 'id') {
			
			if($indexID) {
				$result = $this->db->delete($this->selected_table, array($filed => $indexID));
				$this->reset();
				return $result;
			}
			
			if(isset($this->statements['wheres']) && count($this->statements['wheres'])) {
				$whereArray = array();
				$bindings   = array();
				foreach ( $this->statements['wheres'] as $where ) {
					$whereArray[ $where['key'] ] = $where['value'];
					$bindings[] = $this->getValuePlaceholder( $where['value'] );
				}
				$result = $this->db->query($this->prepare('delete'));
				$this->reset();
				return $result;
			} else {
				return $this->throwError(__("Where clause does not exist, Please add where clause first", 'ninjadb'));
			}

		}

	}