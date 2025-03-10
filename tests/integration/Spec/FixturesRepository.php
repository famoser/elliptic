<?php

namespace Mdanter\Ecc\Integration\Spec;

use Symfony\Component\Yaml\Yaml;

class FixturesRepository
{
    private const FIXTURES_DIR = __DIR__ . DIRECTORY_SEPARATOR.'fixtures';

    /**
     * @var array<string,string>
     */
    private static array $fileCache = [];

    private static function parseFile(string $fileName): array
    {
        if (!isset(self::$fileCache[$fileName])) {
            self::$fileCache[$fileName] = Yaml::parseFile($fileName);
        }

        return self::$fileCache[$fileName];
    }

    public static function read(string $type): array
    {
        $fixtureCollectionFiles = array_diff(scandir(self::FIXTURES_DIR), array('..', '.'));

        $results = [];
        foreach ($fixtureCollectionFiles as $fixtureCollectionFile) {
            $fixtureCollection = self::parseFile(self::FIXTURES_DIR.DIRECTORY_SEPARATOR.$fixtureCollectionFile);
            if (!array_key_exists($type, $fixtureCollection)) {
                continue;
            }

            $results[] = [
                'file' => $fixtureCollectionFile,
                'curve' => $fixtureCollection['curve'],
                'fixtures' => $fixtureCollection[$type]
            ];
        }

        return $results;
    }
}
