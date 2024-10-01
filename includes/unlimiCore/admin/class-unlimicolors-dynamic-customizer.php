<?php
/**
 *Dynamic Customizer.
 */

namespace UNLIMICOLORS\Admin;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;

/**
 * Dynamic customizer class.
 */
class UNLIMICOLORS_Dynamic_Customizer extends UNLIMICOLORS_Base
{
    protected $wp_customize;

    protected $structure;

    protected $sections;

    protected $settings;

    protected $controls;

    protected $availableSectionOptions = ['title', 'description', 'priority'];

    protected $availableSettingOptions = ['default'];

    protected $availableControlOptions = ['label', 'section', 'type'];

	/**
	 * Constructor.
	 */
    public function __construct($structure) 
    {
        $this->structure = $this->_toObject($structure, false);
    
        $this->_init();

        add_action('customize_register', [$this, '_customizeRegister']);
    }

    protected function _init()
    {
        $structures = ['sections', 'settings', 'controls'];

        foreach ($structures as $structure) {
            if (!isset($this->structure[$structure])) {
                $this->{$structure} = [];
            }
    
            $this->{$structure} = $this->structure[$structure];
        }
    }

    protected function _getSetting($id)
    {
        if (!isset($this->settings[$id])) {
            return [];
        }

        return $this->settings[$id];
    }

    protected function _getControl($id)
    {
        if (!isset($this->controls[$id])) {
            return [];
        }

        return $this->controls[$id];
    }

    public function _customizeRegister(\WP_Customize_Manager $wp_customize)
    {
        $this->wp_customize = $wp_customize;

        $this->_addSections();
        $this->_addSettings();
    }

    protected function _addSections()
    {
        foreach ($this->sections as $section) {
            $this->_addSection(@$section['id'], @$section['options']);
        }
    }

    protected function _addSection(string $id, array $options)
    {
        $options = array_intersect_key($options, array_flip($this->availableSectionOptions));
        $options['title'] = $options['title'];

        $this->wp_customize->add_section($id, $options);
    }

    protected function _addSettings()
    {
        foreach ($this->settings as $setting) {
            $id = @$setting['id'];
            $control = $this->_getControl($id);
            $this->_addSetting($id, @$setting['options']);
            $this->_addControl(@$control['id'], @$control['options']);
        }
    }

    protected function _addSetting(string $id, array $options)
    {
        $options = array_intersect_key($options, array_flip($this->availableSettingOptions));

        $this->wp_customize->add_setting($id, $options);
    }

    protected function _addControl(string $id, array $options)
    {
        $options = array_intersect_key($options, array_flip($this->availableControlOptions));
        $options['label'] = $options['label'];

        $this->wp_customize->add_control($id, $options);
    }

    /**
     * Sanitize select.
     *
     * @return mixed
     */
    protected function sanitizeSelect($input, $setting) 
    {
        // Ensure input is a slug.
        $input = sanitize_key( $input );

        // Get list of choices from the control associated with the setting.
        $choices = $setting->manager->get_control( $setting->id )->choices;

        // If the input is a valid key, return it; otherwise, return the default.
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

    /**
     * Sanitize checkbox.
     *
     * @return bool
     */
    protected function sanitizeCheckbox($input)
    {
        return isset( $input ) && $input == '1' ? true : false;
    }

    /**
     * Sanitize radio.
     */
    protected function sanitizeRadio($input, $setting)
    {   
        //input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
        $input = sanitize_key($input);

        //get the list of possible radio box options
        $choices = $setting->manager->get_control( $setting->id )->choices;

        //return input if valid or return default option
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

    /**
     * Sanitize input.
     */
    protected function sanitizeInput($input, $setting)
    {
        wp_filter_nohtml_kses();        
    }

    /**
     * Sanitize textarea.
     */
    protected function sanitizeTextarea($input, $setting)
    {
        wp_filter_nohtml_kses();        
    }

    /**
     * Sanitize email.
     */
    protected function sanitizeEmail($input, $setting)
    {
        sanitize_email();        
    }

    /**
     * Sanitize html color code.
     */
    protected function sanitizeHexColor($input, $setting)
    {
        sanitize_hex_color($input, $setting);
    }

    /**
     * Sanitize script imput.
     */
    function sanitizeJsCodeScripInput($input){
        return base64_encode($input);
    }
}
