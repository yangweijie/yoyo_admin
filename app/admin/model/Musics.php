<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Musics extends Model
{
    protected $connection = 'music';

	public function renderColumns(){
		return [
			['name' => 'id', 'title'=>'ID', 'type'=>'text'],
			['name' => 'type', 'title'=>'来源', 'type'=>'text'],
			['name' => 'playlist_cn', 'title'=>'歌单', 'type'=>'text'],
			['name' => 'info', 'title'=>'歌曲信息','type'=>'text'],
			['name'=>'pic', 'title'=>'封面', 'type'=>'picture'],
			['name' => 'create_time', 'title'=>'创建时间', 'type'=>'text'],
			['name' => 'update_time', 'title'=>'更新时间', 'type'=>'text'],
		];
	}

	public function getPlaylistCnAttr($value, $data){
		return Playlist::cache(1)->getFieldById($data['playlist_id'], 'title');
	}

	public function getInfoAttr($value, $data){
		return "{$data['name']}-{$data['artist']}";
	}

	// 获取新的需下载至数据库的列表
    public static function new($all, $playlist_id){
        $exist = self::where('playlist_id', $playlist_id)->where(function($query){
        	$query->where('mp4', '-');
        	$query->whereOr('mp4', '');
        })->column(['concat(name, "-", artist)'=>'title'])?:[];
        if($exist){
            return collect($all)->filter(function($item)use($exist){
                return !in_array("{$item['name']}-{$item['artist']}", $exist);
            })->toArray();
        }
        return $all;
    }
}
