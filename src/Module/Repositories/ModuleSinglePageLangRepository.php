<?php

namespace Noking50\Modules\SinglePage\Repositories;

use Noking50\Modules\SinglePage\Models\ModuleSinglePageLang;

class ModuleSinglePageLangRepository {

    protected $moduleSinglePageLang;
    protected $table;
    protected $parent_table;

    public function __construct(ModuleSinglePageLang $moduleSinglePageLang) {
        $this->moduleSinglePageLang = $moduleSinglePageLang;
        $this->table = $this->moduleSinglePageLang->getTable();
        $this->parent_table = config('module_single_page.datatable');
    }

    # List

    public function listAll($parent_id) {
        $dataSet = $this->moduleSinglePageLang->select([
                    "{$this->table}.{$this->parent_table}_id",
                    "{$this->table}.lang",
                    "{$this->table}.title",
                    "{$this->table}.content",
                ])
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->orderBy("{$this->table}.lang", 'asc')
                ->get();

        return $dataSet;
    }

    # Detail

    public function detail($parent_id, $lang, $columns = ['*']) {
        $dataRow = $this->moduleSinglePageLang
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->where("{$this->table}.lang", '=', $lang)
                ->first($columns);

        return $dataRow;
    }

    # insert update delete

    public function insert($data) {
        $result = $this->moduleSinglePageLang->create($data);

        return $result;
    }

    public function update($parent_id, $lang, $data) {
        $before = $this->detail($parent_id, $lang);
        $result = $this->moduleSinglePageLang
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->where("{$this->table}.lang", '=', $lang)
                ->update($data);
        $after = $this->detail($parent_id, $lang);

        if ($before && $after) {
            return collect([
                'before' => $before,
                'after' => $after,
            ]);
        }
        return null;
    }

    public function delete($parent_id, $lang) {
        $before = $this->detail($parent_id, $lang);
        $result = $this->moduleSinglePageLang
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->where("{$this->table}.lang", '=', $lang)
                ->delete();

        if ($before) {
            return $before;
        }
        return null;
    }

    public function deleteAll($parent_id) {
        $before = $this->moduleSinglePageLang
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->get();
        $result = $this->moduleSinglePageLang
                ->where("{$this->table}.{$this->parent_table}_id", '=', $parent_id)
                ->delete();

        if (count($before) > 0) {
            return $before;
        }
        return null;
    }

}
