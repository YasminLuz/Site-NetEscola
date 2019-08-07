(function ($, Drupal) {
  if (typeof(Drupal.AjaxCommands) != 'undefined') {
    Drupal.AjaxCommands.prototype.progressive_cms_blog_timeline = function(ajax, response, status) {
      var $article = $('#' + response.selector).closest('.post');
      $article.find('.livicon').attr('data-n', response.data.livicon).updateLivicon({name: response.data.livicon});
      $article.find('.timeline-content, .timeline-icon').removeClassPrefix('bg').removeClassPrefix('border');
      $article.find('.timeline-icon').addClass('bg-' + response.data.color);
      var bg = response.data.tranparent_bg ? 'border border-' : 'bg bg-';
      $article.find('.timeline-content').addClass(bg + response.data.color);
      if(response.data.title) {
        $article.find('.entry-title').show();
      }
      else {
        $article.find('.entry-title').hide();
      }
      if(response.data.no_padding) {
        $article.addClass('no-padding');
      }
      else {
        $article.removeClass('no-padding');
      }
    }
  }
})(jQuery, Drupal);

(function() {
  var $ = jQuery;
  $.fn.removeClassPrefix = function(prefix) {
      this.each(function(i, el) {
          var classes = el.className.split(" ").filter(function(c) {
              return c.lastIndexOf(prefix, 0) !== 0;
          });
          el.className = classes.join(" ");
      });
      return this;
  };

  Drupal.behaviors.active_menu_expand = {
    attach: function (context, settings) {
      setTimeout(function(){
        $('ul.menu .active').parent('.sub').show();
      }, 1000);
    }
  };

  Drupal.behaviors.rotate_blocks = {
    attach: function (context, settings) {
      if(!navigator.userAgent.match(/iPad|iPhone|Android/i)) {
        $('.product, .employee', context).hover(function(event) {
          event.preventDefault();
          $(this).addClass('hover');
        }, function(event) {
          event.preventDefault();
          $(this).removeClass('hover');
        });
      }
    }
  };


  Drupal.behaviors.href_click = {
    attach: function (context, settings) {
       $('a[href="#"]').click(function() {
        return false;
       });
    }
  };

  Drupal.behaviors.attachSelectBox = {
    attach: function (context, settings) {
      if(typeof($.fn.selectBox) !== 'undefined') {
       $('select:not(".without-styles")').selectBox();
      }
    }
  };

  Drupal.behaviors.removefromcart = {
    attach: function (context, settings) {
      // Remove from block Cart
      $('.cart-header .product-remove:not(.ajax-processed)').once('ajax').click(function() {
        $(this).parents('li').animate({'opacity': 0, 'height' : 0}, 700, function() {
          $(this).remove();
          $('.cart-count').text(parseInt($('.cart-header .cart-count').text()) - 1);
        });
        $(this).closest('li').find('input').click();
        return false;
      });
      // Click on the button from styled icon on the Cart Page
      $('.button-click:not(.click-processed)').once('click').click(function() {
        $(this).prev('input').click();
        return false;
      });
      // Click on the Update Shopping Cart link
      $('.update-cart-link').once('click').click(function() {
        $(this).closest('form').find('.update-cart-button').click();
        return false;
      });
    }
  };

  Drupal.behaviors.removefromcompare = {
    attach: function (context, settings) {
      // Remove from block Cart
      $('#compare-table .product-remove', context).click(function(event) {
        $('#compare-table tr .data-index-' + $(this).attr('data-index')).animate({'opacity': 0, 'height' : 0}, 700, function() {
          $(this).remove();
        });
        let flag_counter = $('.compare-header .flag-counter');
        flag_counter.text(parseInt(flag_counter.text()) - 1);
        $(this).closest('td').find('.compare-flag a')[0].click();
        return false;
      });
    }
  };

  Drupal.behaviors.contextual_form = {
    attach: function (context, settings) {
      // Stop the handler of contextual links to close the popup
      $('.contextual-form:not(.contextual-form-processed)', context).once('contextual-form').click(function(e) {
        e.stopPropagation();
      });
    }
  };

  Drupal.behaviors.livicons = {
    attach: function (context, settings) {
      if(typeof($.fn.updateLivicon) !== 'undefined') {
        $('.livicon:not(.livicon-processed)', context).once('livicon').updateLivicon();
      }
    }
  };

  /**
   *
   * @param href_path_str
   * @returns {*}
   */
  function set_flag_action_class(href_path_str) {
    if (href_path_str.indexOf("unflag") != -1) {
      return ' unflag-action';
    }
    else {
      return ' flag-action';
    }
  }

  Drupal.behaviors.js_styles = {
    attach: function (context, settings) {
      const compare_icon = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><path fill="#1e1e1e" d="M16,3.063L13,0v2H1C0.447,2,0,2.447,0,3s0.447,1,1,1h12v2L16,3.063z"></path><path fill="#1e1e1e" d="M16,13.063L13,10v2H1c-0.553,0-1,0.447-1,1s0.447,1,1,1h12v2L16,13.063z"></path><path fill="#1e1e1e" d="M15,7H3V5L0,7.938L3,11V9h12c0.553,0,1-0.447,1-1S15.553,7,15,7z"></path></svg>';
      const wishlist_icon = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><path fill="#1e1e1e" d="M11.335,0C10.026,0,8.848,0.541,8,1.407C7.153,0.541,5.975,0,4.667,0C2.088,0,0,2.09,0,4.667C0,12,8,16,8,16s8-4,8-11.333C16.001,2.09,13.913,0,11.335,0z M8,13.684C6.134,12.49,2,9.321,2,4.667C2,3.196,3.197,2,4.667,2C6,2,8,4,8,4s2-2,3.334-2c1.47,0,2.666,1.196,2.666,2.667C14.001,9.321,9.867,12.49,8,13.684z"></path></svg>';

      $('.flag-wrapper.flag-compare a').each(function(index) {
        let href_str = $(this).attr('href');
        let classes = 'add-compare flag' + set_flag_action_class(href_str);
        $(this, context)
          .addClass(classes)
          .html(compare_icon);
      });

      $('.flag-wrapper.flag-wishlist a').each(function(index) {
        let href_str = $(this).attr('href');
        let classes = 'add-wishlist flag' + set_flag_action_class(href_str);
        $(this, context)
          .addClass(classes)
          .html(wishlist_icon);
      });

      $('details.search-advanced summary').addClass('btn');
      $('.search-form a.search-help-link').addClass('btn').css({'position':'absolute', 'right':0});

      $('.link.field_link a').addClass('btn btn-default');

      $('.field table').addClass('table');
    }
  };

  Drupal.behaviors.add_cart_link = {
    attach: function (context, settings) {
      $('.add-cart.js-active-link', context).click(function() {
        $(this).addClass('unflag-action');
        $(this).closest('.actions').find('input.form-submit').click();
        return false;
      });
    }
  };

  Drupal.behaviors.quantity_regulator = {
    attach: function (context, settings) {
      //Regulator Up/Down
      $('.number-up', context).click(function(){
        let value = ($(this).closest('.number').find('input[type="text"]').attr('value'));
        $(this).closest('.number').find('input[type="text"]').attr('value', parseFloat(value)+1);
        return false;
      });
      $('.number-down', context).click(function(){
        let value = ($(this).closest('.number').find('input[type="text"]').attr('value'));
        if (value > 1) {
          $(this).closest('.number').find('input[type="text"]').attr('value', parseFloat(value)-1);
        }
        return false;
      });
    }
  };

  Drupal.behaviors.fixing_footer = {
    attach: function (context, settings) {
      $('footer #Footer-Top').removeClass('footer-top');
      $('footer #footer_top').removeAttr('class');
      $('footer #Footer-Bottom').removeClass('footer-bottom');
    }
  };

  Drupal.behaviors.charts = {
    attach: function (context, settings) {

      $('.graph-resize').html('');

      $('.bar-with-title', context).each(function() {
        return Morris.Bar({
          element    : $(this).attr('id'),
          data       : $(this).data('values'),
          xkey        : "item",
          ykeys       : ["value"],
          labels      : [$(this).attr('data-label')],
          barRatio    : 0.4,
          xLabelAngle : 35,
          hideHover   : "auto",
          barColors   : ["#ef005c"]
        });
      });

      $('.donut-graph', context).each(function() {
        Morris.Donut({
          element   : $(this).attr('id'),
          data      : $(this).data('values'),
          colors    : $(this).data('colors'),
          height    : 100,
          formatter : function(y) {
            return y + "%";
          }
        });
      });

    }
  };

  Drupal.behaviors.view_price_filter = {
    attach: function (context, settings) {
      if (typeof($.fn.slider) !== 'undefined' && $('#filter', context).length) {
        let from_date = parseInt($('#filter', context).parents('.views-element-container').find('.form-control[name*=min]').val().substr(0, 4));
        let to_date = parseInt($('#filter', context).parents('.views-element-container').find('.form-control[name*=max]').val().substr(0, 4));
        $('#filter', context).attr('value', from_date + ';' + to_date);
        $('#filter', context).slider({
          from: from_date - 3,
          to: to_date + 3,
          limits: false,
          step: 1,
          dimension: '',
          calculate: function( value ) {
            return ( value );
          },
          callback: function(value) {
            let dates = value.split(';');
            let current_date = new Date();
            let curr_day = current_date.getDay() + 1;
            let curr_month = current_date.getMonth() + 1;
            let array_date_from = [dates[0], curr_month, curr_day];
            let array_date_to   = [dates[1], curr_month, curr_day];
            let s_from_date = array_date_from.join('/');
            let s_to_date = array_date_to.join('/');
            $('#filter', context).parents('.views-element-container').find('.form-control[name*=min]').val(s_from_date);
            $('#filter', context).parents('.views-element-container').find('.form-control[name*=max]').val(s_to_date);
            $('#filter', context).parents('.views-element-container').find('.form-actions .form-submit').click();
          }
        });
      }

      if (typeof($.fn.slider) !== 'undefined') {
        $("#edit-sell-price-wrapper:not(.processed), #edit-list-price-wrapper:not(.processed)", context).each(function() {
          $(this).addClass('processed');
          $(this).after('<div class="price-regulator pull-right"><b>' + $(this).find('> label').text() + ':</b><div class="layout-slider"><input type="slider" name="year" value="$0;$2000" class="form-control price-filter"></div></div>');
          $(this).hide();
          var from_price = parseInt($(this).find("input[name$='[min]']").val());
          var to_price = parseInt($(this).find("input[name$='[max]']").val());
          var $this = $(this);
          $('.price-filter', context).attr('value', from_price + ';' + to_price);
          $('.price-filter', context).slider({
            from: from_price > 500 ? from_price - 500 : 0,
            to: to_price + 500,
            limits: false,
            step: 1,
            dimension: '&nbsp;' + Drupal.settings.ubercart_currency,
            calculate: function( value ) {
              return ( value );
            },
            callback: function(value) {
              var prices = value.split(';');
              $this.find("input[name$='[min]']").val(prices[0]);
              $this.find("input[name$='[max]']").val(prices[1]).change();
            }
          });

        });
      }

      if (typeof($.fn.slider) !== 'undefined') {
        $('.jslider-pointer').html('\n\
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 8 12" enable-background="new 0 0 8 12" xml:space="preserve">\n\
          <path fill-rule="evenodd" clip-rule="evenodd" fill="#1e1e1e" d="M2,0h4c1.1,0,2,0.9,2,2l-2,8c-0.4,1.1-0.9,2-2,2l0,0c-1.1,0-1.6-0.9-2-2L0,2C0,0.9,0.9,0,2,0z"/>\n\
        </svg>\n\
        ');
      }
    
    }
  };

  if(!navigator.userAgent.match(/iPad|iPhone|Android/i)) {
    var delay_drupal = ( function() {
    var timeout = { };
    
    return function( callback, id, time ) {
      if( id !== null ) {
      time = ( time !== null ) ? time : 100;
      clearTimeout( timeout[ id ] );
      timeout[ id ] = setTimeout( callback, time );
      }
    };
    })();

    $(window).on('resize', function() {
      delay_drupal( function() {

        var graphResize_drupal;
        
        clearTimeout(graphResize_drupal);
        return graphResize_drupal = setTimeout(function() {
          return Drupal.behaviors.charts.attach($(document), Drupal.settings);
        }, 500);
        
      }, 'resize');
    });
  }

  $(document).ready(function() {
    $('.dropdown-toggle[href="#"]').click(function() {
      $(this).parent().toggleClass('open');
    });

    $(document).bind('flagGlobalAfterLinkUpdate', function(event, data) {
      if(data.flagName == 'compare' || data.flagName == 'wishlist') {
        let new_value = parseInt($('.flag-count-' + data.flagName + ':first').text());
        new_value = data.flagStatus == 'unflagged' ? new_value - 1 : new_value + 1;
        if(new_value > 0) {
          $('.flag-status-' + data.flagName + ', .flag-count-' + data.flagName).show();
        }
        else {
          $('.flag-status-' + data.flagName + ', .flag-count-' + data.flagName).hide();
        }
        $('.flag-count-' + data.flagName).text(new_value);
      }
    });

    $('.modern-gallery-action a').click(function(event) {
      event.preventDefault();
      let column = $(this).attr('data-id') * 3;
      $.post(Drupal.url('ajax/progressive/save-variable'), {
          'variable' : 'progressive_modern_gallery',
          'variable_key' : $(this).parents('.modern-gallery-action').attr('data-id'),
          'value' : column
        });

      $(this).parents('.images-box').removeClassPrefix('col-md-').addClass('col-md-' + column);
      $(window).resize();

      return false;
    });

    // To Blog Timeline form.
    $('.form-item-form-id-clone').each(function(index) {
      let form_id = $(this).closest('form').attr('id');
      $(this).find('input').val(form_id);
    });

    // Sidebar Menu.
    $('.sidebar-menu.drop-down li.parent:first').addClass('active');
    $('.sidebar-menu.drop-down li.parent:first ul.sub').css('display', 'block');

    // Autosubmit views exposed form.
    $("form.views-exposed-form").find("select").bind("change", function () {
      $(this).closest("form").trigger("submit");
    }).end().find("input[type='submit']").addClass("visually-hidden");
  });
}());
