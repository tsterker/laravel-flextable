<?php

namespace Tsterker\Tests\Flextable\Stubs;

use Tsterker\Flextable\IsFlextable;

class Post extends \Illuminate\Database\Eloquent\Model
{
    use IsFlextable;

    public $timestamps = false;

    protected $connection = 'custom-connection';
}
