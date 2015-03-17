/*!
 * Validate JavaScript Library v0.1
 * http://whitecat.com.ar/
 * 
 * libreria para validacion de formularios
 *
 * Requiere: jquery >= 1.8
 * http://jquery.com/
 * 
 * wCat (cualquier version)
 * 
  * Released under the MIT license
 * http://whitecat.com.ar/license
 *
 * Date: 2013-7-31
 */
var validate = (function( window, undefined ) {

// PRIVATE PROPERTIES -------------------------------------------------------------------------------------------
var
    error = {
        "errorRepeat" : "Los datos est&aacute;n repetidos",
        "onlyNumbers" : "Debe ingresar solo n&uacute;meros",
        "obligatorio" : "Completar campos obligatorios",
        "onlyIp" : "Direcci&oacute;n IP inv&aacute;lida",
        "onlyIpPort" : "Direcci&oacute;n IP inv&aacute;lida (es obligatorio especificar puerto)",
        "onlyEmail" : "Direcci&oacute;n email inv&aacute;lida",
        "onlyTime" : "Hora inv&aacute;lida",
        "onlyDate" : "D&iacute;a inv&aacute;lido"
    }    
    

    
// PUBLIC METHODS -------------------------------------------------------------------------------------------    
    return {
/*
 * test(id): formulario #id a testear
 */        
        test : function (ctx) {
            
            $('.errorField', ctx).removeClass('errorField').removeClass('errorRepeat');

            $('.obligatorio', ctx).each(function(i,e){
                if(wcat.validate.empty(e.value)) $(e).addClass('errorField');
            });


            $('.onlyIp', ctx).each(function(i,e){
                if(!wcat.validate.ip.test(e.value)) $(e).addClass('errorField');
            });


            $('.onlyIpPort', ctx).each(function(i,e){
                var ipSplitted = e.value.split(":");
                if(ipSplitted.length > 1) { // separo en 2
                    if(!wcat.validate.ip.test(ipSplitted[0]) || !validate.number.test(ipSplitted[1])) $(e).addClass("errorField");
                } else {
                    $(e).addClass("errorField");
                }
            });


            $('.onlyNumbers', ctx).each(function(i,e){
                if(!wcat.validate.number.test(e.value)) $(e).addClass("errorField");

                // validacion de rangos minimo y maximo
                if( (!_.isUndefined($(e).attr("max"))) && (!_.isUndefined($(e).attr("min"))) ) {
                        var value = parseFloat(e.value);
                        var min = parseFloat($(e).attr("min"));
                    var max = parseFloat($(e).attr("max"));
                    if(!wcat.validate.range(min,max).test(value)) $(e).addClass("errorField");
                }

                // validacion "mayor a..."
                if( (_.isUndefined($(e).attr("max"))) && (!_.isUndefined($(e).attr("min"))) ) {
                    var value = parseFloat(e.value);
                    var min = parseFloat($(e).attr("min"));
                    if(!wcat.validate.gt(min).test(value)) $(e).addClass("errorField");
                }

                // validacion "menor a..."
                if( (!_.isUndefined($(e).attr("max"))) && (_.isUndefined($(e).attr("min"))) ) {
                    var value = parseFloat(e.value);
                    var max = parseFloat($(e).attr("max"));
                    if(!wcat.validate.lt(max).test(value)) $(e).addClass("errorField");
                }
            });

            $('.onlyEmail', ctx).each(function(i,e){
                if(!wcat.validate.email.test(e.value)) $(e).addClass("errorField");
            });

            $('.onlyTime', ctx).each(function(i,e){
                if(!wcat.validate.time.test(e.value)) $(e).addClass("errorField");
            });        
        }, // end test
        
        
        
        showErrors : function(context, sMsg) {
            
            var out = '';
            if(!_.isUndefined(sMsg)) out = sMsg;            
            
            $('.errorField', context).each(function(i,e){

                if(e.className.search('errorRepeat') !== -1){
                   out += '<strong>' + error.errorRepeat +'</strong> (' + e.title +')<br />';
                } else {
                    if(e.className.search('onlyNumbers') !== -1){
                        if((e.min.length !== 0) || (e.max.length !== 0)) {
                            var rango = '';
                            if((e.min.length !== 0) && (e.max.length !== 0)) {
                                rango = 'entre '+ e.min +' y '+ e.max;
                            } else {
                                if(e.min.length !== 0) {
                                    var rangoMinimo = parseInt(e.min) + 1;
                                    rango = 'm&iacute;nimo: >= ' + rangoMinimo;
                                }
                                if(e.max.length !== 0) {
                                    var rangoMaximo = parseInt(e.max) - 1;
                                    rango = 'm&aacute;ximo: <= ' + rangoMaximo;
                                }
                            }

                            out += '<strong>'+ error.onlyNumbersMinMax +'</strong> ('+ e.title +' - '+ rango +')<br />';
                        } else {
                            out += '<strong>'+ error.onlyNumbers +'</strong> ('+ e.title +')<br />';
                        }
                    } else {
                        if ((e.className.search('onlyIp') !== -1) ||
                            (e.className.search('onlyEmail') !== -1) ||
                            (e.className.search('onlyTime') !== -1) ||
                            (e.className.search('onlyDate') !== -1)) {

                            if(e.className.search('onlyIpPort') !== -1) { 
                                out += '<strong>' + error.onlyIpPort +'</strong> ('+ e.title +')<br />';
                            } else {
                                if(e.className.search('onlyIp') !== -1) out += '<strong>' + error.onlyIp +'</strong> ('+ e.title +')<br />';                     
                            }

                            if(e.className.search('onlyEmail') !== -1) out += '<strong>' + error.onlyEmail +'</strong> ('+ e.title +')<br />';
                            if(e.className.search('onlyTime') !== -1) out += '<strong>' + error.onlyTime +'</strong> ('+ e.title +')<br />';
                            if(e.className.search('onlyDate') !== -1) out += '<strong>' + error.onlyDate +'</strong> ('+ e.title +')<br />';

                        } else { // solo queda chequear si es obligatorio
                            out += '<strong>' + error.obligatorio +'</strong> ('+ e.title +')<br />';
                        } // resto 

                    } // onlyNumbers
                } // errorRepeat
            });

            return out;
        }, // end showErrors
        
        
        getErrorCount: function(ctx) {
            return $('.errorField', ctx).size();            
        }
        
    
    } // end return 
})(window);




	









