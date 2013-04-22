<?php

function getFileList( $path ){
	$direct = dir( $path );
	$file_list = array( 'folder' => array(), 'file' => array() );
	while( False !== ( $filename = $direct -> read() ) ) {
		if( $filename == '.' || $filename == '..' ) continue;
		$full_path = "$path/$filename";
		if( is_dir( $full_path ) ) {
			$file_list[ 'folder' ][] = $full_path;
			$sub_file_list = getFileList( $full_path );
			$file_list[ 'folder' ] = array_merge( $file_list[ 'folder' ], $sub_file_list[ 'folder' ] );
			$file_list[ 'file' ] = array_merge( $file_list[ 'file' ], $sub_file_list[ 'file' ] );
		}
		else{
			$file_list[ 'file' ][] = $full_path;
		}
	}
	return $file_list;
}

if( !function_exists( 'tb_json_encode' ) ){
	/**
	 * 将各种对象转换编码并编译成JSON代码
	 * @param	$value		要被转换的对象
	 * @param	$toCode		转换成的编码格式，默认为UTF-8
	 * @param	$fromCode	原编码格式，默认为GB2312
	 * @return	json		json代码
	 */
	function tb_json_encode($value, $toCode='UTF-8', $fromCode='gb2312') {
		return json_encode(tb_json_convert_encoding($value, $fromCode, $toCode));
	}
}

if( !function_exists( 'tb_json_decode' ) ){
	/**
	 * 将JSON代码转换编码并解析回队列
	 * @param	$value		要被转换的json代码
	 * @param	$toCode		转换成的编码格式，默认为GB2312
	 * @param	$fromCode	原编码格式，默认为UTF-8
	 * @return	array		解析后的队列
	 */
	function tb_json_decode($str, $toCode='gb2312', $fromCode='UTF-8') {
		return tb_json_convert_encoding(json_decode($str, true), $fromCode, $toCode);
	}
}

if( !function_exists( 'tb_json_convert_encoding' ) ){
	/**
	 * 将对象编译成需要的代码格式
	 * @param	$m			要被编译的对象
	 * @param	$from		原编码格式
	 * @param	$to			转换成的编码格式
	 * @return	object		编码后的对象
	 */
	function tb_json_convert_encoding($m, $from, $to) {
		switch(gettype($m)) {
			case 'integer':
			case 'boolean':
			case 'float':
			case 'double':
			case 'NULL':
				return $m;
			case 'string':
				return iconv( $from, $to."//IGNORE", $m );
			case 'object':
				$vars = array_keys(get_object_vars($m));
				foreach($vars as $key) {
					$m->$key = tb_json_convert_encoding($m->$key, $from ,$to);
				}
				return $m;
			case 'array':
				foreach($m as $k => $v) {
					$m[tb_json_convert_encoding($k, $from, $to)] = tb_json_convert_encoding($v, $from, $to);
				}
				return $m;
			default:
		}
		return $m;
	}
}

?>