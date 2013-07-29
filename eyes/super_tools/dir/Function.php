<?php

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

if( !function_exists( 'getFileList' ) ){
	function getFileList( $path ){
	// 	$direct = opendir( $path );
		$filename_list = array_map( basename, glob( $path . '/*' ) );
		$file_list = array( 'folder' => array(), 'file' => array() );
	// 	while( False !== ( $filename = readdir( $direct ) ) ) {
		foreach( $filename_list as $filename ) {
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
}

if( !function_exists( 'getFileContent' ) ){
	function getFileContent( $path ){
		$read_file = fopen( $path, 'rb' );
		$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
		fclose( $read_file );
	
		return $file_contents;
	}
}

if( !function_exists( 'selectIcon' ) ){
	function selectIcon( $file_ext, $ext_dict ){
		foreach ( $ext_dict as $image => $ext_list ) {
			if( in_array( $file_ext, $ext_list ) ){
				return $image;
			}
		}
		return 'file';
	}
}

if( !function_exists( 'superDir' ) ){
	function superDir( $path, $ajax = 0 ) {
		global $my_path;
		
		$folder_list = array();
		$file_list = array();
	
		$folder = explode( '/', $path );
		$folder = array_pop( $folder );
		$auth = (is_readable($path)?'R':'-').
				(is_writeable($path)?'W':'-').
				(is_executable($path)?'E':'-');
		if( stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'linux' ) ) $own =  posix_getpwuid( fileowner( $path ) );
		echo $ajax==0?"<li class='open_folder' >":"";
		echo "<span title='$own[name] $auth' onclick='folder(this)' path='$path' >$folder</span>
		<ul class='open_file' >";
	// 	$direct = opendir( $path );
		$filename_list = array_map( basename, glob( $path . '/*' ) );
	// 	while( False !== ( $filename = readdir( $direct ) ) ) {
		foreach( $filename_list as $filename ) {
			if( $filename == '.' || $filename == '..' ) continue;
			$filename = iconv( 'gb2312', 'utf-8', $filename );
			if( is_dir( "$path/$filename" ) ) {
				$folder_list[] = $filename;
				//			superDir( "$path/$filename", $level + 1 );
			}
			else {
				$file_list[] = $filename;
				// 			$ext = explode( '.', $file_name );
				// 			$ext = array_pop( $ext );
				// 			$li_class = selectIcon( $ext, $ext_dict );
				// 			$down_file = "$path/$file_name";
				// 			$auth = (is_readable("$path/$file_name")?'R':'-').
				// 					(is_writeable("$path/$file_name")?'W':'-').
				// 					(is_executable("$path/$file_name")?'E':'-');
				// 			$own =  posix_getpwuid( fileowner( "$path/$file_name" ) );
				// 			echo "<li class='$li_class' >
				// 					  <a title='$own[name] $auth' href='javascript:file(\"$path/$file_name\")' >
				// 						  $file_name
				// 					  </a>
				// 					  <a href='?action=download&file=$down_file' >
				// 						  <img src='$my_path/ico/download.gif' >
				// 					  </a>";
			}
			//		echo '</li>';
		}
	
		sort($folder_list);
		sort($file_list);
		foreach ($folder_list as $folder_name) {
			$auth = (is_readable("$path/$folder_name")?'R':'-').
			(is_writeable("$path/$folder_name")?'W':'-').
			(is_executable("$path/$folder_name")?'E':'-');
			if( stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'linux' ) ) $own =  posix_getpwuid( fileowner( "$path/$folder_name" ) );
			echo "<li class='close_folder' ><span title='$own[name] $auth' onclick='folder(this)' path='$path/$folder_name' >$folder_name</span>";
		}
		foreach ($file_list as $file_name) {
			$ext = explode( '.', $file_name );
			$ext = array_pop( $ext );
			$li_class = selectIcon( $ext, $ext_dict );
			$down_file = "$path/$file_name";
			$auth = (is_readable("$path/$file_name")?'R':'-').
					(is_writeable("$path/$file_name")?'W':'-').
					(is_executable("$path/$file_name")?'E':'-');
			if( stripos( $_SERVER[ 'HTTP_USER_AGENT' ], 'linux' ) ) $own = posix_getpwuid( fileowner( "$path/$file_name" ) );
			echo "<li class='$li_class' >
			<a title='$own[name] $auth' href='javascript:file(\"$path/$file_name\")' >
			$file_name
			</a>
			<a href='?action=download&file=$down_file' >
			<img src='$my_path/ico/download.gif' >
			</a>";
		}
		echo '</li>';
		echo '</ul>';
		echo $ajax==0?'<li>':'';
	}
}

?>