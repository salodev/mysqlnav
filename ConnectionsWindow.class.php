<?php

class ConnectionsWindow extends cuif\Window {
	public function init(array $params = array()) {
		$this->x = 5;
		$this->y = 5;
		$this->width=64;
		$this->height=16;
		$this->title = 'CONEXIONES';
		$list = $this->list = $this->createObject('cuif\ListBox');
		$list->addColumn('Nombre','name', 15);
		$list->addColumn('Host','host', 15);
		$list->addColumn('Usuario','user', 15);
		$this->setToolKeys(array(
			'+'     => 'Nueva Conexion',
			'DEL'   => 'Borrar',
			'F2'    => 'Editar',
			'ENTER' => 'Login',
		));
		$this->refreshList();
		$this->keyPress('+', function() {
			$window = $this->_application->openWindow('AddConnectionWindow');
			$window->bind('saved', function($params, $source) {
				$this->_application->closeActiveWindow();
				$this->refreshList();
			});
		});
		$this->keyPress('F2', function() {
			$row = $this->list->getRowData();
			$window = $this->_application->openWindow('AddConnectionWindow', array(
				'name'=>$row['name'],
				'host'=>$row['host'],
				'user'=>$row['user'],
				'pass'=>$row['pass'],
				'db'  =>$row['db'  ],
			));
			$window->bind('saved', function($params, $source) {
				$this->_application->closeActiveWindow();
				$this->refreshList();
			});
		});
		$this->keyPress('RETURN', function() {
			$row = $this->list->getRowData();
			$connection = new \salodev\MysqlConnection($row['host'], $row['user'], $row['pass'], $row['db']);
			\cuif\CUIF::Log("CONEXION CREADA....");
			$connection->connect()->done(function() use($connection) {
				\cuif\CUIF::Log("LISTO.. VAMOS CON LA SIGUIENTE VENTANA...");
				$this->_application->openWindow('DatabasesListWindow', array(
					'connection' => $connection,
				));
			});
		});
	}
	
	public function refreshList() {
		$rs = $this->_application->getConnectionsList();
		$this->list->clear();
		foreach($rs as $row) {
			$this->list->addRow($row);
		}
	}
}