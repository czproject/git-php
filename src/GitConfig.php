<?php

namespace CzProject\GitPhp;

class GitConfig
{
    /** @var GitRepository */
    protected $repository;

    /** @var IRunner */
    protected $runner;


    public function __construct(GitRepository $repository, IRunner $runner = NULL)
    {
        $this->repository = $repository;
        $this->runner = $runner !== NULL ? $runner : new Runners\CliRunner;
    }


    /**
     * @param  string $name
     * @param  string $value
     * @param  array|string $params
     * @throws GitException
     * @return static
     */
    public function set($name, $value, $params = [])
    {
        $this->run('config', $params, (string) $name, (string) $value);
        return $this;
    }


    /**
     * @param  string $name
     * @param  array|string $params
     * @return string|null NULL if the option is not set
     * @throws GitException
     */
    public function get($name, $params = [])
    {
        try {
            $result = $this->run('config', $params, (string)$name);
            return rtrim($result->getOutputAsString());
        } catch (GitException $e) {
            if (false === $e->getRunnerResult()->hasOutput()
                && false === $e->getRunnerResult()->hasErrorOutput()
            ) {
                return NULL;
            }
            throw $e;
        }
    }


    /**
     * Runs command and returns result.
     * @param  mixed ...$args
     * @return RunnerResult
     * @throws GitException
     */
    private function run(...$args)
    {
        $result = $this->runner->run($this->repository->getRepositoryPath(), $args);

        if (!$result->isOk()) {
            throw new GitException("Command '{$result->getCommand()}' failed (exit-code {$result->getExitCode()}).", $result->getExitCode(), NULL, $result);
        }

        return $result;
    }
}