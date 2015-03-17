// Servicios App
Servicios = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();


            wcat.dtWrapper(
                'servicios.php',
                'datatable',
                this
            );

            $('.bottom',main.servicios.$el).prepend($('#ABMButtonsServiciosTemplate').html());
        }, // end initialize 
        
        events: {
            "click #nuevo": "nuevo",
            "click #modificar": "modificar",
            "click #borrar": "borrar",
            "click #nuevaFechaVigencia" : "nuevaFechaVigencia"
        },

        
        nuevaFechaVigencia : function(isNew) {
            var _this = this; 
            
            var html = "Se actualizará la fecha de vigencia de los servicios. <br /> Desea continuar?"
            var fechaVigencia = moment().format('YYYY-MM-DD HH:mm:ss');
            
            wcat.jConfirm(html, function(){
                $.ajax({
                    url: 'servicios.php?action=nuevaFechaVigencia',
                    data : {fechaVigencia : fechaVigencia},
                    success: function() {
                        $('#fechaVigencia').html(fechaVigencia);
                        _this.oTable.fnStandingRedraw();
                        _this.lastRowSelected = null;                        
                    }
                })
            }); // jConfirm    
        },
        
        refresh: function() {
            // poner en initDT que mande como dato lo q esta en #fechaVigencia
            this.oTable.fnStandingRedraw();
            this.lastRowSelected = null;
        },    
        
        
        nuevo: function(){

            this.model = new ServiciosModel();
            var _this = this;
            $.ajax({
                url:'servicios.php?action=getListaInsumos',
                success: function(response) {
                    _this.model.set("listaInsumosCombo", response.listaInsumosCombo);
                    _this.modificarModel(true);
                }
            });

            
        },
        
        
        modificar: function(){
            var _this = this;
            this.model = new ServiciosModel();

            this.model.id = main.servicios.lastRowSelected;
            this.model.fetch({
                success: function(response){
                    _this.modificarModel(false);
                }
            })
        },
        
        
        modificarModel : function(isNew) {
            var title = (isNew)? 'Nuevo' : 'Editar';
            var _this = this;
            
            $('#editServicioPopup').dialog({
                height: 400,
                width: 480,
                title: title +' servicio',
                resizable: false,
                modal: true
            });         
            
            this.serviciosModificarView = new ServiciosModificarView({
                model: _this.model
            });            
        },
        
        borrar: function(){
            var _this = this; 
        	
            wcat.jConfirm("Est&aacute; seguro?", function(){
            	var m = new ServiciosModel();
            	m.id = _this.lastRowSelected;

                m.destroy({
                    success: function() {
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
            this.$el.html($('#serviciosTemplate').html());
            return this;        
        }
});





ServiciosModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "descripcion" : "",
        "valor" : 0,
        "listaInsumos" : null,
        "activo" : 1
    },
    
    url : function() {
        var base = "servicios.php?action=serviciosModelCRUD";

        if (this.isNew()) return base;
        return base + "&codigo=" + this.id;
    },
    
    parse: function(response){
        this.set(response);
        this.set("listaInsumos", new InsumosServiciosCollection(this.get("listaInsumos")));
    },
            
    initialize: function() {
        if(this.isNew()) this.set("listaInsumos", new InsumosServiciosCollection());
    }
}); 





ServiciosModificarView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#serviciosModificarContainer');
        this.el = this.$el[0];

        this.render();
        $('.focus', this.el).focus();
        
        
        
        this.insumosServicios = new InsumosServicios({
            collection : _this.model.get("listaInsumos")
        });
        
    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #cancelar": "cancelar",
        "click #addItem" : "addItem"
    },
            

            
            
    addItem : function(e) {
        e.preventDefault();
        var _this = this;
        $('.stateEdit').removeClass('stateEdit');
        
        var m = new InsumosServiciosModel({
            codigoServicio : _this.model.get("codigo"),
            added : true
        });
        
        main.servicios.model.get("listaInsumos").add(m);
        $(e.target).attr('disabled', true);
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

        _this.model.get("listaInsumos").each( function(m){ 
            if(m.isNew()) m.set("codigo", -1);
        });
        

        _this.model.set({
            "descripcion": $('#descripcion', this.$el).val(),
            "valor": $('#valor', this.$el).val()
        }, {silent: true});

        wcat.waitDialog();
        _this.model.save({}, {
           success: function(response) {
                main.servicios.lastRowSelected = null;
                main.servicios.oTable.fnStandingRedraw();
                wcat.waitDialog(true);
                if(!response.get("success")) {
                    wcat.jAlert("El servicio no se puede modificar porque esta siendo utilizado. Deber&aacute crear una nueva <strong>Fecha de Vigencia</strong>");
                }
                _this.cancelar(e);
           } // success
        });

    },

    cancelar: function(e) {

        if(!_.isUndefined(e)) e.preventDefault(); 

        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();

        $('#serviciosModificarContainer').empty();
        $('#editServicioPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#serviciosModificarTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    }
    
});


