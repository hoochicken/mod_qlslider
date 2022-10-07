
// initialization of magnific popup for all slider instances
jQuery(document).ready(function()
{
	jQuery('.qlslider').each(function()
    {
		jQuery(this).magnificPopup(
            {
	        delegate: '.image-link',
	        type: 'image',
	        mainClass: 'mfp-img-mobile',
	        gallery:
            {
	          enabled: true
	        },
			image:
            {
				verticalFit: true
			}
	    });
	});
});