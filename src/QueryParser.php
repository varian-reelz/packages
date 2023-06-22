<?php

namespace VarianReelz\QueryParser;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryParser {
    private $query;

    public function __construct($modelClass) {
        $model = app($modelClass);
        $key = $model->getKeyName();
        $this->query = $model->whereNotNull($key);
    }

    public function select(?array $fields = []): self {
        if (!empty($fields)) {
            $columns = [];

            foreach ($fields as $val) {
                if (is_string($val)) {
                    $columns[] = $val;
                } elseif (is_array($val)) {
                    $columns[] = count($val) > 1 ? "{$val[0]} AS {$val[1]}" : $val[0];
                }
            }

            if (!empty($columns)) {
                $this->query->select($columns);
            }
        }

        return $this;
    }

    private function buildWhere(Builder $builder, string $column, $value, bool $isOr = false): Builder {
        $where = $isOr ? 'orWhere' : 'where';
        
        if (in_array($column, ['and', 'or'])) {
            return $builder->{$where}(function ($qry) use ($column, $value) {
                foreach ($value as $v) {
                    $k = key($v);
                    $this->buildWhere($qry, $k, $v[$k], $column === 'or');
                }
            });
        }
        
        $allowedTypesNonArray = ['boolean', 'double', 'integer', 'string', 'NULL'];
        $valueType = gettype($value);
        $qry = null;
        
        if (in_array($valueType, $allowedTypesNonArray)) {
            $qry = $builder->{$where}($column, $value);
        } elseif ($valueType === 'array') {
            if (count($value) > 1) {
                $qry = $builder->{$where}($column, $value[0], $value[1]);
            } else {
                $qry = $builder->{$where}($column, $value);
            }
        }

        return $qry;
    }

    public function where(string $column, $value): self {
        $this->buildWhere($this->query, $column, $value, $column === 'or');

        return $this;
    }

    public function limit(?int $limit = 10, ?int $offset = 1): self {
        $this->query->take($limit)->skip(($offset - 1) * $limit);
        
        return $this;
    }

    public function orderBy(string $column, ?int $direction = 1): self {
        $this->query->orderBy($column, $direction === 1 ? 'ASC' : 'DESC');

        return $this;
    }

    public function run(Request $request) {
        try {
            if ($request->filled('select')) {
                $this->select($request->select);
            }

            if ($request->filled('where')) {
                foreach ($request->where as $key => $val) {
                    $this->where($key, $val);
                }
            }
            
            $this->limit($request->rpp ?? null, $request->page ?? null);

            if ($request->filled('orderBy')) {
                foreach ($request->orderBy as $val) {
                    if (is_string($val)) {
                        $this->orderBy($val);
                    } elseif (is_array($val)) {
                        $this->orderBy($val[0], $val[1] ?? 1);
                    }
                }
            }
            
            if ($request->filled('asSql') && $request->asSql === 1) {
                return response()->json([
                    'Status' => 1, 
                    'Message' => 'Data retrieved', 
                    'Data' => [
                        'Query' => $this->query->toSql()
                    ]
                ]);
            }
            
            return response()->json([
                'Status' => 1, 
                'Message' => 'Data retrieved.', 
                'Data' => $this->query->get()
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'Status' => 0, 
                'Message' => $ex->getMessage()
            ]);
        }
    }
}