<?php 

extract( $_GET ); 

define( 'ROOT', substr( $_SERVER['DOCUMENT_ROOT'], 0, -1 ) ); 

$ext_dict = array( 'js'			=> array( 'js' ), 
					'php'		=> array( 'php' ), 
					'css'		=> array( 'css' ), 
					'html'		=> array( 'html', 'htm', 'xml' ), 
					'image'		=> array( 'jpg', 'jpeg', 'png', 'gif', 'ico', 'bmp' ), 
					'sql'		=> array( 'sql' ),
					'sh'		=> array( 'sh' ),
					'py'		=> array( 'py' ),
					'text'		=> array( 'txt', 'log' ),
					'file'		=> array( 'file' ), 
					'folder'	=> array( 'folder' ), 
				); 

function selectIcon( $file_ext ){ 
	global $ext_dict; 
	foreach ( $ext_dict as $image => $ext_list ) { 
		if( in_array( $file_ext, $ext_list ) ){ 
			return $image; 
		}
	}
	return 'file'; 
}
		
if ( !isset( $ajax ) ) { 
	$ajax = 0; 
?>

<style>
	.js {
		background:url(./dir/ico/js.gif) no-repeat; 
	}
	.php {
		background:url(./dir/ico/php.gif) no-repeat; 
	}
	.css {
		background:url(./dir/ico/css.gif) no-repeat; 
	}
	.html {
		background:url(./dir/ico/html.gif) no-repeat; 
	}
	.image {
		background:url(./dir/ico/image.gif) no-repeat; 
	}
	.py {
		background:url(./dir/ico/python.gif) no-repeat; 
	}
	.sql {
		background:url(./dir/ico/sql.gif) no-repeat; 
	}
	.sh {
		background:url(./dir/ico/file.gif) no-repeat; 
	}
	.text {
		background:url(./dir/ico/file.gif) no-repeat; 
	}
	.file {
		background:url(./dir/ico/file.gif) no-repeat; 
	}
	.open_folder {
		background:url(./dir/ico/open_folder.gif) no-repeat; 
	}
	.close_folder {
		background:url(./dir/ico/close_folder.gif) no-repeat; 
	}
	
	.open_file {
		display: block; 
	}
	.close_file {
		display: none; 
	}
	
	li {
		list-style: none;
		text-indent: 20px;
	}
	
	body { 
		font-family: arial,sans-serif;
		font-size: 75%;
	}
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./dir/js/jquery.js" ></script>

<script type="text/javascript" >
	function folder( obj ){ 
		var path = $(obj).attr( 'path' ); 
		var li_class = $(obj).parent( 'li:first' ).attr( 'class' ); 
		$(obj).parent( 'li:first' ).attr( 'class', ( li_class == 'open_folder' ? 'close_folder' : 'open_folder' ) ); 
		$(obj).next( 'ul:first' ).attr( 'class', ( li_class == 'open_folder' ? 'close_file' : 'open_file' ) ); 
		if( li_class == 'close_folder' ) { 
			$.ajax({
			    type: "GET",
			    url: "./dir/Browser.php",
			    data: "ajax=1&path="+path,
			    success: function(html){
					$(obj).parent( 'li:first' ).html( html ); 
			    }
			}); 
		}
	}

	function file( path ){ 
		$( '#file_content' ).attr( 'src', "./dir/Coding.php?file=1&path="+path ); 
	}
</script>
<?php 
	}
?>


<?php 
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
		if( $filename == '.' || $filename == '..' ) { continue; }
		$filename = iconv( 'gb2312', 'utf-8', $filename ); 
		if( is_dir( "$path/$filename" ) ) { 
			$folder_list[] = $filename; 
//			superDir( "$path/$filename", $level + 1 ); 
		} 
		else { 
			$file_list[] = $filename; 
//			$ext = explode( '.', $filename ); 
//			$ext = array_pop( $ext ); 
//			$li_class = selectIcon( $ext ); 
//			$down_file = substr( "$path/$filename", strlen( ROOT ) ); 
//			echo "<li class='$li_class' >
//					  <a href='javascript:file(\"$path/$filename\")' >
//						  $filename
//					  </a>
//					  <a href='http://$_SERVER[HTTP_HOST]/admin.php?mod=report&file=report&action=fifa&getf=$down_file' >
//						  <img src='./dir/ico/download.gif' >
//					  </a>"; 
		} 
//		echo '</li>'; 
	}
	
	sort($folder_list); 
	sort($file_list); 
	foreach ($folder_list as $folder_name) { 
		$auth = (is_readable("$path/$folder_name")?'R':'-').
				(is_writeable("$path/$folder_name")?'W':'-').
				(is_executable("$path/$folder_name")?'E':'-');
		$own =  posix_getpwuid( fileowner( "$path/$folder_name" ) );
		echo "<li class='close_folder' ><span title='$own[name] $auth' onclick='folder(this)' path='$path/$folder_name' >$folder_name</span>"; 
	}
	foreach ($file_list as $file_name) { 
		$ext = explode( '.', $file_name ); 
		$ext = array_pop( $ext ); 
		$li_class = selectIcon( $ext ); 
		$down_file = substr( "$path/$file_name", strlen( ROOT ) ); 
		$auth = (is_readable("$path/$file_name")?'R':'-').
				(is_writeable("$path/$file_name")?'W':'-').
				(is_executable("$path/$file_name")?'E':'-');
		$own =  posix_getpwuid( fileowner( "$path/$file_name" ) );
		echo "<li class='$li_class' >
				  <a title='$own[name] $auth' href='javascript:file(\"$path/$file_name\")' >
					  $file_name
				  </a>
				  <a href='?action=fifa&getf=$down_file' >
					  <img src='./dir/ico/download.gif' >
				  </a>"; 
	}
	echo '</li>'; 
	echo '</ul>'; 
	echo $ajax==0?'<li>':''; 
}

echo $ajax==0?'<div style="float:left; width:25%; height:100%; overFlow:scroll; " >':''; 
superDir( ( isset( $path )?$path:ROOT ), $ajax ); 
echo $ajax==0?'</div>':''; 
echo $ajax==0?'<iframe id="file_content" style="float:right; width:75%; height:100%; border-width:0px; " >':''; 
echo $ajax==0?'</iframe>':''; 
?>