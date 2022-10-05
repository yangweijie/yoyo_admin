<?php
namespace app\component;

use Clickfwd\Yoyo\ViewProviders\BaseViewProvider;
use Clickfwd\Yoyo\Interfaces\ViewProviderInterface;
use Clickfwd\Yoyo\Services\Configuration;

class ThinkViewProvider extends BaseViewProvider implements ViewProviderInterface
{
    protected $view;

    protected $name;

    protected $vars;

    protected $engine;

    protected $view_path;

    public function __construct($view)
    {
        $this->view      = $view;
        $this->engine    = app('view');
        $this->view_path = Configuration::get('viewPath');
    }


    public function render($name, $vars = []) :self{
        $this->name = $name;
        $this->vars = $vars;
    	return $this;
    }

    public function makeFromString($content, $vars = []): string{
    	return $this->engine->display($content, $vars);
    }

    public function exists($template): bool{
        $view_suffix = $this->engine->getConfig('view_suffix');
        $file        = "{$this->view_path}{$template}.{$view_suffix}";
        return $this->engine->exists($file);
    }

    public function startYoyoRendering($component): void{
        $this->view->startYoyoRendering($component);
    }

    public function stopYoyoRendering(): void{
    	//
    }

    public function __toString()
    {
        $this->engine->config(['view_path'=>$this->view_path]);
        return $this->engine->fetch("/{$this->name}", $this->vars);
        // 获取并清空缓存
    }
}
