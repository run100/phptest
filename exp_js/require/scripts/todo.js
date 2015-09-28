(function(win){
  require.config({
    baseUrl: "scripts/lib", // 绝对目录
    paths : {
      jquery: 'jquery.min',
      underscore: 'underscore.min',
      backbone: 'backbone.min',
      td: 'td'
    },
    shim: {
　　　　　 'underscore':{
　　　　　　　　exports: '_'
　　　　　　},
　　　　　　'backbone': {
　　　　　　　　deps: ['underscore', 'jquery'],
　　　　　　　　exports: 'Backbone'
　　　　　　}
　　}
  });

  require(['jquery', 'underscore', 'backbone', 'td'], function($, _, backbone, td){
    console.log(111);
  });
})(window);
