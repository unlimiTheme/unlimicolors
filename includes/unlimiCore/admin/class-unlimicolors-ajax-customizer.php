<?php

namespace UnlimiCore\Admin;

/**
 * Ajax customizer.
 */
class UnlimiColor_AjaxCustomizer {

    /**
     * Constructor
     */
    public function __construct() {
        
        // ajax actions
        $class_methods = get_class_methods( $this );
        unset($class_methods[0]);

        foreach ($class_methods as $method) {
            add_action( 'wp_ajax_nopriv_' . $method, [$this, $method] );
            add_action( 'wp_ajax_' . $method, [$this, $method] );
        }
    }

    public function getBox()
    {
        $api = new UnlimiColor_API();
        $api->getBox();
    }

    public function save()
    {
        $api = new UnlimiColor_API();
        $api->add();
    }

    public function remove()
    {
        $api = new UnlimiColor_API();
        $api->remove();
    }

    public function removeall()
    {
        $api = new UnlimiColor_API();
        $api->removeall();
    }
}