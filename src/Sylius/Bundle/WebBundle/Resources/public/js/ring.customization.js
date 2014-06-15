$(document).ready(function()
{	
	$('#productForm').submit(function(e)
	{
		e.preventDefault();
		
		$("#wizardModal").modal('show');
	});
	
	$(".customizable").steps({
		transitionEffect: "slideLeft",
		enableKeyNavigation: false,
		enableCancelButton: false,
		transitionEffectSpeed: 500,
		titleTemplate: '#title#',
        onStepChanging: function (event, currentIndex, newIndex)
        {
            //$(this).prev('form').validate().settings.ignore = ":disabled,:hidden";
            return true;//$(this).prev('form').valid();
        },
        onStepChanged: function (event, currentIndex, priorIndex) 
        { 
        	if(currentIndex == 3)
        	{
        		$('.wizard > .actions a[href="#finish"]').css('display', 'block');
        	}else
        	{
        		$('.wizard > .actions a[href="#finish"]').hide();
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
	
	$(".customizable #caratSlider").slider({ 
		min: 0.4,
		max: 3,
		step: 0.1,
		change: function(event, ui) 
		{
			$(".customizable .selection h4 span.ctSelection").html(ui.value);
		}
	});
	
	$(".wizard > .content .selection .selectionContent.choice div").click(function(e)
	{
		$(this).siblings().removeClass('selected');
		$(this).addClass('selected');
	});
});