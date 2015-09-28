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
    //console.log(Backbone);
    $('#divhtml').html( math.add(1, 10) );
    //console.log($);
  });
})(window);



