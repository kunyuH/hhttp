<?php

namespace Hhttp\io\common\Console\Command;

use hhttp\io\common\Models\ApiLogModel;
use hhttp\io\common\Models\HttpLogModel;
use hhttp\io\common\Models\SqlLogModel;
use hhttp\io\http\HHttp;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DevCommand extends BaseCommand
{
    # 支持不传递参数
    protected $signature = 'hm:dev {action} {args?*}';
    //hm:dev test x
    // Command description
    protected $description = 'hm:dev';

    // Execute the console command
    public function handle()
    {
        $args = $this->argument();
        $arg = $args['args']??[];

        $this->{$args['action']}(...$arg);
    }

    public function test($a='')
    {
        $this->info($a);
        $this->info('test');

        $clien = new HHttp();
        $resx = $clien->get('https://www.baidu.com');
        $res = $resx->getBody()->getContents();
        print_r($res);
    }

    /**
     * logicalPipelines 模块初始化
     * @return void
     */
    public function logicalPipelinesInit()
    {
        $ApiLog = new ApiLogModel();
        if (!hoo_schema()->hasTable($ApiLog->getTable())) {
            hoo_schema()->create($ApiLog->getTable(), function (Blueprint $table) {
                $table->bigIncrements('id')->comment('主键 自增id');
                $table->string('app_name',100)->nullable();
                $table->string('hoo_traceid',50)->nullable();
                $table->string('user_id',100)->nullable();
                $table->string('domain',50)->nullable();
                $table->string('path',100)->nullable();
                $table->string('method',20)->nullable();
                $table->integer('run_time')->nullable();
                $table->longText('user_agent')->nullable();
                $table->longText('input')->nullable();
                $table->longText('output')->nullable();
                $table->string('status_code',50)->nullable();
                $table->string('ip',50)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('user_id','idx_user_id');
                $table->index('path','idx_path');
                $table->index('hoo_traceid','idx_hoo_traceid');
                $table->index('created_at','idx_created_at');
                $table->index('domain','idx_domain');
                $table->index('method','idx_method');
            });
            $this->info('hm_api_log 表创建成功');
        }
        $HttpLog = new HttpLogModel();
        if (!hoo_schema()->hasTable($HttpLog->getTable())) {
            hoo_schema()->create($HttpLog->getTable(), function (Blueprint $table) {
                $table->bigIncrements('id')->comment('主键 自增id');
                $table->string('app_name',100)->nullable();
                $table->string('hoo_traceid',50)->nullable();
                $table->longText('url')->nullable();
                $table->string('path',150)->nullable();
                $table->string('method',50)->nullable();
                $table->longText('options')->nullable();
                $table->longText('response')->nullable();
                $table->longText('err')->nullable();
                $table->integer('run_time')->nullable();
                $table->string('run_trace',255)->nullable();
                $table->string('run_path',150)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('path','idx_path');
                $table->index('run_path','idx_run_path');
                $table->index('hoo_traceid','idx_hoo_traceid');
                $table->index('created_at','idx_created_at');
                $table->index('method','idx_method');
            });
            $this->info($HttpLog->getTableName().' 表创建成功');
        }
        $SqlLog = new SqlLogModel();
        if (!hoo_schema()->hasTable($SqlLog->getTable())) {
            hoo_schema()->create($SqlLog->getTable(), function (Blueprint $table) {
                # bin
                $table->bigIncrements('id')->comment('主键 自增id');
                $table->string('app_name',100)->nullable();
                $table->string('hoo_traceid',50)->nullable();
                $table->string('database',100)->nullable();
                $table->string('connection_name',100)->nullable();
                $table->longText('sql')->nullable();
                $table->integer('run_time')->nullable();
                $table->string('run_trace',255)->nullable();
                $table->string('run_path',150)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->index('hoo_traceid','idx_hoo_traceid');
                $table->index('database','idx_database');
                $table->index('connection_name','idx_connection_name');
                $table->index('run_path','idx_run_path');
                $table->index('created_at','idx_created_at');
            });
            $this->info($SqlLog->getTableName().' 表创建成功');
        }

        $this->info('操作成功');
    }
}
