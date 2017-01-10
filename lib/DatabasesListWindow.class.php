<?php

class DatabasesListWindow extends cuif\Window {
	private $_connection = null;
	public function init(array $params = array()) {
		$this->_connection = $params['connection'];
		$this->list = $this->createListBox();
		$this->list->addColumn('Database', 'Database', 30);
		$this->setToolKeys(array(
			'ENTER' => 'Listar Tablas',
			'F7'    => 'Crear BBDD',
			'ESC'   => 'Cerrar',
		));
		$this->keyPress('RETURN', function() {
			$this->openWindow('TablesListWindow', array(
				'database' => $this->list->getRowData('Database'),
				'connection' => $this->_connection,
			));
		});
		$this->keyPress('+|F7', function() {
			$this->_application->promptWindow('Create Database', 'Name', null, function($name) {
				$this->_connection->query("CREATE DATABASE {$name};", function() use ($name) {
					$this->updateList();
					$connection = $this->_connection->duplicate();
					$connection->connect()->done(function() use ($name, $connection) {
						$connection->selectDB($name);
						$this->openWindow('TablesListWindow', array(
							'database'   => $name,
							'connection' => $connection,
						));
					});
				});
			});
		});
		$this->updateList();
	}
	
	/**
	 * 
	 * @return \salodev\Deferred
	 */
	public function updateList() {
		return $this->_connection->query('SHOW DATABASES', function($rs) {
			$this->list->setData($rs);
		});
	}
}