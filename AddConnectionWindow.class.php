<?php

class AddConnectionWindow extends cuif\Window {

    public function init(array $params = array()) {
		$this->x = 15;
		$this->y = 15;
        $this->title = 'AGREGAR/EDITAR CONEXION';
        $this->width = 64;
        $this->login = array(
            'host' => 'localhost',
            'db' => '',
            'user' => '',
            'pass' => '',
        );
        
        $this->inputName = $this->createInputBox(2, 1, 'Nombre    ', $params['name']);
        $this->inputHost = $this->createInputBox(2, 2, 'Host / IP ', $params['host']);
        $this->inputDB   = $this->createInputBox(2, 3, 'BBDD opc  ', $params['db'  ]);
        $this->inputUser = $this->createInputBox(2, 4, 'Usuario   ', $params['user']);
        $this->inputPass = $this->createInputBox(2, 5, 'Clave     ', $params['pass'], '*');
        $window = $this;
		$this->buttonLogin = $this->createButton(2, 7, 'GUARDAR',  function() use ($window) {
			
			//$name, $host, $user, $pass, $db
			$window->_application->storeNewConnection(
				$window->inputName->value,
				$window->inputHost->value,
				$window->inputUser->value,
				$window->inputPass->value,
				$window->inputDB->value
			);
			$this->trigger('saved');
		});
		$this->buttonCancel  = $this->createButton(15, 7, 'VOLVER', function() use ($window) {
			
		});
        $this->setTabStop(0);
    }
}