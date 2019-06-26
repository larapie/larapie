<?php

namespace App\Packages\IdeHelper\Manager;

use Illuminate\Support\Str;
use Larapie\Core\Internals\Module;
use Larapie\Core\Support\Facades\Larapie;

class IdeHelperManager
{
    public static function getModelPaths()
    {
        return collect(Larapie::getModules())->map(function (Module $module) {
            if (file_exists($module->getModels()->getPath()))
                return Str::replaceFirst(base_path(), '', $module->getModels()->getPath());
        })->filter(function ($path) {
            return $path !== null;
        })->flatten()->toArray();
    }
}
