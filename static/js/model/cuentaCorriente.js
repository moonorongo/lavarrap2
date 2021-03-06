// Pedidos App
CuentaCorriente = Backbone.View.extend({
        
        initialize: function() {
            var _this = this;
            
            this.$el = $('#sectionContainer');
            this.el = this.$el[0];
            this.saldo = 0;
                    
            this.render();
            this.initDT();
            
            $('.bottom',this.$el).prepend($('#ABMButtonsCuentaCorrienteTemplate').html());

        }, // end initialize 
        
        events: {
            "click #ingresarPagoCuentaCorriente" : "ingresarPagoCuentaCorriente",
            "click #ingresarCorreccionCuentaCorriente" : "ingresarCorreccionCuentaCorriente",
            "change #codigoClienteCuentaCorriente" : "refresh",
            "change #monthCuentaCorriente" : "refresh",
            "change #yearCuentaCorriente" : "refresh"
        },

                
                
                
        ingresarCorreccionCuentaCorriente: function() {
            var _this = this;
            var codigoCliente = $('#codigoClienteCuentaCorriente').val();
            if(codigoCliente == "-99") {
                alert("Debe seleccionar un cliente!");
            } else {
                var cantidad = prompt("nro negativo: a favor del cliente \n nro positivo: a favor nuestro \nIngrese cantidad:");
                if (!_.isNull(cantidad)) {
                    $.ajax({
                        url: "cuentaCorriente.php?action=ingresarCorreccion",
                        data: {cantidad: cantidad, codigoCliente: codigoCliente},
                        success: function(response) {
                            _this.realizarPago(0, codigoCliente, false); // actualiza saldo CC, no muestra confirm
                            _this.refresh();
                        }
                    });
                }
            }
        },
                
        
        realizarPago: function(cantidad, codigoCliente, mostrarAlertas) {
            var mostrarAlertas = _.isUndefined(mostrarAlertas)? true : mostrarAlertas,
                _this = this;

            console.log(mostrarAlertas);

            $.ajax({
                url: "cuentaCorriente.php?action=ingresarPago",
                data: { 
                        cantidad: cantidad, 
                        codigoCliente: codigoCliente
                      },
                success: function(response){
                    var codigosSaldados = "";
                    var medioSaldar = "";
                    var condicionExcedente = "";
                    
                    if (response.success) {
                        _.each(response.itemsAfectados, function(e) {
                            if(e.monto == 0) {
                                codigosSaldados += " " + e.codigoPedido + ",";
                            } else {
                                medioSaldar = e.codigoPedido +" ($"+ Math.abs(e.monto) +")";
                            }
                        });
                        codigosSaldados = codigosSaldados.substr(0,codigosSaldados.length - 1);
                        
                        if(response.aFavorDelCliente != 0) condicionExcedente = "Saldo a favor del cliente: $ "+ response.aFavorDelCliente;
                        if(!_.isEmpty(codigosSaldados)) codigosSaldados = "Los siguientes pedidos ser&aacute;n saldados:<br />" + codigosSaldados +"<br />";
                        if(!_.isEmpty(medioSaldar)) medioSaldar = "El siguiente pedido queda a medio saldar: " + medioSaldar + "<br />";

                        if(mostrarAlertas) {
                            wcat.jConfirm(
                                codigosSaldados +
                                medioSaldar +
                                condicionExcedente , 
                                function() {
                                    _this.confirmarIngresarPago(JSON.stringify(response), codigoCliente)
                                }, 
                                null, 
                                { width: 400, height: 300, title: "Confirmar" });
                        } else {
                            _this.confirmarIngresarPago(JSON.stringify(response), codigoCliente)
                        }
                    } // response.success
                }
            });
        },


        confirmarIngresarPago: function(model, codigoCliente) {
            var _this = this;
            $.ajax({
                url: 'cuentaCorriente.php?action=confirmarIngresarPago',
                data: {model : model, codigoCliente : codigoCliente},
                success: function(response){
                    _this.refresh();
                }
            });
        },

                
        ingresarPagoCuentaCorriente: function() {
            var codigoCliente = $('#codigoClienteCuentaCorriente').val();
            var _this = this;
            
            if(codigoCliente == "-99") {
                alert("Debe seleccionar un cliente!");
            } else {
                var cantidad = prompt("Ingrese cantidad:");
                    cantidad = parseFloat(cantidad);
            
                if ( (!_.isNaN(cantidad)) && (cantidad > 0) ) {
                    this.realizarPago(cantidad, codigoCliente);
                } else {
                    alert("Debe ingresar un numero valido mayor de 0");
                }


            }
        },
                
                
        refresh: function() {
            $("#saldoCC").html("");
            this.saldo = 0;
            this.oTable.fnReloadAjax();
        },

                
        close : function() {
            this.undelegateEvents();
            $(this.el).removeData().unbind();
            this.unbind();
            $('#sectionContainer').empty();
            return false;            
        },
        
        
        render: function() {
            this.$el.html($('#cuentaCorrienteTemplate').html());
            return this;        
        },
                
        initDT: function() {

            var DTConfig = _.clone(wcat.getDataTableDefaults());
            var _this = this;

            DTConfig.sAjaxSource = 'cuentaCorriente.php?action=listAll';
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

            DTConfig.fnServerParams = function ( aoData ) {
                var codigoCliente = $('#codigoClienteCuentaCorriente').val();
                var month = $('#monthCuentaCorriente').val();
                var year = $('#yearCuentaCorriente').val();
                if(_.isUndefined(codigoCliente)) codigoCliente = -1;
                if(_.isUndefined(month)) month = -1;
                if(_.isUndefined(year)) year = -1;
                aoData.push( { "name": "codigoCliente", "value": codigoCliente } );
                aoData.push( { "name": "month", "value": month } );
                aoData.push( { "name": "year", "value": year } );
            }

            DTConfig.sScrollY = ($('#'+ idContainer).parent().height() - 62) +"px";
            DTConfig.iDisplayLength = Math.floor($('#'+ idContainer).parent().height() / 25) - 1;
            DTConfig.aoColumnDefs = aoColumnDefs;
            DTConfig.aaSorting = [[0,'asc']];


			DTConfig.fnServerData = function ( sSource, aoData, fnCallback, oSettings ) {

		      oSettings.jqXHR = $.ajax( {
		        "dataType": 'json',
		        "type": "POST",
		        "url": sSource,
		        "data": aoData,
		        "success": function(aaData, response, jqXHR) {
		        	var totalFacturado = 0;

		        	_.each(aaData.aaData, function(e) {
		        		totalFacturado += ( _.isNumber(e[2]) && !_.isNaN(e[2]))? e[2] : 0;
		        	});

		        	_this.$('#facturadoCC').html(totalFacturado.toFixed(2));

		        	fnCallback(aaData, response, jqXHR);
		        }
		      });
		    }


            DTConfig.fnCreatedRow = function(nRow, aData, iDataIndex ) {
                  // fix date columns
                  $('td:eq(1)', nRow).html(wcat.swapDateFormat(aData[1]));
                  if(!_.isNull(aData[2])) {
                    $('td:eq(2)', nRow).html( "$ "+ aData[2].toFixed(2) );
                  }
                  if(!_.isNull(aData[3])) {
                    $('td:eq(3)', nRow).html( "$ "+ aData[3].toFixed(2) );
                  }
                  
                  _this.saldo += aData[3];
                  $("#saldoCC").html(_this.saldo.toFixed(2));
                  
                  if (aData.DT_RowId == 0) { // si es un id=0 (es un saldo a favor del cliente)
                      $(nRow).addClass('saldoFavor');
                  }

            } // end fnCreatedRow

            _this.oTable = $('#'+ idContainer,_this.el).dataTable(DTConfig).fnSetFilteringDelay();
        } // end initDT
});


