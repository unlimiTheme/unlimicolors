<?php

namespace UNLIMICOLORS\Admin;

use UNLIMICOLORS\Base\UNLIMICOLORS_Settings_Base;

class UNLIMICOLORS_Settings extends UNLIMICOLORS_Settings_Base
{
    protected $box_settings_key = 'box_settings';

    protected $key_version_key = 'key_version';
    
    protected $app_version_key = 'app_version';
    
    protected $styles_key = 'styles';
    
    protected $customizer_key = 'customizer';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getBoxSetting( bool $arr = true )
    {
        return $this->_getFileSetting( $this->box_settings_key, $arr );
    }

    public function getKeyVersion(): string
    {
        if ( !property_exists( $this->settings, $this->key_version_key ) ) {
            return 'v1';
        }

        return ( string ) $this->settings->{$this->key_version_key};
    }

    public function getAppVersion(): string
    {
        if ( property_exists( $this->settings, $this->app_version_key ) ) {
            return '1.0.0';
        }

        return ( string ) $this->settings->{$this->app_version_key};
    }

    public function getStylesSettings( bool $toObject = true )
    {
        return $this->_getFileSetting( $this->styles_key, !$toObject );
    }

    public function getCustomizer( bool $arr = true )
    {
        return $this->_getFileSetting( $this->customizer_key, $arr );
    }
}
