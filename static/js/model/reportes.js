// Reportes App: TODAVIA NO LO UTILICE... borrar
Reportes = Backbone.View.extend({
        
    initialize: function() {
        var _this = this;

        this.$el = $('#sectionContainer');
        this.el = this.$el[0];

        this.render();
        //$('button.enableDisable', main.clientes.$el).attr({ disabled: true });

        var cajaCollection = new CajaCollection();
        cajaCollection.fetch({
            success: function(collection) {
                _this.collectionView = new Backbone.CollectionView( {
                    el : $( "table#movimientosCajaContainer" ),
                    collection : collection,
                    modelView : CajaRowView,
                    selectable: false
                } );
                _this.collectionView.render();
            }
        });
        


    }, // end initialize 
        
    events: {
        "click #actualizar": "actualizar"
    },

    actualizar: function(){


    },

    close : function() {
        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();
        $('#sectionContainer').empty();
        return false;            
    },

    render: function() {
        this.$el.html($('#movimientosCajaTemplate').html());
        return this;        
    }
});


