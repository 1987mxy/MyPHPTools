<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./js/jquery.js" ></script>
<script type="text/javascript" src="./js/code/shCore.js"></script>
<script type="text/javascript" src="./js/code/shBrushCss.js"></script>
<script type="text/javascript" src="./js/code/shBrushJScript.js"></script>
<script type="text/javascript" src="./js/code/shBrushPhp.js"></script>
<script type="text/javascript" src="./js/code/shBrushXml.js"></script>

<script type="text/javascript" src="./js/code/shBrushSql.js"></script>
<script type="text/javascript" src="./js/code/shBrushPython.js"></script>
<script type="text/javascript" src="./js/code/shBrushPowerShell.js"></script>

<link type="text/css" rel="stylesheet" href="./css/shCore.css"/>
<link type="text/css" rel="stylesheet" href="./css/shThemeEclipse.css"/>
<script type="text/javascript">
	SyntaxHighlighter.config.clipboardSwf = './js/code/clipboard.swf';
	SyntaxHighlighter.all();
</script>


<?php 

extract( $_GET ); 

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
					'folder'	=> array( 'folder' ) 
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

if ( isset( $file ) ) { 
	$ext = explode( '.', $path ); 
	$ext = array_pop( $ext ); 
	switch( selectIcon( $ext ) ) { 
		case 'php': 
			$read_file = fopen( $path, 'rb' ); 
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : ''; 
			fclose( $read_file ); 
			
			$code_type = 'php'; 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'html': 
			$read_file = fopen( $path, 'rb' ); 
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : ''; 
			fclose( $read_file ); 
			
			$code_type = 'xml'; 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'js': 
			$read_file = fopen( $path, 'rb' ); 
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : ''; 
			fclose( $read_file ); 
			
			$code_type = 'jscript'; 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'css': 
			$read_file = fopen( $path, 'rb' ); 
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : ''; 
			fclose( $read_file ); 
			
			$code_type = 'css'; 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'py':
			$read_file = fopen( $path, 'rb' );
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
			fclose( $read_file );
				
			$code_type = 'python';
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'sql':
			$read_file = fopen( $path, 'rb' );
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
			fclose( $read_file );
				
			$code_type = 'sql';
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'sh':
			$read_file = fopen( $path, 'rb' );
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
			fclose( $read_file );
				
			$code_type = 'powershell';
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'image':
			$root_len = strlen( $_SERVER['DOCUMENT_ROOT'] ) - 1;
			$image_url = substr( $path, $root_len );
			exit( "<img src='$image_url' />" );
			break;
		case 'text':
			$read_file = fopen( $path, 'rb' );
			$file_contents = filesize( $path ) > 0 ? fread( $read_file, filesize( $path ) ) : '';
			fclose( $read_file );
			break;
		default:
			$code_type = ''; 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', 'Œﬁ∑®‘§¿¿£°'); 
			break; 
	}
	echo "<h3>$path</h3>"; 
	echo "<pre class='brush:$code_type;' >"; 
	echo $file_contents; 
	echo '</pre>'; 
}

?>