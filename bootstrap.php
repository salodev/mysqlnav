<?php
set_time_limit(0);
spl_autoload_register(function($className) {
	$baseDir = dirname(dirname(dirname(__FILE__))) . '/repos';
	$className = str_replace('\\','/', $className);
    $classFile = "{$baseDir}/{$className}.class.php";
    if (file_exists($classFile)) {
        require_once($classFile);
		return;
	}
	$interfaceFile = "{$baseDir}/{$className}.interface.php";
	if (file_exists($interfaceFile)) {
		require_once($interfaceFile);
	}
});
spl_autoload_register(function($className) {
	$className = str_replace('\\','/', $className);
    $classFile = dirname(__FILE__) . "/lib/{$className}.class.php";
    if (file_exists($classFile)) {
        require_once($classFile);
		return;
	}
	$interfaceFile = dirname(__FILE__) . "/lib/{$className}.interface.php";
	if (file_exists($interfaceFile)) {
		require_once($interfaceFile);
	}
});
cuif\CUIF::SetFileLog(dirname(__FILE__) . '/cuif.log');

set_error_handler(function($number, $string, $file = null, $line = null, $context = null) {
	$types = array(
		E_ERROR => 'E_ERROR', //1
		E_WARNING => 'E_WARNING', // 2
		E_PARSE => 'E_PARSE', // 4
		E_NOTICE => 'E_NOTICE', // 8
		E_CORE_ERROR => 'E_CORE_ERROR', // 16
		E_CORE_WARNING => 'E_CORE_WARNING', // 32
		E_COMPILE_ERROR => 'E_COMPILE_ERROR', // 64
		E_COMPILE_WARNING => 'E_COMPILE_WARNING', // 128
		E_USER_ERROR => 'E_USER_ERROR', // 256
		E_USER_WARNING => 'E_USER_WARNING', // 512
		E_USER_NOTICE => 'E_USER_NOTICE', // 1024
		E_STRICT => 'E_STRICT', // 2048
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', // 4096
		E_DEPRECATED => 'E_DEPRECATED', // 8192
		E_USER_DEPRECATED => 'E_USER_DEPRECATED', // 16384
	);
	$type = isset($types[$number]) ? $types[$number] : 'E_UNKNOWN_ERROR';
	$log = "{$type}: '{$string}' at {$file} ({$line})\n";
	file_put_contents('salo.log', $log, FILE_APPEND);
	
});

function str_split_unicode($str, $l = 0)  {
    return preg_split('/(.{'.$l.'})/us', $str, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
}