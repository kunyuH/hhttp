<?php

namespace hhttp\io\common\Console;

use hhttp\io\common\Console\Command\LogClean;
use Illuminate\Console\Scheduling\Schedule;

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
        }catch (\Throwable $e){}
    }
}
