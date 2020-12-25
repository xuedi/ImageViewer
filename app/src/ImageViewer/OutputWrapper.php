<?php declare(strict_types=1);

namespace ImageViewer;

class OutputWrapper
{
    private array $headers = [];

    /**
     * @codeCoverageIgnore
     */
    public function addHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    public function echo(string $output)
    {
        foreach ($this->headers as $header) {
            // @codeCoverageIgnoreStart
            header($header);
            // @codeCoverageIgnoreEnd
        }

        echo $output;
    }


}
