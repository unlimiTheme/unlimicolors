<?php 

namespace UNLIMICOLORS\Admin;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;
use \UNLIMICOLORS\Base\UNLIMICOLORS_Paths;
use \UNLIMICOLORS\Base\UNLIMICOLORS_ItemStructure;
use \UNLIMICOLORS\Base\UNLIMICOLORS_Structure;
use \UNLIMICOLORS\Core\UNLIMICOLORS_Customize;

class UNLIMICOLORS_API extends UNLIMICOLORS_Base
{
    protected $unsetData = ['key', 'path', 'key_version'];

    protected $key;

    protected $key_version;

    protected $path;

    protected $data;

    protected $structure;

    protected $settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_parseRequest();

        $this->settings = new UNLIMICOLORS_Settings();
        $customize = new UNLIMICOLORS_Customize();

        $this->structure = new UNLIMICOLORS_Structure($customize->getStructure(), $this->settings->getAppVersion());
    }

    public function getBox()
    {
        $boxSetting = $this->settings->getBoxSetting();
        $defaultKeyVersion = $this->settings->getKeyVersion();
        $already_exists = false;

        $path = new UNLIMICOLORS_Paths();
        $keys = $path->toKeys( $this->path );

        $elementStructure = [];
        foreach ($keys as $version => $key) {
            $elementStructure = $this->structure->getElementStructure( $key );

            if ( !empty( (array) $elementStructure ) ) {
                $already_exists = true;
                $this->key_version = $version;
                break;
            }
        }
        if ( empty( (array) $elementStructure ) ) {
            $key = $path->toKey( $this->path ?? [], $defaultKeyVersion );
            $this->key_version = $defaultKeyVersion;
        }
        $css = $path->keyToCssPath( $key );
        
        $elemntStructure = new UNLIMICOLORS_ItemStructure( $elementStructure );
        $elementStyles = $elemntStructure->getStylesStructure();
        
        $box = new UNLIMICOLORS_Box( $boxSetting, $key, $css, $elementStyles, $this->key_version, $already_exists );
        $html = $box->get();

        $this->_response('success', ['html' => $html]);
    }

    public function add()
    {
        $elementStructure = $this->structure->getElementStructure( $this->key );
        $item = new UNLIMICOLORS_ItemStructure( $elementStructure );
        $item_key_version = $item->keyVersion();

        if ( $item_key_version != $this->key_version ) {
            // generate new element
            $path = new UNLIMICOLORS_Paths();
            $this->key = $path->toKey( $this->path, $this->key_version );
        }

        $this->structure->add( $this->key, $this->key_version, $this->data );

        $structure = $this->structure->getStructure();

        $data = [
            'target' => UNLIMICOLORS_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function remove()
    {
        $this->structure->remove( $this->key );

        $structure = $this->structure->getStructure();
        $data = [
            'target' => UNLIMICOLORS_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function removeall()
    {
        $this->structure->removeall($this->key);

        $structure = $this->structure->getStructure();
        $data = [
            'target' => UNLIMICOLORS_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function cancel()
    {
        $this->structure->cancel($this->key);

        $structure = $this->structure->getStructure();
        
        $data = [
            'target' => UNLIMICOLORS_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    protected function _getKey(object $request)
    {
        $property = 'key';

        if (property_exists($request, $property)) {
            return $request->{$property};
        }

        return false;
    }

    protected function _getKeyVersion(object $request)
    {
        $property = 'key_version';

        if (property_exists($request, $property)) {
            return $request->{$property};
        }

        return false;
    }

    protected function _getData(object $request) 
    {
        $data = new \stdClass();
        $data = $request;
        foreach ($this->unsetData as $unset) {
            unset($data->{$unset});
        }

        return $data;
    }

    protected function _getPath(object $request)
    {
        $property = 'path';

        if (property_exists($request, $property)) {
            return $request->{$property};
        }

        return false;
    }

    protected function _parseRequest()
    {
        $nonce = isset( $_GET['customize_preview_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['customize_preview_nonce'] ) ) : '';

        if ( wp_verify_nonce( $nonce, UNLIMICOLORS_NONCE ) ) {
             die( 'Security check' ); 
        }
        
        $request = isset( $_POST['data'] ) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ) : [];
        $request = $this->_toObject($request);

        $this->key = $this->_getKey($request);
        $this->key_version = $this->_getKeyVersion($request);
        $this->path = $this->_toObject($this->_getPath($request));
        $this->data = $this->_getData($request);
    }

    protected function _response($success, $data=[])
    {
        $response = [
            'status' => 'success',
            'data' => $data
        ];

        header( 'Content-Type: application/json' );
        echo wp_json_encode( $response );

        exit();
    }
}