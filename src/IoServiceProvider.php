<?php


use Hhttp\io\common\Console\Command\DevCommand;
use Hhttp\io\common\Console\Command\LogClean;
use Hhttp\io\common\Console\Command\RunCodeCommand;
use Hhttp\io\common\Enums\SessionEnum;
use Hhttp\io\common\Middleware\ApiLogMid;
use Hhttp\io\common\Middleware\LoadConfig;
use Hhttp\io\gateway\GatewayFirstMiddleware;
use Hhttp\io\gateway\GatewayLastMiddleware;
use Hhttp\io\monitor\hm\Controllers\HHttpViewerController;
use Hhttp\io\monitor\hm\Controllers\LogViewerController;
use Hhttp\io\monitor\hm\Controllers\SqlViewerController;
use Hhttp\io\monitor\hm\Models\LogicalPipelinesModel;
use Hhttp\io\common\Support\Facade\HooSession;
use Hhttp\io\database\services\BuilderMacroSql;
use Hhttp\io\common\Middleware\HooMid;
use Hhttp\io\monitor\hm\Controllers\LogicalBlockController;
use Hhttp\io\monitor\hm\Controllers\IndexController;
use Hhttp\io\monitor\hm\Controllers\LogicalPipelinesController;
use Hhttp\io\monitor\hm\Controllers\HooLogController;
use Hhttp\io\monitor\hm\Controllers\LoginController;
use Hhttp\io\monitor\hm\Middleware\HmAuth;
use Hhttp\io\monitor\hm\Services\LogicalPipelinesApiRunService;
use Hhttp\io\gateway\GatewayController;
use Hhttp\io\gateway\GatewayMiddleware;
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

class IoServiceProvider extends ServiceProvider
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
            \hoo\io\app()->booted(function () {
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
        $key = 'hoo-io';
        $path = __DIR__."/config/{$key}.php";

        if (! $this->app->configurationIsCached()) {
            $this->app['config']->set($key, array_merge(
                require $path, $this->app['config']->get($key, [])
            ));
        }
    }
}
