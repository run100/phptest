define(['jquid', 'jqvali', 'jqvalicn', 'jqlazy'], function (){
	var view = Backbone.View.extend({
		el: 'body',
		events: {
			'mouseenter .list-product-list'    : 'getFocus',
			'mouseleave .list-product-list'    : 'getFocus',
			'click      .list-small-pic li'    : 'showImg',
			'click #ser_chlid'                 : 'ser_clid',
			'click #ser_price'                 : 'ser_clid',
			'keyup #keyword_c'                 : 'per_ser_clid',
			'click .smore'                     : 'showMoreClass',
			'mouseenter .search_drop'          : 'showSearch',
			'mouseleave .search_drop'          : 'hideSearch',
			'click .complex_del'               : 'changeSearch',
			'mouseenter .basesearch-moreclass' : 'showClass',
			'mouseleave .basesearch-moreclass' : 'hideClass'
			
		},
		initialize: function (){
			$('.list-stop-right').each(function (){
				var $this = $(this);
				var $list = $('>ul', $this);
				if(+$list.height() > +$this.height()) $('.smore', $this).show();
			});
			$('img[lazy]').lazyload({
				threshold  : 800,
				effect     : 'fadeIn',
				effectspeed: 1000
			});

			var conW = 0;
			var count = $('.list_search_con li.select').size();
			$('.list_search_con li.select').each(function (){
				var $this = $(this);
				conW  += $this.width();
			});
			if(count>1) conW +=  (count -1) * 9 ;
			if(conW > 900) $(".list_top").addClass("addhigh");
			else $(".list_top").removeClass("addhigh");

		},
		getFocus: function (e){
			$(e.currentTarget)[e.type == 'mouseenter' ? 'addClass' : 'removeClass']('hover');
		},
		showImg: function (e){
			var $obj = $(e.currentTarget);
			$obj.addClass('sel').siblings().removeClass('sel');

			var $img = $obj.find('img');
			var src = $img.attr('src');
			src = src.replace(/30x30/, '220x220');

			var $showImg = $obj.closest('.list-product-list').find('.bigpic li img');
			$showImg.attr('src', src);
		},
		per_ser_clid: function (e){
			if(e.keyCode == 13) this.ser_clid();
		},
		ser_clid:function (){
			var price_s   = $("#price_s").val();
			var price_e   = $("#price_e").val();
			var keyword_c = $("#keyword_c").val();
			var spm=$("meta[name='data-spm']").attr("content");
			var scm=$("meta[name='data-scm']").attr("content");
			
			var freeship = ($('[name="freeship"]:checked').length?1:0);
			var url = "index.php?act=search&keyword=" + keyword_c + "&price_s=" + price_s + "&price_e=" + price_e;
			url = url + "&xscm=" + scm + "&xspm=" + spm;
			
			if(freeship==1)
				url += '&freeship=1';
			window.location.href = url;
		},
		showMoreClass: function (e){
			var $obj = $(e.currentTarget);
			var $container = $obj.closest('.list-stop-right');

			if($obj.hasClass('list-down')){
				$container.css({
					'max-height': '120px'
				});
				$obj.removeClass('list-down').html('更多');
				return;
			};
			$container.css({'max-height':'none'});
			$obj.addClass('list-down').html('收起');
		},
		showSearch: function(e){
			var $obj = $(e.currentTarget);
			var count = $obj.find('.drop_bd ul').size();
			var w = $obj.find('.drop_bd ul:first').width(); 
			w = w* count ;
			$obj.find('.drop_bd').show().css({'width':w});
			$obj.find('.drop_bd div').css({'width':w});
		},
		hideSearch: function(e){
			var $obj = $(e.currentTarget);
			$obj.find('.drop_bd').hide().css({'width':0});
			$obj.find('.drop_bd div').css({'width':0});
		},
		showClass: function() {
			$('.basesearch-moreclass').addClass('moreclass-hover');
		},
		hideClass: function() {
			$('.basesearch-moreclass').removeClass('moreclass-hover');
		},
		changeSearch: function(e){
			$(e.currentTarget).parent().remove();
			var obj = $('.complex_con a[wytype]');
			if(obj.length==0)
				$('.complex_search').prev().remove();
			var b_id = null;
			var a_id = '';
			for(var i=0; i<obj.length; i++){
				if($(obj[i]).attr('wytype')=='brand'){
					b_id = $(obj[i]).attr('rel');
				}else if($(obj[i]).attr('wytype')=='attr'){
					if(a_id!='')
						a_id += '_';
					a_id += $(obj[i]).attr('rel');
				}
			}
			var url = window.location.href;
			url = url.replace(/(b_id\=\d*)/, '');
			url = url.replace(/(a_id\=[\d_]*)/, '');
			url = url.replace(/(\&\&)/, '&');
			if(url.substr(-1)!='&')
				url += '&';
			if(b_id)
				url += 'b_id='+b_id+'&';
			if(a_id)
				url += 'a_id='+a_id;
			window.location.href = url;
		}
		// searchPre :function(e){
		// 	var $searchObj = $(e.currentTarget).parent();
		// 	var $listSearch = $searchObj.find(".list_search_left");
		// 	var $searchCon 	= $searchObj.find(".list_search_con");
		// 	var searchW = $listSearch.width();
		// 	var conW = $searchCon.width();
		// 	console.log(1,searchW,conW);
		// 	var l = $searchCon.attr("left");
		// 	if(l > 0)return false;
		// 	l = 0;
		// 	$searchCon.attr('left', l).animate({'left':l});
		// 	$searchObj.find('.complex_pre').css({'visibility':'hidden'});
		// 	$searchObj.find('.complex_next').css({'visibility':'visible'});
		// },
		// searchNext:function(e){
		// 	var $searchObj = $(e.currentTarget).parent();
		// 	var $listSearch = $searchObj.find(".list_search_left");
		// 	var $searchCon 	= $searchObj.find(".list_search_con");
		// 	var searchW = $listSearch.width();
		// 	var conW = $searchCon.width();
		// 	console.log(2,searchW,conW);
		// 	var l = $searchCon.attr("left");
		// 	if(l > 0)return false;
		// 	l = l + (conW - searchW);
		// 	$searchCon.attr('left', l).animate({'left':l});
		// 	$searchObj.find('.complex_next').css({'visibility':'hidden'});
		// 	$searchObj.find('.complex_pre').css({'visibility':'visible'});
		// }

	});
	(function(window) {
		var listTop = $('.list-default').offset().top;

		$(window).scroll(function() {
			var scrollTop = $(window).scrollTop();

			if (scrollTop >= listTop) {
				$('.list-default').addClass('list-fixed');
			} else {
				$('.list-default').removeClass('list-fixed');
			}
	    });
	})(window)

	return view;
});