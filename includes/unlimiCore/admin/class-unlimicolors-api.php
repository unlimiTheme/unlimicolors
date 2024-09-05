<?php 

namespace UnlimiCore\Admin;

use \UnlimiCore\Base\UnlimiColor_Base;
use \UnlimiCore\Base\UnlimiColor_Paths;
use \UnlimiCore\Base\UnlimiColor_ItemStructure;
use \UnlimiCore\Base\UnlimiColor_Structure;
use \UnlimiCore\Core\UnlimiColors_Customize;

class UnlimiColor_API extends UnlimiColor_Base
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

        $this->settings = new UnlimiColor_Settings();
        $customize = new UnlimiColors_Customize();

        $this->structure = new UnlimiColor_Structure($customize->getStructure(), $this->settings->getAppVersion());
    }

    public function getBox()
    {
        $boxSetting = $this->settings->getBoxSetting();
        $defaultKeyVersion = $this->settings->getKeyVersion();
        $already_exists = false;

        $path = new UnlimiColor_Paths();
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
        
        $elemntStructure = new UnlimiColor_ItemStructure( $elementStructure );
        $elementStyles = $elemntStructure->getStylesStructure();
        
        $box = new UnlimiColor_Box( $boxSetting, $key, $css, $elementStyles, $this->key_version, $already_exists );
        $html = $box->get();

        $this->_response('success', ['html' => $html]);
    }

    public function add()
    {
        $elementStructure = $this->structure->getElementStructure( $this->key );
        $item = new UnlimiColor_ItemStructure( $elementStructure );
        $item_key_version = $item->keyVersion();

        if ( $item_key_version != $this->key_version ) {
            // generate new element
            $path = new UnlimiColor_Paths();
            $this->key = $path->toKey( $this->path, $this->key_version );
        }

        $this->structure->add( $this->key, $this->key_version, $this->data );

        $structure = $this->structure->getStructure();

        $data = [
            'target' => UnlimiColors_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function remove()
    {
        $this->structure->remove( $this->key );

        $structure = $this->structure->getStructure();
        $data = [
            'target' => UnlimiColors_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function removeall()
    {
        $this->structure->removeall($this->key);

        $structure = $this->structure->getStructure();
        $data = [
            'target' => UnlimiColors_Customize::$stucture_key, // $this->settings->getStructureId(),
            'value' => $structure
        ];

        $this->_response('success', $data);
    }

    public function cancel()
    {
        $this->structure->cancel($this->key);

        $structure = $this->structure->getStructure();
        
        $data = [
            'target' => UnlimiColors_Customize::$stucture_key, // $this->settings->getStructureId(),
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
        $nonce = get_query_var('customize_preview_nonce');
        if ( empty( $nonce ) || wp_verify_nonce( $nonce, UNLIMICOLORS_NONCE ) ) {
             die( 'Security check' ); 
        }

        $request = (array) get_post_field('data');
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