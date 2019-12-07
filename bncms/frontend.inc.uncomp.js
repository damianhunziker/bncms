$.fn.textWidth = function(text, font) {

    if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span .pseudoInputTextWidth>').hide().appendTo(document.body);

	//if (!$.fn.textWidth.fakeEl2) $.fn.textWidth.fakeEl2 = $('<span>').appendTo(document.body);

    $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));

	w = $.fn.textWidth.fakeEl.width();

	//$.fn.textWidth.fakeEl2.text(w);

    return w; 

};



(function($) {

//onvisible

    $.fn.onVisible = function (callback) {

        var self = this;

        var selector = this.selector;

		

        if (self.is(":visible")) {

			

            callback.call(self);

        } else {

			

            timer = setInterval(function() {

										 

                if ($(selector).is(":visible")) {

                    callback.call($(selector));

                    clearInterval(timer);

                }

            }, 50);

        }

    }

    

}(jQuery));



function ntomAjaxSearch(e,relationId) {

	//nicht verwendet

	switch (e.keyCode) {

		case 40:

		alert('return40');

		return;

		case 38:

		alert('return38');

		return;

	}

	if (e.value.indexOf(',') > 0)

		var va = strrchr(e.value,',').replace(', ','');

	else

		var va = e.value;

	jQuery.ajax({

		method: "GET",

		url: RELATIVEPATH+"/ajax.php?ntomAjaxSearch="+relationId+"&value="+va,

		success: function(msg) {

			console.log(msg);

			a = unserialize(urldecode(msg));

			console.log(a);

			var s = "";

			var c = 0;

			for (var key in a) {

   				if (a.hasOwnProperty(key)) {

					c++;

					if (c == 1) 

						se = "selected";

					else

						se = "";

					s = s+ "<li class='"+se+"'><a href='javascript:void(0)' onclick=\"jQuery('#ntom_"+relationId+"').val(document.getElementById('ntom_"+relationId+"').value.replace('"+va+"', '"+a[key].title+", ')); jQuery('#ntom_"+relationId+"_autocomplete').html('').slideUp('slow'); \">"+a[key].title+"</a><br></li>";

				}

			}

			jQuery('#ntom_'+relationId+'_autocomplete').html('<ul>'+s+'</ul><script>jQuery(\'input#ntom_'+relationId+'\').keydown(function (e) {var searchbox = jQuery(this); switch (e.keyCode) {case 40:	jQuery(\'li:not(:last-child).selected\').removeClass(\'selected\').next().addClass(\'selected\'); break;case 38:jQuery(\'li:not(:last-child).selected\').removeClass(\'selected\').prev().addClass(\'selected\');}});</script>').slideDown('slow');

		}

	});

}



/*fe*/

var userValues = new Array();

var ds = 0;

function validate(table, formid, projectpath) {

	ds = 0;

	$('#'+formid+' *').filter(':input').each(function(){

		userValues[this.name] = this.value;

		jQuery('#val_'+formid+'_'+this.name).html('');

	});

	jQuery('#bu_'+formid).css('display','none');

	jQuery('#lo_'+formid).css('display','block');

	//console.log(userValues);

	//alert(RELATIVEPATH+"/ajax.php?validate="+table+"&formid="+formid+"&projectpath="+projectpath);

	jQuery.ajax({

		method: "POST",

		url: RELATIVEPATH+"/ajax.php?validate="+table+"&formid="+formid+"&projectpath="+projectpath,

		data: { 'userValues': encodeURIComponent(serialize(userValues)) },

		success: function(msg) {

			a = unserialize(urldecode(msg));

			//alert(msg);

			

			//console.log(a);

			for (var key in a) {

					//alert(key);

   				if (a.hasOwnProperty(key)) {

					//console.log(a[key],key);

					jQuery('#val_'+formid+'_'+key).html(a[key]);

					//alert(a[key]);

					ds = 1;

				}

			}

			if (ds == 0) {

				document.getElementById(formid).submit();

			} else {

				jQuery('#'+formid).animate({marginLeft: "-=5px"},{

    duration: 200}).animate({marginLeft: "+=5px"},{

    duration: 200}).animate({marginLeft: "-=4px"},{

    duration: 200}).animate({marginLeft: "+=4px"},{

    duration: 200});

				jQuery('#bu_'+formid).css('display','block');

				jQuery('#lo_'+formid).css('display','none');

			}

	}

	});

}

function allowChars(e, s) {

	var start = e.selectionStart,

    	end = e.selectionEnd;

	e.value = e.value.replace(s,'');

	e.setSelectionRange(start, end);

}



function urldecode(url) {

  return decodeURIComponent(url.replace(/\+/g, ' '));

}

function show_lightbox(id) {

	if (document.getElementById(id).style.display == 'none')

		document.getElementById(id).style.display = 'block';

	else

		document.getElementById(id).style.display = 'none';

}

function unserialize(data) {

  var that = this,

    utf8Overhead = function(chr) {

      // http://phpjs.org/functions/unserialize:571#comment_95906

      var code = chr.charCodeAt(0);

      if (code < 0x0080) {

        return 0;

      }

      if (code < 0x0800) {

        return 1;

      }

      return 2;

    };

  error = function(type, msg, filename, line) {

    throw new that.window[type](msg, filename, line);

  };

  read_until = function(data, offset, stopchr) {

    var i = 2,

      buf = [],

      chr = data.slice(offset, offset + 1);



    while (chr != stopchr) {

      if ((i + offset) > data.length) {

        error('Error', 'Invalid');

      }

      buf.push(chr);

      chr = data.slice(offset + (i - 1), offset + i);

      i += 1;

    }

    return [buf.length, buf.join('')];

  };

  read_chrs = function(data, offset, length) {

    var i, chr, buf;



    buf = [];

    for (i = 0; i < length; i++) {

      chr = data.slice(offset + (i - 1), offset + i);

      buf.push(chr);

      length -= utf8Overhead(chr);

    }

    return [buf.length, buf.join('')];

  };

  _unserialize = function(data, offset) {

    var dtype, dataoffset, keyandchrs, keys, contig,

      length, array, readdata, readData, ccount,

      stringlength, i, key, kprops, kchrs, vprops,

      vchrs, value, chrs = 0,

      typeconvert = function(x) {

        return x;

      };



    if (!offset) {

      offset = 0;

    }

    dtype = (data.slice(offset, offset + 1))

      .toLowerCase();



    dataoffset = offset + 2;



    switch (dtype) {

      case 'i':

        typeconvert = function(x) {

          return parseInt(x, 10);

        };

        readData = read_until(data, dataoffset, ';');

        chrs = readData[0];

        readdata = readData[1];

        dataoffset += chrs + 1;

        break;

      case 'b':

        typeconvert = function(x) {

          return parseInt(x, 10) !== 0;

        };

        readData = read_until(data, dataoffset, ';');

        chrs = readData[0];

        readdata = readData[1];

        dataoffset += chrs + 1;

        break;

      case 'd':

        typeconvert = function(x) {

          return parseFloat(x);

        };

        readData = read_until(data, dataoffset, ';');

        chrs = readData[0];

        readdata = readData[1];

        dataoffset += chrs + 1;

        break;

      case 'n':

        readdata = null;

        break;

      case 's':

        ccount = read_until(data, dataoffset, ':');

        chrs = ccount[0];

        stringlength = ccount[1];

        dataoffset += chrs + 2;



        readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));

        chrs = readData[0];

        readdata = readData[1];

        dataoffset += chrs + 2;

        if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {

          error('SyntaxError', 'String length mismatch');

        }

        break;

      case 'a':

        readdata = {};



        keyandchrs = read_until(data, dataoffset, ':');

        chrs = keyandchrs[0];

        keys = keyandchrs[1];

        dataoffset += chrs + 2;



        length = parseInt(keys, 10);

        contig = true;



        for (i = 0; i < length; i++) {

          kprops = _unserialize(data, dataoffset);

          kchrs = kprops[1];

          key = kprops[2];

          dataoffset += kchrs;



          vprops = _unserialize(data, dataoffset);

          vchrs = vprops[1];

          value = vprops[2];

          dataoffset += vchrs;



          if (key !== i)

            contig = false;



          readdata[key] = value;

        }



        if (contig) {

          array = new Array(length);

          for (i = 0; i < length; i++)

            array[i] = readdata[i];

          readdata = array;

        }



        dataoffset += 1;

        break;

      default:

       // error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);

        break;

    }

    return [dtype, dataoffset - offset, typeconvert(readdata)];

  };



  return _unserialize((data + ''), 0)[2];

}

function adaptWidthToText(valOrPlace, t) {

	var o='';

	if (valOrPlace == 'placeholder') {

		var w = jQuery(t).textWidth('aa'+jQuery(t).attr('placeholder'));

	} else {

		var w = jQuery(t).textWidth('aa'+jQuery(t).val());

	}

	if (w > jQuery(document).width()*0.7)

		w = jQuery(document).width()*0.7;

	jQuery(t).css('width',w);

}

function serialize(mixed_value) {

  var val, key, okey,

    ktype = '',

    vals = '',

    count = 0,

    _utf8Size = function(str) {

      var size = 0,

        i = 0,

        l = str.length,

        code = '';

      for (i = 0; i < l; i++) {

        code = str.charCodeAt(i);

        if (code < 0x0080) {

          size += 1;

        } else if (code < 0x0800) {

          size += 2;

        } else {

          size += 3;

        }

      }

      return size;

    };

  _getType = function(inp) {

    var match, key, cons, types, type = typeof inp;



    if (type === 'object' && !inp) {

      return 'null';

    }

    if (type === 'object') {

      if (!inp.constructor) {

        return 'object';

      }

      cons = inp.constructor.toString();

      match = cons.match(/(\w+)\(/);

      if (match) {

        cons = match[1].toLowerCase();

      }

      types = ['boolean', 'number', 'string', 'array'];

      for (key in types) {

        if (cons == types[key]) {

          type = types[key];

          break;

        }

      }

    }

    return type;

  };

  type = _getType(mixed_value);



  switch (type) {

    case 'function':

      val = '';

      break;

    case 'boolean':

      val = 'b:' + (mixed_value ? '1' : '0');

      break;

    case 'number':

      val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;

      break;

    case 'string':

      val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';

      break;

    case 'array':

    case 'object':

      val = 'a';

      /*

        if (type === 'object') {

          var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);

          if (objname == undefined) {

            return;

          }

          objname[1] = this.serialize(objname[1]);

          val = 'O' + objname[1].substring(1, objname[1].length - 1);

        }

        */



      for (key in mixed_value) {

        if (mixed_value.hasOwnProperty(key)) {

          ktype = _getType(mixed_value[key]);

          if (ktype === 'function') {

            continue;

          }



          okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);

          vals += this.serialize(okey) + this.serialize(mixed_value[key]);

          count++;

        }

      }

      val += ':' + count + ':{' + vals + '}';

      break;

    case 'undefined':

      // Fall-through

    default:

      // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP

      val = 'N';

      break;

  }

  if (type !== 'object' && type !== 'array') {

    val += ';';

  }

  return val;

}





function ajax_submit(id, variable, path, projectpath) {

	//alert(id);

	sp = new Array();

	if (variable) {

		a = variable.split("=");

		sp[a[0]] = a[1];

	}

	arg_list = unserialize(urldecode(document.getElementById("arg_list_"+id).value));

	//console.log(arg_list);

	//document.getElementById("s_"+id).style.display="block";	

	jQuery('#f_'+id+' #loading_overlay').css('height', jQuery('#f_'+id+' #loading_overall_table').outerHeight());

	jQuery('#f_'+id+' #loading_overlay').css('width', jQuery('#f_'+id+' #loading_overall_table').outerWidth());

	

	var ml = ((jQuery('#f_'+id+' #loading_overall_table').outerWidth()/2) - 25)+"px";

	var mt = ((jQuery('#f_'+id+' #loading_overall_table').outerHeight()/2) - 25)+"px";

	

	document.getElementById("l_"+id).style.position="absolute";

	document.getElementById("l_"+id).style.width="50px";

	document.getElementById("l_"+id).style.backgroundBlendMode="none";

	document.getElementById("l_"+id).style.marginLeft=ml;

	document.getElementById("l_"+id).style.marginTop=mt;

	document.getElementById("l_"+id).style.display="block";	

	//jQuery("#"+id).toggle('slow');	

	//condition = "";

	

	jQuery('.c'+id).each(

    function(index, elem){  

        var input = jQuery(this).val();

		var e = jQuery(elem);

		//console.log(input);

		//wegl&ouml;schen alter anfragen mit parameter

		//condition = condition.replace(" AND "+ field+" LIKE \"%"+input+"%\","");

		if (e.attr('id').indexOf("earch_") && input != '') {

			sp[e.attr('id')] = input;

		}

		if (e.attr('id') == "page_table" && input != '') {

			sp[e.attr('id')] = input;

		}

		if (e.attr('id') == "place" && input != '') {

			sp[e.attr('id')] = input;

		}

		if (e.attr('id') == "page" && input != '') {

			//console.log(input);

			sp[e.attr('id')] = input;

		}

		

    }

);

	

	if (arg_list[2] == "null")

		arg_list[2] = "";

	//if (condition)

	//	arg_list[2] = arg_list[2] + condition;



	arg_list[12] = serialize(sp); //searchParams

	//console.log(arg_list); 

	//alert( path);

	//console.log(arg_list);

	arg_list = new Array();

	arg_list['id'] = id;

	arg_list['sp'] = serialize(sp);

	jQuery.ajax({

	  method: "POST",

	  url: path+"/ajax.php?projectpath=",

	  success: function(msg) {  

	  //alert(msg);

			jQuery('#'+id).html(msg);

			document.getElementById('l_'+id).style.display = 'none';

		},

	 data: { func: "dt", param: encodeURIComponent(serialize(arg_list)) }

	});

}



function anschalten(div)

{

	//setze_scrollposition();

	jQuery( "#"+div ).toggle('slow'); 

	//document.getElementById(div).style.display = '';

	document.getElementById('minus'+div).style.display = '';

	document.getElementById('plus'+div).style.display = 'none';

}



function ausschalten(div)

{

	//setze_scrollposition();

	jQuery( "#"+div ).fadeOut(); 

	//document.getElementById(div).style.display = 'none';

	document.getElementById('minus'+div).style.display = 'none';

	document.getElementById('plus'+div).style.display = '';



}

var http = null;

if (window.XMLHttpRequest) {

   http = new XMLHttpRequest();

} else if (window.ActiveXObject) {

   http = new ActiveXObject("Microsoft.XMLHTTP");

}





function pageOffset(win)

{

    if(!win) win = window;

    var pos = {left:0,top:0};



    if(typeof win.pageXOffset != 'undefined')

    {

         // Mozilla/Netscape

         pos.left = win.pageXOffset;

         pos.top = win.pageYOffset;

    }

    else

    {

         var obj = (win.document.compatMode && win.document.compatMode == "CSS1Compat") ?

         win.document.documentElement : win.document.body || null;



         pos.left = obj.scrollLeft;

         pos.top = obj.scrollTop;

    }

    return pos;

}



//Paging

window.addEvent('domready', function(){

	var scroll2 = new Scroller('container', {area: 30, velocity: 2});

	

	// container

	$('container').addEvent('mouseover', scroll2.start.bind(scroll2));

	$('container').addEvent('mouseout', scroll2.stop.bind(scroll2));

}); 

var wstat

var ns4up = (document.layers) ? 1 : 0

var ie4up = (document.all) ? 1 : 0

var xsize = screen.width

var ysize = screen.height

var breite=xsize

var hoehe=ysize

var xpos=(xsize-breite)

var ypos=(ysize-hoehe)

function opwin(url, name) {

	wstat=window.open(url,name,"scrollbars=yes,status=no,toolbar=no,location=no,directories=no,resizable=yes,menubar=no,width="+breite+",height="+hoehe+",screenX="+xpos+",screenY="+ypos+",top="+ypos+",left="+xpos)

	wstat.focus();

}

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

