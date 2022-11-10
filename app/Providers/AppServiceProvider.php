<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(){

    }

    public function boot()
    {
        If (env('APP_ENV') != 'local') {
            $this->app['request']->server->set('HTTPS', true);
        }

        Blade::directive('require', function ($args) {
            $args = Blade::stripParentheses($args);
            $viewBasePath = Blade::getPath();
            foreach ($this->app['config']['view.paths'] as $path) {
                if (substr($viewBasePath,0,strlen($path)) === $path) {
                    $viewBasePath = substr($viewBasePath,strlen($path));
                    break;
                }
            }

            $viewBasePath = dirname(trim($viewBasePath,'\/'));
            $pathArray = explode('/', $viewBasePath);
            $count = count($pathArray) - substr_count($args, "../");
            $path = [];
            for($i=0; $i < $count; $i++){ $path[] = $pathArray[$i]; }
            $viewBasePath = implode('/', $path);
            $args = str_replace('../','',$args);
            $args = substr_replace($args, $viewBasePath.'.', 1, 0);
            return "<?php echo \$__env->make({$args}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });
    }
}
