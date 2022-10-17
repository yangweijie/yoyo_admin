const $tree_list = {};
const $key_auth  = {};
$(document).ready(function(){
    let curr_tab     = 0;

    // 切换节点tab
    $('#menu-tab > li > a').click(function () {
        curr_tab = $(this).data('tab');
        $('#search-auth').val($key_auth[curr_tab]);
    });
    // 初始化节点
    $('.js-tree').each(function () {
        const $tree = $(this);
        const tab   = $(this).data('tab');

        $key_auth[tab] = '';

        $tree.jstree({
            plugins: ["checkbox", "search"],
            "checkbox" : {
                "keep_selected_style" : false,
                "three_state" : false,
                "cascade" : 'down+up'
            },
            "search" : {
                'show_only_matches' : true,
                'show_only_matches_children' : true
            }
        });
        $tree_list[tab] = $tree;
    });

    // 全选
    $('#check-all').click(function () {
        $tree_list[curr_tab].jstree(true).check_all();
    });
    // 取消全选
    $('#uncheck-all').click(function () {
        $tree_list[curr_tab].jstree(true).uncheck_all();
    });
    // 展开所有
    $('#expand-all').click(function () {
        $tree_list[curr_tab].jstree(true).open_all();
    });
    // 收起所有
    $('#collapse-all').click(function () {
        $tree_list[curr_tab].jstree(true).close_all();
    });

    var to = false;
    $('#search-auth').keyup(function (event) {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
            var v = $('#search-auth').val();
            $key_auth[curr_tab] = v;
            $tree_list[curr_tab].jstree(true).search(v);
        }, 250);
    });

    // 提交表单
    $('#form').submit(function () {
        var form_data = $(this).serialize();
        var auth_node = [];
        $.each($tree_list, function () {
            auth_node.push.apply(auth_node, $(this).jstree(true).get_checked());
        });

        if (auth_node.length) {
            form_data.menu_auth = auth_node.join(',');
        }
        $.post("{:strtolower(admin_url(''))}", form_data, {}, 'json').done(function (res) {
            if (res.code) {
                yoyo.notify(res.msg, 'success');
                setTimeout(function () {
                    location.href = res.url;
                }, 1500);
            } else {
                yoyo.notify(res.msg, 'danger');
            }
        }).fail(function () {
            yoyo.notify('服务器发生错误~', 'danger');
        });
        return false;
    });
});