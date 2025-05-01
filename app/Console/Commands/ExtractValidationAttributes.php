<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractValidationAttributes extends Command
{
    protected $signature = 'extract:attributes';

    protected $description = 'Extract all attributes from validation requests and add them to validation.php';

    public function handle()
    {
        // Path with wildcard for modules
        $requestPaths = glob(app_path('Modules/*/Requests'));

        $langFile = resource_path('lang/en/validation.php');

        // Load the existing validation.php content
        $validation = File::exists($langFile) ? include $langFile : [];
        $attributes = $validation['attributes'] ?? [];

        // Iterate through all request files in each module's Requests directory
        foreach ($requestPaths as $requestPath) {
            foreach (File::allFiles($requestPath) as $file) {
                $className = $this->getClassFromFile($file);
                if ($className && class_exists($className)) {
                    $attributes = array_merge($attributes, $this->extractAttributesFromFile($file));
                }
            }
        }

        // Update the validation.php file
        $validation['attributes'] = $attributes;
        File::put($langFile, '<?php return ' . var_export($validation, true) . ';');

        $this->info('Attributes have been successfully extracted and added to validation.php!');
    }

    private function getClassFromFile($file)
    {
        $content = File::get($file);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/', $content, $matches)) {
                return $namespace . '\\' . $matches[1];
            }
        }
        return null;
    }

    private function extractAttributesFromFile($file)
    {
        $content = File::get($file);
        $attributes = [];

        if (preg_match('/return\s*\[\s*(.*)\s*\]\s*;/sU', $content, $matches)) {
            $rulesArray = $matches[1];

            // Extract array keys from the rules array string
            preg_match_all('/\'([^\']+)\'\s*=>/', $rulesArray, $attributeMatches);

            foreach ($attributeMatches[1] as $attribute) {
                $attributes[$attribute] = ucfirst($attribute);
            }
        }

        return $attributes;
    }
}
