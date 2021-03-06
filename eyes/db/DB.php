<?php

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
	$my_path = './';
}
else{
	$my_path = './db/';
}

include_once 'db_mysql.class.php';

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
}

if( file_exists( ROOT . '/include/config.inc.php' ) ){
	include_once ROOT . '/include/config.inc.php';
}
elseif( file_exists( ROOT . '/config.php' ) ){
	include_once ROOT . '/config.php';
	define( 'DB_HOST', $db_config["db"]["host"] );
	define( 'DB_USER', $db_config["db"]["user"] );
	define( 'DB_PW', $db_config["db"]["pass"] );
	define( 'DB_NAME', $db_config["db"]["database"] );
	define( 'DB_PRE', '' );
	define( 'DB_CHARSET', $db_config["db"]["charset"] );
	define( 'DB_PCONNECT', 0 );
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
		
		echo '<meta http-equiv="Content-Type" content="text/html; charset=' . DB_CHARSET . '" />';
		echo '<script type="text/javascript" src="' . $my_path . 'js/jquery.js" ></script>';
		echo '<script type="text/javascript" src="' . $my_path . 'js/myfunc.js"></script>';
		echo '<link rel="stylesheet" type="text/css" href="' . $my_path . 'css/mystyle.css" />';
		$table_sql = $database ? 'show full columns from `'.$database.'`.`'.$table . '`' : 'show full columns from `'.$table . '`';
		$table_columns = $db -> select($table_sql);
		
		echo '<div id="console" >';
		echo '<h3><span id="db" >' . ( $database ? $database : $db->dbname ) . '</span>.<span id="table" >' . $table . '</span></h3>';
		echo "<a href='?action=db&op=tables&database=$database&anchor=$table' >back</a>
				<input onclick='$(\".data\").hide();$(\".table\").show();' type='button' value='table' >
				<input onclick='$(\".data\").hide();$(\".array\").show();' type='button' value='array' >
				<input onclick='$(\".data\").hide();$(\".sql\").show();' type='button' value='sql' >";
		echo '</div>';
		
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

		echo '<meta http-equiv="Content-Type" content="text/html; charset=' . DB_CHARSET . '" />';
		echo '<script type="text/javascript" src="' . $my_path . 'js/jquery.js"></script>';
		echo '<script type="text/javascript" src="' . $my_path . 'js/myfunc.js"></script>';
		echo '<link rel="stylesheet" type="text/css" href="' . $my_path . 'css/mystyle.css" />';
		
		echo '<div id="console" >';
		echo '<h3><span id="db" >' . ( $database ? $database : $db->dbname ) . '</span>.<span id="table" >' . $table . '</span>' . ( $select_flag ? '<br>查询 ：' . $table_sql : '' ) . '</h3>';
		echo "<form action='?$_SERVER[QUERY_STRING]' method='post' >
				<textarea name='sql' cols='100' rows='5' >" . stripslashes($sql) . "</textarea>
				<input name='sql_submit' type='submit' value='running' />
				</form>";
		echo "<p>search:&nbsp;&nbsp;<input onkeyup='search(this,event);' autocomplete='off' />&nbsp;&nbsp;<span id='search_op'><img src='" . $my_path . "img/loading.gif'><a id='search_prev' class='disabled' onclick='prevK();' >Prev</a>&nbsp;&nbsp;<a id='search_next' class='disabled' onclick='nextK();' >Next</a></span></p>";
		$field_sql = $database ? 'show full columns from '.$database.'.'.$table : 'show full columns from '.$table;
		$table_fields = $db -> select($field_sql);
		$field_list = array();
		foreach ( $table_fields as $field ) {
			$field_list[] = $field['Field'];
		}
	
		if ( !$select_flag ) $table_sql = $database ? 'select `' . join( '`, `', $field_list ) . '` from '.$database.'.'.$table : 'select `' . join( '`, `', $field_list ) . '` from '.$table;
		$table_rows = $db -> select($table_sql . " limit 0, 30000");
		echo '<font size="3" color="red" style="font-weight:bolder">'.(count($table_rows)>=30000?'限制3w条记录':count($table_rows)).'</font><br>';
		echo "<a href='?action=db&op=tables&database=$database&anchor=$table' >back</a>
				<input onclick='showLayer(this);' type='button' value='table' />
				<input onclick='get_where()' type='button' value='where' />
				<input onclick='get_tdata()' type='button' value='data' />";
		echo '</div>';
		
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
		exit();
		break;
	default:
		echo '<meta http-equiv="Content-Type" content="text/html; charset=' . DB_CHARSET . '" />';
		$db_sql = ( $database ? 'show table status from '.$database : 'show table status' ) . ' where Engine is not null';
		$db_tables = $db -> select($db_sql);
		echo '<script type="text/javascript" src="' . $my_path . 'js/jquery.js"></script>';
		echo '<script type="text/javascript" src="' . $my_path . 'js/myfunc.js"></script>';
		echo '<link rel="stylesheet" type="text/css" href="' . $my_path . 'css/mystyle.css" />';
		
		if( isset( $anchor ) ) echo "<span id='anchor' anchor='$anchor' ></span>";
		echo '<div id="console" >';
		echo '<h3>' . ( $database ? $database : $db->dbname ) . '</h3>';
		echo "<p>search:&nbsp;&nbsp;<input onkeyup='search(this,event);' autocomplete='off' />&nbsp;&nbsp;<span id='search_op'><img src='" . $my_path . "img/loading.gif'><a id='search_prev' class='disabled' onclick='prevK();' >Prev</a>&nbsp;&nbsp;<a id='search_next' class='disabled' onclick='nextK();' >Next</a></span></p>";
		echo "<input onclick='showLayer(this);' type='button' value='table' />
				<input onclick='showLayer(this);' type='button' value='dump' />
				<input onclick='showLayer(this);' type='button' value='rollback' />";
		if( $_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'] ){
			$priv_sql = "select `user`.`User`
						from `mysql`.`user` as `user`
						left join `mysql`.`db` as `db` on `db`.`User`=`user`.`User`
						where `db`.`Db`='" . ($database ? $database : $db->dbname) . "' and
								`db`.`Host`='%' and
								`db`.`Select_priv`='Y' and
								`db`.`Lock_tables_priv`='Y' and
								`user`.`Host`='%'
						order by `user`.`Password`";
			$dump_user = $db -> get_one( $priv_sql );
			if( !empty( $dump_user ) ){
				$dump_command = array( "mysqldump -u " . $dump_user['User'] . " -h " . $_SERVER['SERVER_ADDR'] . " -p " . ($database ? $database : $db->dbname) . " ", '',"--result-file=" . ($database ? $database : $db->dbname) . "_db_" . $_SERVER['SERVER_ADDR'] . ".sql" );
				$rollback_command = "mysql -u root -p --default-character-set=" . DB_CHARSET . " " . ($database ? $database : $db->dbname) . "<" . ($database ? $database : $db->dbname) . "_db_" . $_SERVER['SERVER_ADDR'] . ".sql";
			}
		}
		else{
			$dump_command = array( "mysqldump -u " . DB_USER . " -h " . DB_HOST . " -p " . ($database ? $database : $db->dbname) . " ", '',"--result-file=" . ($database ? $database : $db->dbname) . "_db.sql" );
			$rollback_command = "mysql -u root -p --default-character-set=" . DB_CHARSET . " " . ($database ? $database : $db->dbname) . "<" . ($database ? $database : $db->dbname) . "_db.sql";
		}
		echo '</div>';
		
		echo '<div class="data table" >';
		echo '<table border="1px" >';
		foreach ( $db_tables as $i => $tab ) {
			if( empty( $tab ) ) continue;
			if( !empty( $dump_command ) ) $dump_command[ 1 ] .= $tab[ 'Name' ] . ' ';
			$field_fields = array_keys( $tab );
			if( $i == 0 ){
				$th = '<tr>';
				$td = '<tr table="'.$tab[$field_fields[0]].'" >';
				foreach ( $tab as $attr_name => $attr ) {
					$th .= "<th>$attr_name</th>";
					$td .= '<td>'.( $attr_name == $field_fields[0] ? "<a name='$attr' href='?action=db&op=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					$td .= ( $attr_name == $field_fields[0] ? "&nbsp;&nbsp;<a href='?action=db&op=table_data&table=$attr&database=$database' >data</a>" : '' )."</td>";
				}
				echo $th.'</tr>';
				echo $td.'</tr>';
			}
			else{
				echo '<tr table="'.$tab[$field_fields[0]].'" >';
				foreach ( $tab as $attr_name => $attr ) {
					echo "<td>".( $attr_name == $field_fields[0] ? "<a name='$attr' href='?action=db&op=columns&table=$attr&database=$database' >" : '' ).$attr.( $attr_name == $field_fields[0] ? "</a>" : '' );
					echo ( $attr_name == $field_fields[0] ? "&nbsp;&nbsp;<a href='?action=db&op=table_data&table=$attr&database=$database' >data</a>" : '' );
					echo "</td>";
				}
				echo '</tr>';
			}
		}
		echo '</table>';
		echo '</div>';
		echo '<div class="data dump" style="display:none;" ><textarea style="width:100%;height:100%" >' . stripslashes( implode( '', $dump_command ) ) . '</textarea></div>';
		echo '<div class="data rollback" style="display:none;" ><textarea style="width:100%;height:100%" >' . stripslashes( $rollback_command ) . '</textarea></div>';
}

?>