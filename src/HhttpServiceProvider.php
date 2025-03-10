<?php
namespace hhttp\io;

use hhttp\io\common\Console\Command\DevCommand;
use hhttp\io\common\Console\Command\LogClean;
use hhttp\io\common\Middleware\ApiLogMid;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Exception;

class HhttpServiceProvider extends ServiceProvider
{

    public function boot()
    {
        try{
            /**
             * 注册中间件
             */
            $this->registerMiddleware();

            /**
             * 注册命令
             */
            $this->registerCommands();

            # 在所有服务提供者 boot 完成后执行【用于覆盖之前注册的路由 已动态注册路由为第一优先级】
            app()->booted(function () {
                /**
                 * 动态注册路由
                 */
//                $this->registerRoutes();
            });

        }catch (\Exception $e){}
    }

    public function register()
    {
        try{
            # 加载配置
            $this->registerConfig();

        }catch (Exception $e){}
    }


    /**
     * 注册中间件
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function registerMiddleware()
    {
        //注册中间件-默认运行
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(ApiLogMid::class);
    }

    /**
     * 注册命令
     * @return void
     */
    public function registerCommands()
    {
        //注册命令
        if ($this->app->runningInConsole()){
//            # 将自定义的Console\Kernel 绑定到 artisan schedule:run
//            # 无侵入实现在自定义的Console\Kernel 内定义定时
//            $this->app->singleton(
//                \Illuminate\Contracts\Console\Kernel::class,
//                \hoo\io\common\Console\Kernel::class
//            );

            $this->commands([
                DevCommand::class,
                LogClean::class,
            ]);
        }
    }

    public function registerConfig()
    {
        $key = 'hhttp';
        $path = __DIR__."/config/{$key}.php";

        if (! $this->app->configurationIsCached()) {
            $this->app['config']->set($key, array_merge(
                require $path, $this->app['config']->get($key, [])
            ));
        }
    }
}
