// Tareas App
Tareas = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            $('.buttons-container',this.$el).html($('#TFDButtonsTemplate').html());

            this.initDT($('#filtroCodigoEstado').val());
            // $('.bottom',this.$el).prepend($('#TFDButtonsTemplate').html());
            

        }, // end initialize 
        
        events: {
            "click #iniciarLavado": "tareasHandler",
            "click #finalizarLavado": "tareasHandler",
            "click #derivar": "tareasHandler",
            "change #filtroCodigoEstado" : "actualizarLista"
        },
                
                
        actualizarLista : function(e){
            this.oTable.fnSettings().sAjaxSource = "tareas.php?action=listAll&codigoEstado="+ e.target.value;
            this.oTable.fnReloadAjax();
        },
                
                
        tareasHandler: function(e) {
    
            var tieneDerivacion = false;
            var tareasSeleccionadas = [];
            this.$('#datatable tr.rowSelected').each(function(i,e){
                tareasSeleccionadas.push(e.id);
                if(!_.isUndefined($(e).attr('data-derivacion'))) tieneDerivacion = true;
            });

            var codigoEstado = 0;
            if(tareasSeleccionadas.length != 0) {
                switch(e.target.id) {
                    case 'iniciarLavado' : codigoEstado = 2;
                                           break;
                    case 'finalizarLavado' : codigoEstado = 3;
                                           break;
                    case 'derivar' : codigoEstado = -1;
                }



                if(codigoEstado == -1) {
                    if(!tieneDerivacion) {
                        
                        $('#tareasModificarPopup').dialog({
                            height: 165,
                            width: 260,
                            title: 'Tareas',
                            resizable: false,
                            modal: true
                        });
                        
                        var tareasModificar = new TareasModificar({
                            attributes : { 
                                tareasSeleccionadas : tareasSeleccionadas, 
                                cantidadTareasSeleccionadas : tareasSeleccionadas.length,
                                codigoEstado : codigoEstado
                            }
                        });
                    } else {
                        wcat.jAlert('No se puede derivar una tarea ya derivada.')
                    }
                } else { // cualquiera de las demas
                    
                    $('#tareasModificarPopup').dialog({
                        height: 165,
                        width: 260,
                        title: 'Tareas',
                        resizable: false,
                        modal: true
                    });
                    
                    var tareasModificar = new TareasModificar({
                        attributes : { 
                            tareasSeleccionadas : tareasSeleccionadas, 
                            cantidadTareasSeleccionadas : tareasSeleccionadas.length,
                            codigoEstado : codigoEstado
                        }
                    });
                }

                

            } else { // debe seleccionar una tarea
                wcat.jAlert('Debe seleccionar al menos una tarea!');
            }

        },
           
        close : function() {
            this.undelegateEvents();
            $(this.el).removeData().unbind();
            this.unbind();
            $('#sectionContainer').empty();
            return false;            
        },
        
        
        render: function() {
            this.$el.html($('#tareasTemplate').html());
            return this;        
        },
                
        initDT: function(codigoEstado) {

            var DTConfig = _.clone(wcat.getDataTableDefaults());
            var _this = this;
            if(_.isUndefined(codigoEstado)) codigoEstado = -1;
            
            DTConfig.sAjaxSource = 'tareas.php?action=listAll&codigoEstado='+ codigoEstado;
            DTConfig.bServerSide = false;

            var idContainer = 'datatable';

            var aoColumnDefs = [];
            $('#'+ idContainer +' thead tr th').each(function(i,e){
                var aTargets = [i];
                aoColumnDefs.push({
                    "sClass" : $(e).css('textAlign'),
                    "aTargets" : aTargets,
                    "sWidth" : $(e).css('width')
                })
            });

            // agrega hidden column con el estado de derivacion
            aoColumnDefs.push({ bVisible : false, aTargets : [5] });

            DTConfig.sScrollY = ($('#'+ idContainer).parent().height() - 62) +"px";
            DTConfig.iDisplayLength = Math.floor($('#'+ idContainer).parent().height() / 26) - 1;

            DTConfig.aoColumnDefs = aoColumnDefs;

            DTConfig.fnCreatedRow = function( nRow, aData, iDataIndex ) {
                  $('td:eq(1)', nRow).html(wcat.swapDateFormat(aData[1]));
                  $(nRow).attr('data-derivacion', aData[5]);
                  
                  // click row handler
                  $(nRow).click( function() {
                      if(!$(this).hasClass("tareaDerivada")) {
                          if ( $(this).hasClass('rowSelected') ) {
                              $(this).removeClass('rowSelected');
                          } else {
                              $(this).addClass('rowSelected');
                          }
                      }
                });
                
                // enable-disable buttons
//                if($(nRow).hasClass('rowSelected')) {
//                    $('button.enableDisable', _this.$el).attr({ disabled: false });            
//                } else {
//                    $('button.enableDisable', _this.$el).attr({ disabled: true });
//                }
           }

            DTConfig.fnDrawCallback = function(oSettings){
/*                
                $("tr", oSettings.nTable).click( function() {
                    if ( $(this).hasClass('rowSelected') ) {
                        $(this).removeClass('rowSelected');
                        _this.lastRowSelected = null;
                        $('button.enableDisable', _this.$el).attr({ disabled: true });            
                    } else {
                        //$('tr.rowSelected', _this.$el).removeClass('rowSelected');
                        $(this).addClass('rowSelected');
                        _this.lastRowSelected = parseInt(this.id);
                        $('button.enableDisable', _this.$el).attr({ disabled: false });            
                    }
                });

                if(_.isNull(_this.lastRowSelected) || !_.isNumber(parseInt(_this.lastRowSelected)) || _.isUndefined(_this.lastRowSelected)) {
                    $('button.enableDisable', _this.$el).attr({ disabled: true });            
                } else {
                    $('button.enableDisable', _this.$el).attr({ disabled: false });            
                }
*/
            }, // end fnDrawCallback
            
/*
            DTConfig.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(!_.isNull(_this.lastRowSelected)) {
                    if(aData.DT_RowId == _this.lastRowSelected) {
                        $(nRow).addClass('rowSelected');
                    } else {
                        $(nRow).removeClass('rowSelected');
                    }
                }
            } // end fnRowCallback
*/
            
//            DTConfig.fnServerParams = function ( aoData ) {
//                var codigoEstado = _this.$('#filtroCodigoEstado').val(); 
//                if(!_.isUndefined(codigoEstado)) {
//                    aoData.push( { codigoEstado : codigoEstado } );
//                }
//            }

            _this.oTable = $('#'+ idContainer,_this.el).dataTable(DTConfig);
        } // end initDT
});








// TareasModificar App
TareasModificar = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#tareasModificarContainer');
            this.el = this.$el[0];
                    
            this.render();

        }, // end initialize 
        
        events: {
            "click #aceptar": "aceptar",
            "click #cancelar": "cancelar"
        },
          
        aceptar: function(e){
            e.preventDefault(); // corta la propagacion de eventos.
            var _this = this;

            var data = {
                codigoProveedor : this.$('#codigoProveedor').val(),
                tareasSeleccionadas : _this.attributes.tareasSeleccionadas.join(','),
                codigoEstado : _this.attributes.codigoEstado
            };
            
            wcat.waitDialog();
            $.ajax({
                url:'tareas.php?action=tareasHandler',
                data : data,
                success: function() {
                    main.tareas.oTable.fnReloadAjax();                    
                    if(_this.attributes.codigoEstado == 2) {  // imprimo ticket Tareas
                        setTimeout(function(){
                            window.open("/static/download/"+ globalConfig.SESSIONID +".pdf","Tareas","directories=0, height=600, location=0, menubar=0, width=500");                        
                        }, 500);
                    }
                    wcat.waitDialog(true);
                    _this.cancelar();
                }
            });
            
            return false;
        },
                
        cancelar : function() {
            this.undelegateEvents();
            this.$el.removeData().unbind();
            this.unbind();
            $('#tareasModificarContainer').empty();
            $('#tareasModificarPopup').dialog("destroy");

            return false;            
        },
        
         render: function() {
            var html = _.template($('#tareasModificarTemplate').html(), this.attributes);
            this.$el.html(html);
            return this;        
        }
})
