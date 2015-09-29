define(['jquid', 'jqvali', 'jqvalicn'], function (){
	var view = {};
	/**
	 * 表单验证视图
	 */
	view.register = Backbone.View.extend({
		el: '.register',
		events: {
			'click .xin, .yin'			  : 'msg'
		},
		initialize: function (){
			$('#phone').focus();
			this.render();
		},
		render: function (){
			var _this = this;
			base.vali('blur', 'bottomLeft', function (){
				_this.submit();
			});
			return _this;
		},
		// 表单提交
		submit: function (){
			var data = {
				phone      : $('#phone').val(),
				code       : $.trim($('#code').val()),
				form_submit: 'ok'
			};
			var op='finish';
			if(MODEL=='addphone')op='addphone';
			base.ajax('login', op, data, 'POST', function (r){
				//绑定手机
				if (op == 'addphone') {
					$('#derr').html(r.msg + '<span class="succJumpWait">(3s)</span>').dialog({
						width       : 340,
						height      : 172,
						modal       : true,
						draggable   : false,
						bgiframe    : true,
						resizable   : false,
						dialogClass : 'ui-derr',
						closeText   : '关闭',
		                close: function() {
		                	if($('#refererurl').length>0){
								location.href = $('#refererurl').val();
							}else{
								location.href = '/index.php?act=login&op=check&uz=' + r.data;
							}
		                },
						buttons     : {
							'确定' : function (){
								$(this).dialog('close');
							}
						}
					});
					var second = 3;
					var $succJumpWait = $('.succJumpWait');

					var times = setInterval(function (){
						second -= 1;
						if(second < 0){
							$('#derr').dialog('close');
							clearInterval(times);
						} else {
							$succJumpWait.text('(' + second + 's)');
						}
					}, 1000);
					return;
				}
				//注册账号
				if($('#refererurl').length>0){
					location.href = $('#refererurl').val();
				}else{
					location.href = '/index.php?act=login&op=check&uz=' + r.data;
				}
			});
		},
		// 手机短信语音验证
		msg: function (e){
			var _this = this;
			var phone = $.trim($('#phone').val());

			if (!phone || phone.length != 11) {
				return;
			}

			var check_tmpl = $('#check_tmpl').html();
			$('#derr').html(check_tmpl).dialog({
				width       : 330,
				height      : 210,
				modal       : true,
				draggable   : false,
				bgiframe    : true,
				resizable   : false,
				dialogClass : 'ui-derr',
				closeText   : '关闭',
				title       : '',
				open    	:function(){
    				if(_this.checkdialog){
    					return;
    				}
    				_this.checkdialog = new view.dialog();
    			},
    			close: function(){
    				_this.checkdialog.remove();
    				_this.checkdialog = null;
    			},
				buttons     : {
					'确定' : function (){
						var captcha = $('#captcha').val();
						var nchash = $('#nchash').val();
						var $this = $(this);
						$.get(MALLURL + '/index.php?act=seccode&op=check', {
							nchash: nchash,
							captcha: captcha
						}, function(r) {
							if (r) {
								$this.dialog('close');
								_this.getMsg(e);
								return;
							}
							_this.checkdialog.changeSeccode();
							$('#captcha').val('');
							$('.capt_error').html('验证码错误');
						}, 'json');

					}
				}
			});
		},
		getMsg: function(e) {
			var data = {
				phone      : $('#phone').val(),
				type       : $(e.target).index(),
				form_submit: 'ok'
			};
			var isYin = !!$(e.currentTarget).hasClass('yin');

			var _this = this;
			base.ajax('login', 'regcode', data, 'POST', function (r){
				var msg = '验证码发送成功，请于' + TIME + '分钟内完成验证。';

				if (isYin) {
					msg += '您将接听到来自陌生电话的语音播报，如果给你带来不便请您谅解！';
				}

				base.err(msg);
				_this.again(60 - r.send_time);
			});
		},
		// 手机验证重发起限制
		again: function (sec){
			var obj = $('.wait');
			obj.text('正在为您努力请求，请稍等...').show().prev().hide();
			var timer = setInterval(function (){
				sec -= 1;
				if(sec < 0){
					obj.hide().prev().show();
					clearInterval(timer);
				}else obj.text( sec + 's' + '后可重新获取');
			}, 1000);
		}
	});
	view.dialog = Backbone.View.extend({
		el: '.checkcode',
		events: {
			'click [action="changecode"]' : 'changeSeccode'
		},
		initialize: function (){
			this.changeSeccode();
			this.render();
		},
		render: function (){
			return this;
		},
		changeSeccode: function(e){
			var nchash = $('#nchash').val();
			$('#codeimage').attr('src', 'index.php?act=seccode&op=makecode&nchash=' + nchash + '&t=' + Math.random());
		}
	});

	return view;
});