<?php

class TableFilterWindow extends \cuif\Window {
	/**
	 *
	 * @var TableWindow 
	 */
	private $_tableWindow;
	public function init(array $params = array()) {
		$this->color = '30;42';
		$this->_tableWindow = $params['tableWindow'];
		$w = $this->_tableWindow;
		$this->x = $w->x + 10;
		$this->y = $w->y + $w->list->getRowInfo()->relativeVPos + 5;
		$this->width = 100;
		$this->columnName = $params['fieldName'];
		$this->title = 'FILTER BY ' . $this->columnName . ' [PRESS <F2> TO CHANGE IT]';
		$value = $params['fieldValue'];
		$y = 0;
		$tabIndex = 0;
		$filters = $this->filters = $w->getFilters();
		if (count($filters)) {
			$tabIndex++;
			$this->createLabelBox(1, $y++, 'APPLIED FILTERS: [<INS> to ENABLE/DISABLE, <DEL> to REMOVE]');
			$y++;
			$this->height+=2;
		}
		foreach($this->filters as &$filter) {
			$tabIndex++;
			$ib = $this->createInputBox(1, $y++, $filter->columnName . ' ' . $filter->spec, $filter->value);
			$ib->filter = $filter;
			$ib->postLabel = ($filter->enabled?'':'[DISABLED]');
			$this->height++;
		}
		if (count($filters)) {
			$b = $this->createButton(1, $y++, 'CLEAR ALL');
			$tabIndex++;
			$y++;
			$this->height+=2;
			$b->bind('press', function() use ($w) {
				$w->clearFilters();
				$this->close();
				$w->updateList();
			});
		}
		$this->createInputBox(1, $y++, '=        ', $value);
		$this->createInputBox(1, $y++, '<>       ', $value);
		$this->createInputBox(1, $y++, '>=       ', $value);
		$this->createInputBox(1, $y++, '<=       ', $value);
		$this->createInputBox(1, $y++, 'LIKE     ', $value);
		$this->createInputBox(1, $y++, 'NOT LIKE ', $value);
		$this->createInputBox(1, $y++, 'MATCH    ', $value);
		$this->createInputBox(1, $y++, 'NOT MATCH', $value);
		$this->createInputBox(1, $y++, 'IN       ', $value);
		$this->createInputBox(1, $y++, 'NOT IN   ', $value);
		list(,$screenHeight) = \cuif\Screen::GetInstance()->getDimensions();
		if ($this->y+$this->height>$screenHeight-3) {
			$this->y = $this->y-$this->height-5;
			if ($this->y <1) {
				$this->y = 1;
			}
		}
		$this->setTabStop($tabIndex);
		$this->render();
		$this->keyPress('RETURN', function () use ($w) {
			$fo = $this->getFocusedObject();
			if (!($fo instanceof \cuif\InputBox)) {
				return;
			}
			if ($fo->filter && $fo->filter instanceof TableFilter) {
				$fo->filter->value = $fo->value;
				$w->clearFilters();
				foreach($this->filters as $filter) {
					$w->addFilter($filter->spec, $filter->columnName, $filter->value);
				}
			} else {
				$filterSpec = trim($fo->label);
				$filterValue = trim($fo->value);
				$w->addFilter($filterSpec, $this->columnName, $filterValue);
			}
			$this->close();
			$w->updateList();
		});
		$this->keyPress('INSERT' , function() use ($w) {
			$fo = $this->getFocusedObject();
			if (!($fo instanceof \cuif\InputBox)) {
				return;
			}
			if ($fo->filter && $fo->filter instanceof TableFilter) {
				$fo->filter->enabled = !$fo->filter->enabled;
				$w->clearFilters();
				foreach($this->filters as $filter) {
					$w->addFilter($filter->spec, $filter->columnName, $filter->value, $filter->enabled);
				}
				$this->close();
				$w->updateList();
			}
			
		});
		$this->keyPress('DELETE' , function() use ($w) {
			$fo = $this->getFocusedObject();
			if (!($fo instanceof \cuif\InputBox)) {
				return;
			}
			if ($fo->filter && $fo->filter instanceof TableFilter) {
				$w->clearFilters();
				foreach($this->filters as $filter) {
					if ($filter !== $fo->filter) {
						$w->addFilter($filter->spec, $filter->columnName, $filter->value, $filter->enabled);
					}
				}
				$this->close();
				$w->updateList();
			}
			
		});
		$this->keyPress('F2', function() use ($w) {
			$wfl = $this->_application->openWindow('TableFilterFieldsWindow', array(
				'fields' => $w->list->getColumns(),
			));
			$wfl->bind('fieldSelected', function($columnName) {
				$this->columnName = $columnName;
				$this->title = 'FILTER BY ' . $this->columnName . ' [PRESS <F2> TO CHANGE IT]';
			});
		});
	}
}