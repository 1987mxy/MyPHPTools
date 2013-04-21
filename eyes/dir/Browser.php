<?php 
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
			    url: "?",
			    data: "action=dir&ajax=1&path="+path,
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
include_once 'Config.php';
include_once 'Function.php';

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
}

echo $ajax==0?'<div style="float:left; width:25%; height:100%; overFlow:scroll; " >':''; 
superDir( ( isset( $path )?$path:ROOT ), $ajax ); 
echo $ajax==0?'</div>':''; 
echo $ajax==0?'<iframe id="file_content" style="float:right; width:75%; height:100%; border-width:0px; " >':''; 
echo $ajax==0?'</iframe>':''; 
?>