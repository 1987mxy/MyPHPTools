<?php

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
}


include_once 'db_mysql.class.php';

if( file_exists( ROOT . '/include/config.inc.php' ) ){
	include_once ROOT . '/include/config.inc.php';
}
else{
	define( 'DB_HOST', 'localhost' );
	define( 'DB_USER', 'root' );
	define( 'DB_PW', '' );
	define( 'DB_NAME', 'mysql' );
	define( 'DB_PRE', 'hm_' );
	define( 'DB_CHARSET', 'gbk' );
	define( 'DB_PCONNECT', 0 );
}

$db = new db_mysql();
$db -> connect( DB_HOST,
				DB_USER,
				DB_PW,
				DB_NAME,
				DB_PCONNECT,
				DB_CHARSET );

switch( $op ){
	case 'columns':
		if( !$table ) break;
		$table_sql = $database ? 'show full columns from '.$database.'.'.$table : 'show full columns from '.$table;
		$table_columns = $db -> select($table_sql);
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '.' . $table . '</h3>';
		echo '<table border="1px" >';
		foreach ( $table_columns as $i => $col ) {
			if( empty( $col ) ) continue;
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
		echo "<br><a href='?action=db&op=tables&database=$database' >back</a>";
		exit();
		break;
	case 'table_data':
		if( !$table ) break;
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
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' ><textarea name='sql' cols='100' rows='5' >" . stripslashes($sql) . "</textarea><br><input name='sql_submit' type='submit' value='running' /></form>";

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
			if( empty( $row ) ) continue;
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
		echo "<br><a href='?action=db&op=tables&database=$database' >back</a>";
		exit();
		break;
	default:
		$db_sql = $database ? 'show full tables from '.$database : 'show full tables';
		$db_tables = $db -> select($db_sql);
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '</h3>';
		echo '<table border="1px" >';
		foreach ( $db_tables as $i => $tab ) {
			if( empty( $tab ) ) continue;
			$field_fields = array_keys( $tab );
			if( $i == 0 ){
				$th = '<tr>';
				$td = '<tr>';
				foreach ( $tab as $attr_name => $attr ) {
					$th .= "<th>$attr_name</th>";
					$td .= "<td>".( $attr_name == $field_fields[0] ? "<a href='?action=db&op=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					$td .= '&nbsp;&nbsp;' . ( $attr_name == $field_fields[0] ? "<a href='?action=db&op=table_data&table=$attr&database=$database' >data</a>" : '' )."</td>";
				}
				echo $th.'</tr>';
				echo $td.'</tr>';
			}
			else{
				echo '<tr>';
				foreach ( $tab as $attr_name => $attr ) {
					echo "<td>".( $attr_name == $field_fields[0] ? "<a href='?action=db&op=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					echo '&nbsp;&nbsp;' . ( $attr_name == $field_fields[0] ? "<a href='?action=db&op=table_data&table=$attr&database=$database' >data</a>" : '' )."</td>";
				}
				echo '</tr>';
			}
		}
		echo '</table>';
}

?>