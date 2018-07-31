// main App
Main = Backbone.View.extend({

    initialize: function() {
        var _this = this;
        
        this.$el = $('div#mainAppContainer');
        this.el = this.$el[0];

        this.render();
        
    }, // end initialize 

    events: {
        "click #clientes" : "clientes",
        "click #servicios" : "servicios",
        "click #insumos" : "insumos",
        "click #proveedores" : "proveedores",
        "click #pedidos" : "pedidos",
        
        "click #tareas" : "tareas",
        "click #ingresoCaja" : "caja",
        "click #egresoCaja" : "caja",
        "click #cerrarCajaMes" : "cerrarCajaMes",
        "click #movimientosCaja" : "movimientosCaja",
        "click #debitoToCaja" : "debitoToCaja",
        
        "click #rPrincipal" : "rPrincipal",
        "click #cajaFacturado" : "cajaFacturado",
        "click #rInsumos" : "reportes",
        "click #rDerivaciones" : "reportes",
        "click #rServiciosRealizados" : "reportes",
        "click #rListaServicios" : "reportes",
        "click #resumenCajaMes" : "resumenCajaMes",
        
        "click #cuentaCorriente" : "cuentaCorriente",
        "click #rCumpleanos" : "reportes",
        "click #rConsumos" : "reportes",
        "click #exportarListaClientes" : "reportes",
        
        "click .menu-item" : "highlightParent",
        
        "click #cambiarSucursal" : "mostrarCambiarSucursal",
        "change #sucursalCombo" : "cambiarSucursal",

        "click #logCaja" : "logCaja",
        "click #logPedidos" : "logPedidos"
    },
            

    debitoToCaja: function(e) {
        e.preventDefault();
        
        $('#cajaPopup').dialog({
            height: 235,
            width: 300,
            title: 'Debitos a Caja',
            resizable: false,
            modal: true
        });         
        
        var debitosCajaView = new DebitosCajaView();
    },


    logCaja: function(e) {
        this.closeAll();
        this.logCaja = new LogCaja();
        return false;
    },

    logPedidos: function(e) {
        this.closeAll();
        this.logPedidos = new LogPedidos();
        return false;
    },


    highlightParent: function(e) {
        var 
            $allRootMenu = $('.root-menu'),
            $rootMenu = $(e.target).closest('.root-menu');

        $allRootMenu.removeClass('selected-menu');
        $rootMenu.addClass('selected-menu');
    },

    cuentaCorriente: function(){
        this.closeAll();
        this.cuentaCorriente = new CuentaCorriente();
        return false;
    },    

    rPrincipal : function() {
        this.closeAll();
        wcat.jConfirm('Mes: <input type="text" id="month" class="datepickerReporte" value="'+ moment().format("MM") +'" />' +
                      '<span style="display: inline-block; margin-left: 31px">A&ntilde;o:</span>'+
                      '<input type="text" id="year" class="datepickerReporte" value="'+ moment().format("YYYY") +'" />', 
            function(){
                var month = $('#month').val();
                var year = $('#year').val();
                window.location = "reportes.php?action=rPrincipal&fechaInicial="+ month +"&fechaFinal="+ year                
            }, null, {title : "Reporte", width: 410, height: 150 }); // jConfirm        
    },

    cajaFacturado : function() {
        this.closeAll();
        wcat.jConfirm('Mes: <input type="text" id="month" class="datepickerReporte" value="'+ moment().format("MM") +'" />' +
                      '<span style="display: inline-block; margin-left: 31px">A&ntilde;o:</span>'+
                      '<input type="text" id="year" class="datepickerReporte" value="'+ moment().format("YYYY") +'" />', 
            function(){
                var month = $('#month').val();
                var year = $('#year').val();
                window.location = "reportes.php?action=cajaFacturado&fechaInicial="+ month +"&fechaFinal="+ year                
            }, null, {title : "Reporte", width: 410, height: 150 }); // jConfirm        
    },


    resumenCajaMes : function() {
        this.closeAll();
        wcat.jConfirm('Mes: <input type="text" id="month" class="datepickerReporte" value="'+ moment().format("MM") +'" />' +
                      '<span style="display: inline-block; margin-left: 31px">A&ntilde;o:</span>'+
                      '<input type="text" id="year" class="datepickerReporte" value="'+ moment().format("YYYY") +'" />', 
            function(){
                var month = $('#month').val();
                var year = $('#year').val();
                window.location = "reportes.php?action=resumenCajaMes&fechaInicial="+ month +"&fechaFinal="+ year                
            }, null, {title : "Reporte", width: 410, height: 150 }); // jConfirm        
    },


            
    reportes : function(e) {
        this.closeAll();

        wcat.jConfirm('Fecha inicial: <input type="text" id="fechaInicial" class="datepickerReporte" value="'+ moment().format("DD/MM/YYYY") +'" />' +
                      '<span style="display: inline-block; margin-left: 31px">Fecha final:</span>'+
                      '<input type="text" id="fechaFinal" class="datepickerReporte" value="'+ moment().format("DD/MM/YYYY") +'" />', 
            function(){
                var fechaInicial = wcat.swapDateFormat($('#fechaInicial').val());
                var fechaFinal = wcat.swapDateFormat($('#fechaFinal').val());
                switch(e.target.id) {
/*
                    case "rPrincipal" : window.location = "reportes.php?action=rPrincipal&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal
                                        break;
*/                                        
                    case "rInsumos" : window.location = "reportes.php?action=rInsumos&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                    case "rDerivaciones" :  window.location = "reportes.php?action=rDerivaciones&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                    case "rServiciosRealizados" :  window.location = "reportes.php?action=rServiciosRealizados&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                    case "rListaServicios" :  window.location = "reportes.php?action=rListaServicios&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
// reportes clientes                                        
                    case "rCumpleanos" :  window.location = "reportes.php?action=rCumpleanos&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                    case "rConsumos" :  window.location = "reportes.php?action=rConsumos&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                    case "exportarListaClientes" :  window.location = "reportes.php?action=exportarListaClientes&fechaInicial="+ fechaInicial +"&fechaFinal="+ fechaFinal;
                                        break;
                } // switch

            }, null, {title : "Reporte", width: 410, height: 150 });
        
        $("input.datepickerReporte").datepicker({
            showOn: "button",
            buttonImage: "static/img/calendar.png",
            buttonImageOnly: true,
            dateFormat: "dd/mm/yy"
        });
        
        return false;
    },
            
    movimientosCaja : function() {
        this.closeAll();
        this.movimientosCaja = new MovimientosCaja();
        return false;
    },
            
    caja : function(e) {
        e.preventDefault();
        var sign = (e.currentTarget.id == "ingresoCaja")? 1:-1;
        
        $('#cajaPopup').dialog({
            height: 235,
            width: 300,
            title: 'Caja',
            resizable: false,
            modal: true
        });         
        
        var cajaView = new CajaView({attributes : {sign : sign}});
    },
        
            
    cerrarCajaMes: function(e) {
        e.preventDefault();
        
        var html = _.template($('#cerrarCajaTemplate').html());
        wcat.jConfirm(html, 
            function(){
                var mesSeleccionado = parseInt($('#monthCerrarCaja').val()) + 1;
                var anoSeleccionado = $('#yearCerrarCaja').val();
                var mesActual = parseInt(moment().format('MM'));
                var anoActual = parseInt(moment().format('YYYY'));
                
                if( (mesSeleccionado >= mesActual) && (anoSeleccionado == anoActual) ){ 
                    alert('No se puede cerrar el mes en curso, o los siguientes') 
                } else {
                    $.ajax({
                        url: 'caja.php?action=cerrarCajaHandler',
                        data: {year: anoSeleccionado, month: mesSeleccionado},
                        success: function(response) {
                            if(!response.success) alert('Ocurrio un error al cerrar la caja');
                        }
                    });
                }
            }, null, {width: 250, height: 150, title: 'Cerrar caja'});
        
    },
            
    mostrarCambiarSucursal: function() {
        $('#cambiarSucursal', this.$el).hide();
        $('#sucursalCombo', this.$el).show();
    },
            
    cambiarSucursal: function(e) {
        location.href = 'main.php?SUCURSAL='+ e.target.value;
    },
            
    pedidos: function() {
        this.closeAll();
        this.pedidos = new Pedidos();
        return false;
    },

    tareas: function() {
        this.closeAll();
        this.tareas = new Tareas();
        return false;
    },


    clientes: function(){
        this.closeAll();
        this.clientes = new Clientes();
        return false;
    },
    
    servicios: function(){
        this.closeAll();
        this.servicios = new Servicios();
        return false;
    },
            
    insumos: function(){
        this.closeAll();
        this.insumos = new Insumos();
        return false;
    },

    proveedores: function(){
        this.closeAll();
        this.proveedores = new Proveedores();
        return false;
    },
            
    

    closeAll: function() {
        if(!_.isUndefined(this.servicios.close)) this.servicios.close();
        if(!_.isUndefined(this.clientes.close)) this.clientes.close();
        if(!_.isUndefined(this.insumos.close)) this.insumos.close();
        if(!_.isUndefined(this.proveedores.close)) this.proveedores.close();
        if(!_.isUndefined(this.pedidos.close)) this.pedidos.close();
        if(!_.isUndefined(this.tareas.close)) this.tareas.close();
        if(!_.isUndefined(this.movimientosCaja.close)) this.movimientosCaja.close();
        if(!_.isUndefined(this.reportes.close)) this.reportes.close();
        if(!_.isUndefined(this.cuentaCorriente.close)) this.cuentaCorriente.close();
        if(!_.isUndefined(this.logCaja.close)) this.logCaja.close();
        if(!_.isUndefined(this.logPedidos.close)) this.logPedidos.close();
    },
            
    render: function() {
        this.$el.html($('#mainAppTemplate').html());
        return this;        
    }
});








