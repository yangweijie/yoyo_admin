<?php
declare (strict_types = 1);

namespace app\command;

use app\admin\model\Musics;
use app\admin\model\Playlist;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use util\Http;

class PlaylistInit extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('playlist init')
            ->setDescription('初始化歌单')
            ->addArgument('id', Option::VALUE_REQUIRED, '歌单id',0)
            ->addOption('id', 'i', Option::VALUE_REQUIRED, '歌单id')
            ;
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('歌单初始化');
        $id = $input->getOption('id')?:0;
        debug('begin');
        if($id){
            $playlist = Playlist::find($id);
            if(!$playlist){
                $output->error('歌单记录不存在');
                return 0;
            }
            dump($playlist['url']);
            $json = Http::get($playlist['url']);
            $json_arr = json_decode($json, true);
            $now = date('Y-m-d H:i:s');
            $insert = collect([]);
            foreach ($json_arr as $music){
                $insert->push(array_merge($music, [
                    'playlist_id'=>$id,
                    'type'=>$playlist['type'],
                    'create_time'=>$now,
                    'mp4'=>'-',
                ]));
                // Musics::create(array_merge($music, [
                //     'playlist_id'=>$id,
                //     'type'=>$playlist['type'],
                //     'create_time'=>$now,
                //     'mp4'=>'-',
                // ]));
            }
            $insert->chunk(100)->each( function($chunk){
                \think\facade\Db::connect('music')->table('musics')->insertAll($chunk->toArray());
            });
            $playlist->last_id = Musics::max('id');
            $playlist->save();
            debug('end');
            $output->info('初始化完成共耗时'. debug('begin', 'end', 6).'秒');
        }else{
            $output->error('歌单id未传');
            return 0;
        }
    }
}
