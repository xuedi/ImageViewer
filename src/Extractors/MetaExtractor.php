<?php declare(strict_types=1);

namespace ImageViewer\Extractors;

use ImageViewer\Database;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class MetaExtractor
{
    private array $tags;
    private array $tagGroup;
    private string $path;
    private Database $database;
    private OutputInterface $output;

    public function __construct(Database $database, OutputInterface $output, string $path, array $tagGroup)
    {
        $this->database = $database;
        $this->output = $output;
        $this->path = $path;
        $this->tagGroup = $this->buildLookup($tagGroup);
    }

    public function parse(array $newFiles): array
    {
        $progressBar = new ProgressBar($this->output, count($newFiles));
        $progressBar->setFormat('Tags:      [%bar%] %memory:6s%');
        $progressBar->start();

        $this->tags = $this->database->getTags();

        foreach ($newFiles as $newFile) {
            $progressBar->advance();
            $tags = $this->getTags($newFile);
            $this->saveTags($tags);
        }
        $progressBar->advance();
        $progressBar->finish();

        $this->output->write(PHP_EOL);
        return $this->database->getTags(true);
    }

    public function getTags(string $file): array
    {
        $tags = [];
        getimagesize($file, $info);
        if (is_array($info) && isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if (isset($iptc['2#025']) && is_array($iptc['2#025'])) {
                foreach ($iptc['2#025'] as $tag) {
                    $tags[] = strtolower($tag);
                }
            }
        }

        return $tags;
    }

    private function saveTags(array $tags): void
    {
        $knownTags = $this->database->getTags(true);
        foreach ($tags as $tag) {
            $tagGroup = $this->getTagGroupId($tag);
            if (!isset($knownTags[$tag])) {
                $knownTags[$tag] = $this->database->insert('tags', [
                    'name' => $tag,
                    'tag_group_id' => $tagGroup
                ]);
            }
        }
    }

    private function getTagGroupId(string $tag): int
    {
        $tag = strtolower(trim($tag));
        if (!isset($this->tagGroup[$tag])) {
            return 1; // unknown
        }
        return (int)$this->tagGroup[$tag];
    }

    private function buildLookup(array $tagGroup): array
    {
        $groupLookup = [
            'unknown' => 1,
            'country' => 2,
            'city' => 3,
            'people' => 4,
            'madeby' => 5,
            'misc' => 6,
            'year' => 7,
            'event' => 8,
        ];
        $retVal = [];
        foreach ($tagGroup as $group => $values) {
            foreach ($values as $tag) {
                $tag = strtolower(trim($tag));
                $group = strtolower(trim((string)$group));
                $retVal[$tag] = $groupLookup[(string)$group];
            }
        }

        return $retVal;
    }
}
