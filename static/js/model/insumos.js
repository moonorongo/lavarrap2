// Insumos App
Insumos = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            wcat.dtWrapper(
                'insumos.php',
                'datatable',
                this
            );
                
            $('.bottom',main.insumos.$el).prepend($('#ABMButtonsInsumosTemplate').html());
        }, // end initialize 
        
        events: {
            "click #nuevo": "nuevo",
            "click #modificar": "modificar",
            "click #borrar": "borrar",
            "click #ingresar" : "ingresar"
        },
        
        ingresar: function() {
            var _this = this;
            wcat.jConfirm(
                'Cantidad <input type="text" id="cantidadInsumo" />', 
                function(){
                    var cantidad = $('input#cantidadInsumo').val();
                    $.ajax({
                        url: 'insumos.php?action=addInsumo',
                        data: {codigo : main.insumos.lastRowSelected, cantidad : cantidad},
                        success: function() {
                            main.insumos.lastRowSelected = null;
                            main.insumos.oTable.fnStandingRedraw();
                        }
                    });
                    
                });
        },
                
        nuevo: function(){

            this.model = new InsumosModel();
            var _this = this;

            _this.modificarModel(true);
        },
        
        modificar: function(){
            var _this = this;
            this.model = new InsumosModel();

            this.model.id = main.insumos.lastRowSelected;
            this.model.fetch({
                success: function(response){
                    _this.modificarModel(false);
                }
            })



        },
        
        modificarModel : function(isNew) {
            var title = (isNew)? 'Nuevo' : 'Editar';
            var _this = this;
            
            $('#editInsumoPopup').dialog({
                height: 205,
                width: 461,
                title: title +' insumo',
                resizable: false,
                modal: true
            });         
            
            var insumosModificarView = new InsumosModificarView({
                model: _this.model
            });            
        },
        
        borrar: function(){
            var _this = this; 
        	
            wcat.jConfirm("Est&aacute; seguro?", function(){
            	var m = new InsumosModel();
            	m.id = _this.lastRowSelected;

                m.destroy({
                    success: function() {
                        // refresh DT
                    	_this.oTable.fnStandingRedraw();
                        _this.lastRowSelected = null;

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
            this.$el.html($('#insumosTemplate').html());
            return this;        
        }
});





InsumosModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "descripcion" : ""
    },
    
    url : function() {
        var base = "insumos.php?action=insumosModelCRUD";

        if (this.isNew()) return base;
        return base + "&codigo=" + this.id;
    },
    
    initialize: function(){
        // nothin here
    }
}); 





InsumosModificarView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#insumosModificarContainer');
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
            "descripcion": $('#descripcion', this.$el).val()
        }, {silent: true});
        
        wcat.waitDialog();
        _this.model.save({}, {
           success: function(response) {
                main.insumos.lastRowSelected = null;
                main.insumos.oTable.fnStandingRedraw();
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

        $('#insumosModificarContainer').empty();
        $('#editInsumoPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#insumosModificarTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    }
    
});