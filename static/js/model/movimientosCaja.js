// MovimientosCaja App
MovimientosCaja = Backbone.View.extend({
        
    initialize: function() {
        var _this = this;

        this.$el = $('#sectionContainer');
        this.el = this.$el[0];

        this.render();
        
        var selectedMonth = parseInt(this.$('#month').val()) + 1,
            $fecha = this.$('#year').val() +'-'+ selectedMonth +'-01',
            search = this.$('#buscarMovientoCaja').val();

        var cajaCollection = new CajaCollection();
        wcat.waitDialog();
        cajaCollection.fetch({
            reset: true, 
            data: {fecha: $fecha, search : search},
            success: function(collection) {
                wcat.waitDialog(true);
                _this.collectionView = new Backbone.CollectionView( {
                    el : $( "table#movimientosCajaContainer" ),
                    collection : collection,
                    modelView : CajaRowView,
                    selectable: false
                } );
                _this.collectionView.render();
                _this.showSaldoDeCaja(collection);
            }
        });

        cajaCollection.on("change", function(m) {
            _this.actualizar();                
        });

    }, // end initialize 
        
    events: {
        "change #month": "actualizar",
        "change #year": "actualizar",
        "click #actualizarLista" : "actualizar",
        "change #buscarMovientoCaja" : "actualizar"
    },

    actualizar: function(){
        var _this=this,
            selectedMonth = parseInt(this.$('#month').val()) + 1,
            $fecha = this.$('#year').val() +'-'+ selectedMonth +'-01',
            search = this.$('#buscarMovientoCaja').val();

        wcat.waitDialog();
        _this.collectionView.collection.fetch({
            reset: true, 
            data: {fecha: $fecha, search : search},
            success: function(collection){
                wcat.waitDialog(true);
                _this.showSaldoDeCaja(collection);
            }
        });
    },

    close : function() {
        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();
        $('#sectionContainer').empty();
        return false;            
    },

    showSaldoDeCaja: function(collection) {
        var sum = 0,
            sumDebito = 0,
            sumEfectivo = 0,
            search = this.$('#buscarMovientoCaja').val();

        if(search.trim().length == 0) {
            collection.each(function(e) { 
                sum += parseFloat(e.get("monto"));

                if(e.get('conDebito') === "1") {
                    sumDebito += parseFloat(e.get("monto"));
                }
            });
        }

        sumEfectivo = sum - sumDebito;
        
        this.$('#saldoDeCaja').html(sum.toFixed(2));
        this.$('#totalDebito').html(sumDebito.toFixed(2));
        this.$('#totalEfectivo').html(sumEfectivo.toFixed(2));
    },

    render: function() {
        this.$el.html($('#movimientosCajaTemplate').html());
        return this;        
    }
});


CajaModel = Backbone.Model.extend({
    idAttribute : "codigo" 
}); 


CajaCollection = Backbone.Collection.extend({
    model: CajaModel,
    url: "caja.php?action=cajaListHandler"
});


CajaRowView = Backbone.View.extend({
    template : _.template( $( "#movimientosCajaRowTemplate" ).html() ),
    tagName: "tr",
    className: function() {
        return (this.model.get("esSaldoInicialMes") == 1)? "saldoInicial" : "";
    },
    events: {
        "click .btnEditCaja": "editCaja",
        "click .btnBorrarCaja": "borrarCaja"
    },

    editCaja : function(e) {
        $('#cajaPopup').dialog({
            height: 235,
            width: 300,
            title: 'Editar Movimiento de Caja',
            resizable: false,
            modal: true
        });         
        
        var cajaView = new CajaView({model: this.model});
    },

    borrarCaja : function(e) {
        var _this = this;

        if(confirm("Esta Seguro?")) {
            $.ajax({
                url: 'caja.php?action=deleteCaja',
                data: {codigo : _this.model.id},
                success : function(response) {
                    if(response.success) {
                        _this.model.collection.remove(_this.model);
                    } else {
                        alert("no se pudo borrar el registro");
                    }
                }
            });
        }
    },

    render : function() {
            var m = this.model.toJSON();
            var html = this.template(m);
            this.$el.html(html);
    }
})





// Ingresos/Egresos Caja App
// y ahora edicion
CajaView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#cajaContainer');
        this.el = this.$el[0];

        this.render();
        if(_.isUndefined(this.attributes)) {
            this.attributes = {};
        }

        this.attributes.codigo = -1;

        if(!_.isUndefined(this.model)) {
            $('#monto', this.$el).val(this.model.get('monto'));
            $('#observaciones', this.$el).val(this.model.get('observaciones'));
            this.attributes.codigo = this.model.id;
        }

        this.$('.focus').focus();
    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #cancelar": "cancelar"
    },
  

    aceptar: function(e){
            e.preventDefault(); // corta la propagacion de eventos.
            var _this = this;
            $(e.currentTarget).prop({disabled : true});
            
            validate.test(this.$el);
            
            var hasError = validate.getErrorCount(this.$el)
            
            if(hasError !== 0) {
                $(e.currentTarget).prop({disabled : false});
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
        var _this = this,
            extraccion = (this.attributes.codigo == -1)? "*EXT*: " : "";

        if(_.isUndefined(this.attributes.sign)) {
            this.attributes.sign = (this.model.get('model') > 0)? 1 : -1;
        }



        var data = {
            "codigo" : this.attributes.codigo,
            "monto": parseFloat(_this.$('#monto').val()),
            "sign": _this.attributes.sign,
            "observaciones" : extraccion + _this.$('#observaciones').val()
        };

        $.ajax({
            url: "caja.php?action=cajaHandler",
            data : {model : JSON.stringify(data)},
            success: function(response){
                if(!_.isUndefined(_this.model)) {
                    _this.model.set(data);
                }

                _this.cancelar();
            }
        })
    },


    cancelar: function(e) {

        if(!_.isUndefined(e)) e.preventDefault(); 

        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();

        $('#cajaContainer').empty();
        $('#cajaPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#cajaTemplate').html(), this.attributes);
        this.$el.html(html);
        return this; 
    }
    
});





DebitosCajaView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this;

        this.$el = $('#cajaContainer');
        this.el = this.$el[0];

        this.render();
        this.$('.focus').focus();
    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #cancelar": "cancelar"
    },
  

    aceptar: function(e){
        e.preventDefault();
        var _this = this;
        $(e.currentTarget).prop({disabled : true});
        
        validate.test(this.$el);
        
        var hasError = validate.getErrorCount(this.$el)
        
        if(hasError !== 0) {
            $(e.currentTarget).prop({disabled : false});
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
        var _this = this,
            data = {
                "monto": parseFloat(_this.$('#monto').val()),
                "observaciones" : _this.$('#observaciones').val()
            };

        $.ajax({
            url: "caja.php?action=debitoToCajaHandler",
            data : {model : JSON.stringify(data)},
            success: function(response) {
                _this.cancelar();
            }
        })
    },


    cancelar: function(e) {
        if(!_.isUndefined(e)) e.preventDefault(); 

        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();

        $('#cajaContainer').empty();
        $('#cajaPopup').dialog("destroy");

        return false;           
    },    


    render: function() {
        var html = _.template($('#cajaTemplate').html(), this.attributes);
        this.$el.html(html);
        return this; 
    }
});

