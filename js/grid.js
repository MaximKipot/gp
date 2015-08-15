/*
Grid System - v3.9 - 2014-01-18
*/

// jQuery(window).load(function($){
	
// jQuery(document).load(function

// );

// window.onload = function () {
//     // console.log('window.onload');
// };

jQuery(document).ready(function($){
	
	// Activate custom scroll

	//var _resources = "json/"; // path to the json file with sizes
	
	var _tolerance = 0;
	var _browser_orientation;
	var screen_orientation;
	var _new_size;
	var _element_ch; // element attribute: height or width, depending on screen orientation
	var _horizontal_brake = 650;
	var _vertical_brake = 450;
	var _element_of_interest = "#grid_content";
	var _element_of_interest_article = ".site-content";
	var _element_of_interest_article_procent = 95;
	var $container = $('#page_grid');
	var _isotope_layout_mode;

	var _template_exceptions = ['page-template-vertical-php'];
	

	// setting the secondary size attribute (width/height) of the smallest grid element	
	var _use_user_element_size = true;
	var _multiplicator = 1; // use of multiplicator
	var _element_margin = 0; // used in size calculation

	// settings for row height
	var _use_user_row_column = true;
	var _default_row_column = false;
	var _row_column;
	
	var _animation_duration = 300;

	var _max_element_size = 6;
	var _max_element_size_page;
	var _unit_size;
	var _num_elements = 1;
	var _current_size;
	var _current_size_contra;
	var _grid_element = "#page_grid";

	var _browser_size_width = $('body').width();
	var _browser_size_height = $('body').height();
	var _timer;
	var _window_changed = false;
	var _debug = false;
	var _secondary_debug = false;

	var _scrollbar_width = get_scrollbar_size();
	var _scroll_step = 60;

	var _inner_element_padding = 12;

	var _left_bar_scroll_step = 60;

	// Horizontal Columns Settings
	var _horizontal_article_gap = 50;
	var _default_accordion = '';

	$(window).resize(function() {
		clearTimeout(_timer);
			_timer = setTimeout(function(){

				detect_browser_orientation();

				if($('body').attr('body_orientation') != _browser_orientation) _window_changed = true;

				if(_browser_orientation != 'undefined' && _browser_orientation == 'vertical') {
					if($('body').width() != _browser_size_width) {
						_window_changed = true;
						// alert('Resizing Width:'+_browser_size_width+'-'+$('body').width());
						_browser_size_width = $('body').width();
						_browser_size_height = $('body').height();
					}
				} else {
					if($('body').height() != _browser_size_height || $('html').hasClass('no-touch') == true) {
						_window_changed = true;
						// alert('Resizing Height:'+_browser_size_height+'-'+$('body').height());
						_browser_size_width = $('body').width();
						_browser_size_height = $('body').height();
					}
				}
				
				if(_window_changed) {
					do_on_resize();
					// _window_changed = false;
					// console.log('Resigin');
				}
			}, 300)               
	})



	function add_horizontal_mousewheel(){
		
		if(_browser_orientation != 'undefined' && _browser_orientation == 'horizontal') {
			// $('html, body').bind('mousewheel', function(e, delta) {
			// 	this.scrollLeft -= (delta * _scroll_step);
			// 	e.preventDefault();
			// });

			$('#grid_content').bind('mousewheel', function(e, delta) {
				this.scrollLeft -= (delta * _scroll_step);
				e.preventDefault();
			});


		} else {
			// $('html, body').unbind('mousewheel');
			$('#grid_content').unbind('mousewheel');	
		}
	}

	function set_browser_orientation() {
		if(_debug==true)console.log('20. set_browser_orientation class');	

		$('#content_printout').html(_browser_orientation+'<br>'+$(window).height()+' x '+$(window).width());
		$("body").removeClass('body_'+_browser_orientation_contra);
		$("body").addClass('body_'+_browser_orientation);	

		$("body").attr('body_orientation', _browser_orientation);
	}

	function detect_browser_orientation() {
		if(_debug==true)console.log('10. detect_browser_orientation');	


		if($(window).height() < $(window).width() && $(window).width() > _horizontal_brake && $(window).height() > _vertical_brake && !$('html').hasClass("no-csscolumns")) {
			_browser_orientation = 'horizontal';
			_browser_orientation_contra = 'vertical';
			_isotope_layout_mode = 'masonryHorizontal';
			_isotope_default_filter_items = ':not(.hide_horizontal)';
			_element_ch = 'height';
			_element_ch_short = "h";
			_element_ch_contra = "width";
			_element_ch_contra_short = "w";
		}
		else {
			_browser_orientation = 'vertical';
			_browser_orientation_contra = 'horizontal';
			_isotope_layout_mode = 'masonry';
			_isotope_default_filter_items = ':not(.hide_vertical)';
			_element_ch = 'width';
			_element_ch_short = "w";
			_element_ch_contra = "height";
			_element_ch_contra_short = "h";
		}	

		// console.log('Element:' + _element_ch);
		// console.log('Element Contra:' + _element_ch_contra);
	}
	
	function get_grid_element_content_size() {
		if(_debug==true)console.log('30. get_grid_element_content_size');
		
		// console.log('element:' + $(_grid_element).width() + ' vs window:' +$(window).width());

		if(_browser_orientation == 'vertical') {
			
			var _paddings = parseInt($(_grid_element).css('paddingLeft')) + parseInt($(_grid_element).css('paddingRight'));

			// _new_size = $(_grid_element).width(); // 21.04
			_new_size = $(window).width() - _paddings; 

			
			// console.log($(_grid_element).css('paddingLeft'));
			// console.log($(_grid_element).css('paddingRight'));
			// console.log('element:' + $(_grid_element).width() + ' vs window:' +$(window).width());
		} else {
			_new_size = $('body').innerHeight() - _scrollbar_width - ($(header).outerHeight() + $("#footer").outerHeight());
			// _new_size = $('body').innerHeight() - ($(header).outerHeight() );
			// console.log($(window).innerHeight());
			// console.log($(header).outerHeight());
			// console.log($("#footer").outerHeight());
		}
	}

	function reset_sizes() {
		if(_debug==true)console.log('45. reset_sizes');
		
		$(_element_of_interest).height('');
		$(_element_of_interest).width('');

		$(_grid_element).height('');
		$(_grid_element).width('');

		_current_size_contra = '';
	}	

	function template_exception() {
		//_template_exceptions
		if(_template_exceptions.length > 0) {
			for(var _i = 0; _i < _template_exceptions.length; _i++) {
				if($('body').hasClass(_template_exceptions[_i])) return true;
			}
		}
		return false;
	}

	// console.info(template_exception());

	function set_size_grid_parent_element() {
		if(_debug==true)console.log('40. set_size_grid_parent_element');
		reset_sizes();

		if(_browser_orientation == 'vertical' || template_exception()==true) {
		// if(_browser_orientation == 'vertical' || template_exception()!=false) {
			//$(_grid_element).width(_new_size);
			$(_element_of_interest).height('');
			_current_size_contra = $('body').height();

			$(_element_of_interest_article).height('auto');
		} else {
			$(_element_of_interest).height(_new_size + _scrollbar_width);
			$(_element_of_interest_article).height(_new_size * _element_of_interest_article_procent / 100);
			

			_current_size_contra = $('body').width();
			//$(_grid_element).width('');
		}


		$('#header-push').height($(header).outerHeight());
		$('#footer-push').height($("#footer").outerHeight());
	}

	function get_sizes_array(screen_orientation) {
		if(_debug==true)console.log('50. get_sizes_array');

		$.getJSON(_resources+'grid_structure_numbers.json', function(data) {
			
			// if(screen_orientation == 'vertical') {
			// 	_current_size = $(_grid_element).width();
			// } else {
			// 	_current_size = _new_size;
			// }

			_current_size = _new_size;
				
			if(_debug == true) console.log('grid_element.width() '+$(_grid_element).width());
			if(_debug == true) console.log('body:'+_current_size_contra);
			
			
			// $('#content_printout').append("<br><br>size TO json: "+_current_size);

			var _found = false;
			var _prev_key;
			$.each(data[screen_orientation], function(_key, _val) {

				// the size taken from json array is the ontop limiting one
				if(_key > _current_size && _found == false) {
					_current_size_json = _prev_key;
					_found = true;
				}

				_prev_key = _key;

			});	



			// variable number of elements pro size
			_num_elements = data[screen_orientation][_current_size_json];	
			_max_element_size_page = _num_elements;						
			_unit_size = Math.floor((_current_size/_num_elements));
			_unit_size_contra = _unit_size * _multiplicator;

			var _element_attr = "el_"+_element_ch;
			var _element_attr_contra = "el_"+_element_ch_contra;
			
			if(_debug == true) console.log('JSON elements num:'+_num_elements);
			if(_debug == true) console.log('_unit_size:'+_unit_size);
			
			// console.log("size OUT json: "+_current_size_json);
			// console.log("container:"+data[screen_orientation][_current_size_json]);
			
			// $('#content_printout').append("<br>unit size:"+_unit_size);

		})
		.done(function() { 
			// JSON is read and now elements are ready to be generated	
			process_all_elements(function(){
				compose_isotope();
			});	
		});
	}

	function process_all_elements(callback){
		if(_debug==true)console.log('55. process_all_elements');
		
		//if(_debug==true)console.log('contra max size:'+_current_size_contra);
		
		$(".element").each(function(g_key, g_val) {
			
			// setting an element size which is the main one for specific layout
			var _size_var = $(this).attr('data-proportional-'+_element_ch);
			var _size_contra_var = $(this).attr('data-proportional-'+_element_ch_contra);
				

			// testing if element fits the maximum defined number of grids for specific page size		
			if(_size_var >= _max_element_size_page || _size_var == 'max') {
				_element_ch_size = (Math.floor(_current_size/_unit_size))*_unit_size ;
				_variable_resize_contra = _size_var / _max_element_size_page;
				
				// // write the new element class into element
				// $(this).removeClass (function (index, css) {
				//     return (css.match (/\bh_\S+/g) || []).join(' ');
				// });
				// // $(this).addClass(_element_ch_contra_short +'_'+_max_element_size_page);
				// // $(this).addClass('h_'+_max_element_size_page);
				// if(_browser_orientation == 'vertical') $(this).addClass('h_'+_size_contra_var);
				// if(_browser_orientation == 'vertical') _element_ch_size = _current_size;
			}
			else _element_ch_size = _unit_size * _size_var + (_element_margin * _size_var * 2 );

			// specific case for the MAX size
			if(_size_contra_var == 'max') {
				_element_ch_contra_size = Math.floor((Math.floor(_current_size_contra/_unit_size_contra))*_unit_size_contra - _inner_element_padding * (Math.floor(_current_size_contra/_unit_size_contra)));
			}
			else {
				
				// If Max elements number fitting the current size is less than the element size	
				if(_size_var >= _max_element_size_page) {

					var _new_size_contra_var = Math.floor(_size_contra_var/_variable_resize_contra);
					if(_new_size_contra_var < 2) _size_contra_var = 2;
					else _size_contra_var = _new_size_contra_var;

					// write the new element class into element
					$(this).removeClass(function (index, css) {
					    return (css.match (/\bw_\S+/g) || []).join(' ');
					});
					// $(this).addClass(_element_ch_contra_short +'_'+ _size_contra_var);
					$(this).addClass('w_'+ _size_contra_var);
				} else {
					// We have to get the size back if it was changed due to resize/rotate
					$(this).removeClass(function (index, css) {
					    return (css.match (/\bw_\S+/g) || []).join(' ');
					});
					// $(this).addClass(_element_ch_contra_short +'_'+ _size_contra_var);
					$(this).addClass('w_'+ _size_contra_var);
				}
				_element_ch_contra_size = Math.floor(_unit_size_contra *_size_contra_var + (_element_margin * _size_contra_var * 2));
				
			}

			$(this).css(_element_ch, _element_ch_size);
			$(this).css(_element_ch_contra, _element_ch_contra_size);

			$(this).find('.inner_element').css(_element_ch, _element_ch_size - _inner_element_padding);
			$(this).find('.inner_element').css(_element_ch_contra, _element_ch_contra_size - _inner_element_padding);

			// console.log(_element_ch_size+' x '+_element_ch_contra_size);

			$(this).find('.inner_element').css('margin-right', _inner_element_padding);	
			$(this).find('.inner_element').css('margin-bottom', _inner_element_padding);	
			if(_browser_orientation == 'vertical') $(this).find('.inner_element').css('margin', _inner_element_padding/2);

		});	

		callback();	
	}

	function subnavigation_show() {
		setTimeout(
		  	function(){
		      	$('#subnavigation').animate({
			        	opacity: 1
			      	}, 400, 'swing', function() {
			        // Animation complete.
		    	});
			}, 100
		);
	}

	function subnavigation_recalculate_show() {
		
		// var _headline = $('#subnavigation h1').text().trim().split(' ');
		// $('#subnavigation h1').empty();
		
		// $.each(_headline, function( index, value ) {
		// 	$('#subnavigation h1').append('' + value + '<br>');
		// });

		var _min_font;
		var _max_font;

		if($('#subnavigation')) $('#subnavigation').textfill({
			debug: false, 
			innerTag: 'h1', 
			widthOnly: true, 
			minFontPixels: 18,
			maxFontPixels: 38,
			success: function() {
				subnavigation_show();
			},
			fail: function() {
				subnavigation_show();
			}
		});
		// $('.jtextfill').textfill({debug: true, maxFontPixels: size});

		// $('body').flowtype({
		// 	minimum : 500,
		// 	maximum : 1200
		// });

	}

	// Show the footer smoothly
	function footer_show() {
		setTimeout(
		  	function(){
		      	$('#footer').animate({
			        	opacity: 1
			      	}, 400, 'swing', function() {
			        // Animation complete.
		    	});
			}, 100
		);	
	}

	function compose_isotope(){
		
		if(_debug==true)console.log('60. compose_isotope');

		// var $container = $('#page_grid');

		if($('.element').length > 0) {
			
			imagesLoaded('#grid_content' ,function() {
				
				$container.isotope({
					itemSelector : '.element',
					filter: _isotope_default_filter_items,
					layoutMode: _isotope_layout_mode,
					// Old Isotope Version Start
						animationEngine : 'jQuery', // we support IE9 upwards, just use css
						animationOptions: {
						    duration: _animation_duration,
						    easing: 'linear',
						    queue: true
						},
					// Old Isotope Version End

					// New Isotope Version
					// hiddenStyle: {
					//     display: 'none',
					//     // opacity: 0
					// },
					// visibleStyle: {
					//     display: 'block',
					//     // opacity: 1
					// },
					// transitionDuration: 0,
					sortBy : 'original-order',
					columnWidth: 200,
					resizesContainer : true,
					onLayout: function($elems, instance){
						functions_after_isotope(function(){
							// added because of filtering and smooth appearance of items
							// $("article").css('opacity','0');
						});		
					}
				}); 

				if(_browser_orientation == 'horizontal') {
					$container.isotope({
						masonryHorizontal : {
							rowHeight: _unit_size
						}
					});
				} 
				else {
					$container.isotope({
						masonry: {columnWidth: _unit_size}
					});
				}

				// console.log('UNITESIZE TEST:'+_unit_size)


				// Moved out of functions_after_isotope Start

				destroy_custom_scrollbars(function(){
					activate_custom_scrollbars();
				});

				add_horizontal_mousewheel();

				subnavigation_recalculate_show();

				footer_show();

				
				// imagesLoaded('#grid_content' ,function() {
					center_isotope_grid_images();
					// alert('center');
					$(".inner_element").each(function(index, value){
			            setTimeout(
							function(){
								$(value).parent().animate({
								  opacity: 1
								}, 500, 'swing', function() {
								// Animation complete.

								console.log('Animiert');

								});
							}, 70*index
			            );
			        });
			});

	        // Moved out of functions_after_isotope END

		} else {
			
			// case of horizontal page without grid elements
			$('body').css('overflow-x', 'hidden');
			
			//$(".site-content article").show();
			$(".site-content").each(function(index, value){
             	
	        	// define open/close global Functionality
	        	if($('#open_up').length > 0) {
	        		_default_accordion = 'yes_accordion';
	        	} else {
	        		_default_accordion = 'no_accordion';
	        	}
				
				// add height to images inside of content
	        	$('#content img').each(function(){
	        		$(this).css('max-height', $('#content').height());
	        		// $(this).css('max-height', $(this).parent('article').height());
	        	});



				// In case Columnbreak doesnt work
				// (function($){

				var $wrapper = $('#page_grid');

				cleanColumnbreak();

				function forceColumnBreaks() {
					var $items = $wrapper.find('.columnbreak'),
					wrapperHeight = $wrapper.height(),
					resizeTimer;

					function adjustColumns() {
					$.each($items, function (i, item) {
					var $item = $(item);
					// Force item to start new column
					// $item.css('margin-top', wrapperHeight - item.offsetTop);
					$item.css('margin-top', wrapperHeight);
					// Push any items below to next column
					$item.css('margin-bottom', wrapperHeight - ($item.height() % wrapperHeight));

					// console.log('updated');
					});
					}

					//   $(window).resize(function(e) {
					// if (resizeTimer) clearTimeout(resizeTimer);
					// 	resizeTimer = setTimeout(function() {
					// 	adjustColumns();
					// }, 100);
					//   });

					adjustColumns();
				}

				function supportsColumnBreak(){
					// alert('supportsColumnBreak');
					// console.log(''+$(document.body).css('column-break-before'));

					// return $(document.body).css('column-break-before') !== undefined
					
					// console.log(''+$(document.body).css('break-before'));
					var _return;

					if($(document.body).css('column-break-before') == undefined && $(document.body).css('break-before') == undefined) _return = false;
					else _return = true;
					return _return;
				}

				// console.log(supportsColumnBreak());

				function cleanColumnbreak() {
					$('.columnbreak').attr('style','');
				}

				if (!supportsColumnBreak() && _browser_orientation == 'horizontal') { 
					forceColumnBreaks(); 
					// alert('forceColumnBreaks');
					//console.log('do some break');
				}
				// })(jQuery);

				// In case Columnbreak doesnt work END


	        	
	        	// recalculate horizontal columns
	        	
	        	if(((_browser_orientation != 'undefined' && _browser_orientation == 'horizontal') || (_window_changed == true)) && ( $('body').not('.page-template-vertical_subnavigation-php') || $('body').not('.page-template-vertical-php'))) {
	        	// if(_browser_orientation != 'undefined' && _browser_orientation == 'horizontal' && ( $('body').not('.page-template-vertical_subnavigation-php') || $('body').not('.page-template-vertical-php'))) {
	        		// $('.slim').attr('style',' ');

	        		clean_horizontal_columns();
	        		count_horizontal_columns();
	        	} else {
	        		$('.accordion_open').remove();
	        		$('.accordion_close').remove();
	        		$('.slim').attr('style',' ');
	        	}

	        	if(_window_changed == true && _browser_orientation == 'vertical') {
	        		$('.accordion_open').remove();
	        		$('.accordion_close').remove();
	        		$('.slim').attr('style',' ');

	        		//console.log('remove Style');
	        	}
	        	
             	setTimeout(
                  	function(){
                      	$(value).animate({
                          	opacity: 1
                      	}, 400, 'swing', function() {
                        	
                        	// Show all items withi one article
                      		$("article").each(function(index, value){
					            setTimeout(
									function(){

										$(value).animate({
										  opacity: 1
										}, 500, 'swing', function() {
										// Animation complete.

										});
									}, 30*index
					            );
					        });



                        	// Animation complete.
                        	// add scroll as it is hidden in css
                        	if(_browser_orientation == 'horizontal') $container.css('overflow-x', 'scroll');
                        	else $container.css('overflow-x', 'hidden');
        					
        					if($('body').hasClass('body_horizontal') && template_exception()!=true) {
	        					setTimeout(function(){
	        						$('#page_grid').bind('mousewheel', function(e, delta) {
										this.scrollLeft -= (delta * _scroll_step);
										e.preventDefault();
									});
	        					}, 250);
        					} else {
        						$('#page_grid').unbind('mousewheel');
        					}
                      	});
                  	}, 70*index
              	);

             	subnavigation_recalculate_show();

             	footer_show();
    //           	setTimeout(
				//   	function(){
				//       	$('#subnavigation').animate({
				// 	        	opacity: 1
				// 	      	}, 400, 'swing', function() {
				// 	        // Animation complete.
				//     	});
				// 	}, 50
				// );
        	});

        	

		}

	}

	// Isotope Filter
	$('#filters a').click(function(){
		// $('article').animate({opacity: 0}, 1);
		
		var selector = $(this).attr('data-filter');
		$('#filters a').removeClass('active-filter');
		$(this).addClass('active-filter');
		
		$container.isotope({ filter: selector });

		// New Isotope Method
		// $container.isotope( 'on', 'layoutComplete',
		//  	function( isoInstance, laidOutItems ) {
		// 	   //  $("article").each(function(index, value){
		//     //         setTimeout(
		// 				// function(){
		// 				// 	$(value).parent().animate({
		// 				// 	  opacity: 1
		// 				// 	}, 500, 'swing', function() {
		// 				// 	// Animation complete.

		// 				// 	});
		// 				// }, 70*index
		//     //         );
		//     //     });
		// 	    // console.log( 'Isotope layout completed on ' + laidOutItems.length + ' items' );
		// 	}
		//  	// function $('article').animate({opacity: 1}, 10);
		// );

		
		return false;
	});

	// all article elements on the horizontal page
	var _hor_elements = $('.body_horizontal').find('#content').find('article');

	// live bind the open_article_accordion() funciton to the open element
	$('#content').on("click", '.accordion_open', function(){
		open_article_accordion($(this));
		$(this).removeClass('accordion_open');
		$(this).addClass('accordion_close');
		// $(this).text('accordion_close');
		return false;
	});

	// live bind the close_article_accordion() function to the close element
	$('#content').on("click", '.accordion_close', function(){
		close_article_accordion($(this));
		$(this).removeClass('accordion_close');
		$(this).addClass('accordion_open');
		// $(this).text('accordion_open');
		return false;
	});


	function open_article_accordion(this_var) {
		var _this_column = this_var.parent();
		var _elements_after = _this_column.nextAll();
		var _add_left = parseInt(_this_column.attr('data-width-real')) - parseInt(_this_column.attr('data-column-width'));

		for (var i = 0, len = _elements_after.length; i < len; i++) { 
			_article_after = $(_elements_after[i]);
			_article_after.animate({
				left: "+="+_add_left
			}, _animation_duration, function() {
				// Show Content of article
				_this_column.css('overflow', 'visible');
			});
		}
	}

	function close_article_accordion(this_var) {
		var _this_column = this_var.parent();
		var _elements_after = _this_column.nextAll();
		var _add_left = parseInt(_this_column.attr('data-width-real')) - parseInt(_this_column.attr('data-column-width'));
		
		// Hide Content of article
		_this_column.css('overflow', 'hidden');
		
		for (var i = 0, len = _elements_after.length; i < len; i++) { 
			_article_after = $(_elements_after[i]);
			_article_after.animate({
				left: "-="+_add_left
			}, _animation_duration, function() {
				
				
			});
		}
	}

	function clean_horizontal_columns() {
		// console.log(_hor_elements.length);
		if(_hor_elements.length > 1) {
			_hor_elements.each(function(i, value){
			  	if( $(this).children().length < 1 ) {
					$(this).remove();
					// $(this).append('<div class="IMEPMTY"></div>');
					// console.log('element');
				}
			});
		}
		// console.log(_hor_elements.length);
	}

	/*
    * Horizontal Columns recalculate
    */
	function count_horizontal_columns() {
		// all article elements on the horizontal page
		// var _hor_elements = $('.body_horizontal').find('#content').find('article');

		// $('.body_horizontal').find('#content').find('article');

		

		var previous;
		var _pos_set = 0;
		var _count = _hor_elements.length - 1;
		// console.log(_hor_elements.length);
		if(_hor_elements.length > 1)

			
			// console.log(_hor_elements.length);
			// _hor_elements = $('.body_horizontal').find('#content').find('article');

			// console.log('Resized:'+_window_changed);

			_hor_elements.each(function(i, value){
			  		
					if($(this).attr('data-inline-style')) $(this).attr('style', $(this).attr('data-inline-style'));
					if($(this).attr('data-column-width')) {
						if( _browser_orientation == 'vertical' ) $(this).width(_new_size);
						else $(this).width($(this).attr('data-column-width'));
					}
						

					// console.log('New Size:'+_new_size);
					// console.log('Data column width:'+$(this).attr('data-column-width'));	

			  		//  start with the second one
				    if( $(this).find('.last_article_element').length < 1 ) $(this).append('<div class="last_article_element"></div>');
				    
				    	
					// find last p of the previous one
					if(_secondary_debug == true) {
						$(this).find('.last_article_element').attr('style','border-color: 1px solid red;');
					}

					var last_element = $(this).find('.last_article_element');  

					// get left position
					var last_position = last_element.position().left;

					// get width
					var last_width = last_element.width();

					// get column-gap || -moz-column-gap || -webkit-column-gap
						// it is set manually with style backup

					// add all 3 above together
					var new_position = _horizontal_article_gap + last_position + last_width;

					// debug
					if(_secondary_debug == true) {
						console.log('last Pos___'+_pos_set+'___');
						console.log('P___'+last_position+'___');
						console.log('W___'+last_width+'___');
						console.log('NewPosition___'+new_position+'___');
						console.log('________');
					}



					// set the left to this number and position is absolute, top is 0px
					var _real_width = last_position + last_width;
					$(this).attr('data-width-real', _real_width);

					// prepend and accordion call button
					if(_real_width > $(this).attr('data-column-width')) {
					  if( $(this).attr('data-accordion') == 'yes_accordion' || (_default_accordion == 'yes_accordion' && $(this).attr('data-accordion') == '')) {
					  	

					  	if($(this).find('.accordion_close').length > 0) {
					  		$(this).find('.accordion_close').remove();
					  	}

					  	if($(this).find('.accordion_close').length < 1 && $(this).find('.accordion_open').length < 1) {
					  		
					  			$(this).prepend('<div class="accordion_open"></div>');
					  			$(this).attr('data-accordion','yes_accordion');

					  			// hide elements overflow
					  			$(this).css('overflow','hidden');
					  		
					  	}	
					  }
					}

					// accumulate positions
					// _pos_set += new_position;

					if (!--_count) replace_horizontal_columns();
			}); 
		
	}

	function replace_horizontal_columns() {
		var _pos = 0;
		// var _hor_elements = $('.body_horizontal #content article');
		var _hor_elements = $('#content article');
		var previous;

		// console.log(_hor_elements.length);
		var ipr = 0;
		_hor_elements.each(function(i, value){
			
			// alert(i);
			// if($(this).hasClass('divider')) console.log('Divider Found');
			// console.log($(this).attr('class'));

			if(previous && ipr > 0) {
				
				if(previous.attr('data-accordion') != 'yes_accordion' ) {
					var _start = parseInt(previous.attr('data-width-real')) + _horizontal_article_gap;	
				} else {
					var _start = parseInt(previous.attr('data-column-width')) + _horizontal_article_gap;
				}

				var _new_pos = _start + _pos;

				$(this).css('left', _new_pos +'px');
				// $(this).css('border','1px solid red');
				// 	// accumulate positions
				_pos += _start;
				

			}
			ipr++;
		    previous = $(this);

		});
		// console.log(ipr);
	}

	/*
	* Center Images inside of Grid Elements
	*/
	function center_isotope_grid_images() {
		// $('.image_overlay img').each(function(){
		// 	var $this = $(this);
		// 	var cont_ratio = $this.parent().width() / $this.parent().height();
		// 	var img_ratio = $this.get(0).width / $this.get(0).height;

		// 	if(cont_ratio <= img_ratio){
		// 		$this.css({ 'width' : 'auto', 'height' : '100%', 'top' : 0 }).css({ 'left' : ~(($this.width()-$this.parent().width())/2)+1 });
		// 		$this.addClass('project-img-visible');
		// 	}else{
		// 		$this.css({ 'width' : '100%', 'height' : 'auto', 'left' : 0 }).css({ 'top' : ~(($this.height()-$this.parent().height())/2)+1 });
		// 		$this.addClass('project-img-visible');
		// 	}
		// });


		var image_overlay = $(".image_overlay").find('img');
		
		for(var i=0; i < image_overlay.length; i++){
			//get height and width (unitless) and divide by 2
			// console.log($(this).width());


			// $("img").one('load', function() {
			// 	// do stuff
			// }).each(function() {
			// 	if(this.complete) $(this).load();
			// });

			_img = $(image_overlay[i]);


			var _values = $('<div class="console_values" style="position: absolute;"></div>');

			var hWide = (_img.width())/2; //half the image's width
			var hTall = (_img.height())/2; //half the image's height, etc.

			_values.append('hWide: '+hWide+'<br>');
			_values.append('hTall: '+hTall+'<br>');

			var ratio_parent = _img.parent().width() / _img.parent().height();	
			var ratio_image = _img.width() / _img.height();

			var vertical_ratio = _img.parent().height() / _img.height();
			var horizontal_ratio = _img.parent().width() / _img.width();

			// console.log(vertical_ratio);
			// console.log(horizontal_ratio);

			// attach negative and pixel for CSS rule
			hWide = '-' + hWide * vertical_ratio + 'px';
			hTall = '-' + hTall * horizontal_ratio + 'px';

			// console.log(hWide);
			// console.log(hTall);

			_values.append('vertical_ratio: '+vertical_ratio+'<br>');
			_values.append('horizontal_ratio: '+horizontal_ratio+'<br>');
			_values.append('hWide: '+hWide+'<br>');
			_values.append('hTall: '+hTall+'<br>');

			if(ratio_parent == ratio_image) {
				_img.removeClass().addClass("image_overlay_fit_horizontally").addClass("exact_fit").css({
					"margin" : "0px"
				});
			} else {
				if( ratio_parent < ratio_image ) {
				
					if(_img.parent().hasClass('contain')) {
						_img.removeClass().addClass("image_overlay_fit_horizontally").css({
						"margin" : "0px",
						"margin-top" : hTall
						});

					} else {
						_img.removeClass().addClass("image_overlay_stretch_vertically_fix").css({
						"margin" : "0px",
						"margin-left" : hWide
						});
					}
					
				} else {
					
					if(_img.parent().hasClass('contain')) {
						_img.removeClass().addClass("image_overlay_fit_vertically").css({
						"margin" : "0px",
						"margin-left" : hWide
						});
					} else {
						_img.removeClass().addClass("image_overlay_stretch_horizontally_fix").css({
						"margin" : "0px",
						"margin-top" : hTall
						});
					}

				}
			}
			
			
			
				
			// _img.parents('article').append(_values);
		  	
		  
		}

		// $(".image_overlay_horizontal img").each(function(){
		//   //get height and width (unitless) and divide by 2
		//   console.log($(this).width());

		//   var hWide = ($(this).width())/2; //half the image's width
		//   var hTall = ($(this).height())/2; //half the image's height, etc.

		//   var ratio = $(this).parent().height() / $(this).height();	

		//   // attach negative and pixel for CSS rule
		//   hWide = '-' + hWide * ratio + 'px';
		//   hTall = '-' + hTall + 'px';

		//   $(this).addClass("js-fix").css({
		//     "margin-left" : hWide
		//     // "margin-top" : hTall
		//   });
		// });

		// $(".image_overlay_vertical img").each(function(){
		//   //get height and width (unitless) and divide by 2
		//   var hWide = ($(this).width())/2; //half the image's width
		//   var hTall = ($(this).height())/2; //half the image's height, etc.

		//   // attach negative and pixel for CSS rule
		//   hWide = '-' + hWide + 'px';
		//   hTall = '-' + hTall + 'px';

		//   $(this).addClass("js-fix").css({
		//     // "margin-left" : hWide,
		//     "margin-top" : hTall
		//   });
		// });
	}


	/*
	* Functions after Isotope is Ready
	*/

	function functions_after_isotope(callback) {
		if(_debug==true) console.log('70. functions_after_isotope');

		// add_horizontal_mousewheel();

		// subnavigation_recalculate_show();

		// run slider script
    	// $('.bxslider').bxSlider({
     //    	infiniteLoop: false,
     //    	hideControlOnEnd: true
     //  	});

		// $(".inner_element").each(function(index, value){
  //            setTimeout(
  //                 function(){
  //                     center_isotope_grid_images();

  //                     $(value).animate({
  //                         opacity: 1
  //                     }, 800, 'swing', function() {
  //                       // Animation complete.


                        
  //                     });
  //                 }, 70*index
  //             );
  //       });


		callback();

		setTimeout(function(){
			//set_header_footer_width();
			
		}, _animation_duration);
	}

	// 6. activate custom scroller but destroy before
    // $('body').queue("custom", function() {
    function destroy_custom_scrollbars(callback){
        if(_debug==true)console.log('80. destroy custom scroller');	

        $(".inner_content._scroll_it").mCustomScrollbar("destroy");
        $("#subnavigation").mCustomScrollbar("destroy");

        callback();
        //compose_isotope();
        // var self = this;
        // _i_time = setTimeout(function() {
        //     $(self).dequeue("custom");
        // }, _delay);
    };

    // $('body').queue("custom", function() {
    function activate_custom_scrollbars(){
        if(_debug==true) console.log('85. activate custom scroller');	

        $(".inner_content._scroll_it").mCustomScrollbar({
				mouseWheelPixels: _scroll_step*2,
				scrollInertia: 300,
				contentTouchScroll: true
        });
        
		$("#subnavigation").mCustomScrollbar({
			mouseWheelPixels: _left_bar_scroll_step*2,
			scrollInertia: 300
	  	});

        //compose_isotope();
        // var self = this;
        // _i_time = setTimeout(function() {
        //     $(self).dequeue("custom");
        // }, _delay + 500);
    };

    function get_scrollbar_size(){
    	// Create the measurement node
		var scrollDiv = document.createElement("div");
		scrollDiv.className = "scrollbar-measure";
		document.body.appendChild(scrollDiv);

		// Get the scrollbar width
		var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
		// console.warn(scrollbarWidth); // Mac:  15

		// Delete the DIV 
		document.body.removeChild(scrollDiv);

		return scrollbarWidth;
    }
		

	function do_on_resize() {

		// $('.isotope-item').css('visibility', 'hidden');
		// $('.inner_element').css('opacity', 0);
		
		$('#footer').animate({opacity:0}, 10);
		$('#subnavigation').animate({opacity: 0}, 10);
		$('article').animate({opacity: 0}, 10);

		// $('.inner_element').animate({opacity:0}, 10);
		//$('.site-content article').hide();
		// $('#content-single').animate({opacity:0}, 10);

		var _delay = 50;
		// 0.  hide the page - show the loader

		// 0.5 define whether the site is in H or V modus
		$('body').queue("custom", function() {
            detect_browser_orientation();
            
            var self = this;
            _i_time = setTimeout(function() {
                $(self).dequeue("custom");
            }, _delay);
        });

        $('body').queue("custom", function() {
            set_browser_orientation();
            
            var self = this;
            _i_time = setTimeout(function() {
                $(self).dequeue("custom");
            }, _delay);
        });
    
		// 1.  get the size of the grid content element
		$('body').queue("custom", function() {
            get_grid_element_content_size();
            
            var self = this;
            _i_time = setTimeout(function() {
                $(self).dequeue("custom");
            }, _delay);
        });

		// 1.1 set the size for the grid content element
		$('body').queue("custom", function() {
            set_size_grid_parent_element();
            
            var self = this;
            _i_time = setTimeout(function() {
                $(self).dequeue("custom");
            }, _delay);
        });
			
		// 3.  get corresponding values out of json file		
		$('body').queue("custom", function() {
            get_sizes_array(_browser_orientation);
            
            var self = this;
            _i_time = setTimeout(function() {
                $(self).dequeue("custom");
            }, _delay);
        });

		// 5.  update isotope
		// $('body').queue("custom", function() {
  //           setTimeout(function(){compose_isotope()}, 500);
  //           //compose_isotope();
  //           var self = this;
  //           _i_time = setTimeout(function() {
  //               $(self).dequeue("custom");
  //           }, _delay);
  //       });



        // 6. activate custom scroller but destroy before
        // $('body').queue("custom", function() {
        //     if(_debug==true)console.log('80. destroy custom scroller');	

        //     $(".inner_content._scroll_it").mCustomScrollbar("destroy");
        //     $("#subnavigation").mCustomScrollbar("destroy");

        //     callback();
        //     //compose_isotope();
        //     // var self = this;
        //     // _i_time = setTimeout(function() {
        //     //     $(self).dequeue("custom");
        //     // }, _delay);
        // });

    //     $('body').queue("custom", function() {
    //         if(_debug==true)console.log('85. activate custom scroller');	

    //         $(".inner_content._scroll_it").mCustomScrollbar({
				// 	mouseWheelPixels: _scroll_step*2,
				// 	scrollInertia: 300
    //         });
            
    // 		$("#subnavigation").mCustomScrollbar({
				// mouseWheelPixels: _left_bar_scroll_step*2,
				// scrollInertia: 300
		  // 	});

    //         //compose_isotope();
    //         var self = this;
    //         _i_time = setTimeout(function() {
    //             $(self).dequeue("custom");
    //         }, _delay + 500);
    //     });
		
						
		$("body").dequeue("custom");
		// 6.  hide the loader - show the page
	}

	function generate_random_elements() {
		//console.log(_max_element_size);
		var _random_num_elements = Math.floor(Math.random() * 30) + 10;	
		
		var items = new Array(2,3,4,6,'max');
		var items2 = new Array('max',2,3,4,2,8,6);
		var items_scroll_not = new Array('_scroll_it', '');
		//var items2 = new Array(2,2);
		var items_image = new Array('600x400.png','400x225.png','225x400.png','200x400.png');
		var items_colors = new Array(1,2,3);


		// var items = new Array(2,2,2);
		// var items2 = new Array(2,2,2);
		
		var _random_width;
		var _random_height;
		
		for(var i=0; i<_random_num_elements; i++) {
										
			// _random_element.attr('data-proportional-width', Math.floor(Math.random() * 4) + 2);
			// _random_element.attr('data-proportional-height', Math.floor(Math.random() * 4) + 2);
			_random_width = items[Math.floor(Math.random()*items.length)];
			_random_height = items2[Math.floor(Math.random()*items2.length)];

			// var _h2 = $.lorem({ type: 'words',amount: Math.floor((Math.random() * 8) + 1),ptags:false});
			// var _descr = $.lorem({ type: 'words',amount: Math.floor((Math.random() * 25) + 5),ptags:false});
			// var _paragraph = $.lorem({ type: 'words',amount: Math.floor((Math.random() * 50) + 20),ptags:false});
			
			var _width_name = _random_width;
			if(_random_width != 'max') _width_name=_random_width; 

			var _height_name = _random_height;
			if(_random_height != 'max') _height_name=_random_height;
			
			// random color selection	
				var _style_number = items_colors[Math.floor(Math.random()*items_colors.length)];

			// add optional scroll
				var _scroll_it = items_scroll_not[Math.floor(Math.random()*items_scroll_not.length)];
			
			// for normal use
				// var _random_element = $('<article class="element w_'+_width_name+' h_'+_height_name+'"><div class="inner_element"><div class="inner_content"></div><p class="number">w:'+_random_width+' x h:'+_random_height+' ('+i+')</p></div></article>');
			
			// for use with image on top 
				var _random_element = $('<article class="element w_'+_width_name+' h_'+_height_name+'"><div class="inner_element border grid-el_border-color-'+_style_number+' grid-el_bg-color-'+_style_number+' grid-el_text-color-'+_style_number+'"><div class="inner_content image_overlay_top_parent '+_scroll_it+'"></div><p class="number">w:'+_random_width+' x h:'+_random_height+' ('+i+')</p></div></article>');
			
			// for use with image at the left 
				// var _random_element = $('<article class="element w_'+_width_name+' h_'+_height_name+'"><div class="inner_element"><div class="inner_content image_overlay_left_parent '+_scroll_it+'"></div><p class="number">w:'+_random_width+' x h:'+_random_height+' ('+i+')</p></div></article>');


			// add some image
				// _random_element.find('.inner_element').prepend('<div class="image_overlay" style="background-image: url(img/'+items_image[Math.floor(Math.random()*items_image.length)]+');"></div>');

			// add some image on top
				_random_element.find('.inner_element').prepend('<div class="image_overlay image_overlay_top" style="background-image: url(img/'+items_image[Math.floor(Math.random()*items_image.length)]+');"></div>');

			// add some image to the left
				// _random_element.find('.inner_element').prepend('<div class="image_overlay image_overlay_left" style="background-image: url(img/'+items_image[Math.floor(Math.random()*items_image.length)]+');"></div>');

			

			// add a header
			_random_element.find('.inner_content').append('<h2 class="article_title"></h2>');
			_random_element.find('.article_title').lorem({ type: 'words',amount: Math.floor((Math.random() * 8) + 3), ptags:false});

			// add a short description	
			_random_element.find('.inner_content').append('<div class="article_short"></div>');
			_random_element.find('.article_short').lorem({ type: 'words',amount: Math.floor((Math.random() * 25) + 5), ptags:false});
			
			// add a text bit
			_random_element.find('.inner_content').append('<div class="article_full"></div>');
			_random_element.find('.article_full').lorem({ type: 'words',amount: Math.floor((Math.random() * 50) + 20), ptags:false});

			
			
			_random_element.attr('data-proportional-width', _random_width);
			_random_element.attr('data-proportional-height', _random_height);
			$(_grid_element).append(_random_element);	
		}
	}

	// generate_random_elements();
	

	do_on_resize();

});