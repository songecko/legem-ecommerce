var refreshProductPrice = function()
{
	var variantOptionIndex = $('#sylius_cart_item_variant_metal').val();
	var variantOption = $('#metal_price_'+variantOptionIndex);
	
	if(variantOption)
	{
		var price = variantOption.data('price');
		$('#productPrice .price').html(price);
	}
};

var pricingMatrix = null;

var getSelectionValue = function(selectionType)
{
	return $(".customizable .selection .review ."+selectionType+" .value").html();
};

var calculateApproxPrice = function()
{
	//Check the carat selection
	var caratValue = getSelectionValue('carat');
	var caratTableValue = 1;
	
	if(caratValue >= .4 && caratValue <= .49) caratTableValue = 1;
	else if(caratValue >= .5 && caratValue <= .69) caratTableValue = 2;
	else if(caratValue >= .7 && caratValue <= .89) caratTableValue = 3;
	else if(caratValue >= .9 && caratValue <= .99) caratTableValue = 4;
	else if(caratValue >= 1 && caratValue <= 1.49) caratTableValue = 5;
	else if(caratValue >= 1.5 && caratValue <= 1.99) caratTableValue = 6;
	else if(caratValue >= 2 && caratValue <= 3) caratTableValue = 7;
	
	var caratTable = $.map(pricingMatrix['ct'+caratTableValue], function(value, index) 
	{
		var clarityValue = getSelectionValue('clarity');
		if(clarityValue == '-' || clarityValue == index)
		{
			return [value];			
		}
	}); 

	var maxValue = 0;
	var minValue = 0;
	
	for (var clarityIndex=0; clarityIndex < caratTable.length; clarityIndex++)
	{
		var clarity = $.map(caratTable[clarityIndex], function(value, index) 
		{
			var colorValue = getSelectionValue('color');
			if(colorValue == '-' || colorValue == index)
			{
				return [value];			
			}
		}); 
		
		for (var colorIndex=0; colorIndex < clarity.length; colorIndex++)
		{
			var priceGuidance = clarity[colorIndex];
			if(priceGuidance > maxValue)
				maxValue = priceGuidance;
			
			if(priceGuidance < minValue || minValue == 0)
				minValue = priceGuidance;
		}
	}
	
	var minPrice = (minValue * 100) * caratValue;
	var maxPrice = (maxValue * 100) * caratValue;
	
	if(minPrice == maxPrice)
	{
		minPrice = minPrice / 1.1;
		maxPrice = maxPrice * 1.1;
	}
	
	$('#wizardModal .approxPrice .range').html('$'+Math.round(minPrice)+' - $'+Math.round(maxPrice));
};

$(document).ready(function()
{	
	//Init
	refreshProductPrice();
	
	$('#productForm .chooseRingButton').click(function(e)
	{
		e.preventDefault();
		
		if(!$(this).hasClass('disabled'))
		{
			$(this).addClass('disabled');
			
			$.ajax({
				url:   $(this).data('pricingMatrixUrl'),
	            type:  'get',
	            success: function (data, textStatus, jqXHR) 
	            {
	            	pricingMatrix = data;
	            	
	            	$('#productForm .chooseRingButton').hide();
	        		$('#productForm .submitButton').show();
	            }
			});
		}
	});
	
	$('#productForm').submit(function(e)
	{
		e.preventDefault();
		
		$("#wizardModal").modal('show');
		calculateApproxPrice();
	});
	
	$('#sylius_cart_item_variant_metal').change(function(e)
	{
		refreshProductPrice();
	});
	
	$(".customizable").steps({
		transitionEffect: "slideLeft",
		enableKeyNavigation: false,
		enableCancelButton: false,
		transitionEffectSpeed: 500,
		titleTemplate: '#title#',
        onStepChanging: function (event, currentIndex, newIndex)
        {
        	//If going forward
        	if(newIndex > currentIndex)
        	{
	        	if(currentIndex == 1 && getSelectionValue('color') == '-')
	        		return false;
	        	if(currentIndex == 2 && getSelectionValue('clarity') == '-')
	        		return false;
	        	if(currentIndex == 3 && getSelectionValue('cut') == '-')
	        		return false;
        	}
        	
            //$(this).prev('form').validate().settings.ignore = ":disabled,:hidden";
            return true;//$(this).prev('form').valid();
        },
        onStepChanged: function (event, currentIndex, priorIndex) 
        { 
        	$('#wizardModal > .carousel-indicators li').removeClass('active');
        	$('#wizardModal > .carousel-indicators .step'+(currentIndex+1)).addClass('active');
        	
        	if(currentIndex == 4)
        	{
        		$('.wizard > .actions a[href="#finish"]').css('display', 'block');
        	}else
        	{
        		$('.wizard > .actions a[href="#finish"]').hide();
        	}

        	if(currentIndex > priorIndex)
        	{
        		calculateApproxPrice();     		
        	}
        },
        onFinishing: function (event, currentIndex)
        {
        	//$('#productForm').settings.ignore = ":disabled";
            return true;//$('#productForm').valid();
        },
        onFinished: function (event, currentIndex)
        {
            $('#productForm').get(0).submit();
        } 
	});
	
	//-- Diamond attribute selections --//
	$(".customizable #caratSlider").slider({ 
		min: 0.4,
		max: 3,
		step: 0.05,
		change: function(event, ui) 
		{
			var value = ui.value;
			var decimals = ((+value).toFixed(2)).replace(/^-?\d*\.?|0+$/g, '').length;
			
			if(decimals == 0)
			{
				value = value+'.00';
			}else if(decimals == 1)
			{
				value = value+'0';
			}
			
			$(".customizable .selection h4 span.ctSelection").html(value);
			$(".customizable .selection .review .carat .value").html(value);
		}
	});
	
	$(".wizard > .content .selection .selectionContent.choice div").click(function(e)
	{
		$(this).siblings().removeClass('selected');
		$(this).addClass('selected');
		
		var selectionType = $(this).parent('.choice').data('selection');
		$(".customizable .selection .review ."+selectionType+" .value").html($(this).html());
	});
});