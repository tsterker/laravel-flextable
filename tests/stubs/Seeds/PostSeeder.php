<?php

namespace Tsterker\Tests\Flextable\Stubs\Seeds;

use Illuminate\Database\Seeder;
use Tsterker\Tests\Flextable\Stubs\Post as PostWithCustomConnection;

class PostSeeder extends Seeder
{
    public function run()
    {
        factory(PostWithCustomConnection::class, 3)->create();
    }
}
