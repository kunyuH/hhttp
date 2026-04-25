<?php

namespace hhttp\io\common\Listeners;

use hhttp\io\common\Models\SqlLogModel;

final class LogRequestHandledListener
{

    public function handle(): void
    {
        try {
            (new SqlLogModel())->logSave();
        } catch (\Throwable $e) {}
    }

}
