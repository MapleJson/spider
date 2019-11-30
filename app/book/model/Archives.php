<?php

declare (strict_types = 1);

namespace app\book\model;

use spider\Model;

class Archives extends Model
{
    protected $connect = 'book';

    public static function getChapter($id, $chapter)
    {
        return self::select('id')
            ->where('typeid = :id AND shorttitle = :title')
            ->bindValues([
                'id'    => $id,
                'title' => $chapter
            ])
            ->single();
    }

}