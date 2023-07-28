<?php
declare (strict_types = 1);

namespace app\listener;

use Clickfwd\Yoyo\View;
// use Clickfwd\Yoyo\ViewProviders\YoyoViewProvider;
use app\component\ThinkViewProvider;
use Clickfwd\Yoyo\Yoyo;

class YoyoInit
{

    public static function getComponentPath(){
        return realpath(__DIR__.'/../../view/yoyo').'/';
    }

    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        $yoyo = new Yoyo();
        $yoyo->configure([
          'url'         => request()->baseFile().'/index/yoyo',
          'scriptsPath' => '/static/js/',
          'namespace'   => 'app\\yoyo\\',
          'htmx'        => '/static/js/htmx.js',
          'viewPath'    => self::getComponentPath(),
        ]);

        // Register the native Yoyo view provider 
        // Pass the Yoyo components' template directory path in the constructor

        $yoyo->registerViewProvider(function() {
            return new ThinkViewProvider(new View(self::getComponentPath()));
        });
    }   
}
