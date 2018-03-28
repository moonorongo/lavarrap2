var ServiciosPedidos = Backbone.View.extend({
    
    initialize : function() {
    
        this.$el = $('#listaServiciosPedidosContainer');
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

        var modelView = new ServiciosPedidosRowView({model : model});
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





var ServiciosPedidosRowView = Backbone.View.extend({
        
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
            }, null);
            return false;
        },
                
        preventReturn : function(e) {
            if(e.keyCode == 13) e.preventDefault(e);
        },
                
        
        switchToEdit : function(e){
            e.preventDefault();
            var _this = this;
            $('#aceptar').attr('disabled', true);
            $('#imprimir').attr('disabled', true);
            $('#addItem').attr('disabled', true)
            
            if(!this.$el.hasClass('stateEdit')) {
                $('.stateEdit').removeClass('stateEdit');
                
                this.$el.addClass('stateEdit');

                var value = $("#codigoServicio option:contains('"+ this.model.get("_descripcion") +"')", this.$el).val();
                $("#codigoServicio", this.$el).val(value);
                $('#cantidad', this.$el).val(this.model.get("cantidad"));
                $('.focusThis', this.$el).focus();
            } 
            return false;
        },

                
        switchToView: function(e) {
    
            var codigoServicio = parseInt($('#codigoServicio', this.$el).val());

            var m = _.filter(main.pedidos.pedidosModificarView.model.get("listaServiciosCombo"), function(o){
                return o.codigo == codigoServicio;
            });
            
            var _subTotal = 0;
            
            var c = parseInt($('#cantidad', this.$el).val());
            if((codigoServicio != "none") && !isNaN(c)) {
                _subTotal = parseFloat(m[0].valor) * c;
                
                if(e.type == "click") {
                    this.model.set({
                        codigoServicio : $('#codigoServicio', this.$el).val(),
                        codigoProveedor : $('#codigoProveedor', this.$el).val(),
                        cantidad : $('#cantidad', this.$el).val(),
                        _descripcion : $('#codigoServicio option:selected', this.$el).html(),
                        _descripcionProveedor : $('#codigoProveedor option:selected', this.$el).html(),
                        _subTotal : _subTotal,
                        added : false
                    });
                    this.cancelar();
                } else {
                    switch(e.keyCode) {

                        case 13 : 
                                    this.model.set({
                                        codigoServicio : $('#codigoServicio', this.$el).val(),
                                        codigoProveedor : $('#codigoProveedor', this.$el).val(),
                                        cantidad : $('#cantidad', this.$el).val(),
                                        _descripcion : $('#codigoServicio option:selected', this.$el).html(),
                                        _descripcionProveedor : $('#codigoProveedor option:selected', this.$el).html(),
                                        _subTotal : _subTotal,
                                        added : false
                                    });
                                    this.cancelar();
                                    break;

                        case 27 :   
                                    this.cancelar();
                                    break;
                    }
                } // end if e.event
            } // if


            return false;
        },
                
        cancelar: function(e) {
            
            if(this.model.get("added")) {
                this.model.destroy();
            } 
            
            this.$el.removeClass('stateEdit');
            $('#aceptar').attr('disabled', false);
            $('#imprimir').attr('disabled', false);
            $('#addItem').attr('disabled', false)
            return false;

        },
                
        render: function() {
            if(this.model.get("deleted")) this.$el.addClass('deleted');
            var html = _.template($('#serviciosPedidosRowTemplate').html(), this.model.toJSON());
            this.$el.html(html);
            
            $("#codigoServicio", this.$el).val(this.model.get("codigoServicio"));
            
            return this;        
        }
});  




ServiciosPedidosModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "_descripcion" : "",
        "cantidad" : 1,
        "codigoServicio" : -1,
        "codigoProveedor": -1,
        "_descripcionProveedor" : "",
        "_subTotal" : 0,
        "codigoEstado" : 0,
        "deleted" : false
    },
            
    initialize: function() {
        this.set("added", (this.isNew())? true:false);
        this.set("_subTotal", parseFloat(this.get("_subTotal")));        
    }
    
}); 



ServiciosPedidosCollection = Backbone.Collection.extend({
    model : ServiciosPedidosModel
});


