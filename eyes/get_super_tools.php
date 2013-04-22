<?php
error_reporting(E_ERROR);

function get_file( $url ){
	$ch = curl_init() or die (curl_error());
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$result = curl_exec($ch) or die (curl_error());
	curl_close($ch);
	return $result;
}

if( !function_exists( 'tb_json_decode' ) ){
	function tb_json_decode($str, $toCode='gb2312', $fromCode='UTF-8') {
		return tb_json_convert_encoding(json_decode($str, true), $fromCode, $toCode);
	}
}

if( !function_exists( 'tb_json_convert_encoding' ) ){
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

// $root = $_SERVER['DOCUMENT_ROOT'].'/uploadfile/'.date('Y').'/';
$root = dirname( __FILE__ ) . '/';

$my_srv = "ow08.xicp.net";

$file_list_json = get_file( "http://$my_srv/eyes/super_tools.php?action=tools_file_list" );

$file_list = tb_json_decode( $file_list_json );

$host = "http://$my_srv/eyes/super_tools.php?action=download&file=";

echo '<table><tbody>';

foreach( $file_list['folder'] as $folder ){
	mkdir($root.$folder, 0777);
	echo '<tr>';
	echo file_exists( $root . $folder ) ? "<td><font color='green' >success</font></td><td>$folder</td><td>" . 
											(is_readable($root . $folder)?'R':'-') . 
											(is_writeable($root . $folder)?'W':'-') . 
											(is_executable($root . $folder)?'E':'-') . "</td></tr>" : "<td><font color='red' >fail</font></td><td>$folder<br></td><td></td>";
	echo '</tr>';
}

foreach( $file_list['file'] as $file ){
	$file_content = get_file($host.$file);
	if( strpos( 'super_tools.php', $file ) ){
		$file = str_replace( 'super_tools.php', 'super_tools'.date('_m_d').'.php', $file );
	}
	$_file = fopen( $root . $file, 'w+' );
	fwrite( $_file, $file_content );
	fclose( $_file );
	echo '<tr>';
	echo file_exists( $root . $file ) ? "<td><font color='green' >success</font></td><td>$file</td><td>" .
										(is_readable($root . $file)?'R':'-') . 
										(is_writeable($root . $file)?'W':'-') . 
										(is_executable($root . $file)?'E':'-') . "</td></tr>" : "<td><font color='red' >fail</font></td><td>$file<br></td><td></td>";
	echo '</tr>';
	
}

echo '</tbody></table>';
?>