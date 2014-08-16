$(document).ready(function()
{
	var $navDots = $("#hero nav a")
	var $next = $(".slide-nav.next");
	var $prev = $(".slide-nav.prev");
	var $slides = $("#hero .slides .slide");
	var actualIndex = 0;
	var swiping = false;
	var interval;
	
	if($('.timeleft').length > 0)
	{
		$('.timeleft').countdown({
			date: $('.timeleft').data('endDate'),
			render: function(data) 
			{
	            $('.timeleft').html(this.leadingZeros((data.days*24)+data.hours, 2) + ":" + this.leadingZeros(data.min, 2) + ":" + this.leadingZeros(data.sec, 2));
	        },
	        onEnd: function() 
	        {
	        	location.reload();
	        }
		});
	}
	
	//Bidding area
	$('.selectBidButton').click(function(e)
	{
		e.preventDefault();
		
		var diamondPrice = $(this).data('price');
		var checkoutUrl = $(this).data('checkoutUrl');
		var ringPrice = $('#diamondSelection .ringPrice').data('price');
		
		$('#diamondSelection .diamondPrice').html('$'+diamondPrice/100);
		$('#diamondSelection .totalPrice').html('$'+(ringPrice + diamondPrice)/100);
		
		$('#diamondSelection .checkoutButton').attr('href', checkoutUrl);
		$('#diamondSelection .checkoutButton').removeClass('disabled');
	});
	
	//Product images
	$('.project .screens .thumb').click(function(e)
	{
		e.preventDefault();
		
		var imageSrc = $(this).data('image');
		var videoSrc = $(this).data('video');
		
		if(imageSrc)
		{
			$('.project .screens .mainPicture').html('<img src="'+imageSrc+'" class="img-responsive">');
		}else if(videoSrc)
		{
			$('.project .screens .mainPicture').html('<video controls><source src="'+videoSrc+'" type="video/mp4"></video>');
		}	
	});
	
	
	function updateSlides(index) {
		// update nav dots
		$navDots.removeClass("active");
		$navDots.eq(index).addClass("active");

		// update slides
		var $activeSlide = $("#hero .slide.active");
		var $nextSlide = $slides.eq(index);

		$activeSlide.fadeOut();
		$nextSlide.addClass("next").fadeIn();
		
		setTimeout(function () {
			$slides.removeClass("next").removeClass("active");
			$nextSlide.addClass("active");
			$activeSlide.removeAttr('style');
			swiping = false;
		}, 1000);
	}
	
	$navDots.click(function (e) {
		e.preventDefault();
		if (swiping) { return; }
		swiping = true;

		actualIndex = $navDots.index(this);
		updateSlides(actualIndex);
	});

	$next.click(function (e) {
		e.preventDefault();
		if (swiping) { return; }
		swiping = true;

		clearInterval(interval);
		interval = null;

		actualIndex++;
		if (actualIndex >= $slides.length) {
			actualIndex = 0;
		}

		updateSlides(actualIndex);
	});

	$prev.click(function (e) {
		e.preventDefault();
		if (swiping) { return; }
		swiping = true;

		clearInterval(interval);
		interval = null;

		actualIndex--;
		if (actualIndex < 0) {
			actualIndex = $slides.length - 1;
		}

		updateSlides(actualIndex);
	});

	interval = setInterval(function () {
		if (swiping) { return; }
		swiping = true;

		actualIndex++;
		if (actualIndex >= $slides.length) {
			actualIndex = 0;
		}

		updateSlides(actualIndex);
	}, 5000);

	// demo player
	var $videoModal = $(".video-modal");
	$("#demo-player").click(function () {
		$videoModal.toggleClass("active");
		clearInterval(interval);
		interval = null;
	});
	$videoModal.click(function () 
	{
		$videoModal.removeClass("active");
		setTimeout(function () {
			$videoModal.find(".wrap").html('<iframe width="560" height="315" src="//www.youtube.com/embed/PR-zqVl6xKk" frameborder="0" allowfullscreen></iframe>');
		}, 1000);
	})
	$videoModal.find(".wrap").click(function (e) {
		e.stopPropagation();
	});


	// Initialize Isotope plugin for filtering
    var $container = $('.isotope_container'),
          $filters = $("#filters a");

    $container.imagesLoaded( function(){
        $container.isotope({
            itemSelector : '.col-md-4',
            masonry: {
                columnWidth: 323
            }
        });
    });

    // filter items when filter link is clicked
    $filters.click(function() {
        $filters.removeClass("active");
        $(this).addClass("active");
        var selector = $(this).data('filter');
        $container.isotope({ filter: selector });
        return false;
    });
});