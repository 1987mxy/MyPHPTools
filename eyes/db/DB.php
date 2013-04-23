<?php

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
	$my_path = './';
}
else{
	$my_path = './dir/';
}

include_once 'db_mysql.class.php';

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
}

if( file_exists( ROOT . '/include/config.inc.php' ) ){
	include_once ROOT . '/include/config.inc.php';
}
else{
	echo '<!-- get my sql config -->';
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
	case 'ajax_get_where':
		$sqls = explode(";",$sql);
		$sql = empty( $sqls ) ? '' : $sqls[ 0 ];
		$table_rows = stristr( $sql, 'select' ) ? $db -> select( $sql ) : $db -> select( "SELECT * FROM `$database`.`$table`" );
		if( empty( $table_rows ) ) exit( 'NULL' );
		$return_sql = '';
		$row_values = array();
		foreach( $table_rows as $i => $row ){
			if( $i == 0 ){
				$fields = array_keys( $row );
				$return_sql .= "`$fields[0]` IN ";
			}
			$values = array_values( $row );
			$row_values[] = $values[ 0 ];
		}
		$return_sql = $return_sql . '("' . implode( '", "', array_unique( $row_values ) ) . '")';
		echo $return_sql;
		break;
	case 'ajax_get_tdata':
		$sqls = explode(";",$sql);
		$sql = empty( $sqls ) ? '' : $sqls[ 0 ];
		if( stristr( $sql, 'select' ) && !stristr( $sql, ' join ' ) ){
			$table_rows = $db -> select( $sql );
			preg_match( '/from +`?([^.`]+)`?\.?`?([^ `]*)`?/i', $sql, $tables );
			$database = $tables[ 2 ] ? $tables[ 1 ] : $database;
			$table = $tables[ 2 ] ? $tables[ 2 ] : $tables[ 1 ];
		}
		else{
			$table_rows = $db -> select( "SELECT * FROM `$database`.`$table`" );
		}
		if( empty( $table_rows ) ) exit( 'NULL' );
		$key = $db -> get_one( "SHOW COLUMNS FROM `$database`.`$table` WHERE `Key`='PRI'" );
		$key = $key[ 'Field' ];
		$return_sql = '';
		foreach( $table_rows as $i => $row ){
			if( empty( $row ) ) continue;
			if( $key ) unset( $row[ $key ] );
			if( $i == 0 ){
				$return_sql .= "INSERT INTO `$table`(`" . implode( '`,`', array_keys( $row ) ) . "`) VALUES
";
			}
			$return_sql .= "('" . implode( "','", array_map( $db -> escape, array_values( $row ) ) ) . "'),
";
		}
		header ( "Content-type: text/html; charset=gbk" );
		echo substr( $return_sql, 0, -3 ) , ';';
		break;
	case 'columns':
		if( !$table ) break;
		echo '<script type="text/javascript" src="' . $my_path . 'js/jquery.js" ></script>';
		$table_sql = $database ? 'show full columns from `'.$database.'`.`'.$table . '`' : 'show full columns from `'.$table . '`';
		$table_columns = $db -> select($table_sql);
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '.' . $table . '</h3>';
		echo "<a href='?action=db&op=tables&database=$database' >back</a>
				<input onclick='$(\".data\").hide();$(\".table\").show();' type='button' value='table' >
				<input onclick='$(\".data\").hide();$(\".array\").show();' type='button' value='array' >
				<input onclick='$(\".data\").hide();$(\".sql\").show();' type='button' value='sql' >";
		echo '<div class="data table"><table border="1px" >';
		$fields_array = '';
		$fields_sql = array();
		foreach ( $table_columns as $i => $col ) {
			if( empty( $col ) ) continue;
			$fields_array .= "'$col[Field]'		=> ''," . ( $col[Comment] ? "		//$col[Comment]" : '' ) ."
	";
			$fields_sql[] = "`$col[Field]`";
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
		echo '</table></div>';
		echo '<div class="data array" style="display:none;" ><pre>array( ' . substr( $fields_array, 0, -3 ) . ' );</pre></div>';
		echo '<div class="data sql" style="display:none;" ><pre>' . implode( ',
', $fields_sql ) . '</pre></div>';
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
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' >
				<textarea name='sql' cols='100' rows='5' >" . stripslashes($sql) . "</textarea>
				<input name='sql_submit' type='submit' value='running' />
				</form>";
		echo '<script type="text/javascript" src="' . $my_path . 'js/jquery.js"></script>
				<script type="text/javascript">
				function get_where(){
					$.post("?",{action:"db",op:"ajax_get_where",database:"' . ( $database ? $database : $db->dbname ) . '",table:"' . $table . '",sql:$("textarea[name=\"sql\"]").val()},function(where){
						$(".where pre").text(where);
						$(".data").hide();
						$(".where").show();
					});
				}
				function get_tdata(){
					$.post("?",{action:"db",op:"ajax_get_tdata",database:"' . ( $database ? $database : $db->dbname ) . '",table:"' . $table . '",sql:$("textarea[name=\"sql\"]").val()},function(tdata){
						$(".tdata pre").text(tdata);
						$(".data").hide();
						$(".tdata").show();
					});
				}
				</script>';
		$field_sql = $database ? 'show full columns from '.$database.'.'.$table : 'show full columns from '.$table;
		$table_fields = $db -> select($field_sql);
		$field_list = array();
		foreach ( $table_fields as $field ) {
			$field_list[] = $field['Field'];
		}
	
		if ( !$select_flag ) $table_sql = $database ? 'select `' . join( '`, `', $field_list ) . '` from '.$database.'.'.$table : 'select `' . join( '`, `', $field_list ) . '` from '.$table;
		$table_rows = $db -> select($table_sql . " limit 0, 30000");
		echo '<font size="3" color="red" style="font-weight:bolder">'.(count($table_rows)>=30000?'限制3w条记录':count($table_rows)).'</font><br>';
		echo "<a href='?action=db&op=tables&database=$database' >back</a>
				<input onclick='$(\".data\").hide();$(\".table\").show();' type='button' value='table' />
				<input onclick='get_where()' type='button' value='where' />
				<input onclick='get_tdata()' type='button' value='data' />";
		echo '<div class="data table" ><table border="1px" >';
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
		echo '</table></div>';
		echo '<div class="data where" style="display:none;" ><pre></pre></div>';
		echo '<div class="data tdata" style="display:none;" ><pre></pre></div>';
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