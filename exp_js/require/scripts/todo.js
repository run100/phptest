(function(win){
  require.config({
    baseUrl: "scripts/lib", // 绝对目录
    paths : {
      jquery: 'jquery.min',
      underscore: 'underscore.min',
      backbone: 'backbone.min',
      bbstorage: 'backbone.localStorage',
      json :　'json2',
      td: 'td'
    },
    shim: {
　　　　　 'underscore':{
　　　　　　　　exports: '_'
　　　　　　},
　　　　　　'backbone': {
　　　　　　　　deps: ['underscore', 'jquery'],
　　　　　　　　exports: 'Backbone'
　　　　　　},
          bbstorage: {
            deps: ['underscore', 'Backbone', 'jquery'],
            exports: 'bbstorage'
          }
　　}
  });

  require(['jquery', 'underscore', 'backbone', 'json', 'bbstorage', 'td'], function($, _, json,bbstorage, backbone, td){
    console.log('todo.js');
  });
})(window);
