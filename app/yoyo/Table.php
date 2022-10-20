<?php

namespace app\yoyo;

use app\admin\model\Module;
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
	public $min           = false; // 是否窄表格
	public $add           = false;  // 是否显示添加按钮
	public $edit          = false;
	public $delete        = false;
	public $pageQuery     = []; // 分页时带的参数
	public $customActions = [];
	public $search        = false;
	public $keyword       = '';
	public $search_keys   = [];
	public $checkble      = false;
	public $check         = 1;
	public $checked       = [];
	public $checkSort     = SORT_NUMERIC;
	public $pk            = 'id';
	public $model         = ''; // 模型命名空间
	public $map           = [];
	public $list_rows     = 0; // -1 表示不分页
	public $paginate      = null;
	public $request       = null;
	public $props         = ['data', 'checked', 'model', 'search_keys'];

	public function mount(){
		$this->request = request();
		if(empty($this->model) && empty($this->data)){
			throw new \Exception("数据未空");
		}
		if(class_exists($this->model)){
			$model = new $this->model;
			if($this->list_rows != -1){
				$this->list_rows = $this->list_rows? : config('app.list_rows');
			}
			// 模糊搜索
			$this->keyword = trim($this->keyword);
			if($this->keyword && $this->search_keys){
				$this->map[] = [
					implode('|', $this->search_keys),
					'like',
					"%{$this->keyword}%"
				];
			}
			$fields = '*';
			if($this->list_rows == -1){
				$this->data = $this->model::where($this->map)->field($fields)->order($this->pk, 'asc')->select();
			}else{
				if($this->keyword){
					$this->pageQuery = array_merge($this->pageQuery, ['keyword'=>$this->keyword]);
				}
				$this->data = $this->model::where($this->map)->field($fields)->order($this->pk, 'asc')->paginate(['list_rows'=>$this->list_rows, 'query'=>$this->pageQuery]);
				$this->paginate = $this->data->render();
			}
			$appends     = [];
			if(method_exists($model, 'renderColumns')){
				$columns     = $model->renderColumns();
				$rawFields   = array_column($columns, 'name');
				$tableFields = $model->getTableFields();
				foreach ($rawFields as $key => $value) {
					if(in_array($value, $tableFields)){
						;
					}else if(method_exists($model, 'get'.Str::studly($value).'Attr')){
						$appends[] = $value;
					}
				}
			}
			if(!$this->data->isEmpty()){
				foreach ($this->data as $key => $row) {
					$tempRow = [];
					foreach ($rawFields as $f) {
						if(isset($row[$f])){
							$tempRow[$f] = $row[$f];
						}else{
							if($appends && in_array($f, $appends)){
								$funName     = 'get'.Str::studly($f).'Attr';
								$tempRow[$f] = $model->$funName('', $row);
							}else{
								$tempRow[$f] = '';
							}
						}
					}
					$this->data[$key] = $tempRow;
				}
			}
		}
	}

	public function getActionProperty(){
		return $this->edit || $this->delete || $this->customActions || $this->search_keys;
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

	public function getSearchLabelProperty(){
		if($this->search_keys){
			$columns      = array_column($this->render_column, 'title', 'name');
			$search_title = [];
			foreach ($this->search_keys as $key => $value) {
				if(isset($columns[$value])){
					$search_title[] = $columns[$value];
				}
			}
			return implode('|', $search_title);
		}else{
			return '搜索';
		}
	}

	public function getTableTokenProperty()
	{
		if(empty($this->model)){
			return '';
		}
		$model = new $this->model;
		$data = [
			'table'      => trim($model->getName()), // 表名或模型名
			'prefix'     => 1,
			'module'     => MODULE,
			'controller' => strtolower($this->request->controller()),
			'action'     => $this->request->action(),
		];

		$table_token = substr(sha1($data['module'].'-'.$data['controller'].'-'.$data['action'].'-'.$data['table']), 0, 8);
		session($table_token, $data);
		return $table_token;
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
			if(is_string($this->data[0])){
				$columns = array_keys(json_decode($this->data[0], true));
			}else{
				$columns = array_keys($this->data[0]);
			}
			foreach ($columns as $key => $column) {
				$ret[] = ['name'=>$column, 'title'=>$column, 'type'=>'text'];
			}
			return $ret;
		}
	}

	public function getRenderDataProperty(){
		$ret = [];
		if($this->model && class_exists($this->model)){
			foreach ($this->data as $key => $value) {
				if(is_string($value)){
					$ret[] = json_decode($value, true);
				}else{
					$ret[] = $value;
				}
			}
		}else{
			foreach ($this->data as $key => $value) {
				if(is_string($value)){
					$ret[] = json_decode($value, true);
				}else{
					$ret[] = $value;
				}
			}
			$modify = [];
			foreach ($this->data as $key => $value) {
				if(is_string($value)){
					$modify[] = json_decode($value, true);
				}else{
					$modify[] = $value;
				}
			}
			if($this->keyword && $this->search_keys){
				$search_values = [];
				foreach ($this->search_keys as $key => $value) {
					$search_values[] = "/{$this->keyword}/i";
				}
				$ret = list_search($modify, array_combine($this->search_keys, $search_values));
			}
		}
		return $ret;
	}

	// 交互check
	public function checkToggle($key = ''){
		if(in_array($key, $this->checked)){
			unset($this->checked[array_search($key, $this->checked)]);
		}else{
			$this->checked[] = $key;
		}
		sort($this->checked, $this->checkSort);
		if(count($this->checked) == count($this->render_data)){
			$this->check = 0;
		}else{
			$this->check = 1;
		}
	}

	public function checkAll($check = 1){
		if($check){
			$this->checked = array_column($this->render_data, $this->pk);
			$this->check   = 0;
		}else{
			$this->checked = [];
			$this->check   = 1;
		}
	}

	public function render()
	{
		return (String) $this->view('table', [
			'id'            => $this->getComponentId(),
			'action'        => $this->action,
			'theader_color' => $this->theader_color,
			'render_column' => $this->render_column,
			'render_data'   => $this->render_data,
			'search_label'  => $this->search_label,
			'table_token'   => $this->table_token,
		]);
	}
}