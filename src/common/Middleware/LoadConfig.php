<?php

namespace hhttp\io\common\Middleware;

use Closure;
use Exception;
use hhttp\io\monitor\hm\Models\LogicalBlockModel;
use hhttp\io\monitor\hm\Support\Facades\LogicalBlock;
use Illuminate\Support\Facades\Log;

/**
 * 动态从逻辑块中加载配置
 */
class LoadConfig
{
    /**
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        # 开启动态从逻辑块中加载配置 且 逻辑块表存在
        if (config('hhttp.HP_LOAD_CONFIG_ENABLE') &&
            hoo_schema()->hasTable((new LogicalBlockModel())->getTable())) {

            $logical_block_object_id = config('hhttp.HP_LOGICAL_BLOCK_OBJECT_ID');
            try {
                // 运行逻辑块
                $config = LogicalBlock::execByObjectId($logical_block_object_id);
                if (!app()->configurationIsCached()) {
                    foreach ($config as $key => $value) {
                        app()['config']->set($key, array_merge(
                            app()['config']->get($key, []), $value
                        ));
                    }
                }
            }catch (\Throwable $e) {
                Log::channel('debug')->log('info', "配置加载失败", [$e]);
            }
        }

        return $next($request);
    }
}
