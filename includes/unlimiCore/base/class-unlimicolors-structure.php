<?php 

namespace UNLIMICOLORS\Base;

use stdClass;

class UNLIMICOLORS_Structure extends UNLIMICOLORS_Base
{
    protected $structure;

    public function __construct($structure = [], string $app_version = '0')
    {
        $this->structure = $this->_toObject($structure);
          
        $this->_initStructure($app_version);
    }

    public function getStructure(): object
    {
        return $this->structure;
    }

    public function getElementStructure( string $key ): ?object
    {
        if (!$this->_hasKey( $key )) {
            return (object) [];
        }

        $innerKey = $this->_getInnerkeyByKey( $key );
        $styles = $this->_getStyles();

        return $styles->{$innerKey};
    }

    public function getStyles(): object
    {
        return $this->_getStyles();
    }

    public function increaseVersion(): void
    {
        $this->structure->version++;
    }

    public function hasKey(string $key): bool
    {
        return $this->_hasKey($key);
    }

    public function get(string $key): object
    {
        return new UNLIMICOLORS_ItemStructure($this->_getStyle($key));
    }

    public function add(string $key, string $key_version, object $items, bool $increaseVersion=true)
    {
        if ( $increaseVersion === TRUE ) {
            $this->increaseVersion();
        }

        if ( empty( trim( $key ) ) ) {
            return;
        }

        if ( empty( (array) $items ) ) {
            return;
        }

        if ( $this->_hasKey( $key ) ) {
            $this->_update( $key, $key_version, $items );
        } else {
            $this->_add( $key, $key_version, $items );
        }

        return;
    }

    public function update(string $key, string $key_version, object $item, bool $increaseVersion=true)
    {
        if ($increaseVersion === TRUE) {
            $this->increaseVersion();
        }

        $this->_update($key, $key_version, $item);
    }

    public function remove( string $key, bool $increaseVersion=true )
    {
        if ($increaseVersion === TRUE) {
            $this->increaseVersion();
        }

        $this->_remove( $key );
    }

    public function removeall( string $key, bool $increaseVersion=true  )
    {
        if ($increaseVersion === TRUE) {
            $this->increaseVersion();
        }

        $this->_removeall();
    }

    public function cancel()
    {
        $this->increaseVersion();
    }

    protected function _initStructure(string $app_version): void
    {
        if ( !property_exists( $this->structure, 'app_version' ) ) {
            $this->structure->app_version = $app_version ?? '0';
        }

        if ( !property_exists( $this->structure, 'version' ) ) {
            $this->structure->version = 0;
        }

        if ( !property_exists( $this->structure, 'styles' ) ) {
            $this->structure->styles = new stdClass();
        }

        if ( !property_exists( $this->structure, 'keys' ) ) {
            $this->structure->keys = new stdClass();
        }

        if ( !property_exists( $this->structure, 'versions' ) ) {
            $this->structure->versions = new stdClass();
        }
    }

    protected function _hasKey( string $key )
    {
        $keys = $this->_getKeys();

        if ( empty( $keys ) ) {
            return false;
        }
        
        return property_exists( $keys, $key );
    }

    protected function _add( string $key, string $key_version, object $item ): bool
    {
        $itemStructure = new UNLIMICOLORS_ItemStructure();
        $itemStructure->add( $key, $key_version, $item );
        $newStyles = $itemStructure->getStructure();

        if (empty((array) $newStyles)) {
            return true;
        }

        $innerKey = $this->_generateInnerKey();

        $styles = $this->_getStyles();
        $styles->{$innerKey} = $newStyles;

        $this->_setInnerKey( $key, $innerKey );
        $this->_setStyles( $styles );
        $this->_addKeyVersion( $innerKey, $key_version );

        return true;
    }

    protected function _update( string $key, string $key_version, object $items )
    {
        $innerKey = $this->_getInnerkeyByKey( $key );

        $styles = $this->_getStyles();

        $itemStructure = $this->get( $innerKey );

        $itemStructure->update( $items );
        $newStyles = $itemStructure->getStructure();

        if (empty((array) $newStyles)) {
            $this->remove($key);
            return true;
        }

        $styles->{$innerKey} = $newStyles;
        $this->_setStyles($styles);

        $this->_addKeyVersion( $innerKey, $key_version );

        return true;
    }

    protected function _remove( string $key )
    {
        if ( !$this->_hasKey( $key ) ) {
            return true;
        }

        $inner_key = $this->_getInnerkeyByKey( $key );

        $styles = $this->_getStyles();
        unset( $styles->{$inner_key} );
        $this->_setStyles( $styles );

        $keys = $this->_getKeys();
        unset( $keys->{$key} );
        $this->_setKeys( $keys );

        $this->_removeKeyVersion( $inner_key );        

        return true;
    }

    protected function _removeall()
    {
        $this->_setStyles((object) []);
        $this->_setKeys((object) []);
        $this->_removeAllKeyVersion();

        return true;
    }

    protected function _getStyle(string $key)
    {
        if ( !$this->_hasInnerKey( $key ) ) {
            return new StdClass();
        }

        $styles = $this->_getStyles();

        return $styles->{$key};
    }

    protected function _getStyles(): object
    {
        if (!property_exists($this->structure, 'styles')) {
            return new stdClass(); 
        }

        return (object) $this->structure->styles;
    }

    protected function _setStyles(object $styles)
    {
        $this->structure->styles = $styles;
    }

    protected function _addKeyVersion( string $inner_key, string $key_version )
    {
        $this->_removeKeyVersion( $inner_key, $key_version );

        $versions = $this->_getKeyVersions();

        $v = (array) @$versions->{$key_version} ?? [];

        if ( !isset( array_flip( (array) @$versions->{$key_version} )[$inner_key] ) ) {
            array_push( $v, $inner_key );
            $versions->{$key_version} = $v;
        }

        $this->_setKeyVersions( $this->_toObject( $versions ) );
    }

    protected function _setKeyVersions( object $versions )
    {
        $this->structure->versions = $versions;
    }

    protected function _removeKeyVersion( string $inner_key, ?string $not_key_version = null )
    {
        $versions = $this->_getKeyVersions();

        foreach ( $versions as $k_version => $keys ) {

            if ( !is_null( $not_key_version ) && $k_version == $not_key_version ) {
                continue;
            }

            $keys = (array) $keys;
            if ( ( $k = array_search( $inner_key, $keys ) ) !== false ) {
                unset( $keys[$k] );
                $versions->{$k_version} = $keys;
            }

            if ( empty( $versions->{$k_version} ) ) {
                unset( $versions->{$k_version} );
            }
        }

        $this->_setKeyVersions( $this->_toObject( $versions ) );
    }

    protected function _removeAllKeyVersion() 
    {
        $this->_setKeyVersions( new StdClass() );
    }

    protected function _getKeyVersions(): object
    {
        if (!property_exists($this->structure, 'versions')) {
            return new stdClass(); 
        }

        return (object) $this->structure->versions;
    }

    protected function _getKeys(): object
    {
        if ( !property_exists( $this->structure, 'keys' ) ) {
            return new stdClass();
        }

        return (object) $this->structure->keys;
    }

    protected function _setKeys( object $keys )
    {
        $this->structure->keys = $keys;
    }

    protected function _setInnerKey( string $key, string $innerKey )
    {
        (object) $this->structure->keys = (object) $this->structure->keys;
        $this->structure->keys->{$key} = $innerKey;
    }

    protected function _generateInnerKey()
    {
        return time();
    }

    protected function _getKeyByInnerkey( $innerKey ): ?string
    {
        return array_flip( (array) $this->structure->keys )[$innerKey];
    }

    protected function _getInnerkeyByKey( $key )
    {
        return $this->structure->keys->{$key};
    }

    protected function _hasInnerKey( string $innerKey )
    {
        $keys = $this->_getKeys();

        if ( empty( $keys ) ) {
            return false;
        }

        $innerKeys = (object) array_flip( (array) $keys );

        return property_exists( $innerKeys, $innerKey );
    }
}

