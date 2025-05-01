<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ExtractTranslations extends Command
{
    protected $signature = 'translations:extract';
    protected $description = 'Extracts all translation strings and updates the api language files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $keys = $this->extractTranslations(resource_path('views'), "__\('([^']*)'\)");
        $keys = array_merge($keys, $this->extractTranslations(app_path(), "__\('([^']*)'\)"));

        // Handle API translations
        $apiKeys = array_filter($keys, function ($key) {
            return strpos($key, 'api.') === 0;
        });

        $this->updateApiLanguageFile('en', $apiKeys);
        $this->updateApiLanguageFile('ar', $apiKeys);

        $this->info("API translations have been updated for 'en' and 'ar'.");
    }

    private function extractTranslations($directory, $pattern)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $regex = "/$pattern/";

        $keys = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/\.(php|blade\.php)$/', $file->getFilename())) {
                $content = file_get_contents($file->getPathname());
                preg_match_all($regex, $content, $matches);

                if (!empty($matches[1])) {
                    $keys = array_merge($keys, $matches[1]);
                }
            }
        }

        return array_unique($keys);
    }

    private function updateApiLanguageFile($locale, $keys)
    {
        $translationFile = resource_path("lang/$locale/api.php");
        $existingTranslations = [];

        if (file_exists($translationFile)) {
            $existingTranslations = include $translationFile;
        }

        $newTranslations = $existingTranslations;

        foreach ($keys as $key) {
            $cleanKey = str_replace('api.', '', $key); // Remove the 'api.' prefix
            if (!isset($existingTranslations[$cleanKey])) {
                $newTranslations[$cleanKey] = $locale === 'en' ? $cleanKey : '';
            }
        }

        // Write the existing translations followed by new translations
        $content = "<?php\n\nreturn [\n";

        foreach ($existingTranslations as $key => $value) {
            $content .= "    '$key' => '$value',\n";
        }

        $content .= "// New translations\n";

        foreach (array_diff_assoc($newTranslations, $existingTranslations) as $key => $value) {
            $content .= "    '$key' => '$value',\n";
        }

        $content .= "];\n";

        file_put_contents($translationFile, $content);

        $this->info("API translations extracted and added to $translationFile.");
    }
}
