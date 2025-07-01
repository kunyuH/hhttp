<?php

namespace hhttp\io\common\Console;

use hhttp\io\common\Console\Command\LogClean;
use Illuminate\Console\Scheduling\Schedule;
use Clockwork\Support\Laravel\ClockworkCleanCommand;

class Kernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        try {

           // 本定时任务的作用是清理过期日志文件 [api;hhttp;sql] 每天执行一次
            $schedule->command(LogClean::class)->daily();

            // 本定时任务的作用是清理过期日志文件
            // 具体时间配置见env配置中的CLOCKWORK_STORAGE_EXPIRATION项 默认7天
            $schedule->command(ClockworkCleanCommand::class)->everyMinute();
        }catch (\Throwable $e){}
    }
}
