<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Lavarrap 1.0</title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="stylesheet" type="text/css" href="static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="static/css/Aristo/Aristo.css">
    <link rel="stylesheet" type="text/css" href="static/css/ultimateMenu.css">
    <link rel="stylesheet" type="text/css" href="static/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="static/css/chosen/chosen.css">

    <link rel="stylesheet" type="text/css" href="static/css/main.css">
    <link rel="stylesheet" type="text/css" href="static/css/insumosServicios.css">
    
    <style type="text/css" title="currentStyle">
        @import "static/css/datatables/demo_table_jui.css";
    </style>    

    <!-- LIB -->
    <script src="static/js/lib/jquery-1.9.1.js"></script>
    <script src="static/js/lib/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="static/js/lib/jquery.dataTables.min.js"></script>
    <script src="static/js/lib/wcat.js"></script>
    <script src="static/js/lib/underscore-min.js"></script>
    <script src="static/js/lib/backbone.js"></script>
    <script src="static/js/lib/datatable.delayQuery.js"></script>
    <script src="static/js/lib/refresh_dt.js"></script>
    <script src="static/js/lib/validate.js"></script>
    <script src="static/js/lib/moment.min.js"></script>
    <script src="static/js/lib/datatables.fnRedrawAjax.js"></script>
    <script src="static/js/lib/backbone.collectionView.min.js"></script>
    <script src="static/js/lib/chosen.jquery.min.js"></script>
    
    
    
    <script>
        
        globalConfig = {};
        globalConfig.SESSIONID = '<?php echo session_id() ?>';

        
        $.extend($.ui.dialog.prototype.options, {
                closeOnEscape: false
        });

        
        
        
        $(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
            var msg = "<strong>ERROR: </strong>"+ thrownError +"<br />";
            msg += "<strong>URL: </strong>"+ ajaxOptions.url +"<br />";
            msg += xhr.responseText;
            
            wcat.jConfirm(msg, function(){ window.location = "login.php" }, function(){ window.location = "login.php" } ,{title: "Error", width: 600, height: 400});
        });        
        
        
        $(document).ready(function(){
            window.main = new Main();
            wcat.init();
            
            Backbone.emulateHTTP = true;
            Backbone.emulateJSON = true;
            
            if($('select#sucursalCombo').size() == 1) {
                var descripcionSucursal = $('select#sucursalCombo option:selected').html();
                $('#descripcionSucursal').html(descripcionSucursal);
            }
            
            var wData = wcat.getWindowData();
            $("<style type='text/css'> .dataTableWrapper{ height: "+ (wData.altoVentana - 100) +"px;}</style>").appendTo("head");            
            $("<style type='text/css'> .dataTableWrapperCaja{ height: "+ (wData.altoVentana - 150) +"px;}</style>").appendTo("head");            
            
            
            globalConfig.mainTimer = setInterval(function(){ 
                $.ajax({ url: 'pedidos.php?action=dummy' });
            }, 180000); 

        });
    </script>



</head>

<body>
<div id="mainAppContainer"></div>

