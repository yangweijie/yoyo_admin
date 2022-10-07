<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;
use think\helper\Str;
class Table extends Component
{
	
	public $data       = [];

	public $column_width  = 'auto';
	public $header        = 'light'; // '' light dark
	public $columns       = [];
	public $striped       = false; 
	public $hoverable     = false;
	public $border        = false;
	public $min           = false;
	public $edit          = true;
	public $delete        = true;
	public $customActions = [];
	public $search        = false;
	public $checkble      = true;
	public $checked       = [];
	public $checkSort     = SORT_NUMERIC;
	public $pk            = 'id';
	public $model         = '';
	public $map           = [];
	public $list_rows     = 0; // -1 表示不分页
	public $paginate      = null;

	public function mount(){
		if(empty($this->model) && empty($this->data)){
			throw new \Exception("数据未空");
		}
		if(class_exists($this->model)){
			$model = new $this->model;
			if($this->list_rows != -1){
				$this->list_rows = $this->list_rows? : config('app.list_rows');
			}
			$fields = '*';
			if(method_exists($model, 'renderColumns')){
				$columns                 = $model->renderColumns();
				$fields                  = array_column($columns, 'name');
				$tableFields             = $model->getTableFields();
				$tableFieldNames         = array_column($tableFields, 'name');
				$temp                    = [];
				$appends                 = [];

				foreach ($fields as $key => $value) {
					if(in_array($value, $tableFieldNames)){
						$temp[] = $value;
					}else if(method_exists($model, 'get'.Str::studly($value).'Attr')){
						$appends[] = $value;
					}
				}
				if($fields !== $temp){
					$fields = $temp;
				}
			}
			if($this->list_rows == -1){
				$this->data = $this->model::where($this->map)->fields($fields)->append($appends)->orderBy($this->pk, 'asc')->select();
			}else{
				$this->data = $this->model::where($this->map)->fields($fields)->append($appends)->orderBy($this->pk, 'asc')->paginate($this->list_rows);
				$this->paginate = $this->data->render();
			}
		}
	}

	public function getActionProperty(){
		if(empty($this->model)){
			return false;
		}
		return $this->edit || $this->delete || $this->customActions;
	}

	public function getTheaderColorProperty(){
		switch($this->header){
			case 'light':
				$class = 'bg-gray-50';
				break;
			case 'dark':
				$class = 'bg-gray-800';
				break;
			default:
				$class = '';
		}
		return $class;
	}

	public function getRenderColumnProperty(){
		if($this->model && class_exists($this->model)){
			$model = new $this->model;
			if(method_exists($model, 'renderColumns')){
				return $model->renderColumns();
			}else{
				goto no_model;
			}
		}else{
			no_model:
			$columns = array_keys($this->data[0]);
			foreach ($columns as $key => $column) {
				$ret[] = ['name'=>$column, 'type'=>'text'];
			}
			return $ret;
		}
	}

	public function getRenderDataProperty(){
		if($this->model && class_exists($this->model)){
			$ret = [];

		}else{
			return $this->data;
		}
	}

	// 交互check
	public function checkToggle($key){
		if(in_array($this->checked, $key)){
			unset($this->checked[array_search($key, $this->checked)]);
		}else{
			$this->checked[] = $key;
		}
		sort($this->checked, $this->checkSort);
	}

	public function checkAll($check = true){
		if($check){
			$this->checked = array_column($this->data, $this->pk);
		}else{
			$this->checked = [];
		}
	}

	public function render()
	{
		return (String) $this->view('table', [
			'id'            => $this->getComponentId(),
			'action'        => $this->action,
			'theader_color'  => $this->theader_color,
			'render_data'   => $this->render_data,
			'render_column' => $this->render_column,
		]);
	}
}