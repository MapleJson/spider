<?php

declare (strict_types = 1);

namespace app\book\model;

use spider\Model;

class AddOnArticle extends Model
{
    protected $connect = 'book';

    public static function getArticle($id)
    {
        return self::select('aid')
            ->where('typeid = :id')
            ->bindValues(['id' => $id])
            ->single();
    }

}