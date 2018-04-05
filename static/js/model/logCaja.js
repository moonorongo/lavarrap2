// LogCaja App
LogCaja = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            this.initDT();
            
//            $('.bottom',this.$el).prepend($('#ABMButtonsPedidosTemplate').html());
            
            $('#fecha', this.$el).datepicker({
                showOn: "button",
                buttonImage: "static/img/calendar.png",
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true
            });
        }, // end initialize 
        
        events: {
            "click #reset" : "clearDatepicker",
            "change #fecha" : "refresh"
        },
        
                
        clearDatepicker : function(){
            this.$('#fecha').val('');
            this.refresh();
        },

        refresh: function() {
            main.logCaja.oTable.fnReloadAjax();
        },
        
        close : function() {
            this.undelegateEvents();
            $(this.el).removeData().unbind();
            this.unbind();
            $('#sectionContainer').empty();
            return false;            
        },
        
        
        render: function() {
            this.$el.html($('#logCajaTemplate').html());
            return this;        
        },
                
        initDT: function() {

            var DTConfig = _.clone(wcat.getDataTableDefaults());
            var _this = this;
            
            var idContainer = 'datatable';

            DTConfig.sAjaxSource = 'caja.php'; // ver esto
            DTConfig.bSort = false;

            var aoColumnDefs = [];
            $('#'+ idContainer +' thead tr th').each(function(i,e){
                var aTargets = [i];
                aoColumnDefs.push({
                    "sClass" : $(e).css('textAlign'),
                    "aTargets" : aTargets,
                    "sWidth" : $(e).css('width')
                })
            });


/*
            DTConfig.fnServerParams = function ( aoData ) {
                var fecha = $('#fecha').val();
                // aoData.push( { "name": "entregado", "value": entregado } );
                if(fecha != "" && !_.isUndefined(fecha)) aoData.push( { "name": "fecha", "value": wcat.swapDateFormat(fecha)} );
            }
*/
            DTConfig.sScrollY = ($('#'+ idContainer).parent().height() - 62) +"px";
            DTConfig.iDisplayLength = Math.floor($('#'+ idContainer).parent().height() / 25) - 1;
            DTConfig.aoColumnDefs = aoColumnDefs;
            // DTConfig.aaSorting = [[1,'desc']];
/*
            DTConfig.fnCreatedRow = function(nRow, aData, iDataIndex ) {
                // fix date columns
                $('td:eq(0)', nRow).html(wcat.swapDateFormat(aData[0]));
            } // end fnCreatedRow
*/

            _this.oTable = $('#'+ idContainer,_this.el).dataTable(DTConfig).fnSetFilteringDelay();
        } // end initDT
});
