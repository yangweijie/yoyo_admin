// +----------------------------------------------------------------------
// | Static Plugin for ThinkAdmin
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 版权所有 2014~2023 ThinkAdmin [ thinkadmin.top ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// | 免责声明 ( https://thinkadmin.top/disclaimer )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/think-plugs-static
// | github 代码仓库：https://github.com/zoujingli/think-plugs-static
// +----------------------------------------------------------------------
// | 自定义后台扩展脚本，需要在加载 admin.js 后载入
// | 使用 composer require zoujingli/think-plugs-static 时不会更新此文件
// +----------------------------------------------------------------------

$(function () {
    window.$body = $('body');

    /*! 初始化异步加载的内容扩展动作 */
    // $body.on('reInit', function (evt, $dom) {
    //     console.log('Event.reInit', $dom);
    // });

    /*! 追加 require 配置参数
    /*! 加载的文件不能与主配置重复 */
    // require.config({
    //     paths: {
    //         'vue': ['plugs/vue/vue.min'],
    //     },
    //     shim: {
    //         'vue': ['json']
    //     },
    // });
    // // 基于 Require 加载测试
    // require(['vue', 'md5'], function (vue, md5) {
    //     console.log(vue)
    //     console.log(md5.hash('content'))
    // });

    /*! 其他 javascript 脚本代码 */

    // 显示表格图片
    window.showTableImage = function (image, circle, size, title) {
        return $.layTable.showImage(image, circle, size, title);
    };
});