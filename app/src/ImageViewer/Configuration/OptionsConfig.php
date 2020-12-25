<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

use ImageViewer\Ensure;
use RuntimeException;

class OptionsConfig
{
    use Ensure;

    private int $threads;

    static public function fromParameters(array $parameter): self
    {
        self::ensureParameter($parameter, ['threads']);
        self::ensureReasonableThreads($parameter['threads']);

        return new self(
            (int)$parameter['threads']
        );
    }

    public function getThreads(): int
    {
        return $this->threads;
    }

    private function __construct(int $threads)
    {
        $this->threads = $threads;
    }

    private static function ensureReasonableThreads($threads)
    {
        if ($threads <= 0) {
            throw new RuntimeException('You have to have minimum of 1 worker (config:options:thread)');
        }
        if ($threads > 256) {
            throw new RuntimeException('Assuming most system have not that many cores (>256), check your config');
        }
    }
}
