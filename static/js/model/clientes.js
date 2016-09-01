// Clientes App
Clientes = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            wcat.dtWrapper(
                'clientes.php',
                'datatable',
                this
            );
                
            $('.bottom',main.clientes.$el).prepend($('#ABMButtonsTemplate').html());

        }, // end initialize 
        
        events: {
            "click #nuevo": "nuevo",
            "click #modificar": "modificar",
            "click #borrar": "borrar"
        },
                
        nuevo: function(){

            this.model = new ClientesModel();
            var _this = this;

            _this.modificarModel(true);
        },
        
        modificar: function() {
            var _this = this;
            this.model = new ClientesModel();

            this.model.id = main.clientes.lastRowSelected;
            this.model.fetch({
                success: function(response){
                    _this.modificarModel(false);
                }
            })

        },
        
        modificarModel : function(isNew) {
            var title = (isNew)? 'Nuevo' : 'Editar';
            var _this = this;
            
            $('#editClientePopup').dialog({
                height: 285,
                width: 330,
                title: title +' cliente',
                resizable: false,
                modal: true
            });         
            
            var clientesModificarView = new ClientesModificarView({
                model: _this.model
            });            
        },
        
        borrar: function(){
            var _this = this; 
        	
            wcat.jConfirm("Est&aacute; seguro?", function(){
            	var m = new ClientesModel();
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
            this.$el.html($('#clientesTemplate').html());
            return this;        
        }
});





ClientesModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "nombres" : "",
        "apellido" : "",
        "direccion" : "",
        "telefono" : "",
        "fechaNacimiento" : "",
        "desdePedido" : false,
        "tieneCuentaCorriente" : 0
    },
    
    url : function() {
        var base = "clientes.php?action=clientesModelCRUD";

        if (this.isNew()) return base;
        return base + "&codigo=" + this.id;
    },
    
    initialize: function(){
        // nothin here
    }
}); 





ClientesModificarView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#clientesModificarContainer');
        this.el = this.$el[0];

        this.render();
        $('.focus', this.el).focus();
        $('#fechaNacimiento', this.$el).datepicker({
                showOn: "button",
                buttonImage: "static/img/calendar.png",
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                yearRange: "1933:2013"
        });

    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #cancelar": "cancelar",
        "keypress input" : "nextField"
    },
            

            
            
    nextField : function(e) {
        
        if(e.keyCode === 13) {
            if($(e.currentTarget).hasClass("submitOnEnter")) {
                this.aceptar(e);
            } else {
                e.preventDefault();
            }
        }
        
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
            "nombres": $('#nombres', this.$el).val(),
            "apellido": $('#apellido', this.$el).val(),
            "direccion": $('#direccion', this.$el).val(),
            "telefono": $('#telefono', this.$el).val(),
            "fechaNacimiento": wcat.swapDateFormat($('#fechaNacimiento', this.$el).val()),
            "tieneCuentaCorriente" : (this.$('#tieneCuentaCorriente').prop('checked'))? 1:0
        }, {silent: true});

        wcat.waitDialog();
        _this.model.save({}, {
           success: function(response) {
               if(!response.get("desdePedido")) {
                    main.clientes.lastRowSelected = null;
                    main.clientes.oTable.fnStandingRedraw();
               } else {
                   main.pedidos.model.set({ 
                       "codigoCliente" : response.id,
                       "_nombreCliente" : response.get("nombres") +' '+ response.get("apellido")
                   });
                   main.pedidos.pedidosModificarView.$("#codigoCliente option").remove();
                   main.pedidos.pedidosModificarView.$('#codigoCliente').append('<option value="'+ response.id +'">'+ response.get("nombres") +' '+ response.get("apellido") +'</option>');
                   main.pedidos.pedidosModificarView.$('#codigoCliente').val(response.id);
                   main.pedidos.pedidosModificarView.$('#searchCliente').val(response.get("nombres") +' '+ response.get("apellido"));
               }
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

        $('#clientesModificarContainer').empty();
        $('#editClientePopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#clientesModificarTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    }
    
});