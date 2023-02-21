#!/usr/bin/env php
<?php
// 关闭英文语法拼写检查[vscode扩展]
/* cSpell:disable */

/**
 * vqmod for composer install
 * run: php ./extend/vqmod/install/index.php
 */

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('Run cli only');

// Load class required for installation
require('ugrsr.class.php');

// Edit file list
$files = ['vendor/autoload.php'];

// Get directory two above installation directory
$code_path = realpath(dirname(__FILE__) . '/../../../');

// Verify path is correct
if (empty($code_path))
	die('ERROR - COULD NOT DETERMINE PATH CORRECTLY - ' . dirname(__FILE__) . PHP_EOL);

$code_path = $code_path . DIRECTORY_SEPARATOR;

// Counters
$writes = 0;
$changes = 0;
$write_files = [];
$write_errors = array();

foreach ($files as $file) {
	if (is_file($code_path . $file) && !is_writeable($code_path . $file)) {
		$write_errors[] = $file . ' not writeable';
	} else {
		$write_files[] = $file;
	}
}

if (!empty($write_errors)) {
	die(implode(PHP_EOL, $write_errors));
}

if (!is_dir($code_path . 'runtime')) {
	mkdir($code_path . 'runtime');
}

// Create new UGRSR class
$u = new UGRSR($code_path);

// remove the # before this to enable debugging info
#$u->debug = true;

// Set file searching to off
$u->file_search = false;

foreach ($write_files as $write_file) {
	$u->clearPatterns();
	$u->resetFileList();

	$u->addFile($write_file);

	// Pattern to run required files through vqmod
	$u->addPattern('/require_once\s+__DIR__\s*\.\s*\'([^\']+)\'\;/', '// VirtualQMOD
if(is_file(dirname(__DIR__).\'/extend/vqmod/init.php\')){
	// VQMODDED Startup https://gitee.com/pgcao/tp6_vqmod.git
	require_once(dirname(__DIR__).\'/extend/vqmod/init.php\');		
	require_once(\VQMod::modCheck(__DIR__ . \'$1\'));
}else{
	require_once(__DIR__ . \'$1\');
}');

	// Get number of changes during run
	$result = $u->run();
	if ($result) {
		$changes += $result['changes'];
		$writes += $result['writes'];
	}
}

if (file_exists($code_path . 'runtime/_vqmod.mods')) {
	unlink($code_path . 'runtime/_vqmod.mods');
}

// output result to user
if (!$changes)
	die('VQMod has been installed on your system!' . PHP_EOL);
if ($writes < 1)
	die('VQMod unable to write to one or more files.' . PHP_EOL);
die('VQMod install succee!' . PHP_EOL);