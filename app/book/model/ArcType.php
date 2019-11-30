<?php

declare (strict_types = 1);

namespace app\book\model;

use spider\Model;

class ArcType extends Model
{
    protected $connect = 'book';
    
    public static function getBookName($name)
    {
        return self::select('id')
            ->where('typename = :name')
            ->bindValues(['name'=>$name])
            ->single();
    }
    
}