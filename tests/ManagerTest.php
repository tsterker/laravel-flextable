<?php

namespace Tsterker\Tests\Flextable;

use Tsterker\Flextable\Manager;
use Tsterker\Tests\Flextable\Stubs\Seeds\DatabaseSeeder;
use Tsterker\Tests\Flextable\Stubs\Seeds\PostSeeder;
use Tsterker\Tests\Flextable\Stubs\User;
use Tsterker\Tests\Flextable\Stubs\Post as PostWithCustomConnection;

class ManagerTest extends TestCase
{
    protected $migrations = __DIR__ . "/stubs/migrations";
    protected $factories = __DIR__ . "/stubs/factories-custom";

    /**
     * @test
     */
    public function it_uses_memory_connection_by_default()
    {
        $manager = new Manager;

        $manager->configure();

        $connection = $manager->getConnection();

        $this->assertIsString($connection);
        $this->assertRegexp('/:memory:.+/', $connection);

        $this->assertEquals(
            ["driver" => "sqlite", "database" => ":memory:"],
            config("database.connections.$connection")
        );
    }

    /**
     * @test
     */
    public function it_accepts_custom_connection_name_and_overrides_config_if_name_exists()
    {
        $manager = new Manager('custom');

        $this->assertNull(config("database.connections.custom"), "No [custom] connection configured yet");

        $manager->configure();

        $this->assertEquals('custom', $manager->getConnection());

        $this->assertEquals(
            ["driver" => "sqlite", "database" => ":memory:"],
            config("database.connections.custom")
        );
    }

    /**
     * @test
     */
    public function it_uses_custom_migrations_path()
    {
        $manager = new Manager('custom');

        $manager->migrate($this->migrations);

        // Empty users table exists
        $this->assertEquals(0, \DB::connection('custom')->table('users')->count());
    }

    /**
     * @test
     */
    public function it_seeds_with_global_factories_by_default()
    {
        $manager = new Manager('custom');

        $manager->migrate($this->migrations);

        $manager->seed(DatabaseSeeder::class);

        $this->assertNotEmpty(User::on('custom')->get());
        $this->assertNotEquals(
            'CUSTOM NAME',  // Using the global factories that don't hardcode the name
            User::on('custom')->first()->name
        );
    }

    /**
     * @test
     */
    public function it_supports_seeding_with_custom_factories()
    {
        $manager = new Manager('custom');

        $manager->migrate($this->migrations);

        $manager->seed(DatabaseSeeder::class, $this->factories);

        $this->assertNotEmpty(User::on('custom')->get());
        $this->assertEquals(
            'CUSTOM NAME',  // Name hardcoded in seeder
            User::on('custom')->first()->name
        );
    }

    /**
     * @test
     */
    public function it_leaves_default_connection_intact()
    {
        config(['database.default' => 'initial']);

        $manager = new Manager('custom');

        $this->assertEquals('initial', config('database.default'));

        // Migration
        $manager->migrate($this->migrations);
        $this->assertEquals('initial', config('database.default'));

        // Seeding
        $manager->seed(DatabaseSeeder::class);
        $this->assertEquals('initial', config('database.default'));
    }

    /**
     * @test
     */
    public function it_overrules_model_connection_property_but_leaves_intact()
    {
        $manager = new Manager('custom');

        $manager->setModel(PostWithCustomConnection::class);

        $manager->migrate($this->migrations);

        $this->assertEmpty(PostWithCustomConnection::on('custom')->get());

        $manager->seed(PostSeeder::class, __DIR__ . "/stubs/factories-custom-connection");

        $this->assertNotEmpty(PostWithCustomConnection::on('custom')->get());

        $this->assertEquals('custom-connection', (new PostWithCustomConnection)->getConnectionName());
    }

}
