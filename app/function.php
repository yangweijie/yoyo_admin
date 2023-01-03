<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

// 为方便系统核心升级，二次开发中需要用到的公共函数请写在这个文件，不要去修改common.php文件


use think\facade\Cache;
use think\facade\Db;
use util\File;
use util\Tree;



/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
    $name = Db::name('admin_user')->getFieldById($uid, 'nickname');
    return $name ? $name : '无名';
}

if (!function_exists('get_cate_name')) {
    function get_cate_name($cid) {
        return Db::name('cms_column')->getFieldById($cid, 'name');
    }

}

//获取标签
function get_tag($id, $link = true) {
    $tags = Db::name('cms_document_article')->getFieldByAid($id, 'tags');
    if ($link && $tags) {
        $tags = explode(',', $tags);
        $link = array();
        foreach ($tags as $value) {
            $link[] = '<a href="' . url('index/index', ['tag'=>$value]) . '">' . $value . '</a>';
        }
        return implode(',', $link);
    } else {
        return $tags ? $tags : 'none';
    }
}

//缩短网址
function short_url($url) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://dwz.cn/create.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"url={$url}",
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        trace("cURL Error #:" . $err);
        return 0;
        echo "cURL Error #:" . $err;
    } else {
        $ret = json_decode($response, 1);
        if($ret['status']){
            return $ret['tinyurl'];
        }else{
            trace('err_msg:'.$ret['err_msg']);
            return 0;
        }
//        echo $response;
    }
}

// 异步curl_post
function curl_post_async($url, $data = null, $type = 'array'){
	return curl_post($url, $data,$type, 1);
}

function curl_post($url, $data = null, $type = 'array', $async = 0)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //协议头 https，curl 默认开启证书验证，所以应关闭
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //强制ipv4解析
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        if($async){
        	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);     //注意，毫秒超时一定要设置这个
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
        }
        if ($type == 'array') {
            if (!$data) {
                $data = [];
            }
            $data = http_build_query($data);
        } else {
            if ($data) {
                $data = json_encode($data);
            } else {
                $data = '';
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        if (curl_errno($ch)) {
            $err_code = curl_errno($ch);
            $err_msg  = curl_error($ch);
            curl_close($ch);
            throw new \Exception("curl错误:" . $err_code . ',' . $err_msg);
        }
        curl_close($ch);
        return $ret;
    }


define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);



if(!function_exists('is_online')){

	function is_online()
	{
		if(PHP_SAPI == 'cli'){
			return isset($_SERVER['LOGNAME']) && $_SERVER['LOGNAME'] != 'jay';
		}else{
			return stripos($_SERVER['HTTP_HOST'], config('web_site_domain')) !== false;
		}
	}
}


/**
 * 导出数据为excel
 * @param  string $filename 文件名
 * @param  array $data     查询出来的数组
 * @param  array  $header   ['name'=>'名称'] 和数组查询出来的顺序保持一致
 * @return mixed  下载
 */
function export_excel_form_select($filename, $data, $header){
    if($data){
        if(isset($header['table'])){
            exception('数据中包含与到处逻辑不兼容的字段 table');
        }
        // dump($data);
    }
    // dump($header);
    // die;
    $table = '<head><meta charset="UTF-8"><title>Document</title></head>';
    // style="vnd.ms-excel.numberformat:@"
    $table .= '<table style="vnd.ms-excel.numberformat:@"><thead><tr>';
    foreach ($header as $key=>$col) {
        $table.= sprintf('<th class="name">%s</th>', $col);
    }
    $table.='</tr></thead><tbody>';
    if($data){
        foreach ($data as $row) {
            extract($row);
            $tr = compact(array_keys($header));
            $table.='<tr>';
            foreach ($tr as $v) {
                $table.= sprintf('<td class="name">%s</td>', (string)$v);
            }
            $table.='</tr>';
        }
    }else{
        $table.='</tbody></table>';
    }

    // header("Content-type:application/octet-stream");
  //   header("Accept-Ranges:bytes");
  //   header("Content-type:application/vnd.ms-excel");
  //   header("Content-Disposition:attachment;filename=".$filename.".xls");
  //   header("Pragma: no-cache");
  //   header("Expires: 0");



    header("Pragma: public");
    header("Content-type: text/html; charset=utf-8");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
    header("Content-Transfer-Encoding:binary");
    exit($table);
}

/**
 * 在数据列表中搜索
 * @access public
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 * @return array
 */
function list_search($list,$condition) {
    if(is_string($condition))
        parse_str($condition,$condition);
    // 返回的结果集合
    $resultSet = [];
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]     =   &$list[$key];
    }
    return $resultSet;
}

// 生成js时间戳
function jstime(){
    return substr(get_num_from_str(microtime(true)), 0, 13);
}

function get_num_from_str($str){
    return preg_replace('/\D/', '', $str);
}

if(!function_exists('datetime')){
    // 方便生成当前日期函数
    function datetime($str = 'now', $formart = 'Y-m-d H:i:s') {
        return @date($formart, strtotime($str));
    }
}

function is_online(){
	return 1;
}

function json_encode_pretty($arr){
    return json_encode($arr, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
}

function lrc2srt($lrc) {
    $transLine = IS_WIN? "\n" : PHP_EOL;
    $lrc = explode( $transLine, $lrc );
    // dump($lrc);
    $srt = "";
    $lines = array();
    foreach ( $lrc as $lrcl ) {
        if (stripos($lrcl, '[') !== false && stripos($lrcl, ']') !== false && stripos($lrcl, ':') !== false) {
            $m = [
                1=>'',
                2=>'',
                3=>'',
            ];
            $m[4] = trim(strstr($lrcl, ']'), ']');
            if(stripos($lrcl, '.') === false){
                get_m:
                $nums = explode(':', trim(strstr($lrcl, ']', true), '['));
                $m[1] = $nums[0];
                $m[2] = $nums[1];
                $m[3] = $nums[2]??'000';
            }else{
                $lrcl = str_ireplace('.', ':', $lrcl);
                goto get_m;
            }
            if($m[4]){
                $lines[] = array(
                    'time' => "00:{$m[1]}:{$m[2]},{$m[3]}0", // convert to SubRip-style time
                    //front 设置字幕颜色
                    'lyrics' => '<font color=#FFFFFF>' . trim($m[4]) . '</font>'
                );
            }
        }
    }
    // dump($lines);
    for ( $i = 0; $i < count( $lines ); $i++ ) {
        $n = $i + 1;
        $nexttime = isset( $lines[$n]['time'] ) ? $lines[$n]['time'] : "99:00:00,000";
        $srt .= "$n\n"
            .  "{$lines[$i]['time']} --> {$nexttime}".$transLine
            .  "{$lines[$i]['lyrics']}".$transLine.$transLine;
    }

    return $srt;
}
