<?php

namespace UNLIMICOLORS\Base;

class UNLIMICOLORS_Settings_Base extends UNLIMICOLORS_Base
{
    protected const SETTINGS_PATH = '../unlimiCore/settings/';

    protected const SETTINGS_FILE_PATH = 'includes/unlimiCore/settings/';

    protected const SETTINGS_FILE = 'settings.json';

    protected $settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_setSettings();
    }

    protected function _getFileSetting(string $key, bool $arr = true)
    {
        $filesSettings = $this->_getFilesSettings();

        if (!property_exists($filesSettings, $key)) {
            return $arr ? [] : new \stdClass();
        }

        $filePath = $this->_getPath() . $filesSettings->{$key};
        $content = $this->_readFileContent($filePath);

        return json_decode($content, $arr);
    }

    protected function _readFileContent(string $filePath): string
    {

        $response = wp_remote_get( $filePath );
        $r = '';

        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $body    = $response['body']; // use the content
            $r = $body;
        }

        return $r;
    }

    protected function _setSettings()
    {
        $settings = $this->_readFileContent($this->_getSettingsPath());

        $this->settings = json_decode($settings);
    }

    protected function _getFilesSettings(): object
    {
        return $this->settings->files;
    }

    protected function _getSettingsPath(): string
    {
        return $this->_getPath() . self::SETTINGS_FILE;
    }

    protected function _getPath(): string
    {
        return UNLIMICOLORS_PLUGIN_PATH . self::SETTINGS_FILE_PATH;
    }
}
