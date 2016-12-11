<?php

class TableFilterFieldsWindow extends \cuif\Window {
	public function init(array $params = array()) {
		$this->title  = 'SELECT AND PRESS <RETURN>';
		$this->height = 16;
		$this->width  = 50;
		$this->y      = 7;
		$this->x      = 58;
		$this->setManualFocus();
		$s = $this->createInputBox(0, 0, 'FIELD NAME');
		$l = $this->createListBox();
		$l->y = 1;
		$fields = $params['fields'];
		$l->addColumn('Field Name', 'name', 64);
		foreach($fields as $field) {
			$l->addRow((array)$field);
		}
		$this->keyPress('RETURN', function() use($l) {
			$fieldName = $l->getRowData('name');
			$this->close();
			$this->trigger('fieldSelected', $fieldName);
		});
		$this->bind('keyPress', function(\cuif\Input $input) use ($s, $l) {
			if (in_array($input->spec, [
				'ARROW_UP',
				'ARROW_DOWN',
				'ARROW_LEFT',
				'ARROW_RIGHT',
				'HOME',
				'END',
				'PAGE_UP',
				'PAGE_DOWN',
				'RETURN',
			])) {
				$l->input($input);
			} else {
				$s->input($input);
			}
		});
		$this->render();
	}
}