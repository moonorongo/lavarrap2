// Pedidos App
Pedidos = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
                    
            this.render();
            _this.porcentajePedido = null;
            
            this.initDT();
            
            $('.bottom',this.$el).prepend($('#ABMButtonsPedidosTemplate').html());
            
            $('#fechaPedidos', this.$el).datepicker({
                showOn: "button",
                buttonImage: "static/img/calendar.png",
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true
            });

        }, // end initialize 
        
        events: {
            "click #nuevoDesdePlantilla": "nuevoDesdePlantilla",
            "click #nuevo": "nuevo",
            "click #modificar": "modificar",
            "click #borrar": "borrar",
            "click #entregar" : "entregar",
            "change #filtroDt" : "updateTopMenuAndRefresh",
            "click #reset" : "clearDatepicker",
            "change #fechaPedidos" : "refresh"
        },
        
        
        nuevoDesdePlantilla: function() {
            var templateCollection = null; 
            var _this = this;
            $.ajax({
                url: 'pedidos.php?action=getTemplates',
                success: function(response) {
                    templateCollection = new TemplateCollection(response);
                    _this.seleccionarPlantilla(templateCollection);
                }
            })
        },

        
        seleccionarPlantilla: function(templateCollection) {
            var html = _.template($('#seleccionarPlantillaTemplate').html(), { collection : templateCollection.toJSON() });
            var _this = this;
            wcat.jConfirm("Seleccione la plantilla:<br /><br />" + html, function(){
                var codigoTemplate = parseInt($('#codigoTemplate').val());

                _this.model = templateCollection.where({codigo : codigoTemplate})[0];
                var tempServiciosPedidosCollection = new ServiciosPedidosCollection();
                _.each( _this.model.get("listaServicios"), function(m){
                    delete m.codigo;
                    delete m.codigoPedido,
                    m.cantidad = 0;
                    m.codigoEstado = 0;
                    var tempM = new ServiciosPedidosModel(m);
                    tempM.unset("added")
                    tempServiciosPedidosCollection.add(tempM);
                });
                
                _this.model.set("listaServicios", tempServiciosPedidosCollection);
                
                _this.model.unset("codigo");
                _this.model.set({ 
                    "fechaRetiro" : moment().format("YYYY-MM-DD"),
                    "fechaPedido" : moment().format("YYYY-MM-DD"),
                    "nombre" : null,
                    "codigoCliente" : null,
                    "isNew" : true
                }, {silent: true});


                $.ajax({
                    url:'pedidos.php?action=getListaServicios',
                    success: function(response) {
                        _this.model.set("listaServiciosCombo", response.listaServiciosCombo);
                        _this.model.set("listaProveedoresCombo", response.listaProveedoresCombo);
                        _this.modificarModel(true);
                    }
                });
                
            }, null, { width : 300, height: 200, title: "Nuevo desde plantilla" });

        },
                
        clearDatepicker : function(){
            this.$('#fechaPedidos').val('');
            this.refresh();
        },
                
        updateTopMenuAndRefresh: function() {
            var filtroDt = parseInt($('#filtroDt').val());
            switch(filtroDt) {
                case 0 :    // sin entregar
                            this.$('div.ABMButtons button.ABMButton').removeClass('occult');
                            this.$('.filtroFecha').removeClass('occult');
                            this.$('.ui-datepicker-trigger').removeClass('occult');
                            this.$('#datatable_filter').removeClass('occult');
                            break;
                case 1 :    // entregados
                            this.$('div.ABMButtons button.ABMButton').removeClass('occult');
                            this.$('#entregar, #borrar, #nuevo, #nuevoDesdePlantilla').addClass('occult');  
                            this.$('.filtroFecha').removeClass('occult');
                            this.$('.ui-datepicker-trigger').removeClass('occult');
                            this.$('#datatable_filter').removeClass('occult');
                            break;
                case 2 :    // plantillas
                            this.$('div.ABMButtons button.ABMButton').removeClass('occult');
                            this.$('#entregar, #nuevoDesdePlantilla').addClass('occult');  
                            this.$('.filtroFecha').addClass('occult');
                            this.$('.ui-datepicker-trigger').addClass('occult');
                            this.$('#datatable_filter').addClass('occult');
                            break;
            }

            this.refresh();
        },

        refresh: function() {
            main.pedidos.oTable.fnReloadAjax();
        },
                
        entregar: function() {
            var _this = this;
            if(this.porcentajePedido != 100) {
                var m = new PedidosModel({codigo : _this.lastRowSelected })
                m.fetch({
                    success: function(response) {
                        var html = _.template($('#pedidoIncompletoAlertaTemplate').html(), m.toJSON());

                        wcat.jConfirm(html, 
                            function(){
                                _this.entregarPedido();
                            }, 
                            function(){}, 
                            {
                                width: 500,
                                height: 260,
                                title: "Alerta!"
                        }); 
                    }
                });
                
            } else {
                _this.entregarPedido();
            } // if
        },


        entregarPedido : function() {
            var m = new PedidosModel({codigo : this.lastRowSelected })
            m.fetch({
                success: function(response) {
                    
                    $('#entregarPedidoPopup').dialog({
                        height: 500,
                        width: 350,
                        title: 'Entregar pedido',
                        resizable: false,
                        modal: true
                    });         
                    
                    m.set({"montoPagado" : ""});
                    var entregarPedidoView = new EntregarPedidoView({
                        model : m
                    });
                } // success
            }); // fetch;
        },
        
        
        nuevo: function(){

            this.model = new PedidosModel();
            var _this = this;
            $.ajax({
                url:'pedidos.php?action=getListaServicios',
                success: function(response) {
                    _this.model.set("listaServiciosCombo", response.listaServiciosCombo);
                    _this.model.set("listaProveedoresCombo", response.listaProveedoresCombo);
                    _this.modificarModel(true);
                }
            });

            
        },
        
        modificar: function(){
            var _this = this;
            this.model = new PedidosModel();

            this.model.id = main.pedidos.lastRowSelected;
            this.model.fetch({
                success: function(response){
                    _this.modificarModel(false);
                }
            })



        },
        
        modificarModel : function(isNew) {
            var title = (isNew)? 'Nuevo' : 'Editar';
            var filtroDt = parseInt($('#filtroDt').val());
            var tipoPedido = (filtroDt == 2)? ' plantilla' : ' pedido';
            var _this = this;
            
            $('#editPedidoPopup').dialog({
                height: 560,
                width: 730,
                title: title + tipoPedido,
                resizable: false,
                modal: true
            });         
            
            this.pedidosModificarView = new PedidosModificarView({
                model: _this.model,
                attributes: {
                    filtroDt : filtroDt
                }
            });            
        },
        
        borrar: function(){
            var _this = this; 
        	
            wcat.jConfirm("Est&aacute; seguro?", function(){
            	var m = new PedidosModel();
            	m.id = _this.lastRowSelected;

                m.destroy({
                    success: function() {
                    	_this.oTable.fnReloadAjax();
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
            this.$el.html($('#pedidosTemplate').html());
            return this;        
        },
                
        initDT: function() {

            var DTConfig = _.clone(wcat.getDataTableDefaults());
            var _this = this;
            
            var idContainer = 'datatable';

            DTConfig.sAjaxSource = 'pedidos.php';
            DTConfig.bSort = false;

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

            DTConfig.fnServerParams = function ( aoData ) {
                var entregado = ($('#filtroDt').val());
                var fechaPedido = $('#fechaPedidos').val();
                aoData.push( { "name": "entregado", "value": entregado } );
                if(fechaPedido != "" && !_.isUndefined(fechaPedido)) aoData.push( { "name": "fechaPedido", "value": wcat.swapDateFormat(fechaPedido)} );
            }

            DTConfig.sScrollY = ($('#'+ idContainer).parent().height() - 62) +"px";
            DTConfig.iDisplayLength = Math.floor($('#'+ idContainer).parent().height() / 25) - 1;
            DTConfig.aoColumnDefs = aoColumnDefs;
            DTConfig.aaSorting = [[1,'desc']];

            DTConfig.fnCreatedRow = function(nRow, aData, iDataIndex ) {
                  // fix date columns
                  $('td:eq(1)', nRow).html(wcat.swapDateFormat(aData[1]));
                  $('td:eq(5)', nRow).html(wcat.swapDateFormat(aData[5]));
                  
                  // click row handler
                  $(nRow).click( function() {
                    if ( $(this).hasClass('rowSelected') ) {
                        $(this).removeClass('rowSelected');
                        _this.lastRowSelected = null;
                        _this.porcentajePedido = null;
                        $('button.enableDisable', _this.$el).attr({ disabled: true });            
                    } else {
                        $('tr.rowSelected', _this.$el).removeClass('rowSelected');
                        $(this).addClass('rowSelected');
                        _this.lastRowSelected = parseInt(this.id);
                        _this.porcentajePedido = parseInt(aData[7]);
                        $('button.enableDisable', _this.$el).attr({ disabled: false });            
                    }
                });
                
                // enable-disable buttons
                if($(nRow).hasClass('rowSelected')) {
                    $('button.enableDisable', _this.$el).attr({ disabled: false });            
                } else {
                    $('button.enableDisable', _this.$el).attr({ disabled: true });
                }
            } // end fnCreatedRow


            // segun valor lastRowSelected, agrega class.
            DTConfig.fnRowCallback = function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(!_.isNull(_this.lastRowSelected)) {
                    if(aData.DT_RowId == _this.lastRowSelected) {
                        $(nRow).addClass('rowSelected');
                    } else {
                        $(nRow).removeClass('rowSelected');
                    }
                }
            } // end fnRowCallback

            _this.oTable = $('#'+ idContainer,_this.el).dataTable(DTConfig).fnSetFilteringDelay();
            
        } // end initDT
});







PedidosModel = Backbone.Model.extend({
    idAttribute : "codigo" ,
  
    defaults : {
        "fechaRetiro" : moment().format("YYYY-MM-DD"),
        "fechaPedido" : moment().format("YYYY-MM-DD"),
        "nombre" : null,
        "codigoCliente" : null,
        "listaServicios" : null,
        "anticipo" : 0,
        "_nombreCliente" : "",
        "activo" : 1,
        "observaciones" : "",
        "codigoTalon" : '' 
    },
    
    url : function() {
        var base = "pedidos.php?action=pedidosModelCRUD";

        if (this.isNew()) return base;
        return base + "&codigo=" + this.id;
    },
    
    parse: function(response){
        this.set(response);
        this.set("listaServicios", new ServiciosPedidosCollection(response.listaServicios));
        this.set("isNew", this.isNew());
    },
            
    initialize: function() {
        if(this.isNew()) this.set("listaServicios", new ServiciosPedidosCollection());
        this.set("isNew", this.isNew());
    }
}); 




TemplateCollection = Backbone.Collection.extend({
    model : PedidosModel
});



PedidosModificarView = Backbone.View.extend({
    
    initialize: function() {
        var _this = this,
            esPlantilla = (this.attributes.filtroDt == 2); 

        this.model.set({ esPlantilla : esPlantilla});

        this.$el = $('#pedidosModificarContainer');
        this.el = this.$el[0];
        this.render();

        $('.focusThis', this.el).focus();
        
        $('#fechaRetiro', this.$el).datepicker({
                showOn: "button",
                buttonImage: "static/img/calendar.png",
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy"
        });

        if (_this.model.isNew()) {
            $("#searchCliente").keypress(function(e) {
                if (e.keyCode == 13)
                    e.preventDefault();
            }).autocomplete({
              minLength: 3,
              delay: 500,
              select: function( event, ui ) {
                _this.$("#codigoCliente option").remove();
                _this.$("#codigoCliente").append('<option value="'+ ui.item.id +'" selected>'+ ui.item.value +'</option>');
              },
            source: function( request, response ) {
                $.ajax({
                    url: "clientes.php?action=getClientes",
                    data: { term: request.term},
                    success: function(data){
                        response(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        alert("error handler!");                        
                    },
                  dataType: 'json'
                });
            }              
            });

            this.$('#aceptar, #imprimir').show();  
            this.$('#soloImprimir').hide();  

        } else {
            if(this.model.attributes.entregado) {
                this.$('#aceptar, #imprimir').hide();  
                this.$('#soloImprimir').show();  
            } else {
                this.$('#aceptar, #imprimir').show();  
                this.$('#soloImprimir').hide();  
            }
            
        }
        
        this.serviciosPedidos = new ServiciosPedidos({
            collection : _this.model.get("listaServicios")
        });

        this.actualizarTotalFactura();

        this.model.get('listaServicios').on("change", function(m) {
            _this.actualizarTotalFactura();                
        });
    }, 

    events: {
        "click #aceptar": "aceptar",
        "click #guardarTemplate": "guardarTemplate",
        "click #cancelar": "cancelar",
        "click #imprimir": "aceptar",
        "click #soloImprimir": "aceptar",
        "click #addItem" : "addItem",
        "click #addCliente" : "addCliente"
    },

    
    guardarTemplate: function(e) {
        e.preventDefault;

        var nombre = prompt("Nombre de la plantilla?");
        if(!_.isNull(nombre)) {
            this.model.set("nombre",nombre, {silent: true});
            this.aceptar(e);
        } else {
            this.cancelar(e);
        }
    },
    
    
    addCliente: function(e) {
        e.preventDefault();
        var model = new ClientesModel({ desdePedido : true});
        
        $('#editClientePopup').dialog({
            height: 285,
            width: 330,
            title: 'Nuevo cliente',
            resizable: false,
            modal: true
        });         

        var clientesModificarView = new ClientesModificarView({
            model: model
        });            
    },
            
            
    addItem : function(e) {

        this.$('#aceptar').attr('disabled', true);
        this.$('#imprimir').attr('disabled', true);
        $(e.currentTarget).attr('disabled', true);
        
        e.preventDefault();
        var _this = this;
        $('.stateEdit').removeClass('stateEdit');
        
        var m = new ServiciosPedidosModel({
            codigoServicio : _this.model.get("codigo")
        });
        
        main.pedidos.model.get("listaServicios").add(m);
        
    },




    aceptar: function(e){
        e.preventDefault(); 
        var _this = this;
        var hasError = 0;

        if(this.model.isNew() && (this.attributes.filtroDt != 2)) { 
            validate.test(this.$el);
            hasError = validate.getErrorCount(this.$el);
        }

        if(hasError !== 0) {
            var erroresEncontrados = validate.showErrors(this.$el);
            wcat.jConfirm(erroresEncontrados, function(){}, null, {width: 370, height: 300, title: 'Se encontraron errores'});
        } else {
            _this.saveModel(e);
        }

        return false;
    },

            
    saveModel: function(e) {
        	
        var _this = this;
        
        var imprimir = (e.target.id == "imprimir")? true : false,
            soloImprimir = (e.target.id == "soloImprimir")? true : false,
            listaCantidadCero = [],
            anticipo = (_.isEmpty(_this.$('#anticipo').val()))? 0 : _this.$('#anticipo').val(),
            totalFactura = this.obtenerTotalFactura();



        if(anticipo > totalFactura) {
            var diferencia = anticipo - totalFactura;
            alert("El anticipo es mayor que el total de la factura! \n\r"+
                  "Se ajustara el anticipo al monto total: $ " + totalFactura +"\n\r"+
                  "Vuelto a entregar: $ " + diferencia);

            anticipo = totalFactura;
        }

        _this.model.get("listaServicios").each( function(m){ 
            if(m.isNew()) m.set("codigo", -1);
            if(m.get("codigoProveedor") == "-1") m.set("codigoProveedor", null);
            if(m.get("cantidad") == 0) listaCantidadCero.push(m.cid);
        });

        _.each(listaCantidadCero, function(cid){
            _this.model.get("listaServicios").remove(cid);
        })

        if(_this.model.isNew()) {
            _this.model.set({
                "codigoTalon" : _this.$('#codigoTalon').val(),
                "codigoCliente" : _this.$('#codigoCliente').val(),
                "fechaRetiro" : wcat.swapDateFormat($('#fechaRetiro', this.$el).val()),
                "anticipo" : anticipo,
                "imprimir" : imprimir,
                "soloImprimir" : soloImprimir,
                "_nombreCliente" : _this.$('#codigoCliente option:selected').html(),
                "observaciones" : _this.$('#observaciones').val()
            }, {silent: true});
        } else {
            _this.model.set({
                "codigoTalon" : _this.$('#codigoTalon').val(),
                "fechaRetiro" : wcat.swapDateFormat($('#fechaRetiro', this.$el).val()),
                "imprimir" : imprimir,
                "soloImprimir" : soloImprimir,
                "_nombreCliente" : _this.$('#searchCliente').val(),
                "observaciones" : _this.$('#observaciones').val(),
                "anticipo" : anticipo
            }, {silent: true});
        }



        wcat.waitDialog();
        _this.model.save({}, {
           success: function(response) {
                main.pedidos.lastRowSelected = null;
                main.pedidos.oTable.fnReloadAjax();
                
                if(imprimir || soloImprimir) {
                    setTimeout(function(){
                        window.open("/static/download/"+ globalConfig.SESSIONID +".pdf","Ticket Cliente","directories=0, height=600, location=0, menubar=0, width=500");
                    }, 500);
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

        $('#pedidosModificarContainer').empty();
        $('#editPedidoPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#pedidosModificarTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    },


    obtenerTotalFactura: function() {
        var totalFactura = 0;

        this.model.get("listaServicios").each( function(m){ 
            totalFactura += (m.get('deleted'))? 0 : m.get('_subTotal');
        });

        return totalFactura;        
    },


    actualizarTotalFactura: function() {
        $('#totalFactura', this.$el).html(this.obtenerTotalFactura());
    }
    
});







EntregarPedidoView = Backbone.View.extend({

    initialize: function() {
        var _this = this;

        this.$el = $('#entregarPedidoContainer');
        this.el = this.$el[0];
        
        var totalServicios = 0;
        
        _.each(this.model.get("listaServicios").toJSON(), function(s) { 
            totalServicios += s._subTotal; 
        }) ;
         
        this.model.set("totalServicios", totalServicios);
        this.model.set("aCobrar", totalServicios - this.model.get("anticipo"));
        this.model.set("vuelto", 0);
        
        this.render();
        
        this.$('.focusThis').focus();
    }, 

    events: {
        "click #aceptar": "entregarPedido",
        "click #cancelar": "cancelar",
        "keyup #montoPagado" : "checkNumbers",
        "change input[name='tipoCliente']" : "disableServiciosEntregaPedido"
    },
          
    disableServiciosEntregaPedido : function() {
        if(this.$("input[name='tipoCliente']:checked").val() === "1" ) {
            this.$(".detalleConsumidorFinal").show();
            this.$('#aceptar').html('<i class="icon-print"></i>Aceptar &amp; Imprimir');
        } else {
            this.$(".detalleConsumidorFinal").hide();
            this.$('#aceptar').html('<i class="icon-ok"></i>Aceptar');
        }
    },
    
    checkNumbers : function(event) {
        this.model.set("montoPagado", parseFloat(event.currentTarget.value));
        this.model.set("vuelto", this.model.get("montoPagado") - this.model.get("aCobrar"));
        
        if(_.isNaN(this.model.get("vuelto"))) {
            this.$("#vuelto").html("");
        } else {
            this.$("#vuelto").html(this.model.get("vuelto").toFixed(2));
        }
        
    },
    
    
    entregarPedido : function(e) {

        $(e.target).attr({ disabled : true });
        
        var _this = this;
        if(this.$("input[name='tipoCliente']:checked").val() === "1" ) { // si es consumidor Final
            
            var montoEntregado = this.$("#montoPagado").val();

            if( (this.model.get("aCobrar") != 0) && (_.isEmpty(montoEntregado) || this.model.get("vuelto") < 0 || !wcat.validate.number.test(montoEntregado)) ) {

                wcat.jAlert("Debe ingresar un monto, igual o superior a lo que hay que cobrar!");
                $(e.target).attr({ disabled : false });

            } else {
                var data = {
                    codigoPedido : main.pedidos.lastRowSelected,
                    codigoEstado : 4,
                    montoPagado : _this.model.get("montoPagado"),
                    vuelto : _this.model.get("vuelto")
                };

                $.ajax({
                    url: 'tareas.php?action=tareasHandler',
                    data : data,
                    error : function(response) {
                        console.log(response);
                    },
                    success : function(response) {
                        _this.cancelar();                            
                        main.pedidos.oTable.fnReloadAjax();
                        main.pedidos.lastRowSelected = null;
                        
                        window.open("tareas.php?action=printTicketHandler&"+ $.param(data), "Imprimir Entrega pedido",
                            "directories=0, height=600, location=0, menubar=0, width=500");
                    } 
                }); // ajax
            } 
        } else { // a cuenta corriente
            
            var data = {
                codigoPedido : main.pedidos.lastRowSelected,
                codigoEstado : 5
            };
            
            $.ajax({
                url: 'tareas.php?action=tareasHandler',
                data : data,
                error : function(response) {
                    console.log(response);
                },
                success : function(response) {
                    _this.cancelar();
                    main.pedidos.oTable.fnReloadAjax();
                    main.pedidos.lastRowSelected = null;
                    
                    window.open("tareas.php?action=printRemitoHandler&"+ $.param(data), "Imprimir Remito",
                                "directories=0, height=600, location=0, menubar=0, width=500");                        
                } 
            }); // ajax            
        }

    },
    
    cancelar: function(e) {

        if(!_.isUndefined(e)) e.preventDefault(); 

        this.undelegateEvents();
        $(this.el).removeData().unbind();
        this.unbind();

        $('#entregarPedidoContainer').empty();
        $('#entregarPedidoPopup').dialog("destroy");

        return false;           
    },    

    render: function() {
        var html = _.template($('#entregarPedidoTemplate').html(), this.model.toJSON());
        this.$el.html(html);
        return this; 
    }
    
}) 