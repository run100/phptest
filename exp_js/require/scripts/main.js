/*require.config({
  paths : {
    jquery: 'jquery.min',
    underscore: 'underscore.min',
    backbone: 'backbone.min',
  }
});*/

(function(win){


  require.config({
    baseUrl: "scripts/lib", // 绝对目录
    paths : {
      jquery: 'jquery.min',
      underscore: 'underscore.min',
      backbone: 'backbone.min',
      math: 'math'
    }
  });

  require(['jquery', 'underscore', 'backbone', 'math'], function($, _, Backbone, math){
    // console.log(Math.random());
    // $('#divhtml').html( math.add(1, 10) );
    var User = Backbone.Model.extend({
        defaults : {
            name : 'tom'
        }
    });

    var View = Backbone.View.extend({

        initialize : function(){
            console.log("initialize");

            this.listenTo( this.model , 'change' , this.show );
            // 当与这个view绑定的model数据发生变化的时候，调用show方法

        },
        show : function(model){ // 向页面中输出信息
            $('body').append( '<div>'+ this.model.get('name')+
            '</br>也可以通过参数调用</br>' + model.get('name') +'</div>' );
        }

    });

    var tom = new User;
    var view = new View({model:tom});       // 创建view实体

    setTimeout(function(){
        tom.set('name','jack');     // 修改数据
    }, 1000);       // 一秒后修改数据，触发show


  });
})(window);



