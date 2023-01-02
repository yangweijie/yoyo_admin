<?php
namespace app\listener;

use think\Collection;
class SqlWatch{

	/**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
    	if($event->type == 'sql'){
    		$sql       = $event->message;
    		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    		$backtrace = new Collection($backtrace);
            $ret = $backtrace->filter(function ($trace) use ($sql) {
                $file = $trace['file']??'';
                return $file && !str_contains($file, 'vendor') && !str_contains($sql, 'SHOW FULL COLUMNS') && !str_contains($sql, 'CONNECT:[');
            });
            // trace($ret->first());
            if($ret && $ret->first()){
	            $ds            = ds();
	            $ds->backtrace = $ret->first();
	            $ds->write($sql);
            }
    	}
    }
}