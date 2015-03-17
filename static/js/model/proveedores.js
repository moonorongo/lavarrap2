// Proveedores App
Proveedores = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            wcat.dtWrapper(
                'proveedores.php',
                'datatable',
                this
            );
                
            $('.bottom',main.proveedores.$el).prepend($('#ABMButtonsTemplate').html());
        }, // end initialize 
        
        events: {
            "click #nuevo": "nuevo",
            "click #modificar": "modificar",
            "click #borrar": "borrar"
        },
                
        nuevo: function(){

            this.model = new ProveedoresModel();
            var _this = this;

            _this.modificarModel(true);
        },
        
        modificar: function(){
            var _this = this;
            this.model = new ProveedoresModel();

            this.model.id = main.proveedores.lastRowSelected;
            this.model.fetch({
                success: function(response){
                    _this.modificarModel(false);
                }
            })



        },
        
        modificarModel : function(isNew) {
            var title = (isNew)? 'Nuevo' : 'Editar';
            var _this = this;
            
            $('#editProveedorPopup').dialog({
                height: 300,
                width: 280,
                title: title +' proveedor',
                resizable: false,
                modal: true
            });         
            
            var proveedoresModificarView = new ProveedoresModificarView({
                model: _this.model
            });            
        },
        
        borrar: function(){
            var _this = this; 
        	
            wcat.jConfirm("Est&aacute; seguro?", function(){
            	var m = new ProveedoresModel();
            	m.id = _this.lastRowSelected;

                m.destroy({
                    success: function() {
                        _this.lastRowSelected = null;
                    	_this.oTable.fnStandingRedraw();

                    }
                });
            }); // jConfirm    
        },
        
        close : function() {
            this.undelegateEvents();
            $(this.el).removeData().unbind();
            this.unbind();
            $('#sectionContainer').empty();
            return false;            
        },
        
        
        render: function() {
            this.$el.html($('#proveedoresTemplate').html());
            return this;        
        }
});





ProveedoresModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "descripcion" : "",
        "esSucursal" : 1,
        "activo" : 1,
        "direccion": "",
        "zona" : "",
        "telefono" : ""
    },
    
    url : function() {
        var base = "proveedores.php?action=proveedoresModelCRUD";

        if (this.isNew()) return base;
        return base + "&codigo=" + this.id;
    }
}); 





ProveedoresModificarView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#proveedoresModificarContainer');
        this.el = this.$el[0];

        this.render();
        $('.focus', this.el).focus();
        

    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #cancelar": "cancelar"
    },
            



    aceptar: function(e){
            e.preventDefault(); // corta la propagacion de eventos.
            var _this = this;
            
            validate.test(this.$el);
            
            var hasError = validate.getErrorCount(this.$el)
            
            if(hasError !== 0) {
                var erroresEncontrados = validate.showErrors(this.$el);
                wcat.jConfirm(erroresEncontrados, function(){
                    _this.saveModel(e);
                }, null, {width: 370, height: 300, title: 'Se encontraron errores'});
            } else {
                _this.saveModel(e);
            }
            return false;
    },

            
    saveModel: function(e) {
        	
        var _this = this;

        _this.model.set({
            "descripcion": $('#descripcion', this.$el).val(),
            "direccion" : $('#direccion', this.$el).val(),
            "zona" : $('#zona', this.$el).val(),
            "telefono" : $('#telefono', this.$el).val(),
            "esSucursal" : ($('#esSucursal', this.$el).prop('checked'))? 1:0
        }, {silent: true});
        
        wcat.waitDialog();
        _this.model.save({}, {
           success: function(response) {
                main.proveedores.lastRowSelected = null;
                main.proveedores.oTable.fnStandingRedraw();
                wcat.waitDialog(true);
                _this.cancelar(e);
           } // success
        });

    },

    cancelar: function(e) {

        if(!_.isUndefined(e)) e.preventDefault(); 

        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();

        $('#proveedoresModificarContainer').empty();
        $('#editProveedorPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#proveedoresModificarTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    }
});