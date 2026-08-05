<?php

namespace App\Helpers;

class Plugins
{

    public static function getPluginsList()
    {
        $folders = scandir(__DIR__ . '/../../Plugins/');
        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $pluginRoutesPath = base_path('Plugins/' . $folder . '/routes/route.php');
            if (file_exists($pluginRoutesPath) && is_readable($pluginRoutesPath)) {
                echo "<li><a href='/Plugins/$folder/index' class='slide-item'>$folder</a></li>";
            } else {
                echo "<li><a href='#' class='slide-item'>not found</a></li>";
            }
        }
    }
}
