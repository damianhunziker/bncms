window.addEvent('domready', function() {
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
});