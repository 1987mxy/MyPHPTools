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
include_once 'Config.php';
include_once 'Function.php';

extract( $_GET ); 

if ( isset( $file ) ) { 
	$ext = explode( '.', $path ); 
	$ext = array_pop( $ext ); 
	switch( selectIcon( $ext, $ext_dict ) ) { 
		case 'php': 
			$code_type = 'php'; 
			
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'html': 
			$code_type = 'xml'; 
			
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'js': 
			$code_type = 'jscript'; 
			
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'css': 
			$code_type = 'css'; 
			
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents ); 
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' ); 
			break; 
		case 'py':
			$code_type = 'python';
				
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'sql':
			$code_type = 'sql';
				
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'sh':
			$code_type = 'powershell';
				
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		case 'image':
			$root_len = strlen( $_SERVER['DOCUMENT_ROOT'] ) - 1;
			$image_url = substr( $path, $root_len );
			exit( "<img src='$image_url' />" );
			break;
		case 'text':
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break;
		default:
			$file_contents = getFileContent( $path ); 
			$file_contents = iconv( 'gb2312', 'utf-8//IGNORE', $file_contents );
			$file_contents = htmlentities( $file_contents, ENT_QUOTES, 'utf-8' );
			break; 
	}
	$auth = (is_readable("$path/$folder_name")?'R':'-').
			(is_writeable("$path/$folder_name")?'W':'-').
			(is_executable("$path/$folder_name")?'E':'-');
	echo "<h3>$path£¨$auth£©</h3>"; 
	echo "<pre class='brush:$code_type;' >"; 
	echo $file_contents; 
	echo '</pre>'; 
}

?>