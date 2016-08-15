// MovimientosCaja App
MovimientosCaja = Backbone.View.extend({
        
    initialize: function() {
        var _this = this;

        this.$el = $('#sectionContainer');
        this.el = this.$el[0];

        this.render();
        
        var selectedMonth = parseInt(this.$('#month').val()) + 1;
        var $fecha = this.$('#year').val() +'-'+ selectedMonth +'-01';

        var cajaCollection = new CajaCollection();
        wcat.waitDialog();
        cajaCollection.fetch({
            reset: true, 
            data: {fecha: $fecha},
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
        


    }, // end initialize 
        
    events: {
        "change #month": "actualizar",
        "change #year": "actualizar"
    },

    actualizar: function(){
        var _this=this;
        var selectedMonth = parseInt(this.$('#month').val()) + 1;
        var $fecha = this.$('#year').val() +'-'+ selectedMonth +'-01';
        wcat.waitDialog();
        _this.collectionView.collection.fetch({
            reset: true, 
            data: {fecha: $fecha},
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
        var sum = 0;
        collection.each(function(e){ sum += parseFloat(e.get("monto")) });
        this.$('#saldoDeCaja').html(sum.toFixed(2));
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
    render : function() {
            var m = this.model.toJSON();
            var html = this.template(m);
            this.$el.html(html);
    }
})


















// Ingresos/Egresos Caja App
CajaView = Backbone.View.extend({
    
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
        	
        var _this = this;
        var data = {
            "monto": parseFloat(_this.$('#monto').val()),
            "sign": _this.attributes.sign,
            "observaciones" : "*EXT*: "+ _this.$('#observaciones').val()
        };

        $.ajax({
            url: "caja.php?action=cajaHandler",
            data : {model : JSON.stringify(data)},
            success: function(response){
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





