<?php

namespace app\admin\model;

use think\Model;
use think\facade\View;

class Queue extends Model
{
    protected $table = 'onethink_system_queue';

	public function renderColumns(){
		return [
			['name' => 'id', 'title'=>'ID', 'type'=>'text'],
			['name' => 'info', 'title'=>'任务名称', 'type'=>'text'],
			['name' => 'plan', 'title'=>'任务计划', 'type'=>'text'],
			['name' => 'status', 'title'=>'任务状态','type'=>'text'],
			['name' => 'create_time', 'title'=>'创建时间', 'type'=>'text'],
			['name' => 'update_time', 'title'=>'更新时间', 'type'=>'text'],
		];
	}

	public function getInfoAttr($value, $data){

		$template = '
{:Yoyo\\\\yoyo_render("badge", ["text"=>$info["loops_time"]>0?"循":"单", "color"=>$info["loops_time"]>0?"info":"danger"])}
 任务编号：{$info.code}<br>
{:Yoyo\\\\yoyo_render("badge", ["text"=>$info["rscript"]==1?"复":"次", "color"=>$info["rscript"]==1?"success":"secondary"])} 任务名称：{$info.title}';
		return View::display($template, ['info'=>$data]);
	}

	public function getPlanAttr($value, $data){
		$template = <<<HTML
执行指令：{\$info.command}<br>
计划执行：{\$info.exec_time|date="Y-m-d H:i:s"}
{gt name="info.loops_time" value="0"}
( 每 <span class="text-primary"> {\$info.loops_time}</span> 秒 )
{else/}
<span class="text-secondary">( 单次任务 )</span>
{/gt}

HTML;
		return View::display($template, ['info'=>$data]);
	}
}
