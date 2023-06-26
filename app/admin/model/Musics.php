<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Musics extends Model
{
    protected $connection = 'music';
    public static function new($all, $playlist_id){
        $exist = self::where('playlist_id', $playlist_id)->where('mp4', '-')->column(['concat(name, "-", artist)'=>'title'])?:[];
        if($exist){
            return collect($all)->filter(function($item)use($exist){
                return !in_array("{$item['name']}-{$item['artist']}", $exist);
            })->toArray();
        }
        return $all;
    }
}
