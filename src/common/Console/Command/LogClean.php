<?php
namespace hhttp\io\common\Console\Command;

use hhttp\io\common\Models\ApiLogModel;
use hhttp\io\common\Models\HttpLogModel;
use hhttp\io\common\Models\SqlLogModel;

class LogClean extends BaseCommand
{
    protected $signature = 'hp:LogClean';

	// Command description
	protected $description = '日志清理';

	// Execute the console command
	public function handle()
	{
        # 获取清理时间 即多久之前的日志需清理
        $apiCleanDay = config('hhttp.HM_API_LOG_CLEAN');
        if (!empty($apiCleanDay) and hoo_schema()->hasTable((new ApiLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $apiCleanDate = date('Y-m-d', strtotime('-'.$apiCleanDay.' days')).' 00:00:00';
            # api log 清理
            ApiLogModel::query()->where('created_at', '<', $apiCleanDate)->delete();
        }
        $hhttpCleanDay = config('hhttp.HM_HHTTP_LOG_CLEAN');
        if (!empty($hhttpCleanDay) and hoo_schema()->hasTable((new HttpLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $hhttpCleanDate = date('Y-m-d', strtotime('-'.$hhttpCleanDay.' days')).' 00:00:00';
            # hhttp log 清理
            HttpLogModel::query()->where('created_at', '<', $hhttpCleanDate)->delete();
        }
        $sqlCleanDay = config('hhttp.HM_SQL_LOG_CLEAN');
        if (!empty($sqlCleanDay) and hoo_schema()->hasTable((new SqlLogModel())->getTable())) {
            # 获取日志需清理 的日期
            $sqlCleanDate = date('Y-m-d', strtotime('-'.$sqlCleanDay.' days')).' 00:00:00';
            # sql log 清理
            SqlLogModel::query()->where('created_at', '<', $sqlCleanDate)->delete();
        }
	}
}
