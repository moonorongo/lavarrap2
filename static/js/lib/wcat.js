/*!
 * wCat Utils JavaScript Library v0.1
 * http://whitecat.com.ar/
 * 
 * Funciones utiles de uso general 
 *
 * Requiere: jquery >= 1.8
 * http://jquery.com/
 * DataTables plugin 
 * https://datatables.net
 * 
 * Copyright 2005, 2012 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://whitecat.com.ar/license
 *
 * Date: 2013-7-31
 */
var wcat = (function( window, undefined ) {

// PRIVATE PROPERTIES -------------------------------------------------------------------------------------------
var
    ventasDatatableDefaults = {
        "bJQueryUI": true,
        "oLanguage": {
                "sInfo" : "Mostrando _START_ a _END_, de un total de _TOTAL_ registros",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sSearch" : "Buscar:",
                "sProcessing": "Procesando...",
                "sLoadingRecords": "Cargando...",
                "sZeroRecords": "No se encontraron registros",
                "sInfoEmpty": "No se encontraron registros",
                "sEmptyTable": "No hay informacion disponible",
                "oPaginate": {
                        "sFirst": '<i class="icon-step-backward"></i>',
                        "sPrevious": '<i class="icon-backward"></i>',
                        "sNext": '<i class="icon-forward"></i>',
                        "sLast": '<i class="icon-step-forward"></i>'
                    }
        },
        "bPaginate": true,
        "bFilter": true,
        "bServerSide": true,
        "bInfo": true,
        "bSort": true,
        "sDom": '<"bottom"fp><"clear">rt',
        "bAutoWidth": false ,
        "iDisplayLength": 10,
        "bLengthChange": false,
        //"sPaginationType": "full_numbers",
        "sPaginationType": "two_button",
        
        "bProcessing": true,
    //      "bScrollCollapse": true,
        "sScrollY": "278px"
    },
    
    jConfirmContainer = "jConfirm" + Math.floor(Math.random()*1000000);
    jConfirmMsgContainer = "msg" + Math.floor(Math.random()*1000000);
    
    
    
    
// PRIVATE METHODS -------------------------------------------------------------------------------------------    

/*
* clone: clona un objeto.
*/ 
    var clone = function(obj) {
        
        if (obj === null || typeof obj !== 'object') {
            return obj;
        }
        return (JSON.parse(JSON.stringify(obj)));
    }




// PUBLIC METHODS -------------------------------------------------------------------------------------------    

    return {
/*
 *  init(): inicializa todo, debe ser llamado al finalizar la carga del DOM 
 */        
        init: function() {
            $('body').append('<div id="'+ jConfirmContainer +'"><div id="'+ jConfirmMsgContainer +'"></div></div>');                
        }, // end init
/*
 *  dtWrapper: wrapper para plugin dataTable, que simplifica el uso.
 * requerido:
 *      sAjaxSource: url handler
 *      idContainer: table
 *      view: backbone view
 * opcional:
 *      aoColumnDefs: si se proporciona sobreescribe el calculo que hace getDatatable
 *      callback: llamado al finalizar la inicializacion
  */        
        dtWrapper : function(sAjaxSource, idContainer, view, aoColumnDefs, callback) {
            
            var DTConfig = clone(ventasDatatableDefaults);
            

            var idContainer = idContainer.replace('#','');
            
            if((_.isUndefined(aoColumnDefs)) || (_.isNull(aoColumnDefs))) {
                var aoColumnDefs = [];
                $('#'+ idContainer +' thead tr th').each(function(i,e){
                    var aTargets = [i];
                    aoColumnDefs.push({
                        "sClass" : $(e).css('textAlign'),
                        "aTargets" : aTargets,
                        "sWidth" : $(e).css('width')
                    })
                });
            } // aoColumnDefs auto
            
            
            DTConfig.sScrollY = ($('#'+ idContainer).parent().height() - 62) +"px";
            DTConfig.iDisplayLength = Math.floor($('#'+ idContainer).parent().height() / 26) - 1;

            DTConfig.sAjaxSource = sAjaxSource,
            DTConfig.aoColumnDefs = aoColumnDefs;
            
            
            DTConfig.fnDrawCallback = function(oSettings){
                
                $("tr", oSettings.nTable).click( function() {
                    if ( $(this).hasClass('rowSelected') ) {
                        $(this).removeClass('rowSelected');
                        view.lastRowSelected = null;
                        $('button.enableDisable', view.$el).attr({ disabled: true });            
                    } else {
                        $('tr.rowSelected', view.$el).removeClass('rowSelected');
                        $(this).addClass('rowSelected');
                        view.lastRowSelected = parseInt(this.id);
                        $('button.enableDisable', view.$el).attr({ disabled: false });            
                    }
                });

                if(_.isNull(view.lastRowSelected) || !_.isNumber(parseInt(view.lastRowSelected)) || _.isUndefined(view.lastRowSelected)) {
                    $('button.enableDisable', view.$el).attr({ disabled: true });            
                } else {
                    $('button.enableDisable', view.$el).attr({ disabled: false });            
                }
                
            }, // end fnDrawCallback
            
            DTConfig.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(!_.isNull(view.lastRowSelected)) {
                    if(aData.DT_RowId == view.lastRowSelected) {
                        $(nRow).addClass('rowSelected');
                    } else {
                        $(nRow).removeClass('rowSelected');
                    }
                }
                
            } // end fnRowCallback
            
        
            if(!_.isUndefined(callback)) {
                DTConfig.fnInitComplete = function(oSettings, json) {
                    callback();
                };
            }

            
            // quiza a futuro ponerlo como parametro, agregado para servicios solamente.
            // a medida q se necesiten mas parametros, agregarlos de la misma manera.
            DTConfig.fnServerParams = function ( aoData ) {
                var fechaVigencia = $('#fechaVigencia').val();
                if(!_.isUndefined(fechaVigencia)) {
                    aoData.push( { "name": "fechaVigencia", "value": fechaVigencia} );
                };
            }
            
            
            view.oTable = $('#'+ idContainer,view.el).dataTable(DTConfig).fnSetFilteringDelay();

        }, // end getDatatable

        
/*
 *  validate: objeto para validaciones de datos
 *  uso: wcat.validate._____(values).test(value) : retorna true si es valido
 */
        validate : {
                "ip" :/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/,
                "number" : /^[-+]?[0-9]*\.?[0-9]+$/,
                "email" : /^([0-9a-zA-Z].*?@([0-9a-zA-Z].*\.\w{2,4}))$/, // QUITAR /g de regexp, que mete lio
                "time" :  /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/, 
                "date" : /^(((0[1-9]|[12][0-9]|3[01])[- /.](0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[- /.](0[469]|11)|(0[1-9]|1\d|2[0-8])[- /.]02)[- /.]\d{4}|29[- /.]02[- /.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))$/ ,
                "range" : function(min,max) {
                    return {
                        test : function(value) {

                            if(value>=min && value <= max) { 
                                return true
                            } else { 
                                return false;
                            }
                        } // test
                    }
                }, // range
                
                "gt" : function(min) {
                    return {
                        test : function(value) {
                            if(value>min) { 
                                return true
                            } else { 
                                return false;
                            }
                        } // test
                    }
                }, // gt
                
                "lt" : function(max) {
                    return {
                        test : function(value) {
                            if(value<max) { 
                                return true
                            } else { 
                                return false;
                            }
                        } // test
                    }
                    
                }, // lt

                
                "empty" : function(value) {
                    return (value.trim().length==0)? true:false;
                } 
        }, // end validate (CODE IS POETRY)        



/*
 * jAlert y jConfirm: sustitutos bellos de alert y confirm.
 */
        jAlert : function(message){
            $('#'+ jConfirmContainer +' #'+ jConfirmMsgContainer).html(message);
            $('#'+ jConfirmContainer).dialog({
                height: 'auto',
                width: 300,
                title: 'Alerta',
                resizable: false,
                modal: true,
                open: function( event, ui ) {
                    $('#'+ jConfirmContainer +' ~ .ui-dialog-buttonpane .ui-dialog-buttonset button')[0].innerHTML = '<span class="ui-button-text"><i class="icon-ok"></i>Aceptar</span>';
                },
                buttons: [ 
                    { text: "Aceptar", click: function(){
                        $(this).dialog("close");
                    } 
                    }
                ]
            });
        },

        // params: optativo (parametros de dialog jquery ui)
        jConfirm : function(message, yesAction, noAction, params) {
            
            var defaultParams = {
                width: 300,
                height: 160,
                title: "Alerta"
            }
            
            params = params || defaultParams;

            var contextDialogMessage = $('#'+ jConfirmContainer +' #'+ jConfirmMsgContainer).html(message);
            $('#'+ jConfirmContainer).dialog({
                height: params.height,
                width: params.width,
                title: params.title,
                resizable: false,
                modal: true,
                open: function( event, ui ) {
                    $('#'+ jConfirmContainer +' ~ .ui-dialog-buttonpane .ui-dialog-buttonset button')[0].innerHTML = '<span class="ui-button-text"><i class="icon-ok"></i>Aceptar</span>';
                    $('#'+ jConfirmContainer +' ~ .ui-dialog-buttonpane .ui-dialog-buttonset button')[1].innerHTML = '<span class="ui-button-text"><i class="icon-remove"></i>Cancelar</span>';
                },
                buttons: [ 
                    { text: "Aceptar", click: function(){
                        yesAction();
                        $(this).dialog("close");
                    } 
                    },
                    { text: "Cancelar", click: function(){
                        if((!_.isUndefined(noAction)) && (!_.isNull(noAction)))noAction();
                        $(this).dialog("close"); 
                    } 
                    }
                ]
            });
            
            $('.focusThis', contextDialogMessage).focus();
        },
        
        
        
        
        waitDialog: function(destroy) {
            if(_.isUndefined(destroy)) {
                $('#waitingPopup').dialog({
                    height: 130,
                    width: 110,
                    title: 'Procesando',
                    resizable: false,
                    modal: true
                });                        
            } else {
                if(destroy) {
                    $('#waitingPopup').dialog("destroy");                    
                } else {
                    $('#waitingPopup').dialog({
                        height: 130,
                        width: 110,
                        title: 'Procesando',
                        resizable: false,
                        modal: true
                    });                            
                }
            }
        },



        
/*
 * setCharAt(String input, int indice, char caracter_a_sustituir): retorna el string con el caracter sustituido en index
 */        
        setCharAt : function (str,index,chr) {
            if(index > str.length-1) return str;
            return str.substr(0,index) + chr + str.substr(index+1);
        },

/*
 *  swapDateFormat(Date fecha): intercambia formato dd/mm/yyyy a formato yyyy-mm-dd (mysql) y viceversa, si recibe con guiones pasa a slash
 */
        swapDateFormat : function (fecha) {
            if(!_.isNull(fecha)) {
                if(fecha.split(' ') != 1) fecha = fecha.split(' ');
                var ftemp = fecha[0].split('-');
                if(ftemp.length == 1) {
                    ftemp = fecha[0].split('/');
                    return ftemp[2] +'-'+ ftemp[1] +'-'+ ftemp[0];
                }
                return ftemp[2] +'/'+ ftemp[1] +'/'+ ftemp[0];                
            }
            
            return null;
        },
                
        getDataTableDefaults : function() {
            return ventasDatatableDefaults;
        },

                
/*
 *  getWindowData: retorna un objeto {anchoVentana, altoVentana}, con el tamaño de la ventana del navegador.
 */                
        getWindowData : function () {
            anchoVentana = $(window).width();
            altoVentana = $(window).height();

            temp = { "anchoVentana":anchoVentana, 
                   "altoVentana":altoVentana };

            return temp;
        }                

    } // end return 
})(window);


