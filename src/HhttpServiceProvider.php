<?php
namespace hhttp\io;

use hhttp\io\common\Console\Command\DevCommand;
use hhttp\io\common\Console\Command\LogClean;
use hhttp\io\common\Middleware\ApiLogMid;
use hhttp\io\common\Enums\SessionEnum;
use hhttp\io\common\Support\Facade\HooSession;
use hhttp\io\common\Middleware\HooMid;
use hhttp\io\common\Middleware\LoadConfig;
use hhttp\io\gateway\GatewayFirstMiddleware;
use hhttp\io\gateway\GatewayLastMiddleware;
use hhttp\io\gateway\GatewayMiddleware;
use hhttp\io\monitor\hm\Middleware\HmAuth;
use hhttp\io\gateway\GatewayController;
use hhttp\io\monitor\hm\Controllers\HHttpViewerController;
use hhttp\io\monitor\hm\Controllers\HooLogController;
use hhttp\io\monitor\hm\Controllers\IndexController;
use hhttp\io\monitor\hm\Controllers\LogicalBlockController;
use hhttp\io\monitor\hm\Controllers\LogicalPipelinesController;
use hhttp\io\monitor\hm\Controllers\LoginController;
use hhttp\io\monitor\hm\Controllers\LogViewerController;
use hhttp\io\monitor\hm\Controllers\SqlViewerController;
use hhttp\io\monitor\hm\Models\LogicalPipelinesModel;
use hhttp\io\monitor\hm\Services\LogicalPipelinesApiRunService;
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
             * 注册权限
             */
            $this->registerAuth();

            /**
             * 注册中间件
             */
            $this->registerMiddleware();

            /**
             * 注册 路由
             */
            $this->registerWebRoutes();

            /**
             * 注册命令
             */
            $this->registerCommands();

            # 在所有服务提供者 boot 完成后执行【用于覆盖之前注册的路由 已动态注册路由为第一优先级】
            app()->booted(function () {
                /**
                 * 动态注册路由
                 */
                $this->registerRoutes();
            });

        }catch (\Exception $e){}
    }

    /**
     * 注册权限
     * @return void
     */
    public function registerAuth()
    {
        /**
         * 定义权限 是否登录
         */
        Gate::define('hooAuth', function ($user = null) {
            # true 设置不开启 默认开启
            if(empty(Config::get('hhttp.HP_ENABLE'))){
                return false;
            }
            # true 设置是否开启登录校验
            if(empty(Config::get('hhttp.IS_LOGIN'))){
                return true;
            }
            if(!\hhttp\io\common\Support\Facade\HooSession::get(\hhttp\io\common\Enums\SessionEnum::USER_INFO_KEY)){
                return false;
            }
            /**
             * 限制环境
             * local 开发环境可进
             * test 测试环境可进
             * production 生产环境 且请求头中有灰度标识可进
             * 其它环境不可进
             */
            if(!in_array(env('APP_ENV'), ['local', 'test', 'production'])) {
                return false;
            }
            if(env('APP_ENV') == 'production' && request()->header('x1-gp-color') != 'gray') {
                return false;
            }
            return true;
        });
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
        $kernel->pushMiddleware(LoadConfig::class);
        $kernel->pushMiddleware(ApiLogMid::class);
        $kernel->pushMiddleware(HooMid::class);
        //注册中间件-路由中引用执行【鉴权中间件】
        Route::aliasMiddleware('hhttp.auth',HmAuth::class);
        Route::aliasMiddleware('hhttp.gateway.first',GatewayFirstMiddleware::class);
        Route::aliasMiddleware('hhttp.gateway',GatewayMiddleware::class);
        Route::aliasMiddleware('hhttp.gateway.last',GatewayLastMiddleware::class);
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
//                \hhttp\io\common\Console\Kernel::class
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

    /**
     * 注册 路由
     * @return void
     */
    public function registerWebRoutes()
    {
        /**
         * 服务代理
         */
        Route::prefix('api/gateway')
            ->middleware([
                'hhttp.gateway.first',
                'hhttp.gateway',
                'hhttp.gateway.last',
            ])
            ->group(function (){
                Route::post('send', [GatewayController::class, 'gateway']);
            });

        Route::prefix('api/hm')->group(function (){

            Route::post('login', [LoginController::class,'login']);
            Route::post('logout', [LoginController::class,'logout']);
            Route::prefix('login')->group(function (){
                Route::get('index',[LoginController::class,'index']);
            });

            Route::middleware('hhttp.auth')->group(function (){

                Route::get('index',[IndexController::class,'index']);
                Route::post('send-command',[IndexController::class,'sendCommand']);

                Route::get('run-command',[IndexController::class,'runCommand']);
                Route::post('run-command',[IndexController::class,'runCommand']);

                Route::prefix('hoo-log')->group(function (){
                    Route::get('index',[HooLogController::class,'index']);
                    Route::get('get-path-tree',[HooLogController::class,'getPathTree']);
                    Route::get('search',[HooLogController::class,'search']);
                    Route::get('del',[HooLogController::class,'del']);
                });

                Route::prefix('log-viewer')->group(function (){
                    Route::get('index',[LogViewerController::class,'index']);
                    Route::get('seven-visits',[LogViewerController::class,'sevenVisits']);
                    Route::get('details',[LogViewerController::class,'details']);
                    Route::get('service-statistics-item',[LogViewerController::class,'serviceStatisticsItem']);
                    Route::post('show-log',[LogViewerController::class,'showLog']);
                    Route::get('seven-visits-item',[LogViewerController::class,'sevenVisitsItem']);
                    Route::get('disk-usage',[LogViewerController::class,'diskUsage']);
                    Route::get('bandwidth-statistics-item',[LogViewerController::class,'bandwidthStatisticsItem']);
                });

                Route::prefix('hhttp-log-viewer')->group(function (){
                    Route::get('index',[HHttpViewerController::class,'index']);
                    Route::get('seven-visits',[HHttpViewerController::class,'sevenVisits']);
                    Route::get('details',[HHttpViewerController::class,'details']);
                    Route::get('service-statistics-item',[HHttpViewerController::class,'serviceStatisticsItem']);Route::get('seven-visits-item',[LogViewerController::class,'sevenVisitsItem']);
                    Route::get('seven-visits-item',[HHttpViewerController::class,'sevenVisitsItem']);
                });

                Route::prefix('sql-log-viewer')->group(function (){
                    Route::get('index',[SqlViewerController::class,'index']);
                    Route::get('seven-visits',[SqlViewerController::class,'sevenVisits']);
                    Route::get('details',[SqlViewerController::class,'details']);
                    Route::get('service-statistics-item',[SqlViewerController::class,'serviceStatisticsItem']);
                });

                Route::prefix('logical-block')->group(function (){
                    Route::get('index',[LogicalBlockController::class,'index']);
//                    Route::get('list',[LogicalBlockController::class,'list']);
                    Route::get('detail',[LogicalBlockController::class,'detail']);
                    Route::post('save',[LogicalBlockController::class,'save']);
                    Route::post('delete',[LogicalBlockController::class,'delete']);
                    Route::post('run',[LogicalBlockController::class,'run']);
                    Route::post('copy-new',[LogicalBlockController::class,'copyNew']);
                    Route::get('copy',[LogicalBlockController::class,'copy']);
                    Route::get('paste',[LogicalBlockController::class,'paste']);
                    Route::post('paste',[LogicalBlockController::class,'paste']);
                });

                Route::prefix('logical-pipelines')->group(function (){
                    Route::get('index',[LogicalPipelinesController::class,'index']);
                    Route::get('save',[LogicalPipelinesController::class,'save']);
                    Route::post('save',[LogicalPipelinesController::class,'save']);
                    Route::post('delete',[LogicalPipelinesController::class,'delete']);
                    Route::post('run',[LogicalPipelinesController::class,'run']);
                    Route::get('arrange',[LogicalPipelinesController::class,'arrange']);
                    Route::get('add-arrange-item',[LogicalPipelinesController::class,'addArrangeItem']);
                    Route::post('add-arrange-item',[LogicalPipelinesController::class,'addArrangeItem']);
                    Route::post('delete-arrange',[LogicalPipelinesController::class,'arrangeDelete']);
                    Route::post('edit-arrange',[LogicalPipelinesController::class,'arrangeEdit']);
                });
            });
        });

        Route::prefix('api/hmr/hm')->group(function (){
            Route::get('{path}',[IndexController::class,'webAsset'])->where('path', '.+');
        });
    }

    /**
     * 动态注册路由【逻辑线】
     * @return void
     */
    public function registerRoutes()
    {
        try{
            # 检查表是否存在
            if (hoo_schema()->hasTable((new LogicalPipelinesModel())->getTable())) {
                $pipelines = LogicalPipelinesModel::query()
                    ->where(function (Builder $q){
                        $q->whereNull('deleted_at')
                            ->orWhere('deleted_at','');
                    })
                    ->get();
                foreach ($pipelines as $pipeline){
                    Route::prefix($pipeline->group)->group(function () use ($pipeline){

                        $pipeline->setting = json_decode($pipeline->setting,true);
                        $method = $pipeline->setting['method'] ?? 'get';

                        $middleware = $pipeline->setting['middleware']??'';
                        if($middleware){
                            Route::middleware($middleware)->group(function () use ($method, $pipeline){
                                Route::{$method}($pipeline->rec_subject_id, function () use ($pipeline){
                                    return (new LogicalPipelinesApiRunService())->run(Request(),$pipeline);
                                });
                            });
                        }else{
                            Route::{$method}($pipeline->rec_subject_id, function () use ($pipeline){
                                return (new LogicalPipelinesApiRunService())->run(Request(),$pipeline);
                            });
                        }
                    });
                }
            }
        }catch (Exception $e){}
    }

}
