/*
 * Copyright (c) 2018.
 * KarmaTechs
 * Evan Karma Alias MADSkill
 * madskill.madskill@gmail.com
 * https://sociamater.com
 */

// Preload images
imageObj = new Image();
imgs = ["img/toggle.gif", "img/nyro/ajaxLoader.gif", "img/nyro/prev.gif", "img/nyro/next.gif"];
for (i = 0; i < imgs.length; i++) imageObj.src = imgs[i];

// Administry object setup
if (!Administry) var Administry = {}

// scrollToTop() - scroll window to the top
Administry.scrollToTop = function (e) {
    $(e).hide().removeAttr("href");
    if ($(window).scrollTop() != "0") {
        $(e).fadeIn("slow")
    }
    var scrollDiv = $(e);
    $(window).scroll(function () {
        if ($(window).scrollTop() == "0") {
            $(scrollDiv).fadeOut("slow")
        } else {
            $(scrollDiv).fadeIn("slow")
        }
    });
    $(e).click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, "slow")
    })
}

// setup() - Administry init and setup
Administry.setup = function () {
    // Open an external link in a new window
    $('a[href^="http://"]').filter(function () {
        return this.hostname && this.hostname !== location.hostname;
    }).attr('target', '_blank');
	
    // build animated dropdown navigation
	$('#menu ul').supersubs({ 
		minWidth:    12,   // minimum width of sub-menus in em units 
		maxWidth:    27,   // maximum width of sub-menus in em units 
		extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
						   // due to slight rounding differences and font-family 
	}).superfish(); 
	
    // build an animated footer
    $('#animated').each(function () {
        $(this).hover(function () {
            $(this).stop().animate({
                opacity: 0.9
            }, 400);
        }, function () {
            $(this).stop().animate({
                opacity: 0.0
            }, 200);
        });
    });

    // scroll to top on request
    if ($("a#totop").length) Administry.scrollToTop("a#totop");

    // setup content boxes
    if ($(".content-box").length) {
        $(".content-box header").css({
            "cursor": "s-resize"
        });
        // Give the header in content-box a different cursor	
        $(".content-box header").click(
        function () {
            $(this).parent().find('section').toggle(); // Toggle the content
            $(this).parent().toggleClass("content-box-closed"); // Toggle the class "content-box-closed" on the content
        });
    }
	
	// setup nyro popup window
	$.nyroModalSettings({
		debug: false,
		processHandler: function(settings) {
			var url = settings.url;
			if (url && url.indexOf('http://www.youtube.com/watch?v=') == 0) {
				$.nyroModalSettings({
					type: 'swf',
					height: 355,
					width: 425,
					url: url.replace(new RegExp("watch\\?v=", "i"), 'v/')
				});
			}
		}
	});
    
	// custom tooltips to replace the default browser tooltips for <a title=""> <div title=""> and <span title="">
    $("a[title], div[title], span[title]").tipTip();
	
	// find closeable boxes and add a "close" action
	$('.closeable').each(function(index){
		$(this).prepend( 
			$('<a></a>')
				.attr({href: '#', title: 'Close'})
				.addClass('close')
				.text('x')
				.click(function() {
					$(this).parent().fadeOut();
					return false;
				})
		);
	});
}

// progress() - animate a progress bar "el" to the value "val"
Administry.progress = function (el, val, max) {
    var duration = 400;
    var span = $(el).find("span");
    var b = $(el).find("b");
    var w = Math.round((val / max) * 100);
    $(b).fadeOut('fast');
    $(span).animate({
        width: w + '%'
    }, duration, function () {
        $(el).attr("value", val);
        $(b).text(w + '%').fadeIn('fast');
    });
}

// videoSupport() - <video> tag support for older browsers through flash player embedding
Administry.videoSupport = function (wrapper, videoURL, width, height) {
    var v = document.createElement("video"); // Are we dealing with a browser that supports <video> tag? 
    if (!v.play) { // If no, use Flash.
        var vobj = $('#' + wrapper).find('video');
        var poster = $(vobj).attr("poster");
        var params = {
            allowfullscreen: "true",
            allowscriptaccess: "always"
        };
        var flashvars = {
            file: videoURL,
            image: poster
        };
        swfobject.embedSWF("player.swf", wrapper, width, height, "9.0.0", "expressInstall.swf", flashvars, params);
    }
}

// dateInput() - <input type="date"> support with fallback
Administry.dateInput = function (e) {
    var i = document.createElement("input"); 
	i.setAttribute("type", "date");
	if (i.type == "text") {
		// No native date picker support :(
		// We shall use jQuery datepick
		$(e).datepick({dateFormat: 'yyyy-mm-dd'}); 
	}    
}

// expandableRows() - expandable table rows
Administry.expandableRows = function () {
    var titles_total = $('td.title').length;
    if (titles_total) { /* setting z-index for IE7 */
        $('td.title').each(function (i, e) {
            $(e).children('div').css('z-index', String(titles_total - i));
        });

        $('td.title').find('a').click(function () {
            // hide previously opened containers
            $('.opened').hide();
            // remove highlighted class from rows
            $('td.highlighted').removeClass('highlighted');

            // locate the row we clicked onto
            var tr = $(this).parents("tr");
            var div = $(this).parent().find('.listingDetails');

            if (!$(div).hasClass('opened')) {
                $(div).addClass('opened').width($(tr).width() - 2).show();
                $(tr).find('td').addClass('highlighted');
            } else {
                $(div).removeClass('opened');
                $(tr).find('td').removeClass('highlighted');
            }
            return false;
        });
    }
}