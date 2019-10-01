Vista-like Ajax Calendar version 2.1.1

Author  : R. Schoo aka rcz
Email   : rcz@base86.com
Website : http://www.base86.com
Script	: http://dev.base86.com/scripts/vista-like_ajax_calendar_version_2.html

----------------------------------- LICENSE -----------------------------------

Licensed under the Creative Commons Attribution- NonCommercial 3.0 License. 
(http://creativecommons.org/licenses/by-nc/3.0/)
What that means is: Use these files however you want, but don't redistribute 
without the proper credits and do not use this for commercial purposes.

The Vista-like Ajax Calendar requires a purchased commercial license when 
used commercially.
(http://dev.base86.com/scripts/vista-like_ajax_calendar_commercial_license.html)


------------------------------------ USAGE ------------------------------------

The vlaCalendar version 2.1 is compatible with mootools version 1.2. 
VlaCalendar version 2.0 is compatible for both mootools version 1.11 and 1.2b2 
beta. This usage chapter is written for vlaCalendar version 2.1, but is the 
same for older versions which have sourcefiles with different names.

Include the javascript files

    * vlaCal-v2.1.js and mootools-1.2-core.js
      OR the compressed versions
    * vlaCal-v2.1-compressed.js and mootools-1.2-core-compressed.js

within the head of your HTML document.

Include either the compressed or normal version of both files. The normal 
versions contain whitespace and comments useful for developing purposes. The 
default path in which the files reside is jslib/ but they could ofcourse be 
moved to where ever it suit your needs.

Same story for the stylesheets. Include vlaCal-v2.1.css and other style files 
also within the head of your HTML document. The default path in which they 
reside is styles/.

Instantiation of the calandar or datepicker classes needs to happen after the 
DOM is ready. This is done by using the mootools domready event. This event 
also needs to be included in the head.

  <script type="text/javascript">
    window.addEvent('domready', function() {
      //Datepicker
      new vlaDatePicker('textbox-id');
      //Calendar
      new vlaCalendar('block-element-id');
    });
  </script>

Both calendar and datepicker have a variety of options to style and format the 
calendar to your needs. This is done by providing options while instantiating 
the class. All options are optional (duh) and reside in a javascript object 
which is passed as the second argument. An object in javascript is a collection 
of key-value pairs separated by commas and contained within curly-brackets {}. 
For more information about the options view the examples and the option list. 
For more information about the arguments view the argument list.

The PHP files, used to create calendar HTML, reside in the default inc/ directory. 
If you want to use a different path you will need to change the default filepath 
in the vlaCalendar javascript file or provide the filepath option.


--------------------------------- CHANGE LIST -----------------------------------

vlaCalendar v2.1.0 - v2.1.1:

    * Minor improvements in the PHP code
    * Added example page in the download package


vlaCalendar v2.0 - v2.1:

    * Version is made for usage with mootools v1.2 only
    * New user requested features:
          o Prefilling of the date
          o Default view specification
          o Input and datepicker linkage, if the input is changed the datepicker 
	    will adapt itself to that input
