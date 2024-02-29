/* @license GNU-GPL-2.0-or-later https://www.drupal.org/licensing/faq */
(function($,Drupal,drupalSettings,once){'use strict';Drupal.swiper_formatter=Drupal.swiper_formatter||{};Drupal.behaviors.nkToolsSwiper={attach:function(context,settings){var self=this;var swiper_formatter_settings=settings.swiper_formatter||null;if(swiper_formatter_settings&&$.type(swiper_formatter_settings.swipers)!=='undefined'){var swipers={};once('swiperFormaterInit','.swiper-container',context).forEach(function(swiperContainer){if(swiperContainer.id){var swiperSettings=swiper_formatter_settings.swipers[swiperContainer.id];if(typeof swiperSettings==='object'&&typeof Swiper!=='undefined'){if(swiperSettings.pagination.type==='progressbar')$(swiperContainer).addClass('progressbar');swipers[swiperContainer.id]=new Swiper('#'+swiperContainer.id,swiperSettings);if(swipers[swiperContainer.id])self.registerTriggers(swipers[swiperContainer.id],$(context).find('.swiper-trigger'),context,settings);}}});}},showHidden:function(swiper){if(swiper&&swiper.slides.length>0)$.each(swiper.slides,function(index,slide){if(index==swiper.activeIndex)$(slide).find('.hidden').each(function(d,hidden){$(hidden).removeClass('hidden');});});},registerTriggers:function(swiper,triggers){triggers.each(function(trigger){$(this).on('click',function(e){if($(this).parent().siblings().length)$(this).parent().siblings().each(function(i,sibling){$(sibling).find('.swiper-trigger').removeClass('active');});$(e.currentTarget).addClass('active');var index=$(this).attr('data-index')?parseInt($(this).attr('data-index'))-1:0;swiper.slideTo(index);return false;});});}};})(jQuery,Drupal,drupalSettings,once);;
