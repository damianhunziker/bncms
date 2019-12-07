<?php

include("bncms/inc/start.inc.php");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <? echo $header; ?>
    <script type="text/javascript" src="bncms/jquery.js"></script>
    <script type="text/javascript" src="bncms/lib/jquery-visible-master/jquery.visible.js"></script>
    <script type="text/javascript" src="bncms/lib/jquery.animate-colors-min.js"></script>
    <script type="text/javascript" src="bncms/frontend.inc.js"></script>

    <link rel="stylesheet" type="text/css" href="s.css">
    <script>RELATIVEPATH = '<?= RELATIVEPATH?>';</script>
    <script>

        $(function() {
            function split( val ) {
                return val.split( /,\s*/ );
            }
            function extractLast( term ) {
                return split( term ).pop();
            }
            var cache = {};
            jQuery( ".ntom_autocomplete" ).bind( "keydown", function( event ) {
                if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).autocomplete( "instance" ).menu.active ) {
                    event.preventDefault();
                }
            }).autocomplete({
                minLength: 1,
                select: function( event, ui ) {
                    var terms = split( this.value );
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push( ui.item.value );
                    // add placeholder to get the comma-and-space at the end
                    terms.push( "" );
                    this.value = terms.join( ", " );
                    return false;
                },
                search: function() {
                    // custom minLength
                    var term = extractLast( this.value );
                    if ( term.length < 1 ) {
                        return false;
                    }
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                source: function( request, response ) {
                    var term = extractLast(request.term);
                    if ( term in cache ) {
                        response( cache[ term ] );
                        return;
                    }
                    var ntomid = this.element[0].id.replace('ntom_','');
                    //alert(RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+ntomid+"&value="+term);
                    jQuery.getJSON( RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+ntomid+"&value="+term, {
                        term: extractLast( request.term )
                    }, function( data, status, xhr ) {
                        //console.log(data);
                        cache[ term ] = data;
                        response( data );
                    });
                }
            });
        });
    </script>
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="bncms/lib/jquery-ui/jquery-ui.min.css">
    <script src="bncms/lib/jquery-ui/jquery-ui.min.js"></script>
    <style>
        .ui-autocomplete-loading {
            padding-right: 10px;
            background: url('<? echo RELATIVEPATH; ?>/image/loading.gif') right center no-repeat;
            background-size: 20px 20px;
            background-origin: content-box;
        }
    </style>
    <title>Jobbrett.net IT Jobs</title>
</head>

<body>
<?php

displayTable(3,"id");
displayTable(4,"id");
?>