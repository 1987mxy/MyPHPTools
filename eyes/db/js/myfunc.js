var search_index = 0;
var search_list = new Array();

function search( obj, event )
{
	if( event.keyCode!=13 && $(obj).val()!='' ) return;
	$('.search_keyword').each(function(){
		$(this).after($(this).text());
		$(this).remove();
	});
	search_index = 0;
	search_list = new Array();
	$("table tr:gt(0)").not(":contains('"+$(obj).val()+"')").hide()
	.end().filter(":contains('"+$(obj).val()+"')").show()
	.find("*").each(function(i){
		if( $(obj).val() == '' ) return false;
		if( $(this).text() != $(this).html() ){
			return;
		}
		src = $(this).text();
		now = $(this).text().replace($(obj).val(),'<a name="k_'+i+'" class="search_keyword">'+$(obj).val()+'</a>');
		if( src!=now )
		{
			search_list[search_list.length] = i;
			$(this).html(now);
		}
	});
	genSearchWidget();
}

function genSearchWidget()
{
	$('#search_op').empty();
	if( search_list.length <= 0 ) return;
	if( search_index <= 0 ) $('#search_op').html('<a onclick="nextK();" >Next</a>');
	else if( search_index >= search_list.length - 1 ) $('#search_op').html('<a onclick="prevK();" >Prev</a>');
	else $('#search_op').html('<a onclick="prevK();" >Prev</a>&nbsp;&nbsp;<a onclick="nextK();" >Next</a>');
}

function nextK()
{
	if( search_index >= search_list.length - 1 ) return false;
	search_index++;
	anchor_top = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().top - $('#console').height();
	$('.search_selected').removeClass('search_selected');
	$('.search_keyword[name="k_'+search_list[search_index]+'"]').addClass('search_selected');
	genSearchWidget();
	$("html, body").animate({scrollTop:anchor_top}, 300);
}

function prevK()
{
	if( search_index <= 0 ) return false;
	search_index--;
	anchor_top = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().top - $('#console').height();
	$('.search_selected').removeClass('search_selected');
	$('.search_keyword[name="k_'+search_list[search_index]+'"]').addClass('search_selected');
	genSearchWidget();
	$("html, body").animate({scrollTop:anchor_top}, 300);
}

function showLayer( obj )
{
	$(".data").hide();
	$("."+$(obj).val()).show();
}

function get_where(){
	$.post("?",{action:"db",op:"ajax_get_where",database:"' . ( $database ? $database : $db->dbname ) . '",table:"' . $table . '",sql:$("textarea[name=\"sql\"]").val()},
			function(where){
				$(".where pre").text(where);
				$(".data").hide();
				$(".where").show();
			});
}

function get_tdata(){
	$.post("?",{action:"db",op:"ajax_get_tdata",database:"' . ( $database ? $database : $db->dbname ) . '",table:"' . $table . '",sql:$("textarea[name=\"sql\"]").val()},
			function(tdata){
				$(".tdata pre").text(tdata);
				$(".data").hide();
				$(".tdata").show();
			});
}

$(function(){
	$('.data').css('top',$('#console').height());
	anchor = $('#anchor').attr('anchor');
	anchor_top = $('a[name="'+anchor+'"]').offset().top - $('#console').height();
	$("html, body").animate({scrollTop:anchor_top}, 1);
});