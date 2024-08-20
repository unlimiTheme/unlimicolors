(function(wp, $) {
    "use strict";

    console.log('init customizer-preview');

    // Bail if the customizer isn't initialized
    if (!wp || !wp.customize) {
        return;
    }

    var api = wp.customize,
        OldPreview;

    // Custom Customizer Preview class (attached to the Customize API)
    api.unlimiCustomizerPreview = {
        // Init
        init: function() {
            var self = this; // Store a reference to "this"

            new UnlimithmBox({ 'customizer': this });
        }
    };

    /**
     * Capture the instance of the Preview since it is private (this has changed in WordPress 4.0)
     *
     * @see https://github.com/WordPress/WordPress/blob/5cab03ab29e6172a8473eb601203c9d3d8802f17/wp-admin/js/customize-controls.js#L1013
     */
    OldPreview = api.Preview;
    api.Preview = OldPreview.extend({
        initialize: function(params, options) {
            // Store a reference to the Preview
            api.unlimiCustomizerPreview.preview = this;

            // Call the old Preview's initialize function
            OldPreview.prototype.initialize.call(this, params, options);
        }
    });

    // Document ready
    $(function() {
        // Initialize our Preview
        api.unlimiCustomizerPreview.init();
    });

})(window.wp, jQuery);