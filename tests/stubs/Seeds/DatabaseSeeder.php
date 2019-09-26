<?php

namespace Tsterker\Tests\Flextable\Stubs\Seeds;

use Illuminate\Database\Seeder;
use Tsterker\Tests\Flextable\Stubs\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        factory(User::class, 3)->create();
    }
}
