<?php
use \salodev\Timer;

class TableWindow extends \cuif\Window{
	/**
	 * Field,Type,Null,Key,Default,Extra
	 */
	public $columns = array();
	public $filters = array();
	public $limit = 100;
	public $offset = 0;
	public function init(array $params = array()) {
		$this->_connection = $params['connection'];
		$this->_tableName = $params['table'];
		
		/**
		 * Field
		 * Type
		 * Null
		 * Key
		 * Default
		 * Extra
		 */
		$this->x = 35;
		$this->y = 12;
		$this->title = $this->_tableName . ' TABLE';
		$this->maximize();
		/**
		 * @var ListBox
		 */
		$this->list = $this->createListBox();
		$this->list->setColumnSelectionMode();
		$this->_connection->query('DESCRIBE ' . $this->_tableName, function($rs) {
			$this->_columns = $rs;
			foreach($rs as $row) {
				$this->list->addColumn($row['Field'], $row['Field'], 15);
			}
			$this->updateList();
		});
		$this->keyPress('F2', function() {
			$window = $this->_application->openWindow('TableFilterFieldsWindow', [
				'fields' => $this->list->getColumns(),
			]);
			$window->bind('fieldSelected', function($fieldName) {
				$this->_application->openWindow('TableFilterWindow', [
					'tableWindow' => $this,
					'fieldName'   => $fieldName,
					'fieldValue'  => '',
				]);
			});
		});
		$this->keyPress('F3', function() {
			$column = $this->list->getCurrentColumn();
			$this->_application->openWindow('TableFilterWindow', [
				'tableWindow' => $this,
				'fieldName'   => $column->name,
				'fieldValue'  => $this->list->getRowData($column->name),
			]);
		});
	}
	
	public function addFilter($spec, $columnName, $value, $enabled = true) {
		$this->filters[] = new TableFilter($spec, $columnName, $value, $enabled);
	}
	
	public function getFilters() {
		return $this->filters;
	}
	
	public function clearFilters() {
		$this->filters = array();
	}
	
	public function updateList() {
		$wheres = array();
		foreach($this->filters as $filter) {
			if ($filter->enabled) {
				$wheres[] = $filter->getSQL();
			}
		}
		$sqlWhere = '';
		if (count($wheres)) {
			$sqlWhere = 'WHERE ' . implode(' AND ', $wheres);
		}
		$sql = "
			SELECT *
			FROM {$this->_tableName}
			{$sqlWhere}
			LIMIT {$this->offset}, {$this->limit}
		";
		$to = Timer::Timeout(function() {
			$msg = $this->msgWindow = $this->_application->openWindow();
			$msg->title = 'QUERYING...';
		}, 1000);
		$this->_connection->query($sql)->done(function($rs) {
			$this->list->setData($rs);
		})->always(function() use ($to) {
			Timer::Delete($to);
			if ($this->msgWindow instanceof \cuif\Window) {
				$this->msgWindow->close();
			}
		});
	}
}

class TableFilter {
	public $spec = null;
	public $columnName = null;
	public $value = null;
	public $enabled = true;
	public function __construct($spec, $columnName, $value, $enabled = true) {
		$this->spec       = $spec;
		$this->columnName = $columnName;
		$this->value      = $value;
		$this->enabled    = $enabled;
	}
	
	public function getSQL() {
		$spec       = $this->spec;
		$columnName = $this->columnName;
		$value      = addslashes($this->value);
		if ($spec=='=') {
			return "{$columnName} = '{$value}'";
		}
		if ($spec=='<>') {
			return "{$columnName} <> '{$value}'";
		}
		if ($spec=='>=') {
			return "{$columnName} >= '{$value}'";
		}
		if ($spec=='<=') {
			return "{$columnName} <= '{$value}'";
		}
		if ($spec=='LIKE') {
			return "{$columnName} LIKE '%{$value}%'";
		}
		if ($spec=='NOT LIKE') {
			return "{$columnName} NOT LIKE '%{$value}%'";
		}
		if ($spec=='MATCH') {
			return "MATCH({$columnName}) AGAINST('{$value}' IN BOOLEAN MODE)";
		}
		if ($spec=='NOT MATCH') {
			return "NOT MATCH({$columnName}) AGAINST('{$value}' IN BOOLEAN MODE)";
		}
		if ($spec=='IN' || $spec=='NOT IN') {
			$values = explode(',', $value);
			$nv = array();
			foreach($values as $v) {
				$nv[] = "'{$v}'";
			}
			$value = implode(',', $nv);
			if ($spec=='IN') {
				return "{$columnName} IN($value)";
			} else {
				return "{$columnName} NOT IN($value)";
			}
		}
		throw new Exception('Not allowed filter');
	}
}