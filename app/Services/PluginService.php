<?php

/**
 * 插件数据交互服务
 * create by Fisher 2016/8/28 <fisher9389@sina.com>
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\Plugins\DataTablesService;

class PluginService {

    public function dataTables(Request $request, $tableName, $basicSql = 1) {
        $service = new DataTablesService();
        return $service->get($request, $tableName, $basicSql);
    }

}
