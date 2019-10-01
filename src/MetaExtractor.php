<?php declare(strict_types=1);

namespace ImageViewer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class MetaExtractor
{
    /** @var Database */
    private $database;

    /** @var string */
    private $path;

    /** @var array */
    private $tags;

    public function __construct(Database $database, string $path)
    {
        $this->database = $database;
        $this->path = $path;
    }

    public function parse(OutputInterface $output, array $newFiles): array
    {
        $progressBar = new ProgressBar($output, count($newFiles));
        $progressBar->setFormat('Tags:      [%bar%] %memory:6s%');
        $progressBar->start();

        $this->tags = $this->database->getLocations();
        foreach ($newFiles as $newFile) {
            $progressBar->advance();
            $this->getTags($newFile);
        }
        $progressBar->advance();
        $progressBar->finish();

        $output->write(PHP_EOL);
        return $this->database->getTags(true);
    }

    private function getTags(string $file): void
    {
        $tags = [];
        getimagesize($file, $info);
        if(is_array($info) && isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if(isset($iptc['2#025'])&&is_array($iptc['2#025'])) {
                $tags = $iptc['2#025'];
            }
        }

        foreach ($tags as $tag) {
            if(!in_array($tag, $this->tags)) {
                $this->tags[] = $tag;
                $this->database->insert('tags', ['name' => $tag]);
            }
        }
    }
}
