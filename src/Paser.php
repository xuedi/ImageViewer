<?php declare(strict_types=1);

namespace ImageViewer;

// TODO: To be put into a nice service & command

class Paser
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    function buildImageTree()
    {
        echo PHP_EOL;
        echo 'buildTree: ... ';
        $tree = [];
        $locations = $this->getFolders($this->path);
        foreach ($locations as $location) {
            $path = $this->path . $location;
            $tree[$location] = $this->getEvents($path);
        }
        $json = json_encode($tree, JSON_PRETTY_PRINT);
        file_put_contents('../data/tree.json', $json);
        echo 'OK' . PHP_EOL;

        $this->buildMenuCache(
            $this->buildFileList(),
            $tree
        );
    }

    private function getEvents(string $path)
    {
        $events = [];
        $folders = $this->getFolders($path);
        foreach ($folders as $folder) {
            $eventPath = $path . '/' . $folder . '/';
            $eventHash = sha1($eventPath);
            list($eventDate, $eventName) = $this->processEventFolder($folder);

            $fileList = [];
            foreach ($this->getFiles($eventPath) as $file) {
                $fileList[sha1($file)] = str_replace($path . '/' . $folder . '/', '', $file);
            }

            $events[] = [
                'hash' => $eventHash,
                'name' => $eventName,
                'path' => $eventPath,
                'date' => $eventDate,
                'files' => $fileList,
            ];
        }

        return $events;
    }

    function getFiles($dir, &$results = [])
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->getFiles($path, $results);
            }
        }

        return $results;
    }

    private function getFolders(string $path)
    {
        $locations = [];
        $folders = scandir($path);
        foreach ($folders as $folder) {
            if (!is_dir($path . '/' . $folder)) {
                continue;
            }
            if ($folder == "." || $folder == "..") {
                continue;
            }
            $locations[] = $folder;
        }
        return $locations;
    }

    private function processEventFolder(string $folder): array
    {
        $chunks = explode(' ', $folder);
        $dateRaw = array_shift($chunks);
        $fileName = implode(' ', $chunks);

        $dateChunks = explode('-', $dateRaw);
        if (empty($dateChunks)) {
            die('Cound not get date for event: ' . $folder);
        }
        for ($i = 1; $i < 3; $i++) {
            if (!isset($dateChunks[$i])) {
                $dateChunks[$i] = '00';
            }
        }
        $date = implode('-', $dateChunks);

        return [$date, $fileName];
    }

    private function buildFileList(): array
    {
        echo 'updateFileList: ';

        $storage = '../data/files.json';
        if (file_exists($storage)) {
            $filesJson = file_get_contents($storage);
            $fileList = json_decode($filesJson, true);
        } else {
            $fileList = [];
        }

        $hashMap = [];
        $files = $this->getFiles($this->path);
        $step = round(count($files) / 10);
        $count = 0;
        foreach ($files as $fileName) {
            $fileHash = sha1($fileName);
            if (!isset($fileList[$fileHash])) {
                $fileList[$fileHash] = [
                    'fileName' => $fileName,
                    'fileHash' => $fileHash,
                ];
            } else {
                if (!file_exists($fileName)) {
                    unset($fileList[$fileHash]);
                }
            }
            if ($count > $step) {
                $count = 0;
                echo '.';
            }

            $meta = $this->getMetaTags($fileName);
            $fileList[$fileHash]['meta'] = empty($meta) ? [] : $meta;

            $hashMap[$fileHash] = $fileName;
            $count++;

        }

        $filesJson = json_encode($fileList, JSON_PRETTY_PRINT);
        file_put_contents($storage, $filesJson);

        $this->buildHashMap($fileList);

        echo 'OK' . PHP_EOL;
        return $fileList;
    }

    private function getMetaTags(string $file): array
    {
        $returnExif = [];
        $imageExif = exif_read_data($file);
        if ($imageExif) {
            $returnExif = [
                'fileName' => $imageExif['FileName'] ?? null,
                'dateTime' => $imageExif['DateTime'] ?? null,
                'orientation' => $imageExif['Orientation'] ?? null,
            ];
        }
        getimagesize($file, $info);
        if(is_array($info) && isset($info["APP13"])) {
            $iptc = iptcparse($info["APP13"]);
            if(isset($iptc['2#025'])&&is_array($iptc['2#025'])) {
                $returnExif['tags'] = $iptc['2#025'];
            }
        }
        return $returnExif;
    }

    private function buildHashMap(array $fileMap)
    {
        $hashMap = [];
        foreach ($fileMap as $file) {
            $hashMap[$file['fileHash']] = $file['fileName'];
        }

        $file = '../data/HashMap.json';
        file_put_contents($file, json_encode($hashMap, JSON_PRETTY_PRINT));
    }

    private function buildMenuCache(array $fileList, array $tree)
    {
        foreach ($tree as $locationName => $locationNameData) {
            foreach ($locationNameData as $eventData) {
                $this->buildEventJson($locationName, $eventData, $fileList);
            }
        }
    }

    private function buildEventJson(string $locationName, array $eventData, array $fileList)
    {
        $images = [];
        foreach ($eventData['files'] as $hash => $name) {
            $thumb = $hash . '.jpg';
            $src = $fileList[$hash]['fileHash'];
            if (strpos($name, '/')) {
                $name = substr($name, strpos($name, '/') + 1);
            }
            $images[] = [
                'name' => $name,
                'thumb' => $thumb,
                'src' => $src,
            ];
        }
        $pageData = [
            'title' => $locationName . ' - ' . $eventData['name'],
            'date' => $eventData['name'],
            'images' => $images,
        ];

        $cacheFile = '../public/cache/events/' . $eventData['hash'] . '.json';
        $filesJson = json_encode($pageData, JSON_PRETTY_PRINT);
        file_put_contents($cacheFile, $filesJson);
    }
}
