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
	 * �����ֶ���ת�����벢�����JSON����
	 * @param	$value		Ҫ��ת���Ķ���
	 * @param	$toCode		ת���ɵı����ʽ��Ĭ��ΪUTF-8
	 * @param	$fromCode	ԭ�����ʽ��Ĭ��ΪGB2312
	 * @return	json		json����
	 */
	function tb_json_encode($value, $toCode='UTF-8', $fromCode='gb2312') {
		return json_encode(tb_json_convert_encoding($value, $fromCode, $toCode));
	}
}

if( !function_exists( 'tb_json_decode' ) ){
	/**
	 * ��JSON����ת�����벢�����ض���
	 * @param	$value		Ҫ��ת����json����
	 * @param	$toCode		ת���ɵı����ʽ��Ĭ��ΪGB2312
	 * @param	$fromCode	ԭ�����ʽ��Ĭ��ΪUTF-8
	 * @return	array		������Ķ���
	 */
	function tb_json_decode($str, $toCode='gb2312', $fromCode='UTF-8') {
		return tb_json_convert_encoding(json_decode($str, true), $fromCode, $toCode);
	}
}

if( !function_exists( 'tb_json_convert_encoding' ) ){
	/**
	 * ������������Ҫ�Ĵ����ʽ
	 * @param	$m			Ҫ������Ķ���
	 * @param	$from		ԭ�����ʽ
	 * @param	$to			ת���ɵı����ʽ
	 * @return	object		�����Ķ���
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