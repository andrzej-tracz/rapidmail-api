<?php

namespace App\Infrastructure\Bus\Middleware;

use League\Tactician\Bundle\Middleware\InvalidCommandException;
use League\Tactician\Middleware;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements Middleware
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function execute($command, callable $next)
    {
        try {
            $clazz = get_class($command);
            $start = microtime(true);

            $this->logger->info('START: '.$clazz);

            $result = $next($command);
            $end = microtime(true);

            $diff = $end - $start;

            $this->logger->info("SUCCESS $clazz after $diff second");

            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('Error handling: '.get_class($command));
            $this->logger->error('Error: '.$e);

            if ($e instanceof InvalidCommandException) {
                foreach ($e->getViolations() as $violation) {
                    $this->logger->error($violation->getPropertyPath().' '.$violation->getMessage());
                }
            }

            throw $e;
        }
    }
}
