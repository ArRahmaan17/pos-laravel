<?php

namespace App\Traits;

use App\Models\MenuRegister;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

trait ModuleResolver
{
    private function resolver()
    {
        $controllersPath = app_path('Http/Controllers');
        $namespace = 'App\\Http\\Controllers';

        return array_values(collect($this->methodFinder($this->fileFinder($controllersPath, $namespace)))->sortBy('name')->toArray());
    }

    private function remover(array $data, int $role, int $user)
    {
        $resultMenuRegister = MenuRegister::where('role_id', $role)->get();
    }

    protected function fileFinder(string $controllersPath, string $namespace)
    {
        $controllers = [];

        foreach (File::allFiles($controllersPath) as $file) {
            $class = $namespace.'\\'.str_replace(
                ['/', '.php'],
                ['\\', ''],
                $file->getRelativePathname()
            );

            if (class_exists($class)) {
                $controllers[] = $class;
            }
        }

        return $controllers;
    }

    protected function methodFinder(array $controllers)
    {
        $results = [];
        $routes = array_values(array_filter(collect(Route::getRoutes())->toArray(), function ($route) {
            return isset($route->action['namespace']) && $route->action['namespace'] === 'mustValidate';
        }));

        foreach ($routes as $route) {
            if ($route->action['controller']) {
                [$file_path, $_] = explode('@', $route->action['controller']);
                $reflection = new ReflectionClass($file_path);
                $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
                if ($methods) {
                    $parts = explode('.', $route->action['as']);
                    $menu_module = null;
                    $name = $parts[0];
                    if (count($parts) > 2) {
                        $menu_module = trans('menus.'.str(array_shift($parts))->kebab()->lower());
                        $name = array_shift($parts);
                    }
                    $translate_name = trans('menus.'.str($name)->snake('_')->lower());
                    $description = trans('menus.'.str($name)->snake('_')->lower().'-desc');
                    $key = ($menu_module ?? 'parent').':'.$name;
                    $results[$key] = [
                        'name' => $translate_name,
                        'unique' => md5($translate_name),
                        'module' => $menu_module,
                        'description' => $description,
                        'state' => (true) ? true : false,
                    ];
                    foreach ($methods as $index => $method) {
                        if ($method->getDeclaringClass()->getName() !== $file_path) {
                            continue;
                        }
                        if (str_starts_with($method->getName(), '__')) {
                            continue;
                        }
                        $results[$key]['route'][$index] = [
                            'route_as' => ($menu_module ? strtolower($menu_module).'.' : '').$name.'.'.$method->getName(),
                            'method' => $method->getName(),
                            'name' => __('menus.'.$method->getName()),
                            'desc' => __('menus.'.$method->getName().'-desc').' '.str($results[$key]['name'])->lower(),
                        ];
                    }
                }
            }
        }
        $results = array_filter($results, function ($result) {
            return isset($result['route']);
        });
        $module = $this->parentModuleSolver($results);
        $results = array_merge($module, array_values($results));
        $results = arrayTree($results, 'module', 'name');

        return $results;
    }

    private function parentModuleSolver(array $menus)
    {
        $results = array_map(function ($menu) {
            return $menu['module'];
        }, $menus);
        $temps = removeDuplicate($results);
        $results = [];
        foreach ($temps as $value) {
            $translate_name = trans('menus.'.str($value)->kebab()->lower());
            $description = trans('menus.'.str($value)->kebab()->lower().'-desc');
            $results[] = [
                'unique' => md5($value),
                'name' => $translate_name,
                'description' => $description,
                'module' => null,
                'state' => (true) ? true : false,
            ];
        }

        return $results;
    }
}
