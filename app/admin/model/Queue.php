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
			['name' => 'title', 'title'=>'任务名称', 'type'=>'hidden'],
			['name' => 'code', 'title'=>'任务编号', 'type'=>'hidden'],
			['name' => 'info', 'title'=>'任务名称', 'type'=>'text'],
			['name' => 'plan', 'title'=>'任务计划', 'type'=>'text'],
			['name' => 'status', 'title'=>'任务状态','type'=>'text'],
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

	public function getStatusAttr($value, $data){
		switch ($data['status']){
			case 0:
				$status_text = '未知';
				$color = 'secondary';
				break;
			case 1:
				$status_text = '等待';
				$color = 'dark';
				break;
			case 2:
				$status_text = '执行';
				$color = 'primary';
				break;
			case 3:
				$status_text = '完成';
				$color = 'success';
				break;
			default:
				$status_text = '失败';
				$color = 'danger';
				break;
		}
		$badge = yoyo_render('badge', ['text'=>$status_text, 'color'=>$color]);
		$data['enter_time'] = $data['enter_time']?:'';
		$data['outer_time'] = $data['outer_time']?:'0.0000';
		$template = <<<HTML
<br>
<span class="text-primary">
{\$badge|raw} <br>执行时间：
{gt name="info.enter_time|strlen" value="12"}
{\$info.enter_time|sub_string=XXX,0,12}
<span class="text-info"> ( {\$info.outer_time}  ) </span> 已执行 <b class="text-primary"> {\$info.attempts|default=0}</b> 次
{else /}
<br>
<span class="text-info">任务未执行</span>
{/gt}
{notempty name="info.exec_desc"}
<a
    href="#"
    class="yoyo-wrapper text-blue-600 hover:text-blue-700 transition duration-300 ease-in-out mb-4 "
    data-te-toggle="tooltip"
    title="{\$info['exec_desc']}"
    >{:substr(\$info['exec_desc'], 0, 30)}</a
{else /}
<span class="text-info">未获取到执行结果</span>
{/notempty}
</span>

HTML;
		return View::display($template, ['info'=>$data, 'badge'=>$badge]);
	}
}
