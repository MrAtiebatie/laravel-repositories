<?php

namespace MrAtiebatie\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * Default model on stub
     */
    const MODEL_DEFAULT = 'App\Your\Model';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {class : Your repository class name.} {--M|model='.self::MODEL_DEFAULT.' : The model that will be use on your repository, write the full namespace.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository class into App/Repositories folder.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';


    /**
     * The namespace of model being generated.
     *
     * @var string
     */
    private $modelNamespace;

    /**
     * The model from the $modelNamespace
     *
     * @var string
     */
    private $model;

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): ?bool
    {
        $this->setRepositoryClass();

        $path = $this->getPath($this->qualifyClass($this->class));

        if ($this->alreadyExists($this->class)) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($this->class));

        $this->info($this->type.' created successfully.');

        $this->line("<info>Created Repository :</info> $this->class");

        return null;
    }

    /**
     * Set repository class name and related model
     */
    private function setRepositoryClass(): MakeRepositoryCommand
    {
        $this->class = ucfirst($this->argument('class'));

        $this->modelNamespace = $this->option('model');

        $arrModelNamespace = explode('\\', $this->modelNamespace);
        $this->model = $arrModelNamespace[count($arrModelNamespace) -1];

        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name): string
    {
        if (!$this->argument('class')) {
            throw new InvalidArgumentException("Missing required argument class name");
        }

        $stub = parent::replaceClass($stub, $name);
        $stub = str_replace('[model_namespace]', $this->modelNamespace, $stub);

        if ($this->option('model') === self::MODEL_DEFAULT) {
            $stub = str_replace('[comment_namespace_when_no_model]', '// Don\'t forget to update the model\'s namespace', $stub);
            $stub = str_replace('[comment_when_no_model]', '// Don\'t forget to update the model\'s name', $stub);
        } else {
            $stub = str_replace('[comment_namespace_when_no_model]', '', $stub);
            $stub = str_replace('[comment_when_no_model]', '// setup the model', $stub);
        }

        return str_replace('[model]', $this->model, $stub);
    }

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/Repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Repositories';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::REQUIRED, 'Your repository class name.'],
        ];
    }
}
