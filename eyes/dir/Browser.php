<?php 

if( !defined( 'ROOT' ) ){
	define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
	extract( $_REQUEST );
	$my_path = './';
}
else{
	$my_path = './dir/';
}

if ( !isset( $ajax ) ) { 
	$ajax = 0; 
?>

<style>
	.js {
		background:url(<?php echo $my_path;?>ico/js.gif) no-repeat; 
	}
	.php {
		background:url(<?php echo $my_path;?>ico/php.gif) no-repeat; 
	}
	.css {
		background:url(<?php echo $my_path;?>ico/css.gif) no-repeat; 
	}
	.html {
		background:url(<?php echo $my_path;?>ico/html.gif) no-repeat; 
	}
	.image {
		background:url(<?php echo $my_path;?>ico/image.gif) no-repeat; 
	}
	.py {
		background:url(<?php echo $my_path;?>ico/python.gif) no-repeat; 
	}
	.sql {
		background:url(<?php echo $my_path;?>ico/sql.gif) no-repeat; 
	}
	.sh {
		background:url(<?php echo $my_path;?>ico/file.gif) no-repeat; 
	}
	.text {
		background:url(<?php echo $my_path;?>ico/file.gif) no-repeat; 
	}
	.file {
		background:url(<?php echo $my_path;?>ico/file.gif) no-repeat; 
	}
	.open_folder {
		background:url(<?php echo $my_path;?>ico/open_folder.gif) no-repeat; 
	}
	.close_folder {
		background:url(<?php echo $my_path;?>ico/close_folder.gif) no-repeat; 
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
<script type="text/javascript" src="<?php echo $my_path;?>js/jquery.js" ></script>

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
		$( '#file_content' ).attr( 'src', "<?php echo $my_path;?>Coding.php?file=1&path="+path ); 
	}
</script>
<?php 
	}
?>


<?php 
include_once 'Config.php';
include_once 'Function.php';



echo $ajax==0?'<div style="float:left; width:25%; height:100%; overFlow:scroll; " >':''; 
superDir( ( isset( $path )?$path:ROOT ), $ajax ); 
echo $ajax==0?'</div>':''; 
echo $ajax==0?'<iframe id="file_content" style="float:right; width:75%; height:100%; border-width:0px; " >':''; 
echo $ajax==0?'</iframe>':''; 
?>