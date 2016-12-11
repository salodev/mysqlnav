<?php

class NewTableWindow extends \cuif\Window {
	public function init(array $params = array()) {
		$this->maximize();
		$this->title = 'NEW TABLE';
		$this->createInputBox(2, 0, 'Name');
		$this->createListInputBox(2, 1, 'Storage Engine', array(
			'InnoDB',
			'MyISAM',
			'MEMORY',
			'CSV',
			'ARCHIVE',
			'EXAMPLE',
			'FEDERATED',
			'HEAP',
			'MERGE',
			'NDB',
		));
	}
}