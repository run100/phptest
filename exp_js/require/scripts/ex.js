(function(win){


  require.config({
    baseUrl: "scripts/lib", // 绝对目录
    paths : {
      jquery: 'jquery.min',
      underscore: 'underscore.min',
      backbone: 'backbone.min'
    }
  });

  require(['jquery', 'underscore', 'backbone'], function($, _, Backbone){

    // ex 3
    var Item  = Backbone.Model.extend({
      defaults: {
        part1: 'hello',
        part2: 'world'
      }
    });

    var List = Backbone.Collection.extend({
      model: Item
    });

    var ListView = Backbone.View.extend({
      el: $('body'),
      events: {
        'click button#add': 'addItem'
      },
      initialize: function(){
         _.bindAll(this, 'render', 'addItem', 'appendItem');

         this.collection = new List();
         this.collection.bind('add', this.appendItem); // collection event binder

         this.counter = 0;
         this.render();
      },

      render: function(){
        var self = this;
        $(this.el).append("<button id='add'>Add list item</button>");
        $(this.el).append("<ul></ul>");

        /*_(this.collection.models).each(function(item){ console.log(item);
          self.appendItem(item);
        }, this);*/
      },

      addItem: function(){
         this.counter++;
         var item = new Item();
         item.set({
           part2: item.get('part2') + this.counter
         });
         this.collection.add(item);
      },

      appendItem: function(item){
        $('ul', this.el).append("<li>"+item.get('part1')+" "+item.get('part2')+"</li>");
      }
    });

    var listView = new ListView();

    // ex 2
    /*var ListView = Backbone.View.extend({
      el: $('body'),

      events: {
        'click button#add': 'addItem'
      },
      initialize: function () {

        _.bindAll(this, 'render', 'addItem');

        this.counter = 0;
        this.render();
      },
      render: function(){
        $(this.el).append("<button id='add'>Add list item</button>");
        $(this.el).append("<ul></ul>");
      },
      addItem: function(){
        this.counter++;
        $('ul', this.el).append("<li>hello world"+ this.counter +"</li>");
      }
    });

    var listview = new ListView();*/

    // ex 1
    /*var ListView = Backbone.View.extend({
      el: $('body'),

      initialize: function(){
        _.bindAll(this, 'render');

        this.render();
      },

      render: function(){
        $(this.el).append("<ul> <li>hello world</li> </ul>");
      }
    });

    var listView = new ListView();*/
  });
})(window);
