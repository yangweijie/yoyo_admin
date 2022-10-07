<?php

namespace app\yoyo;

use Clickfwd\Yoyo\Component;
use app\admin\model\Attachment;

class Image extends Component
{
	
	public $label   = '图片';
	public $value   = 0;
	public $name    = 'pic';
	public $error   = '';
	public $from    = '';
	public $module  = 'admin';
	public $preview = '';
	public $request = null;

	public function mount(){
		$this->request = request();
	}

	public function getUploadedProperty($dir = 'images'){
		// 附件大小限制
        $size_limit = $dir == 'images' ? config('app.upload_image_size') : config('app.upload_file_size');
        $size_limit = $size_limit * 1024;
        // 附件类型限制
        $ext_limit = $dir == 'images' ? config('app.upload_image_ext') : config('app.upload_file_ext');
        $ext_limit = $ext_limit != '' ? parse_attr($ext_limit) : '';
        // 缩略图参数
        $thumb = $this->request->post('thumb', '');
        // 水印参数
        $watermark       = $this->request->post('watermark', '');
        $file_input_name = $this->name;
        $file            = $this->request->file($file_input_name);
        if($file){

	        // 判断附件大小是否超过限制
	        if ($size_limit > 0 && ($file->getInfo('size') > $size_limit)) {
	            $this->error = '附件过大';
	            return false;
	        }
	        // 判断附件格式是否符合
	        $file_name = $file->getOriginalName();
	        $file_ext  = strtolower(substr($file_name, strrpos($file_name, '.')+1));
	        $error_msg = '';
	        if ($ext_limit == '') {
	            $error_msg = '获取文件信息失败！';
	        }
	        if ($file->getOriginalMime() == 'text/x-php' || $file->getOriginalMime() == 'text/html') {
	            $error_msg = '禁止上传非法文件！';
	        }
	        if (preg_grep("/php/i", $ext_limit)) {
	            $error_msg = '禁止上传非法文件！';
	        }
	        if (!preg_grep("/$file_ext/i", $ext_limit)) {
	            $error_msg = '附件类型不正确！';
	        }

	        if ($error_msg != '') {
	        	$this->error = $error_msg;
	            return false;
	        }

	        // 判断附件是否已存在
	        if ($file_exists = Attachment::where(['md5' => $file->hash('md5')])->find()) {
	            if ($file_exists['driver'] == 'local') {
	                $file_path = PUBLIC_PATH. $file_exists['path'];
	            } else {
	                $file_path = $file_exists['path'];
	            }
	       		
	       		$this->value   = $file_exists['id'];
	       		$this->preview = $file_path;
	            return true;
	        }

	        // 移动到框架应用根目录/uploads/ 目录下
	        $info = \think\facade\Filesystem::disk('public')->putFile($dir, $file);
	        $info = 'uploads/'.$info;
	        if($info){
	            // 缩略图路径
	            $thumb_path_name = '';
	            // 图片宽度
	            $img_width = '';
	            // 图片高度
	            $img_height = '';

	            // 获取附件信息
	            $file_info = [
	                'uid'    => session('user_auth.uid') ?? 1,
	                'name'   => $file_name,
	                'mime'   => $file->getMime(),
	                'path'   => str_replace('\\', '/', $info),
	                'ext'    => $file->extension(),
	                'size'   => $file->getSize(),
	                'md5'    => $file->md5(),
	                'sha1'   => $file->sha1(),
	                'thumb'  => $thumb_path_name,
	                'module' => $this->module,
	                'width'  => $img_width,
	                'height' => $img_height,
	            ];

	            // 写入数据库
	            if ($file_add = Attachment::create($file_info)) {
	                $file_path     = PUBLIC_PATH. $file_info['path'];
	                $this->value   = $file_add['id'];
	                $this->preview = $file_path;
	                return true;
	            } else {
	            	$this->error = '上传失败';
	            }
	        }else{
	            $this->error = '上传失败';
	        }
        }
        return false;
	}

	public function clear(){
		$this->value   = 0;
		$this->preview = '';
	}

	public function render()
	{
		return (String) $this->view('image', [
			'id'       => $this->getComponentId(),
			'uploaded' => $this->uploaded,
		]);
	}
}