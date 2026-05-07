<?php

declare(strict_types=1);

namespace MailerPress\Services;

\defined('ABSPATH') || exit;

class ThemeStyles
{
    public function getThemeStyles(): array
    {
        return $this->readJsonFilesFromDirectory(get_template_directory() . '/styles');
    }

    public function loadJsonSettings()
    {
        $jsonFile = get_template_directory() . '/mailerpress/blocks.json';
        if (file_exists($jsonFile)) {
            return json_decode(file_get_contents($jsonFile), true);
        }

        return null;
    }

    private function readJsonFilesFromDirectory($directoryPath): array
    {
        $result = [];

        // Add the default theme under 'theme'
        $result['Core'] = array_merge(
            ['title' => 'Default'],
            \WP_Theme_JSON_Resolver::get_merged_data('theme')->get_raw_data()
        );

        $variations = \WP_Theme_JSON_Resolver::get_style_variations();
        $unique_variations = [];


        foreach ($variations as $variation) {
            $title = $variation['title'] ?? '';
            if (!isset($unique_variations[$title])) {
                $unique_variations[$title] = $variation;
            }
        }


        foreach ($unique_variations as $title => $variation) {
            if (!empty($variation['settings']['color'])) {

                $result[$title] = $this->deepMerge(
                    \WP_Theme_JSON_Resolver::get_merged_data('theme')->get_raw_data(),
                    $variation
                );
            }
        }

        return $result;
    }

    private function deepMerge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (\is_array($value) && isset($array1[$key]) && \is_array($array1[$key])) {
                $array1[$key] = $this->deepMerge($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }
}
