$(function () {
    /************************
     *         MENU         *
     * TODO: create object, *
     *       document       *
     ************************/
    function showDrop(element) {
        var $drop = element.siblings('.drop');

        //Reset css first to get a accurate offset().left reading
        $drop.css({
            left:'1px',
            right:'auto'
        });
        $drop.show().parent().addClass('opened');

        if ($drop.offset().left + $drop.outerWidth() > $(window).width()) {
            $drop.css({
                right:'0',
                left:'auto'
            });
        }
    }

    if ($('#subnav').children().length) {
        $('#subnav').children().andSelf().show();
    }

    $('[role=banner]').on('click', '[data-subnav]:not(.current)', function () {
        var elem = $(this),
            url = elem.attr('data-subnav');

        if ($('#subnav').filter(':visible').length) {
            $('[data-subnav]').removeClass('current');
            elem.addClass('current');

            $('#subnav').children().slideUp(function () {
                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#subnav').html(data).children().slideDown();
                    }
                });
            });

        } else {
            $('#subnav').show();
            elem.addClass('current');

            $.ajax({
                url: url,
                success: function(data) {
                    $('#subnav').html(data).children().slideDown();
                }
            });

        }
    });
    $('#subnav, .tabs').on('click', 'button.dropdown', function (e) {
        var $drop = $(this).siblings('.drop'),
            $container = $(this).closest('#subnav, .tabs');
        if ($drop.filter(':visible').length) {
            $('.drop').hide().parent().removeClass('opened');
        } else {
            $('.drop').hide().parent().removeClass('opened');
            showDrop($(this));

            e.preventDefault();
            e.stopPropagation();
        }

    }).on('mouseenter', 'li', function () {
        if ($(this).children('a.dropdown').length) {
            showDrop($(this).children('.dropdown'));
        }
    }).on('mouseleave', 'li', function () {
        if ($(this).children('a.dropdown').length) {
            $(this).removeClass('opened').children('.drop').hide();
        }
    });

    $(document).on('click.hideDrops', function () {
        $('.drop').hide();
        $('#subnav, .tabs').find('li').removeClass('opened');
    });

    //Header icons tooltips
    $('header[role=banner] li button[title]').each(function () {
        //remove title to avoid having native tooltip
        $(this).attr('data-title', $(this).attr('title'));
        $(this).removeAttr('title');

        $(this).on('mouseenter', function () {
            var $tooltip = $('<div class="tooltip" />').html($(this).attr('data-title')).hide();

            $(this).parent().append($tooltip);
            $tooltip.css('left', ($tooltip.outerWidth() / 2 - $(this).parent().outerWidth() / 2) / -1).fadeIn(300);
        }).on('mouseleave', function () {
            $(this).siblings('.tooltip').remove();
        });
    });

    //background color switch in content
    if ($('#content-wrapper nav').length) {
        $(window).on('load.contentBackground resize.contentBackground', function() {
            if ($('#content-wrapper nav').outerHeight() > $('#content').outerHeight()) {
                $('#content-wrapper').css('background-color', $('#content').css('background-color'));
            } else {
                $('#content-wrapper').css('background-color', $('#content-wrapper nav').css('background-color'));
            }
        });
    }

    //scroll with in-page anchors
    $('a[href^=#]').each(function () {
        var id = $(this).attr('href').substring(1);

        if (id && $('#' + id).length) {
            $(this).on('click', function(e) {
                $('html,body').animate({scrollTop: $('#' + id).offset().top});
                window.location.hash = id;
                e.preventDefault();
            });
        }
    });


    /**************************************************
     *                                                *
     *    from here on is script for the old style    *
     *         and functions, needs cleanup           *
     *                                                *
     **************************************************/


	/* alert bar */
	var alertTimer;
	if ($('.alert-bar').length) {
		$('.alert-bar').animate({
			'height': 40,
			'line-height': '40px'
		});
		clearTimeout(alertTimer);

		alertTimer = setTimeout(function () {
			$('.alert-bar').animate({
				'height': 0,
				'line-height': 0
			}, function () {
				$(this).remove();
			});
		}, 6000);
	}
	/* hidable sections */
	$('section.hideable h1').click(function () {
		$(this).parent().toggleClass('hide');
	});

	/* sortable table init */
	$('[data-type=sortable-table]').each(function () {
		new OMS.SortableTable($(this)[0]);
	});
	/* long list */
	$('[data-type=infinite-scroll]').each(function () {
		new OMS.InfiniteScroll($(this)[0]);
	});
	/* autocomplete */
	$('[data-type=autocomplete]').each(function () {
		new OMS.AutoComplete($(this)[0]);
	});
	/* tree */
	$('[data-type=tree]').each(function() {
		new OMS.Tree($(this)[0]);
	});

	/* tooltip */
	$('[data-tooltip]').each(function () {
		$(this).append($('<div class="tooltip" hidden />').html($(this).attr('data-tooltip')));
		$(this).children('.tooltip').css('bottom', ($(this).height() + 8));
	}).hover(function () {
		$(this).children('.tooltip').stop(false, true).fadeToggle();
	});

	/* toggle checkboxes/radiobuttons */
	$('input[type=radio], input[type=checkbox]').change(function() {
		$(this).parent().parent().children('input').toggleStatus();
	});

	/* TinyMCE */
	$('textarea.tinymce').tinymce({
		script_url: 'js/tiny_mce/tiny_mce.js',
		theme: 'advanced',
		language: 'nl',
		plugins : "advhr,autosave,table,noneditable,contextmenu,inlinepopups,media,paste,searchreplace",
		theme_advanced_buttons1 : "bold,italic,underline,formatselect,removeformat,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,outdent,indent,replace,anchor,link,unlink,image,media",
		theme_advanced_buttons2 : "tablecontrols,seperator,charmap,advhr",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true,
		theme_advanced_blockformats : "p,h2,h3,h4",
		entity_encoding : "raw",
		body_class : "user-content",
		width : "600",
		paste_use_dialog : true,
		fix_list_elements : true,
		fix_nesting : true,
		forced_root_block : false,
		force_br_newlines : false,
		force_p_newlines : true,
		verify_css_classes : true,
		relative_urls : false,
		theme_advanced_resize_horizontal : true,
		paste_convert_headers_to_strong : false,
		paste_auto_cleanup_on_paste : true,
		paste_retain_style_properties : "",
		paste_strip_class_attributes : "all",
		paste_remove_styles : true,
		paste_remove_spans : true,
		paste_convert_middot_lists : true,
		imagemanager_handle : "media,image",
		imagemanager_remember_last_path : false,
		filemanager_handle : "file",
		media_use_script : false,
		table_styles : "Zonder rand=noborder",
		table_cell_styles : false,
		table_row_styles : false,
		table_default_cellpadding : "0",
		table_default_cellspacing : "0",
		nvs_type : "home",
		nvs_page_id : "2",
		nvs_item_id : "",
		extended_valid_elements : "hr[class|width|size|noshade],img[style|class|src|border=0|alt|title|hspace|vspace|width|height|align]",
		custom_elements : "",
		tab_focus : ':prev,:next',
		setup : function(ed) {
		}
	}).closest('label').click(function () {
		$(this).find('.tinymce').tinymce().focus();
	});

	/* Light box */
	$('[data-open-lightbox]').click(function (e) {
		$('.lightboxes').show();
		$('#overlay, .lightboxes .close, #' + $(this).attr('data-open-lightbox')).fadeIn();
		e.preventDefault();
	});

	if ($('[data-open-onload]').length === 1) { //exactly once, so not true or more than 1
		$('.lightboxes').show();
		$('#overlay, .lightboxes .close, [data-open-onload]').fadeIn();
	}

	$('#overlay, .lightboxes .close').click(function() {
		$('.lightboxes>div, #overlay, .lightboxes').fadeOut(function () {
			$('.lightboxes').hide();
		});
	});

	/* Polyfills */
	Modernizr.load({
		test: ($("input[type^='date']").length && !Modernizr.inputtypes.datetime),
		yep: [
			'js/datepicker.js',
			'css/ui-lightness/jquery-ui-1.8.16.custom.css'
		]
	});

	/* jQuery extensions */
	$.fn.toggleStatus = function () {
		if ($(this).prop('disabled') === true) {
			$(this).removeAttr('disabled');
		} else {
			$(this).prop('disabled', true);
		}
		return this;
	};
});