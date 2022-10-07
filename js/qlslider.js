/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2017 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

(function($)
{
	$.fn.qlslider=function( method )
	{
		var methods =
		{
			init :	function(options){return this.each(function(){ initialize(this, options);});},
			infinite: false,
			destroy : function(options){return this.each(function(){ destroySlider(this,options);});}
		};

		var defaults =
		{
			margin							: 0,
			resizeItem						: null,
			responsive						: null,
			timerAnimSlide					: 300,
			itemsMove						: 1,
			activate						: function() {}
		};

		var settings;
		var objSlider;
		var setNext;
		var setPrev;
		var content;
		var container;
		var sliderItems;
		var stateAnimation;

		if(methods[method])
		{
			return methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		}
		else if (typeof method==='object'||!method)
		{
			return methods.init.apply(this,arguments);
		}
		else
		{
			$.error( 'Method ' +  method + ' does not exist on jQuery.plugin' );
		}
        /**
         * method to initialize the slider
         * @param $this
         * @param options
         */
		function initialize($this, options)
		{
			objSlider=$($this);
			settings=$.extend(defaults, options);
			initializeSettings($this);
            mouseEvents();
		}

        /**
         * method to initialize the slider settinge
         * @param $this
         */
		function initializeSettings($this)
		{
			content=$('.content',objSlider);
			container=$('.qlsliderContent',objSlider);
			sliderItems=$('.items',objSlider);
			setPrev=$('.prev',objSlider);
            setNext=$('.next',objSlider);

			var widthAllItems=0;
			var marginSet;
			$('.item',content).each(function(index, element)
            {
				widthAllItems += $(element).outerWidth(true);
				marginSet=getSize($(element),'marginRight');
			});

            if($('.item',content).length <= 1)
			{
				$('.navigation',objSlider).hide();
			}
			else
			{
				if(settings.infinite)
				{
					moebius();
				}
                mouseEvents();
			}

			if(settings.responsive)
			{
				resizeResponsive();
			}

			resizeContent();

			if(content.width() < (container.width()+settings.margin+1))
			{
				if(!settings.infinite)
				{
					$('.navigation',objSlider).hide();
				}
			}
		}

        /**
         * method to activate settings
         */
        function activate ()
        {
			settings.activate.call(this, {});
		}

        /**
         * method to resize slider
         */
		function resizeResponsive ()
		{
			$(window).resize(resizeSlider);
            resizeSlider();
		}

		function resizeSlider ()
		{
			if(settings.responsive.minWidth)
			{
				var _minWidth=settings.responsive.minWidth;

				container.hide();
				var _w=objSlider.width();
				container.show();

				if(getSize(content,'marginLeft') != 0)
				{
					content.css('marginLeft',0);
				}
				container.scrollLeft(0);
				if($(window).width() <= _minWidth)
				{
					var marginSet=0;
					container.width(_w);

					if(settings.resizeItem)
                    {
						var _wResizeItem=settings.resizeItem.width;
						var _wResized=(_wResizeItem * _w)/100;

						$('.item',content).each(function(index, element)
                        {
							$(element).width(_wResized);
						});
					}
					resizeContent ();


				}
				else
				{
					container.removeAttr('style');
					if(settings.resizeItem) {
						if($('.item',content).attr('style'))
						{
							$('.item',content).each(function(index, element) {
								$(element).removeAttr('style');
							});
						}
					}
					resizeContent ();
				}

				$('.item',content).each(function(index, element) {
					if(getSize($(element),'marginRight')>0)
					{
						marginSet=getSize($(element),'marginRight');
					}
				});

				if(content.width() > (container.width()+settings.margin+1))
				{
					$('.navigation',objSlider).show();
				}
				else
				{
					$('.navigation',objSlider).hide();
				}
			}
		}

        /**
         * method to append elements
         */
		function moebius()
		{
			$('.item',content).each(function(index, element)
			{
				var _item=$(element);
				cloneElement({from:_item, to:sliderItems});
			});
		}

        /**
         * method to clone element
         * @param objElement
         */
        function cloneElement (objElement)
        {
            var fromElement=objElement.from ;
            var toElement=objElement.to;
            var element=fromElement.clone();
            element.appendTo(toElement);
        }

        /**
         * method to resize content
         */
		function resizeContent ()
		{
			var widthItem;
			var sizeItems=0;
			content.width(999999);
			var marginSet;
			$('.item',content).each(function(index, element)
			{
				widthItem=$(element).outerWidth(true);
            	sizeItems += widthItem;
			});
			content.width(sizeItems+1);

		}

        /**
         * method to define mouse events
         */
		function mouseEvents ()
		{

			setNext.click(function()
			{
				moveSlide('right');
				return false;
			});

			setPrev.click(function()
			{
				moveSlide('left');
				return false;
			});

		}

        /**
         * method to do what this slider is actually for: sliding
         * @param direction
         */
		function moveSlide (direction)
		{
			var numMoveX;
			var _posAtual=getSize(content,'marginLeft');
			var _maxPosMove=content.width() -  container.width();

			var widthItem=$('.item',content).outerWidth(true);
			if( direction == 'right' )
			{
				numMoveX=-(widthItem - _posAtual);

				if((Math.abs(numMoveX) <= Math.abs(_maxPosMove)) && !stateAnimation)
				{
					stateAnimation=true;
					content.stop(true,true).animate({marginLeft:numMoveX},settings.timerAnimSlide,function(){
						stateAnimation=false;
						if(settings.infinite)
						{

							if(Math.abs(numMoveX) >=Math.floor(content.width()/2))
							{
								content.stop(true,true).animate({marginLeft:0},0);
							}
						}
					});
				}
			}
			else
			{
				console.log();
				if(Math.abs(Math.round(_posAtual)) == 0 && settings.infinite)
				{
					numMoveX=-(content.width()/2);
					content.css({marginLeft:numMoveX});
					_posAtual=numMoveX;
				}

				numMoveX=(_posAtual + widthItem);

				if((numMoveX <= 0) &&  !stateAnimation)
				{
					stateAnimation=true;
					content.stop(true,true).animate({marginLeft:numMoveX},settings.timerAnimSlide,function()
                    {
						stateAnimation=false;
					});
				}
			}
		}

        /**
         * method to destroy slider
         * @param $obj
         * @param $property
         */
		function destroySlider ($obj,$property)
        {
			$(window).unbind('resize',resizeSlider);
		}

        /**
         * method to get size of an Element
         * @param objObject
         * @param strCss
         * @returns {Number}
         */
		function getSize(objObject,strCss)
		{
			if(objObject.size()>0)
			{
				var regex=new RegExp('[a-z][A-Z]','g');
				return parseFloat(objObject.css(strCss).replace(regex,''));
			}
		}
	};
})(jQuery);