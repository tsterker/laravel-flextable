<?php

namespace Tsterker\Flextable;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Faker\Generator as Faker;

use Gleif\Oais\Models\Concatenation\BaseModel;

class Manager
{

    protected $connection;

    protected $model;

    public function __construct($connection = null)
    {
        $this->connection = $connection ?? ":memory:" . rand();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function configure()
    {
        config([
            "database.connections.{$this->connection}" => [
                "driver" => "sqlite",
                "database" => ":memory:",
            ]
        ]);
    }

    public function migrate(string $migrations)
    {
        if (! is_dir($migrations)) {
            throw new \RuntimeException("Migrations path does not exist: [$migrations]");
        }

        $this->configure();

        // Remember the original default DB connection,
        // as it will be changed during migration
        $originalDefaultConn = config('database.default');

        \Artisan::call(
            'migrate:fresh',
            [
                '--database' => $this->connection,
                '--path' => $migrations,
                '--realpath' => true,
            ]
        );

        // Restore original default connection
        config(["database.default" => $originalDefaultConn]);
    }

    public function seed($seeder, $factories = null)
    {
        $this->configure();

        // Remember the original default DB connection,
        // as we need to swap it for seeding
        $originalDefaultConn = config('database.default');

        config(["database.default" => $this->connection]);

        if ($factories && ! is_dir($factories)) {
            throw new \RuntimeException("Factories path does not exist: [$factories]");
        }

        try {
            if ($factories) {
                $this->withCustomFactories($factories, function () use ($seeder) {
                    $this->runSeedCommand($seeder);
                });
            } else {
                $this->runSeedCommand($seeder);
            }
        }
        finally {
            // Restore original default connection
            config(["database.default" => $originalDefaultConn]);
        }

    }

    protected function runSeedCommand($seeder)
    {
        try {

            // TODO: TEST FOR SUPERCLASS OR TRAIT?
            $this->overrideModelConnection();

            \Artisan::call('db:seed', [
                '--class' => $seeder,
                '--database' => $this->connection,
            ]);
        } catch (\InvalidArgumentException $e) {
            // TODO: Inspect error message to ensure we are catching the right exception
            $tip = "Make sure to provide a factory path if you defined your factories in a custom location.";
            throw new \Exception($e->getMessage() . "\n$tip", 0, $e);
        } finally {
            $this->clearModelConnectionOverride();
        }
    }


    protected static function withCustomFactories($factories, \Closure $callback)
    {
        $originalFactory = app(EloquentFactory::class);

        // Create Eloquent Factory with configured factories
        // and temporarily swap it in
        $factory = EloquentFactory::construct(
            app(Faker::class),
            $factories
        );

        app()->instance(EloquentFactory::class, $factory);

        try {
            // dd(app(EloquentFactory::class));
            $callback();
        } finally {
            app()->instance(EloquentFactory::class, $originalFactory);
        }

    }

    protected function overrideModelConnection()
    {
        if ($this->model) {
            $this->model::setDefaultConnection($this->connection);
        }
    }

    protected function clearModelConnectionOverride()
    {
        if ($this->model) {
            $this->model::clearDefaultConnection();
        }
    }

}
