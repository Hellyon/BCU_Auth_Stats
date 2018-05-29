// app.js
let $ = require('jquery');

require('../css/main.scss');
require('bootstrap-sass');

$('[data-toggle="tabajax"]').click(function(e) {
    let $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});

$(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
});

$( function() {
    $( "#tabs" ).tabs({
        beforeLoad: function( event, ui ) {
            ui.jqXHR.fail(function() {
                ui.panel.html(
                    "Erreur lors du chargement de la page");
            })
        }
    }).addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
} );

$(document).ready(function(){
    let date_input=$('input[name="date"]'); //our date input has the name "date"
    let container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
    let options={
        format: 'yyyy-mm-dd',
        container: container,
        todayHighlight: true,
        autoclose: true,
    };
    date_input.datepicker(options);
});