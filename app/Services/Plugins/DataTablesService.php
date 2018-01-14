<?php

/**
 * 插件数据交互服务
 * create by Fisher 2017/7/1 <fisher9389@sina.com>
 */

namespace App\Services\Plugins;

use Illuminate\Http\Request;
use DB;

class DataTablesService {

    protected $builder = [];
    protected $columns = [];
    protected $relations = [];
    protected $filter = [];
    protected $searchValue = '';
    protected $searchFilter = [];
    protected $order = [];
    protected $start = 0;
    protected $length = 0;

    public function get(Request $request, $tableName, $basicSql = 1) {
        $draw = $request->draw; //这个值直接返回给前台
        $this->builder = $this->makeBuiler($tableName)->whereRaw($basicSql);
        $this->columns = $this->getColumns($request);
        $this->relations = $this->makeRelations();
        $this->filter = $this->makeFilter($request);
        $this->searchFilter = $this->makeSearchFilter($request);
        $this->order = $this->makeOrder($request);
        $this->start = $request->start ? $request->start : 0;
        $this->length = $request->length ? $request->length : 0;
        /* 表的总记录数 必要 */
        $recordsTotal = $this->builder->count();
        /* 定义过滤条件查询过滤后的记录数 */
        $recordsFiltered = $this->builder
                ->where(function($query) {
                    $query = $this->dataTablesFilter($query);
                })
                ->where(function($query) {
                    $query = $this->dataTablesSearch($query);
                })
                ->count();
        /* 数据 */
        $infos = $this->builder
                ->with($this->relations)
                ->when(count($this->order) > 0, function($query) {
                    foreach ($this->order as $column => $dir) {
                        $query->orderBy($column, $dir);
                    }
                    return $query;
                })
                ->when($this->length > 0, function($query) {
                    return $query->skip($this->start)->take($this->length);
                })
                ->get()
                ->toArray();
        /*
         * Output 包含的是必要的
         */
        return [
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ];
    }

    protected function makeBuiler($tableName) {
        if (is_object($tableName) && $tableName instanceof \Illuminate\Database\Eloquent\Builder) {
            $builder = $tableName;
        } else if (is_object($tableName) && $tableName instanceof \Illuminate\Database\Eloquent\Model) {
            $builder = $tableName->newQuery();
        } else if (is_string($tableName) && preg_match('/^App\\Models\\[\w\\]+$/', $tableName) != -1) {
            $builder = $tableName::query();
        } else if (is_string($tableName) && preg_match('/^\w+$/', $tableName) != -1) {
            $builder = DB::table($tableName);
        } else {
            abort(500, '模型初始化错误');
        }
        return $builder;
    }

    protected function getColumns($request) {
        $columns = [];
        foreach ($request->columns as $k => $v) {
            $columns[$k] = $v['data'];
        }
        return $columns;
    }

    protected function makeRelations() {
        $relations = [];
        foreach ($this->columns as $index => $column) {
            if (preg_match('/\./', $column) > 0) {
                $relations[$index] = preg_replace('/\.\w+$/', '', $column);
            }
        }
        return $relations;
    }

    protected function makeFilter($request) {
        $filterOrigin = $request->filter ? $request->filter : [];
        $filter = [];
        foreach ($filterOrigin as $k => $v) {
            array_set($filter, $k, $v);
        }
        return $filter;
    }

    protected function makeSearchFilter($request) {
        $this->searchValue = $request->search['value'];
        $searchFilter = [];
        if (!empty($this->searchValue)) {
            foreach ($request->columns as $v) {
                if ($v['searchable'] == "true") {
                    $searchFilter[] = explode('.', $v['data']);
                }
            }
        }
        return $searchFilter;
    }

    protected function makeOrder($request) {
        $order = [];
        if (!empty($request->order)) {
            foreach ($request->order as $v) {
                $index = $v['column'];
                $dir = $v['dir'];
                if (array_has($this->relations, $index)) {
                    $closestRelationName = explode('.', $this->relations[$index])[0];
                    $closestRelation = $this->builder->getModel()->$closestRelationName();
                    $column = method_exists($closestRelation, 'getForeignKey') ? $closestRelation->getForeignKey() : $closestRelation->getParent()->getKeyName();
                } else {
                    $column = $this->columns[$index];
                }
                $order[$column] = $dir;
            }
        }
        return $order;
    }

    /* --filter start-- */

    private function dataTablesFilter($query) {
        $filter = $this->filter;
        foreach ($filter as $key => $value) {
            $query = $this->filterByTree($query, $key, $value);
        }
        return $query;
    }

    private function filterByTree($query, $column, $filter) {
        if (is_string($filter)) {
            $filter = ['is' => $filter];
        }
        foreach ($filter as $k => $v) {
            switch ($k) {
                case 'min':
                    $query->where($column, '>', $v);
                    break;
                case 'max':
                    $query->where($column, '<', $v);
                    break;
                case 'in':
                    $v = is_array($v) ? $v : explode(',', $v);
                    $query->whereIn($column, $v);
                    break;
                case 'like':
                    $query->where($column, 'like', '%' . $v . '%');
                    break;
                case 'null':
                    $query->whereNull($column, 'and', $v);
                    break;
                case 'is':
                    $query->where($column, '=', $v);
                    break;
                case 'or':
                    $query->orWhere(function($q)use($column, $v) {
                        $this->filterByTree($q, $column, $v);
                    });
                    break;
                default:
                    $query->whereHas($column, function ($q) use ($k, $v) {
                        $this->filterByTree($q, $k, $v);
                    });
                    break;
            }
        }
        return $query;
    }

    /* --filter end-- */

    /**
     * 模糊搜索
     * @param object $query
     * @return object
     */
    private function dataTablesSearch($query) {
        foreach ($this->searchFilter as $column) {
            $this->searchByTree($query, $column, $this->searchValue);
        }
        return $query;
    }

    /**
     * 递归拼接搜索Sql语句
     * @param object $query
     * @param array $column
     * @param string $search
     */
    private function searchByTree(& $query, $column, $search) {
        $first = array_shift($column);
        if (count($column) > 1) {
            $query->orWhereHas($first, function ($q) use ($column, $search) {
                $q->where(function($qq)use($column, $search) {
                    $this->searchByTree($qq, $column, $search);
                });
            });
        } else if (count($column) > 0) {
            $query->orWhereHas($first, function ($q) use ($column, $search) {
                $q->where([[head($column), 'like', '%' . $search . '%']]);
            });
        } else {
            $query->orWhere([[$first, 'like', '%' . $search . '%']]);
        }
    }

}
