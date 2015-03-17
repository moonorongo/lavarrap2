var InsumosServicios = Backbone.View.extend({
    
    initialize : function() {
    
        this.$el = $('#listaInsumosServiciosContainer');
        this.el = this.$el[0];

        _(this).bindAll('add', 'remove');
    
        this._aViews = {};
    
        this.collection.each(this.add);
     
        this.collection.on('add', this.add);
        this.collection.on('remove', this.remove);
        this.collection.on('refresh', this.render);
        
        this.render();
    },
    
    
    add : function(model) {

        var modelView = new InsumosServiciosRowView({model : model});
        this._aViews[model.cid] = modelView;
     
        // esto cuando ya se inicializo
        if (this._rendered) {
          $(this.el).append(modelView.render().el);
          $('.focusThis', this.el).focus();
        }
      },
     
      
    remove : function(model) {

        var viewToRemove = this._aViews[model.cid]
        if (this._rendered) $(viewToRemove.el).remove();
        delete this._aViews[model.cid];
      },    
    
    
    
    render : function() {
        
        this._rendered = true;
        var _this = this; 
        $(this.el).empty();


        _(this._aViews).each(function(dv) {
            $(_this.el).append(dv.render().el, _this);
        });
 
        return this;        
    }
});    





var InsumosServiciosRowView = Backbone.View.extend({
        
        tagName: 'li',
        className : function(){
            return (this.model.get("added"))? "ulRow stateEdit":"ulRow";
        }, 
        
        initialize: function() {
            this.model.on('change', this.render, this);
        },
        
        events: {
            "click button#edit" : "switchToEdit",
            "click button#delete" : "delete",                    
            "keyup button#ok" : "switchToView",
            "click button#ok" : "switchToView",
            "click button#cancel" : "cancelar",
            "keypress input" : "preventReturn"
        },
                
        delete: function(e) {
            e.preventDefault();
            var _this = this;
            wcat.jConfirm("Est&aacute; seguro?", function(){
                _this.model.set("deleted", true);
                //main.servicios.serviciosModificarView.insumosServicios.render();
            }, null);
            return false;
        },
                
        preventReturn : function(e) {
            if(e.keyCode == 13) e.preventDefault(e);
        },
                
        
        switchToEdit : function(e){
            e.preventDefault();
            var _this = this;
            
            if(!this.$el.hasClass('stateEdit')) {
                $('.stateEdit').removeClass('stateEdit');
                
                this.$el.addClass('stateEdit');

                // seteo valores del modelo a los inputs
                var value = $("#codigoInsumo option:contains('"+ this.model.get("_descripcion") +"')", this.$el).val();
                $("#codigoInsumo", this.$el).val(value);

                $('#cantidad', this.$el).val(this.model.get("cantidad"));
                                
                $('.focusThis', this.$el).focus();
                
            } 
            return false;
        },

                
        switchToView: function(e) {
            //e.preventDefault();
            if(e.type == "click") {
                this.model.set({
                    codigoInsumo : $('#codigoInsumo', this.$el).val(),
                    cantidad : $('#cantidad', this.$el).val(),
                    _descripcion : $('#codigoInsumo option:selected', this.$el).html(),
                    isNew : false
                });
                this.cancelar();
            } else {
                switch(e.keyCode) {

                    case 13 : 
                                this.model.set({
                                    codigoInsumo : $('#codigoInsumo', this.$el).val(),
                                    cantidad : $('#cantidad', this.$el).val(),
                                    _descripcion : $('#codigoInsumo option:selected', this.$el).html(),
                                    isNew : false
                                });
                                this.cancelar();
                                break;

                    case 27 :   
                                this.cancelar();
                                break;
                }
            } // end if

            return false;
        },
                
        cancelar: function(e) {
            //e.preventDefault();
            if(this.model.get("isNew")) {
                this.model.destroy();
            } 
            
            this.$el.removeClass('stateEdit');
            $('#addItem').attr('disabled', false)
            return false;

        },
                
        
        render: function() {
            if(this.model.get("deleted")) this.$el.addClass('deleted');
            var html = _.template($('#InsumosServiciosRowTemplate').html(), this.model.toJSON());
            this.$el.html(html);
            return this;        
        }
});  




InsumosServiciosModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "_descripcion" : "",
        "cantidad" : "",
        "codigoServicio" : "",
        "deleted" : false
    },
            
    initialize: function() {
        this.set("isNew", this.isNew());
    }
    
}); 



InsumosServiciosCollection = Backbone.Collection.extend({
    model : InsumosServiciosModel
});


