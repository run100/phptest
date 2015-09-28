define(function (){
	/**
	 * 模板调用伪控制器
	 */
	var Controller = Backbone.View.extend({
		el: 'body',
		initialize: function (){
			var _this = this;
		},
		render: function (){
			var _this = this;
			require(['./view/init', './view/adsv'], function (View, Adsv){
				new View.global.document;
				new Adsv;
				if($('.naver').length > 0) new View.global.nav;
				if($('.sider').length > 0) new View.global.sider;
			});

			switch(CATEGORY){
				case 'login': // 登录页
					switch(MODEL){
						case 'register':
						case 'addphone':
							require(['./view/register'], function (View){
								new View.register;
							});
							break;
						default:
							require(['v'], function (View){
								new View;
							});
							break;
					};
					break;

				case 'index': // 首页
					require(['v'], function (View){
						new View.index();
					});
					break;

				case 'search': // 商品搜索列表页
					require(['v'], function (View){
						new View();
					});
					break;

				case 'goods': // 商品详情页
					require(['v'], function (View){
						new View.detail;
						new View.search;
						new View.nav;
						new View.remind;
					});
					break;

				case 'cart': // 购物车页
					require(['v'], function (View){
						new View;
					});
					break;

				case 'hongbao':
					require(['v'], function (View){
						new View;
					});
					break;

				case 'buy': // 购买页
					switch(MODEL){
						case 'buy_step1':
						case 'cash_voucher':
							require(['v'], function (View){
								new View.address;
							});
							break;
					};
					break;

				case 'article': // 帮助页
					require(['./view/helper']);
					break;

				case 'voucher': // 优惠劵
					switch(MODEL){
						case 'index':
							require(['v'], function (View){
								new View.voucher;
							});
							break;
					};
					break;

				case 'member':
					require(['member', 'v'], function (Member, View){
						new View.home;
						new Member.nav;
						new Member.topnav;

					});
					break;

				case 'member_goodsbrowse':
					require(['member', 'v'], function (Member, View){
						new View.browse;
						new Member.nav;
						new Member.topnav;
					});
					break;

				case 'member_order':
					require(['member', './view/order'], function (Member,Order){
						new Order.order;
						new Member.nav;
						new Member.topnav;
						new Member.date;
					});
					break;

				case 'member_address':
					require(['member', 'v'], function (Member, View){
						new View.address;
						new Member.nav;
						new Member.topnav;
					});
					break;

				case 'member_points'   :
				case 'member_hongbao'  :
				case 'member_return'   :
				case 'member_vr_refund':
				case 'member_cash'     :
				case 'member_vr_order' :
				case 'member_consult'  :
				case 'member_inform'   :
				case 'member_security' :
					require(['member'], function (Member){
						new Member.nav;
						new Member.topnav;
						new Member.date;
					});
					break;
				case 'member_voucher'  :
					require(['member', 'v'], function (Member, View){
						new View.voucher;
						new Member.nav;
						new Member.topnav;
					});
					break;
				case 'member_favorites':
					require(['member', 'v'], function (Member, View){
						new View.favorites;
						new Member.nav;
						new Member.topnav;
					});
					break;

				case 'member_information':
					require(['member', 'v'], function (Member, View){
						new View.memberinfo;
						new Member.nav;
						new Member.topnav;
					});
					break;

				case 'member_evaluate':
					require(['member', 'v'], function (Member, View){
						new View.addimage;
						new Member.nav;
						new Member.topnav;
					});
					break;

				case 'member_refund':
					switch(MODEL){
						case 'index':
							require(['member'], function (Member){
								new Member.nav;
								new Member.topnav;
								new Member.date;
							});
							break;
						case 'v':
							require(['member'], function (Member){
								new Member.nav;
								new Member.topnav;
							});
							break;
						case 'view':
							require(['member'], function (Member){
								new Member.nav;
								new Member.topnav;
							});
							break;
						default:
							require(['member', 'v'], function (Member, View){
								new View.addrefund;
								new Member.nav;
								new Member.topnav;
							});
							break;
					};
					break;

				case 'member_complain':
					switch(MODEL){
						case 'complain_show':
							require(['member'], function (Member){
								new Member.nav;
								new Member.topnav;
							});
							break;
						default:
							require(['member', 'v'], function (Member, View){
								new Member.nav;
								new Member.topnav;
								new View.complain;
							});
							break;
					};
					break;
				case 'member_complain':
					switch(MODEL){
						case 'complain_show':
							require(['member'], function (Member){
								new Member.nav;
								new Member.topnav;
							});
							break;
						default:
							require(['member', 'v'], function (Member, View){
								new Member.nav;
								new Member.topnav;
								new View.complain;
							});
							break;
					};
					break;
				case 'show_store':
					switch(MODEL){
						case 'goods_all':
							require(['v'], function (View){
								new View.search;
							});
							break;
					};
					break;
				case 'show_joinin':
					switch(MODEL){
						case 'transfer':
							require(['v'], function(View) {
								new View.search;
							});
							break;
					}
					break;
				// 交易快照
				case 'snapshot':
					switch(MODEL) {
						case 'index':
							require(['v'], function(View) {
								new View;
							});
							break;
					}
					break;

				case 'v':
					require(['v'], function (View){
						new View;
					});
					break;
				// 活动页
				case 'activity':
					switch(MODEL){
						default:
							require(['v'], function (View){
								new View;
							});
					};
					break;
				case 'furniture':
					require(['./view/furniture'], function (View){
						new View;
					});
					break;
				case 'card':
					require(['./view/card'], function (View){
						new View.apply;
					});
					break;

				case 'link': // 友情链接
					require(['v'], function (View){
						new View.topnav();
					});
					break;

				case 'decoration_category':
					require(['v'], function (View){
						new View;
					});
					break;
					
				case 'decoration_news':
					require(['v'], function (View){
						new View;
					});
					break;

				case 'decoration_index':
					require(['v'], function (View){
						new View;
					});
					break;

			};

			return this;
		}

	});

	return Controller;
});