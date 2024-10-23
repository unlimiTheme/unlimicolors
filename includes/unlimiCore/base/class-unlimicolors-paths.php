<?php 

namespace UNLIMICOLORS\Base;

use \UNLIMICOLORS\Base\UNLIMICOLORS_Base;

class UNLIMICOLORS_Paths extends UNLIMICOLORS_Base
{
    /**
     * Delimiters list
     */
    protected $delimiters = [
        'space' => [' ', '{_}'],
        'id'    => ['#', '{#}'],
        'class' => ['.', '{.}'],
        'link'  => ['>', '{>}']
    ];

    protected $allowed_tags = [
		'body',
        'main', 
		'header',
		'footer',
		'aside',
		'title',
		'area', 
		'article',
		'link',
		'map',
		'menu',
		'section',
		'table',
		'tbody',
		'thead',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
    ];

    protected $classes_to_ignore = [
        'logged-in',
        'wp-embed-responsive',
        'customize-partial-edit-shortcuts-shown',
        'has-x-large-font-size',

        'logged-in', 
        'wp-embed-responsive', 
        'customize-partial-edit-shortcuts-shown',
    ];
    
    /**
     * Ignored tag names
     */
    protected $ignore_tag_names = ['html'];
    
    /**
     * Key versions
     */
    protected $key_versions = [
        // 100 => ['tag', 'All elements like this on all pages'],
        // 120 => ['class', ''],
        // 140 => ['tagClass', ''],
        // 160 => ['tagId', ''],
        170 => ['tagSmartClassId', 1 , 'All elements like this on all pages'],
        // 180 => ['tagClassId', 'All elements exactly like this one on all pages'],

        // 200 => ['tagThisPage', ''],
        // 220 => ['classThisPage', ''],
        // 240 => ['tagClassThisPage', ''],
        // 260 => ['tagIdThisPage', ''],
        270 => ['tagSmartClassIdThisPage', 1, 'All elements like this on this page only'],
        // 280 => ['tagClassIdThisPage', ''],

        // 300 => ['tagSmartPath', ''],
        // 320 => ['classSmartPath', ''],
        // 340 => ['tagClassSmartPath', ''],
        // 360 => ['tagIdSmartPath', ''],
        370 => ['tagSmartClassSmartPath', 1, 'This element on all pages'],
        // 380 => ['tagClassIdSmartPath', ''],
        
        // 400 => ['tagSmartPathThisPage', ''],
        // 420 => ['classSmartPathThisPage', ''],
        // 440 => ['tagClassSmartPathThisPage', ''],
        // 460 => ['tagIdSmartPathThisPage', ''],
        470 => ['tagSmartClassSmartPathThisPage', 1, 'This element on this page only'],
        // 480 => ['tagClassIdSmartPathThisPage', ''],

        // 800 => ['tagFullPath', ''],
        // 820 => ['classFullPath', ''],
        // 840 => ['tagClassFullPath', ''],
        // 860 => ['tagIdFullPath', ''],
        870 => ['tagSmartClassFullPath', 1, 'Exactly this element on all pages'],
        // 880 => ['tagClassIdFullPath', ''],

        // 900 => ['tagFullPathThisPage', ''],
        // 920 => ['classFullPathThisPage', ''],
        // 940 => ['tagClassFullPathThisPage', ''],
        // 960 => ['tagIdFullPathThisPage', ''],
        970 => ['tagSmartClassFullPathThisPage', 1, 'Exactly this element on this page only'],
        // 980 => ['tagClassIdFullPathThisPage', ''],
    ];

    public function __construct()
    {
    }

    public function getKeyVersions( bool $active = true ): array
    {
        return array_map( function( $item ) {
            return $item[1];
        }, $this->key_versions );
    }

    public function getKeyVersionsInfo( bool $active = true ): array
    {
        return $this->_getKeyVersionsInfo( $active );
    }

    public function toKey( $path, string $key_version ): string
    {
        if ( !$this->_isKeyVersion( $key_version ) ) {
            return '';
        }

        $path = $this->_toObject( $path );

        return $this->_toKey( $path, $key_version );
    }

    public function toPath( $key ): array
    {
        return $this->_toPath( $key );
    }

    public function toKeys( object $path ): array
    {
        $available_versions = $this->_getKeyVersions();

        $keys = [];
        foreach ( $available_versions as $version ) {
            $keys[$version] = $this->_toKey( $path, $version );
        }

        krsort( $keys );

        return $keys;
    }

    public function keyToPath(string $key, bool $obj=true): array
    {
        return $this->_convertKeyToPath($key, $obj);
    }

    public function validateKey( string $key, int $key_version ): bool
    {
        return $this->_validateKey( $key, $key_version );
    }

    public function keyToCssPath( string $key ): string
    {
        return $this->_convertKeyToCssPath( $key );
    }

    protected function _toKey( $path, string $key_version ): string
    {
        $key_info = $this->_getKeyVersion( $key_version );
        if ( empty($key_info) ) {
            return '';
        }

        $f = '_'.$key_info[0];
        if ( !method_exists( $this, $f ) ) {
            return '';
        }

        $key = $this->$f( $path );

        return $key;
    }

    protected function _toPath( string $key )
    {
        $elements = explode( $this->delimiters['space'][1], $key );

        $path = [];
        foreach ( $elements as $el ) {
            $path[] = $this->_toPathEl( $el );
        }

        $result = array_filter( $path, function( $item ) {
            return !empty( $item );
        });

        return $result;
    }

    protected function _toPathEl( string $key ): array
    {
        $result = [];
        $delimiters = array_reverse( $this->delimiters );

        foreach ( $delimiters as $k => $v ) {

            $tags = explode( $v[1], $key, 2 );

            switch (true) {
                case $k == 'class':
                    $e = explode( $v[1], @$tags[1] );
                    $result['class'] = empty( $e[0] ) ? '' : $e;
                case $k == 'id':
                    $result['id'] = @$tags[1];
                    break;
                default:
                    $result['tagname'] = @$tags[0];
                    break;
            }

            $key = @$tags[0];
        }

        return array_filter( $result, function( $item ) { 
            return !empty( $item );
        });
    }



    protected function _tag( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = array_reverse( $path )[0];

        $key = $this->_convertPathToKey( $this_path, true, false, false );
        
        return $key;
    }

    protected function _class( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = array_reverse( $path )[0];

        $key = $this->_convertPathToKey( $this_path, false, true, false );
        
        return $key;
    }

    protected function _classId( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = array_reverse( $path )[0];

        $key = $this->_convertPathToKey( $this_path, false, false, true );
        
        return $key;
    }

    protected function _tagSmartClassId( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = (array) @array_reverse( $path )[0];

        $key = $this->_smartConvertPathToKey( $this_path, true, true, true );
        
        return $key;
    }

    protected function _tagClassId( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = array_reverse( $path )[0];

        $key = $this->_convertPathToKey( $this_path, true, true, true );
        
        return $key;
    }

    protected function _tagThisPage( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = $path[0];
        $this_path[] = array_reverse($path)[0];

        $key = $this->_convertPathToKey( $this_path, true, false, false, true );
        
        return $key;
    }

    protected function _tagSmartClassIdThisPage( object $path )
    {
        $key = '';

        $path = (array) $path;
        $this_path[] = (array) @$path[0];
        $this_path[] = (array) @array_reverse($path)[0];

        $key = $this->_smartConvertPathToKey( $this_path, true, true, true, true );
        
        return $key;
    }

    protected function _tagSmartClassSmartPath( object $path )
    {
        $key = '';

        $this_path = (array) $path;
        unset( $this_path[0] );

        $key = $this->_smartAllowedTagsAndIdConvertPathToKey( $this_path, true, true, true );
        
        return $key;
    }

    protected function _tagSmartClassSmartPathThisPage( object $path )
    {
        $key = '';

        $this_path = (array) $path;

        $key = $this->_smartAllowedTagsAndIdConvertPathToKey( $this_path, true, true, true, true );
        
        return $key;
    }

    
    protected function _tagFullPath( object $path )
    {
        $key = '';
        
        $this_path = (array) $path;

        $key = $this->_convertPathToKey( $this_path, true, false, false );
        
        return $key;
    }

    
    protected function _tagSmartClassFullPath( object $path )
    {
        $key = '';

        $this_path = (array) $path;
        unset( $this_path[0] );

        $key = $this->_smartConvertPathToKey( $this_path, true, true, false );
        
        return $key;
    }

    protected function _tagFullPathThisPage( object $path )
    {
        $key = '';

        $this_path = (array) $path;

        $key = $this->_convertPathToKey( $this_path, true, false, false, true );
        
        return $key;
    }

    protected function _tagSmartClassFullPathThisPage( object $path )
    {
        $key = '';

        $this_path = (array) $path;

        $key = $this->_smartConvertPathToKey( $this_path, true, true, false, true );
        
        return $key;
    }

    protected function _convertPathToKey( $path, $use_tagname = false, $use_class = false, $use_id = false, $use_body = false ): string
    {
        $key = '';
        foreach ( $path as $p ) {

            $_tagname = $this->_convertTagname( $p, $use_tagname );
            $_class = $this->_convertClass( $p, $use_body && $this->_getTagName( $p ) == 'body' ? true : $use_class );
            $_id = $this->_convertId( $p, $use_id );

            $key .= $_tagname . $_id . $_class;
        }
        
        if ( strpos( $key, $this->delimiters['space'][1] ) === 0 ) {
            $key = trim( $key, $this->delimiters['space'][1] );
        }

        return $key;
    }

    protected function _smartConvertPathToKey( $path, $use_tagname = false, $use_class = false, $use_id = false, $use_body = false ): string
    {
        $key = '';
        foreach ( $path as $p ) {

            $_tagname = $this->_convertTagname( $p, $use_tagname );
            $_class = $this->_smartConvertClass( $p, $use_body && $this->_getTagName( $p ) == 'body' ? true : $use_class );
            $_id = $this->_convertId( $p, $use_id );

            $key .= $_tagname . ( empty( $_class ) ? $_id : '') . $_class;
        }
        
        if ( strpos( $key, $this->delimiters['space'][1] ) === 0 ) {
            $key = trim( $key, $this->delimiters['space'][1] );
        }

        return $key;
    }


    protected function _smartAllowedTagsAndIdConvertPathToKey( $path, $use_tagname = false, $use_class = false, $use_id = false, $use_body = false ): string
    {
        $key = '';

        end( $path );
        $last_key = key( $path );

        foreach ( $path as $k => $p ) {
            $last = $k === $last_key;
            
            $_tagname = $this->_convertTagname( $p, $use_tagname );
            $_class = $this->_smartConvertClass( $p, $use_body && $this->_getTagName( $p ) == 'body' ? true : $use_class );
            $_id = $this->_convertId( $p, $use_id );
            
            $tagname = trim( $_tagname, $this->delimiters['space'][1] );

            if ( !$last && in_array( $tagname, $this->ignore_tag_names ) ) {
                continue;
            }

            if ( !$last && !in_array( $tagname, $this->allowed_tags ) ) { 

                if ( empty( $_id ) ) {
                    continue;
                }

                $_class = '';
            }

            $key .= $_tagname . $_class;
        }
              
        if ( strpos( $key, $this->delimiters['space'][1] ) === 0 ) {
            $key = substr( $key, 3, strlen($key) );
        }

        if ( strrpos( $key, $this->delimiters['class'][1] ) === strlen( $key ) - 3 ) {
            $key = substr( $key, 0, strlen($key) - 3 );
        }

        return $key;
    }

    protected function _convertTagname( $p, $use = true ) 
    {
        if ( !$use ) {
            return '';
        }

        $tagname = $this->_getTagName( $p );

        return $this->delimiters['space'][1] . $tagname;
    }

    protected function _convertClass( $p, $use = true ) 
    {
        if ( !$use ) {
            return '';
        }

        $class = $this->_getClasses( $p );

        return is_null( $class ) 
                    ? '' 
                    : $this->delimiters['class'][1] . str_replace( 
                        ' ', 
                        $this->delimiters['class'][1], 
                        $class 
                    );
    }

    protected function _smartConvertClass( $p, $use = true ) 
    {
        if ( !$use ) {
            return '';
        }

        $class = $this->_getSmartClasses( $p );

        return is_null( $class ) 
                    ? '' 
                    : $this->delimiters['class'][1] . str_replace( 
                        ' ', 
                        $this->delimiters['class'][1], 
                        $class 
                    );
    }

    protected function _convertId( $p, $use = true ) 
    {
        if ( !$use ) {
            return '';
        }

        $id = $this->_getId( $p );

        return is_null( $id ) 
                ? '' 
                : $this->delimiters['id'][1] . str_replace(
                    ' ', 
                    $this->delimiters['id'][1]
                    , $id
                );
    }

    protected function _getClasses( $p ) 
    {
        if ( is_null( @$p->class ) || !$p->class ) {
            return null;
        }

        $tagname = $this->_getTagName( $p );
        $class = $this->_filterClasses( $p->class );

        if ( $tagname == 'body' ) {

            $class = [
                $class[0],
                $class[1]
            ];
        }

        return implode( 
            $this->delimiters['class'][1], 
            array_filter( 
                array_map( 'trim', $class ), 
                function( $item ) { 
                    return !empty( $item ); 
                } ) 
            );
    }

    protected function _getSmartClasses( $p ) 
    {
        if ( is_null( @$p->class ) || !$p->class ) {
            return null;
        }

        $tagname = $this->_getTagName( $p );
        $class = $this->_filterClasses( $p->class );

        if ( $tagname == 'body' ) {

            $class = [
                $class[0],
                $class[1]
            ];
        }

        return implode( 
            $this->delimiters['class'][1], 
            array_filter( 
                array_map( 'trim', $class ), 
                function( $item ) { 
                    return !empty( $item ); 
                } ) 
            );
    }

    protected function _getTagName( $p ) 
    {
        return @$p->tagname;
    }

    protected function _getId( $p ) 
    {
        return @$p->id;
    }

    protected function _isKeyVersion( $key_version ): bool
    {
        return isset( $this->key_versions[$key_version] );
    }

    protected function _getKeyVersion( int $key_version ): array
    {
        return (array) @$this->key_versions[$key_version];
    }

    protected function _getKeyVersions(): array
    {
        return (array) array_keys( $this->key_versions );
    }

    protected function _getKeyVersionsInfo( bool $active = true ): array
    {
        return array_filter( $this->key_versions, function( $item ) {
            return $item[1] == 1;
        } );
    }

    protected function _convertKeyToCssPath( $key )
    {
        foreach ( $this->delimiters as $delimiter ) {
            $key = str_replace( $delimiter[1], $delimiter[0], $key );
        }
        
        return $key;
    }

    protected function _validateKey( $key, $key_version ): bool
    {
        $elements = explode( $this->delimiters['space'][1], $key );

        foreach ( $elements as $el ) {
            $res = $this->_validateKeyElement( $el );

            if ( !$res ) {
                return $res;
            }
        }

        $path = $this->_toPath( $key );
        $new_key = $this->_toKey( $this->_toObject( $path ), $key_version ); 

        return $new_key == $key;
    }

    protected function _validateKeyElement( string $el ): bool
    {
        $pos = 0;
        foreach ( $this->delimiters as $d ) {
            $new_pos = strpos( $el, $d[1] );

            if ( $new_pos === false ) {
                continue;
            }

            if ( $pos > $new_pos ) {
                return false;
            }

            $pos = $new_pos;
        }

        return true;
    }

    protected function _filterClasses( $classes )
    {
        $classes = array_diff( $classes, $this->classes_to_ignore );
        $classes = array_filter( $classes, function( $item ) {
            return strpos( $item , '__unlimithm__' ) === false;
        });

        return $classes;
    }
}