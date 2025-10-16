jQuery(document).ready(function($) {
    // Initialize all collapsible sections
    $('.collapsible-section').each(function() {
        var $section = $(this);
        var $trigger = $section.find('.collapsible-trigger');
        var $content = $section.find('.collapsible-content');
        
        // Initially hide content
        $content.hide();
        
        // Handle click events
        $trigger.on('click', function(e) {
            e.preventDefault();
            var isExpanded = $trigger.attr('aria-expanded') === 'true';
            
            // Toggle aria attributes
            $trigger.attr('aria-expanded', !isExpanded);
            
            // Animate content
            if (isExpanded) {
                $content.slideUp(300, function() {
                    $content.attr('hidden', '');
                });
            } else {
                $content.slideDown(300, function() {
                    $content.removeAttr('hidden');
                });
            }
        });
    });
}); 