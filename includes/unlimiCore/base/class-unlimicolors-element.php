<?php 

namespace UnlimiCore\Base;

class UnlimiColor_Element extends UnlimiColor_Base
{
    public function getFirst($e, bool $obj=true)
    {
        if (is_string($e)) {
            $e = $this->keyToPath($e);
        }

        return $this->_toObject(current($e), $obj);
    }

    public function getLast($e, bool $obj=true)
    {
        if (is_string($e)) {
            $e = $this->keyToPath($e);
        }

        return $this->_toObject(end($e), $obj);
    }

    public function toEntry($obj)
    {
        $key = $this->_convertPathToKey($obj->path);

        $result = new \stdClass();
        $style = new \stdClass();
        $style->style = $obj->style;
        $result->{$key} = $style;

        return $result;
    }
}