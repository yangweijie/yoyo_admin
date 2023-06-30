<?php

namespace app\admin\controller;

use think\admin\model\SystemQueue;
use think\admin\service\ProcessService;
use think\admin\service\QueueService;
use think\exception\HttpResponseException;
use think\facade\View;

class Queue extends Admin
{
	public function index(){
		cookie('__forward__', $_SERVER['REQUEST_URI']);
		$data = [
			'title' => '系统任务',
			'iswin'=>ProcessService::iswin(),
			'command'=>ProcessService::think('xadmin:queue start'),
		];
		if (!$data['iswin'] && !empty($_SERVER['USER'])) {
			$data['command'] = "sudo -u {$_SERVER['USER']} {$data['command']}";
		}
		return View::fetch('', $data);
	}

	/**
	 * 停止监听服务
	 * @login true
	 */
	public function stop()
	{
		 try {
			$message = $this->app->console->call('xadmin:queue', ['stop'])->fetch();
			if (stripos($message, 'sent end signal to process')) {
				sysoplog('系统运维管理', '尝试停止任务监听服务');
				$this->success('停止任务监听服务成功！');
			} elseif (stripos($message, 'processes to stop')) {
				$this->success('没有找到需要停止的服务！');
			} else {
				$this->error(nl2br($message));
			}
		} catch (HttpResponseException $exception) {
			throw $exception;
		} catch (\Exception $exception) {
			trace_file($exception);
			$this->error($exception->getMessage());
		}
	}

	/**
	 * 启动监听服务
	 * @login true
	 */
	public function start()
	{
		try {
			$message = $this->app->console->call('xadmin:queue', ['start'])->fetch();
			if (stripos($message, 'daemons started successfully for pid')) {
				sysoplog('系统运维管理', '尝试启动任务监听服务');
				$this->success('任务监听服务启动成功！');
			} elseif (stripos($message, 'daemons already exist for pid')) {
				$this->success('任务监听服务已经启动！');
			} else {
				$this->error(nl2br($message));
			}
		} catch (HttpResponseException $exception) {
			throw $exception;
		} catch (\Exception $exception) {
			trace_file($exception);
			$this->error($exception->getMessage());
		}
	}

	/**
	 * 检查监听服务
	 * @login true
	 */
	public function status()
	{
		try {
			$message = $this->app->console->call('xadmin:queue', ['status'])->fetch();
			if (preg_match('/process.*?\d+.*?running/', $message)) {
				return $this->tooptip('success', '已启动', $message);
//				return "<span class='text-success pointer' data-tips-text='{$message}'>已启动</span>";
			} else {
				return $this->tooptip('danger', '未启动', $message);
//				return "<span class='text-danger pointer' data-tips-text='{$message}'>未启动</span>";
			}
		} catch (\Error|\Exception $exception) {
			return $this->tooptip('danger', '异 常', $exception->getMessage());
//			return "<span class='text-danger pointer' data-tips-text='{$exception->getMessage()}'>异 常</span>";
		}
	}

	private function tooptip($color,  $text, $body){
		return <<<HTML
<a
    href="#"
    class="yoyo-wrapper text-{$color}-600 hover:text-{$color}-700 transition duration-300 ease-in-out mb-4 "
    data-te-toggle="tooltip"
    title="{$body}"
    >{$text}</a
HTML;

	}

	/**
	 * 查询任务进度
	 * @login true
	 * @throws \think\admin\Exception
	 */
	public function progress($code)
	{
		$message = SystemQueue::mk()->where(['code'=>$code])->value('message', '');
		if (!empty($message)) {
			$this->success('获取任务进度成功！', '', json_decode($message, true));
		} else {
			$queue = QueueService::instance()->initialize($code);
			$this->success('获取任务进度成功！', '', $queue->progress());
		}
	}

	/**
	 * 清理运行数据
	 * @auth true
	 */
	public function test()
	{
		// $this->_queue('定时清理系统运行数据', "xadmin:queue clean", 0, [], 0, 3600);
		$this->_queue('测试', "test", 0, [], 0, 10);
	}

	/**
	 * 重启系统任务
	 * @auth true
	 */
	public function redo($code)
	{
		try {
			$data = ['code'=>$code];
			$queue = QueueService::instance()->initialize($data['code'])->reset();
			$queue->progress(1, '>>> 任务重置成功 <<<', 0.00);
			$this->success('任务重置成功！', '', $queue->code);
		} catch (HttpResponseException $exception) {
			throw $exception;
		} catch (\Exception $exception) {
			trace_file($exception);
			$this->error($exception->getMessage());
		}
	}
}