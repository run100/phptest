(function(win){
  require.config({
    baseUrl: "scripts/lib", // 绝对目录
    paths : {
      jquery: 'jquery.min',
      underscore: 'underscore.min',
      backbone: 'backbone.min',
      page: 'page'
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

  require(['jquery', 'underscore', 'backbone', 'page'], function($, _, backbone, page){

  });
})(window);
