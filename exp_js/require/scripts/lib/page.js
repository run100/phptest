define(['underscore', 'backbone'], function(_, Backbone){


  var model = new Backbone.Model();
  var col = new Backbone.Collection();
  var view = new Backbone.View();



  //_.each([1,2,3], alert);
  //$('#divshow').html('world');

  /*var User = Backbone.Model.extend({
    sayHello : function(){  //实例方法
            console.log("hello");
        }
    },
    {
    sayWorld : function(){  //静态方法
            console.log("world");
        }
    }
  );

  var tom = new User;
  tom.sayHello();
  User.sayWorld();*/

  /*var User = Backbone.Model.extend({
      defaults : {        // 默认属性，但是子类也会继承
          "name": "tom"
      },
      sayHello : function(){  // 父类的方法
          console.log("hello");
      }
  });

  var ChildUser = User.extend({   // ChildUser 继承自User
      sayChild : function(){  // 子类的方法
          console.log("child");
      }
  });

  var child = new ChildUser;  // 创建ChildUser实例
  child.sayHello();
  child.sayChild();
  console.log(child.get("name"));  // 子类继承父类属性
*/


  // initialize
  /*var User = Backbone.Model.extend({
      defaults : {
          name : 'tom'    // 默认的名字
      },
      initialize : function(){  //当model创建的时候，调用
          console.log("initialize");

          this.on('change',function(){    // 当数据发生变化的时候触发
              console.log("此时我的名字是："+this.get("name"));
          });

      }
  });

  var tom = new User;
  tom.set('name','jack'); // 修改模型的数据，会被change检测到*/

  // view

  /*var BodyView = Backbone.View.extend({

      el : $('body'), // 如果没有指定el，el就会是个空div
      events : {
          'click input' : 'sayHello', // 点击input的时候调用sayHello方法
          'mouseover li' : 'moveLi'// 鼠标悬浮li标签的时候调用moveLi方法
      },
      sayHello : function(){
          console.log("Hello");
      },
      moveLi : function(){
          console.log("mouseover li");
      }

  });

  var view = new BodyView;*/
  /*var datajson = { name0 : 'jack', name1 : 'hi' };
  var Name = Backbone.Model.extend({
      defaults : {
        name : 'tom'
      }
  });

  var NameView = Backbone.View.extend({

      initialize : function(){

          this.listenTo( this.model , 'change' , this.showName );

      },
      showName : function(model){
          // $('body').append( "<div>" + model.get("name") + "</div>" );
          // 不使用template的时候html代码与js写在一起
          $('body').append( this.template(this.model.toJSON()) );
          // 使用模版之后，html代码与js代码相分离
      },
      template: _.template($('#name').html())
      // _.template中传入需要编译的模版
      // 返回的结果就是编译后的html代码
      // 最后在showName中调用，将编译后的html显示到body中
  });

  var name = new Name;
  var nameView = new NameView({model:name});

  for ( key in datajson ) {
    //console.log(datajson[key]);
    name.set('name', datajson[key])
  }*/



});
