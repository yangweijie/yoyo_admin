cocoMessage.config({       //全局配置
    duration: 3000,         //消息显示时长
});

var yoyo = function () {
    /**
     * 处理ajax方式的post提交
     */
    var ajaxPost = function () {
        jQuery(document).delegate('.ajax-post', 'click', function () {
            var msg, self   = jQuery(this), ajax_url = self.attr("href") || self.data("url");
            var target_form = self.attr("target-form");
            var text        = self.data('tips');
            var title       = self.data('title') || '确定要执行该操作吗？';
            var confirm_btn = self.data('confirm') || '确定';
            var cancel_btn  = self.data('cancel') || '取消';
            var form        = jQuery('form[name=' + target_form + ']');
            if (form.length === 0) {
                form = jQuery('.' + target_form);
            }
            var form_data   = form.serialize();

            if ("submit" === self.attr("type") || ajax_url) {
                // 不存在“.target-form”元素则返回false
                if (undefined === form.get(0)) return false;
                // 节点标签名为FORM表单
                if ("FORM" === form.get(0).nodeName) {
                    ajax_url = ajax_url || form.get(0).getAttribute('action');;

                    // 提交确认
                    if (self.hasClass('confirm')) {
                        swal({
                            title: title,
                            text: text || '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d26a5c',
                            confirmButtonText: confirm_btn,
                            cancelButtonText: cancel_btn,
                            closeOnConfirm: true,
                            html: false
                        }, function () {
                            pageLoader();
                            self.attr("autocomplete", "off").prop("disabled", true);

                            // 发送ajax请求
                            jQuery.post(ajax_url, form_data, {}, 'json').done(function(res,  textStatus, jqXHR) {
                                if(textStatus == 302){
                                    console.log(jqXHR);
                                }
                                pageLoader('hide');
                                msg = res.msg;
                                if (res.code) {
                                    if (res.url && !self.hasClass("no-refresh")) {
                                        msg += " 页面即将自动跳转~";
                                    }
                                    tips(msg, 'success');
                                    setTimeout(function () {
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                                    }, 1500);
                                } else {
									alert(333);
                                    jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                                    tips(msg, 'danger');
                                    setTimeout(function () {
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                    }, 2000);
                                }
                            }).fail(function (res) {
								alert(444);
                                pageLoader('hide');
                                tips('服务器内部错误~', 'danger');
                                self.attr("autocomplete", "on").prop("disabled", false);
                            });
                        });
                        return false;
                    } else {
                        self.attr("autocomplete", "off").prop("disabled", true);
                    }
                } else if ("INPUT" === form.get(0).nodeName || "SELECT" === form.get(0).nodeName || "TEXTAREA" === form.get(0).nodeName) {
                    // 如果是多选，则检查是否选择
                    if (form.get(0).type === 'checkbox' && form_data === '') {
                        yoyo.notify('请选择要操作的数据', 'warning');
                        return false;
                    }

                    // 提交确认
                    if (self.hasClass('confirm')) {
                        swal({
                            title: title,
                            text: text || '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d26a5c',
                            confirmButtonText: confirm_btn,
                            cancelButtonText: cancel_btn,
                            closeOnConfirm: true,
                            html: false
                        }, function () {
                            pageLoader();
                            self.attr("autocomplete", "off").prop("disabled", true);

                            // 发送ajax请求
                            jQuery.post(ajax_url, form_data, {}, 'json').done(function(res,  textStatus, jqXHR) {
                                pageLoader('hide');
                                msg = res.msg;
                                if (res.code) {
                                    if (res.url && !self.hasClass("no-refresh")) {
                                        msg += " 页面即将自动跳转~";
                                    }
                                    tips(msg, 'success');
                                    setTimeout(function () {
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                                    }, 1500);
                                } else {
                                    jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                                    tips(msg, 'danger');
                                    setTimeout(function () {
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                    }, 2000);
                                }
                            }).fail(function (res) {
                                pageLoader('hide');
                                tips('服务器内部错误~', 'danger');
                                self.attr("autocomplete", "on").prop("disabled", false);
                            });
                        });
                        return false;
                    } else {
                        self.attr("autocomplete", "off").prop("disabled", true);
                    }
                } else {
                    if (self.hasClass("confirm")) {
                        swal({
                            title: title,
                            text: text || '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d26a5c',
                            confirmButtonText: confirm_btn,
                            cancelButtonText: cancel_btn,
                            closeOnConfirm: true,
                            html: false
                        }, function () {
                            pageLoader();
                            self.attr("autocomplete", "off").prop("disabled", true);
                            form_data = form.find("input,select,textarea").serialize();

                            // 发送ajax请求
                            jQuery.post(ajax_url, form_data, {}, 'json').done(function(res,  textStatus, jqXHR) {
                                pageLoader('hide');
                                msg = res.msg;
                                if (res.code) {
                                    if (res.url && !self.hasClass("no-refresh")) {
                                        msg += " 页面即将自动跳转~";
                                    }
                                    tips(msg, 'success');
                                    setTimeout(function () {
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                                    }, 1500);
                                } else {
                                    jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                                    tips(msg, 'danger');
                                    setTimeout(function () {
                                        // 刷新父窗口
                                        if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                            res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                            return false;
                                        }
                                        // 关闭弹出框
                                        if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                            parent.layer.close(index);return false;
                                        }
                                        // 新窗口打开
                                        if (res.data && (res.data === '_blank' || res.data._blank)) {
                                            window.open(res.url === '' ? location.href : res.url);
                                            return false;
                                        }
                                        self.attr("autocomplete", "on").prop("disabled", false);
                                    }, 2000);
                                }
                            }).fail(function (res) {
                                pageLoader('hide');
                                tips('服务器内部错误~', 'danger');
                                self.attr("autocomplete", "on").prop("disabled", false);
                            });
                        });
                        return false;
                    } else {
                        form_data = form.find("input,select,textarea").serialize();
                        self.attr("autocomplete", "off").prop("disabled", true);
                    }
                }

                // 直接发送ajax请求
                pageLoader();
                jQuery.post(ajax_url, form_data, {}, 'json').done(function(res, textStatus, jqXHR) {
                    pageLoader('hide');
                    msg = res.msg;

                    if (res.code) {
                        if (res.url && !self.hasClass("no-refresh")) {
                            msg += "， 页面即将自动跳转~";
                        }
                        tips(msg, 'success');
                        setTimeout(function () {
                            self.attr("autocomplete", "on").prop("disabled", false);
                            // 刷新父窗口
                            if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                return false;
                            }
                            // 关闭弹出框
                            if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                parent.layer.close(index);return false;
                            }
                            // 新窗口打开
                            if (res.data && (res.data === '_blank' || res.data._blank)) {
                                window.open(res.url === '' ? location.href : res.url);
                                return false;
                            }
                            return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                        }, 1500);
                    } else {
                        jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                        tips(msg, 'danger');
                        setTimeout(function () {
                            // 刷新父窗口
                            if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                return false;
                            }
                            // 关闭弹出框
                            if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                parent.layer.close(index);return false;
                            }
                            // 新窗口打开
                            if (res.data && (res.data === '_blank' || res.data._blank)) {
                                window.open(res.url === '' ? location.href : res.url);
                                return false;
                            }
                            self.attr("autocomplete", "on").prop("disabled", false);
                        }, 2000);
                    }
                }).fail(function (res, textStatus, jqXHR) {
                    pageLoader('hide');
//					console.log(res);
//					console.log(textStatus);
					if(res.responseJSON != undefined && res.responseJSON.message	!= undefined){
						tips(res.responseJSON.message, 'danger');
					}else{
						tips('服务器内部错误~', 'danger');
					}
                    self.attr("autocomplete", "on").prop("disabled", false);
                });
            }

            return false;
        });
    };

    /**
     * 处理ajax方式的get提交
     */
    var ajaxGet = function () {
        jQuery(document).delegate('.ajax-get', 'click', function () {
            var msg, self = $(this), text = self.data('tips'), ajax_url = self.attr("href") || self.data("url");
            var title       = self.data('title') || '确定要执行该操作吗？';
            var confirm_btn = self.data('confirm') || '确定';
            var cancel_btn  = self.data('cancel') || '取消';
            // 执行确认
            if (self.hasClass('confirm')) {
                swal({
                    title: title,
                    text: text || '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d26a5c',
                    confirmButtonText: confirm_btn,
                    cancelButtonText: cancel_btn,
                    closeOnConfirm: true,
                    html: false
                }, function () {
                    pageLoader();
                    self.attr("autocomplete", "off").prop("disabled", true);

                    // 发送ajax请求
                    jQuery.get(ajax_url, {}, {}, 'json').done(function(res) {
                        pageLoader('hide');
                        msg = res.msg;
                        if (res.code) {
                            if (res.url && !self.hasClass("no-refresh")) {
                                msg += " 页面即将自动跳转~";
                            }
                            tips(msg, 'success');
                            setTimeout(function () {
                                self.attr("autocomplete", "on").prop("disabled", false);
                                // 刷新父窗口
                                if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                    res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                    return false;
                                }
                                // 关闭弹出框
                                if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);return false;
                                }
                                // 新窗口打开
                                if (res.data && (res.data === '_blank' || res.data._blank)) {
                                    window.open(res.url === '' ? location.href : res.url);
                                    return false;
                                }
                                return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                            }, 1500);
                        } else {
                            tips(msg, 'danger');
                            setTimeout(function () {
                                // 刷新父窗口
                                if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                    res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                    return false;
                                }
                                // 关闭弹出框
                                if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);return false;
                                }
                                // 新窗口打开
                                if (res.data && (res.data === '_blank' || res.data._blank)) {
                                    window.open(res.url === '' ? location.href : res.url);
                                    return false;
                                }
                                self.attr("autocomplete", "on").prop("disabled", false);
                            }, 2000);
                        }
                    }).fail(function (res) {
                        pageLoader('hide');
                        tips('服务器内部错误~', 'danger');
                        self.attr("autocomplete", "on").prop("disabled", false);
                    });
                });
            } else {
                pageLoader();
                self.attr("autocomplete", "off").prop("disabled", true);

                // 发送ajax请求
                jQuery.get(ajax_url, {}, {}, 'json').done(function(res) {
                    pageLoader('hide');
                    msg = res.msg;
                    if (res.code) {
                        if (res.url && !self.hasClass("no-refresh")) {
                            msg += " 页面即将自动跳转~";
                        }
                        tips(msg, 'success');
                        setTimeout(function () {
                            self.attr("autocomplete", "on").prop("disabled", false);
                            // 刷新父窗口
                            if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                return false;
                            }
                            // 关闭弹出框
                            if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                parent.layer.close(index);return false;
                            }
                            // 新窗口打开
                            if (res.data && (res.data === '_blank' || res.data._blank)) {
                                window.open(res.url === '' ? location.href : res.url);
                                return false;
                            }
                            return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                        }, 1500);
                    } else {
                        tips(msg, 'danger');
                        setTimeout(function () {
                            // 刷新父窗口
                            if (res.data && (res.data === '_parent_reload' || res.data._parent_reload)) {
                                res.url === '' || res.url === location.href ? parent.location.reload() : parent.location.href = res.url;
                                return false;
                            }
                            // 关闭弹出框
                            if (res.data && (res.data === '_close_pop' || res.data._close_pop)) {
                                var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                parent.layer.close(index);return false;
                            }
                            // 新窗口打开
                            if (res.data && (res.data === '_blank' || res.data._blank)) {
                                window.open(res.url === '' ? location.href : res.url);
                                return false;
                            }
                            self.attr("autocomplete", "on").prop("disabled", false);
                        }, 2000);
                    }
                }).fail(function (res) {
                    pageLoader('hide');
                    tips('服务器内部错误~', 'danger');
                    self.attr("autocomplete", "on").prop("disabled", false);
                });
            }

            return false;
        });
    };

    /**
     * 处理普通方式的get提交
     */
    var jsGet = function () {
        jQuery(document).delegate('.js-get', 'click', function () {
            var self = $(this), text = self.data('tips'), url = self.attr("href") || self.data("url");
            var target_form = self.attr("target-form");
            var form        = jQuery('form[name=' + target_form + ']');
            var form_data   = form.serialize() || [];
            var title       = self.data('title') || '确定要执行该操作吗？';
            var confirm_btn = self.data('confirm') || '确定';
            var cancel_btn  = self.data('cancel') || '取消';

            if (form.length === 0) {
                form = jQuery('.' + target_form + '[type=checkbox]:checked');
                form.each(function () {
                    form_data.push($(this).val());
                });
                form_data = form_data.join(',');
            }

            if (form_data === '') {
                yoyo.notify('请选择要操作的数据', 'warning');
                return false;
            }

            if (url.indexOf('?') !== -1) {
                url += '&' + target_form + '=' + form_data;
            } else {
                url += '?' + target_form + '=' + form_data;
            }

            // 执行确认
            if (self.hasClass('confirm')) {
                swal({
                    title: title,
                    text: text || '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d26a5c',
                    confirmButtonText: confirm_btn,
                    cancelButtonText: cancel_btn,
                    closeOnConfirm: true,
                    html: false
                }, function () {
                    if (self.hasClass('js-blank')) {
                        window.open(url);
                    }  else {
                        location.href = url;
                    }
                });
            } else {
                if (self.hasClass('js-blank')) {
                    window.open(url);
                }  else {
                    location.href = url;
                }
            }

            return false;
        });
    };

    /**
     * 页面小提示
     * @param $msg 提示信息
     * @param $type 提示类型:'info', 'success', 'warning', 'danger'
     */
    var tips = function ($msg, $type) {
        switch ($type) {
            case 'info':
                cocoMessage.info($msg);
                break;
            case 'success':
                cocoMessage.success($msg);
                break;
            case 'warning':
                cocoMessage.warning($msg);
                break;
            case 'danger':
                cocoMessage.error($msg);
                break;
        }
    };

    /**
     * 页面加载提示
     * @param $mode 'show', 'hide'
     */
    var pageLoader = function ($mode) {
        var $loadingEl = jQuery('#loading');
        $mode          = $mode || 'show';

        if ($mode === 'show') {
            if ($loadingEl.length) {
                $loadingEl.fadeIn(250);
            } else {
                jQuery('body').prepend(`<div class="bg-opacity-10 bg-white w-fit fixed z-[1030]" id="loading">
  <div class="bg-black bg-opacity-60 p-2.5 top-2/4 left-2/4 rounded-md text-white fixed -ml-12 -mt-6">
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512" class="animate-spin w-6 y-6 inline-block mr-1"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) -->
    <path d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"/></svg>
    <span class="loding-text mt-1 inline-block text-sm">请稍等...</span>
  </div>
</div>`);
            }
        } else if ($mode === 'hide') {
            if ($loadingEl.length) {
                $loadingEl.fadeOut(250);
            }
        }

        return false;
    };

    /**
     * 启用图标搜索
     */
    var iconSearchLoader = function () {
        // Set variables
        var $searchItems = jQuery('.js-icon-list > li');
        var $searchValue = '';

        // When user types
        jQuery('.js-icon-search').on('keyup', function(){
            $searchValue = jQuery(this).val().toLowerCase();

            if ($searchValue.length) { // If more than 2 characters, search the icons
                $searchItems.hide();

                jQuery('code', $searchItems)
                    .each(function(){
                        if (jQuery(this).text().match($searchValue)) {
                            jQuery(this).parent('li').show();
                        }
                    });
            } else if ($searchValue.length === 0) { // If text deleted show all icons
                $searchItems.show();
            }
        });
    };

    /**
     * 刷新页面
     */
    var pageReloadLoader = function () {
        // 刷新页面
        $('.page-reload').click(function () {
            location.reload();
        });
    };

    /**
     * 初始化图片查看
     */
    var viewerLoader = function () {
        $('.gallery-list,.uploader-list').each(function () {
            $(this).viewer('destroy');
            $(this).viewer({url: 'data-original'});
        });
    };

    return {
        // 初始化
        init: function () {
            ajaxPost();
            ajaxGet();
            jsGet();
            pageReloadLoader();
        },
        // 页面加载提示
        loading: function ($mode) {
            pageLoader($mode);
        },
        // 页面小提示
        notify: function ($msg, $type, $icon, $from, $align) {
            tips($msg, $type, $icon, $from, $align);
        },
        // 启用图标搜索
        iconSearch: function () {
            iconSearchLoader();
        },
        // 初始化图片查看
        viewer: function () {
            viewerLoader();
        }
    };
}();

// Initialize app when page loads
jQuery(function () {
    yoyo.init();
    $( document ).ajaxComplete(function( event, request, settings ) {
    });
//	$(document).ajaxError(
//		//所有ajax请求异常的统一处理函数，处理
//		function(event,xhr,options,exc ){
//			if(xhr.status == 'undefined'){
//				return;
//			}
//			switch(xhr.status){
//				case 401:
//					// 未授权异常
//					alert("系统拒绝：您没有访问权限。");
//				break;
//				case 404:
//					alert("您访问的资源不存在。");
//					break;
//				case 500:
//					alert("服务器出错。");
//					break;
//			}
//		}
//	);
});