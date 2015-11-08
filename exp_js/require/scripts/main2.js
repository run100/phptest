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
    /*var tom = new Backbone.Model({'name':'tom'});  // 创建学生tom
    var peter = new Backbone.Model({'name':'peter'});  // 创建学生tom

    var students = new Backbone.Collection(); // tom和peter都是学生
    students.add( tom );
    students.add( peter );*/

    // console.log( JSON.stringify(students) );
  });
})(window);
