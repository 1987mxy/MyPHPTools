<?php

function getFileContent( $path ){
	$read_file = fopen( $path, 'rb' );
	$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
	fclose( $read_file );
	
	return $file_contents;
}

function selectIcon( $file_ext, $ext_dict ){ 
	foreach ( $ext_dict as $image => $ext_list ) { 
		if( in_array( $file_ext, $ext_list ) ){ 
			return $image; 
		}
	}
	return 'file'; 
}

function superDir( $path, $ajax = 0 ) {
	$folder_list = array();
	$file_list = array();

	$folder = explode( '/', $path );
	$folder = array_pop( $folder );
	echo $ajax==0?"<li class='open_folder' >":"";
	echo "<span onclick='folder(this)' path='$path' >$folder</span>
	<ul class='open_file' >";
	$direct = dir( $path );
	while( False !== ( $filename = $direct -> read() ) ) {
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
// 						  <img src='./dir/ico/download.gif' >
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
					<img src='./dir/ico/download.gif' >
				</a>";
	}
	echo '</li>';
	echo '</ul>';
	echo $ajax==0?'<li>':'';
}
?>