jQuery.fn.textWidth = function(text, font) {
    if (!jQuery.fn.textWidth.fakeEl) jQuery.fn.textWidth.fakeEl = jQuery('<span .pseudoInputTextWidth>').hide().appendTo(document.body);
	//if (!$.fn.textWidth.fakeEl2) $.fn.textWidth.fakeEl2 = $('<span>').appendTo(document.body);
    jQuery.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
	w = jQuery.fn.textWidth.fakeEl.width();
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
										 
                if ($(selector).is(":visible")) {;
                    callback.call($(selector));
                    clearInterval(timer);
                }
            }, 2000); 
        }
    }
    
}(jQuery));

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
	jQuery.ajax({
		method: "POST",
		url: RELATIVEPATH+"/ajax.php?validate="+table+"&formid="+formid+"&projectpath="+projectpath,
		data: { 'userValues': encodeURIComponent(serialize(userValues)) },
		success: function(msg) {
			a = unserialize(urldecode(msg));
			for (var key in a) {
   				if (a.hasOwnProperty(key)) {
					jQuery('#val_'+formid+'_'+key).html(a[key]);
					ds = 1;
				}
			}
			if (ds == 0) {
				//document.getElementById(formid).submit();
				//alert('submit');
				return true;
			} else {
				jQuery('#'+formid).animate({marginLeft: "-=5px"},{
					duration: 200}).animate({marginLeft: "+=5px"},{
					duration: 200}).animate({marginLeft: "-=5px"},{
					duration: 200}).animate({marginLeft: "+=5px"},{
					duration: 200});
				jQuery('#bu_'+formid).css('display','block');
				jQuery('#lo_'+formid).css('display','none');
				//alert('not submit');
				return false;
			}
	}
	});
}

function showPossibleRelations(tableId, users, div, path) {
  jQuery.ajax({
		method: "GET",
		url: RELATIVEPATH+"/ajax.php?showPossibleRelations="+tableId+"&users="+encodeURIComponent(serialize(users))+"&path="+path,
		success: function(msg) {
			jQuery("#"+div).html(msg);
			jQuery(".darstellung_"+path).show();
		}
	});
}
function saveTitleIcon(path) {
	jQuery('#bu_'+path).css('display','none');
	jQuery('#lo_'+path).css('display','block');
	var data = new FormData();
	data.append('file', jQuery("#icon_"+path)[0].files[0]); 
	$.ajax({
		url: RELATIVEPATH+"/ajax.php?saveTitleIcon=1&path="+path+"&title="+jQuery("#title_"+path).val()+"&showWithEditIcons="+jQuery("#showWithEditIcons_"+path).val()+"&deleteIcon="+jQuery("#deleteIcon"+path).val(),
		data: data,
		type: 'POST',
		processData: false,
		contentType: false,
		success: function(m) {
			clearFileInput(document.getElementById("icon_"+path));
			$("#responses_"+path).html("Gespeichert"); 
			if (m)
				jQuery('#sir_'+path).html("<img src='"+m+"' style='display:inline;height:16px;width:16px'> Icon l&ouml;schen <input type=checkbox id='deleteIcon_"+path+"'>");
			else
				jQuery('#sir_'+path).html('');
			jQuery('#bu_'+path).css('display','block');
			jQuery('#lo_'+path).css('display','none');
		}
	});
}
function clearFileInput(ctrl) {
  try {
	ctrl.value = null;
  } catch(ex) { }
  if (ctrl.value) {
	ctrl.parentNode.replaceChild(ctrl.cloneNode(true), ctrl);
  }
}
function strrchr (haystack, needle) {
    var pos = 0;

    if (typeof needle !== 'string') {
        needle = String.fromCharCode(parseInt(needle, 10));
    }
    needle = needle.charAt(0);
    pos = haystack.lastIndexOf(needle);
    if (pos === -1) {
        return false;
    }

    return haystack.substr(pos);
}
function ntomAjaxSearch(e,relationId) {
	alert('hu');
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
			a = unserialize(urldecode(msg));
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
function ajax_submit(id, variable, path, projectpath) {
	//alert(id);
	sp = new Array();
	if (variable) {
		a = variable.split("=");
		sp[a[0]] = a[1];
	}
	arg_list = unserialize(urldecode(document.getElementById("arg_list_"+id).value));
	jQuery('#f_'+id+' #loading_overlay').css('height', jQuery('#f_'+id+' #loading_overall_table').outerHeight());
	jQuery('#f_'+id+' #loading_overlay').css('width', jQuery('#f_'+id+' #loading_overall_table').outerWidth());
	
	var ml = ((jQuery('#f_'+id+' #loading_overall_table').outerWidth()/2) - 25)+"px";
	var mt = ((jQuery('#f_'+id+' #loading_overall_table').outerHeight()/2) - 25)+"px";
	
	document.getElementById("l_"+id).style.position="absolute";
	document.getElementById("l_"+id).style.width="50px";
	document.getElementById("l_"+id).style.backgroundBlendMode="multiply";
	document.getElementById("l_"+id).style.marginLeft=ml;
	document.getElementById("l_"+id).style.marginTop=mt;
	document.getElementById("l_"+id).style.display="block";	
	//jQuery("#"+id).toggle('slow');	
	//condition = "";
	
	jQuery('.c'+id).each(
    function(index, elem){  
        var input = jQuery(this).val();
		var e = jQuery(elem);
		//wegl&ouml;schen alter anfragen mit parameter
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

	arg_list[12] = serialize(sp); //searchParams

	arg_list = new Array();
	arg_list['id'] = id;
	arg_list['sp'] = serialize(sp);
	jQuery.ajax({
	  method: "POST",
	  url: path+"/ajax.php?projectpath=",
	  success: function(msg) {  
			jQuery('#'+id).html(msg);
			document.getElementById('l_'+id).style.display = 'none';
		},
	 data: { func: "dt", param: encodeURIComponent(serialize(arg_list)) }
	});
}

function anschalten(div)
{
    if (document.getElementById(div)) {
		jQuery( "#"+div ).fadeIn('slow');
		document.getElementById('minus'+div).style.display = 'inline';
		document.getElementById('plus'+div).style.display = 'none';
	}
}

function ausschalten(div)
{
	if (document.getElementById(div)) {
		jQuery( "#"+div ).fadeOut(); 
		document.getElementById('minus'+div).style.display = 'none';
		document.getElementById('plus'+div).style.display = 'inline';
	}

}
var http = null;
if (window.XMLHttpRequest) {
   http = new XMLHttpRequest();
} else if (window.ActiveXObject) {
   http = new ActiveXObject("Microsoft.XMLHTTP");
}

function ajax_send_scrollpos(sPhpSelf, an, aus) {
	
	pos = pageOffset();
	if (an) {
		anschalten(an);
	}
	if (aus) {
		ausschalten(aus);
	}
	if (http != null) {
	   http.open("GET", "save_scrollpos.php?an="+an+"&aus="+aus+"&scrollTop="+pos.top+"&scrollLeft="+pos.left+"&phpSelf="+sPhpSelf, true);
	   http.send(null);
	}
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
/*window.addEvent('domready', function() {
	var status = {
		'true': 'open',
		'false': 'close'
	};
	
	//-vertical
	var myVerticalSlide = new Fx.Slide('vertical_slide');

	$('v_slidein').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.slideIn();
	});

	$('v_slideout').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.slideOut();
	});

	$('v_toggle').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.toggle();
	});

	$('v_hide').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.hide();
		$('vertical_status').set('html', status[myVerticalSlide.open]);
	});
	
	$('v_show').addEvent('click', function(e){
		e.stop();
		myVerticalSlide.show();
		$('vertical_status').set('html', status[myVerticalSlide.open]);
	});
	
	// When Vertical Slide ends its transition, we check for its status
	// note that complete will not affect 'hide' and 'show' methods
	myVerticalSlide.addEvent('complete', function() {
		$('vertical_status').set('html', status[myVerticalSlide.open]);
	});
});*/
//Paging
/*window.addEvent('domready', function(){
	var scroll2 = new Scroller('container', {area: 30, velocity: 2});
	
	// container
	$('container').addEvent('mouseover', scroll2.start.bind(scroll2));
	$('container').addEvent('mouseout', scroll2.stop.bind(scroll2));
}); */

function opwin(url, name) {
	var breite = jQuery(window).width();
	var hoehe = jQuery(window).height();
	alert(hoehe);
wstat=window.open(url,name,"scrollbars=yes,status=no,toolbar=no,location=no,directories=no,resizable=yes,menubar=no,width="+breite+",height="+hoehe+",screenX=0,screenY=0,top=0,left=0")
wstat.focus();
}


<!-- PreLoad Wait - Script -->
<!-- This script and more from http://www.rainbow.arch.scriptmania.com 

function waitPreloadPage() { //DOM
if (document.getElementById){
jQuery('#prepage').fadeOut();
}else{
if (document.layers){ //NS4
document.prepage.visibility = 'hidden';
}
else { //IE4
document.all.prepage.style.visibility = 'hidden';
}
}
}
// End -->
function rowAnimationMouseOver(element) {
	document.getElementById(element).style.backgroundColor = "white";
	window.setTimeout("setColorOfElement('"+element+"','grey')",500);
	window.setTimeout("setColorOfElement('"+element+"','white')",500);
	window.setTimeout("setColorOfElement('"+element+"','grey')",500);
	window.setTimeout("setFontSizeOfElement('"+element+"')",500);
}
function setColorOfElement(asdf,color) {
	document.getElementById(asdf).style.backgroundColor = color;
}
function setFontSizeOfElement(asdf) {
	document.getElementById(asdf).style.fontSize=(parseInt(document.getElementById(asdf).style.fontSize.replace('px',''))+1)+"px";
}
function resetRowAnimation(element) {
	/*document.getElementById(element).style.backgroundColor = "yellow";*/
}
function absLeft(el) {
     return (el.offsetParent)? 
     el.offsetLeft+absLeft(el.offsetParent) : el.offsetLeft;
  }

  function absTop(el) {
     return (el.offsetParent)? 
     el.offsetTop+absTop(el.offsetParent) : el.offsetTop;
  }
function markEntry(element, resetColor) {
	if (document.getElementById(element).style.backgroundColor == "yellow") {
		document.getElementById(element).style.backgroundColor='white';
	} else {
		document.getElementById(element).style.backgroundColor='yellow';
	}
}