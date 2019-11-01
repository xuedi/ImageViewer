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

    /** @var array */
    private $tagGroup;

    public function __construct(Database $database, string $path, array $tagGroup)
    {
        $this->database = $database;
        $this->path = $path;
        $this->tagGroup = $this->buildLookup($tagGroup);
    }

    public function parse(OutputInterface $output, array $newFiles): array
    {
        $progressBar = new ProgressBar($output, count($newFiles));
        $progressBar->setFormat('Tags:      [%bar%] %memory:6s%');
        $progressBar->start();

        $this->tags = $this->database->getTags();
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
        if (is_array($info) && isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if (isset($iptc['2#025']) && is_array($iptc['2#025'])) {
                $tags = $iptc['2#025'];
            }
        }

        foreach ($tags as $tag) {
            $tagGroup = $this->getTagGroupId($tag);
            if (!in_array($tag, $this->tags) && $tagGroup != 1) {
                $this->tags[] = $tag;
                $this->database->insert('tags', [
                    'name' => $tag,
                    'tag_group_id' => $tagGroup
                ]);
            } else {
                echo PHP_EOL . "No TagGroup for '$tag'" . PHP_EOL;
            }
        }
    }

    private function getTagGroupId(string $tag): int
    {
        $tag = strtolower(trim($tag));
        if (!isset($this->tagGroup[$tag])) {
            //throw new RuntimeException("Unknown Tag '$tag'");
            //echo $tag.PHP_EOL;
            return 1;
        }
        return $this->tagGroup[$tag];
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
                $group = strtolower(trim($group));
                $retVal[$tag] = $groupLookup[$group];
            }
        }
        return $retVal;
    }
}
