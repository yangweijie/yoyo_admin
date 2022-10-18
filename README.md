Yoyo Admin
===============

基于 ThinkPHP、Yoyo库 和 tailwind Element 开发的一个易于扩展的后台框架。再也不用羡慕 Laravel 有 livewire 和 laravel-admin 了。再也不用去学vue了。

Yoyo 基于 `htmlx` 一种类似pjax ajax重新加载 html片段来实现组件动态刷新的技术。

[Htmx意外走红，我们从React“退回去”后：代码行数减少 67%，JS 依赖项从 255 下降到 9](https://mp.weixin.qq.com/s/MuwyfbT5eg0QfRpqF18eJg)

## 安装

下载后 composer install
将 data/init.sql 建一个库后导入
cp example.env 修改数据库文件
本地 php think run 访问 localhost:8000

## 账号
admin ya123456

## yoyo 的使用

参见 [yoyo.md](yoyo.md)

在本项目中已经 集成好 好think-view了。

在view 里 `{:yoyo_render('组件名', [组件参数])}` 即可

## 依赖开源库

[yoyo](https://github.com/clickfwd/yoyo)

[tailwind element](https://tailwind-elements.com)

[jquery](https://jquery.com)

[font-awesome v6](https://fontawesome.com)

## todo

[] liveSearch 动态下拉

[] trigger 某些表单变化触发别的表单项显隐

[] 富文本编辑器

[] mdb js table组件？？

[] 其他 laravel-admin 扩展 Dcat Admin 非官方应用市场 https://github.com/Sparkinzy/dcat-extension-client

## 注意点

尽量不要嵌套 组件使用，`tabs` 组件 试过 `taglib` 失败了。



