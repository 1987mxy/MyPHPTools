var search_index = 0;
var search_list = new Array();

function search( obj, event )
{
	if( event.keyCode!=13 && $(obj).val()!='' ) return;
	$('#search_op img').show().nextAll('a').hide();
	$('.search_keyword').each(function(){
		$(this).after($(this).text());
		$(this).remove();
	});
	search_index = -1;
	search_list = new Array();
	var i = 0;
	$("table tr:gt(0)")
	.not(":contains('"+$(obj).val()+"')").hide()
	.end().filter(":contains('"+$(obj).val()+"')").show()
	.each(function(){
		if( $(obj).val() == '' ) return false;
		var flag = false;
		$(this).find("*").each(function(){
			if( $(this).text() != $(this).html() ) return;
			src = $(this).text();
			now = $(this).text().replace($(obj).val(),'<a name="k_'+i+'" class="search_keyword">'+$(obj).val()+'</a>');
			if( src!=now )
			{
				search_list[search_list.length] = i;
				$(this).html(now);
				flag = true;
				i++;
			}
		});
		if( flag == false ) $(this).hide();
	});
	$("html, body").animate({scrollTop:0,scrollLeft:0}, 300);
	genSearchWidget();
	$('#search_op img').hide().nextAll('a').show();
}

function genSearchWidget()
{
	if( search_list.length <= 0 ) $('#search_prev,#search_next').addClass('disabled');
	else if( search_index <= 0 ){
		$('#search_prev').addClass('disabled');
		$('#search_next').removeClass('disabled');
	}
	else if( search_index >= search_list.length - 1 ){
		$('#search_prev').removeClass('disabled');
		$('#search_next').addClass('disabled');
	}
	else $('#search_prev,#search_next').removeClass('disabled');
}

function nextK()
{
	if( search_index >= search_list.length - 1 ) return false;
	search_index++;
	anchor_top = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().top - $('#console').height() - 10;
	anchor_left = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().left;
	$('.search_selected').removeClass('search_selected');
	$('.search_keyword[name="k_'+search_list[search_index]+'"]').addClass('search_selected');
	genSearchWidget();
	$("html, body").animate({scrollTop:anchor_top,scrollLeft:anchor_left}, 300);
}

function prevK()
{
	if( search_index <= 0 ) return false;
	search_index--;
	anchor_top = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().top - $('#console').height() - 10;
	anchor_left = $('.search_keyword[name="k_'+search_list[search_index]+'"]').offset().left;
	$('.search_selected').removeClass('search_selected');
	$('.search_keyword[name="k_'+search_list[search_index]+'"]').addClass('search_selected');
	genSearchWidget();
	$("html, body").animate({scrollTop:anchor_top,scrollLeft:anchor_left}, 300);
}

function showLayer( obj )
{
	$(".data").hide();
	$("."+$(obj).val()).show();
}

function get_where(){
	$.post("?",{action:"db",
				op:"ajax_get_where",
				database:$('#db').text(),
				table:$('#table').text(),
				sql:$("textarea[name='sql']").val()},
			function(where){
				$(".where pre").text(where);
				$(".data").hide();
				$(".where").show();
			});
}

function get_tdata(){
	$.post("?",{action:"db",
				op:"ajax_get_tdata",
				database:$('#db').text(),
				table:$('#table').text(),
				sql:$("textarea[name='sql']").val()},
			function(tdata){
				$(".tdata pre").text(tdata);
				$(".data").hide();
				$(".tdata").show();
			});
}

$(function(){
	$('.data').css('top',$('#console').height() + 10);
	if( typeof( $('#anchor').attr('anchor') ) != 'undefined' ){
		anchor = $('#anchor').attr('anchor');
		anchor_top = $('a[name="'+anchor+'"]').offset().top - $('#console').height() - 10;
		$("html, body").animate({scrollTop:anchor_top,scrollLeft:0}, 1);
	}
});