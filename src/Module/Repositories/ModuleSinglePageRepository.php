<?php

namespace Noking50\Modules\SinglePage\Repositories;

use Noking50\Modules\SinglePage\Models\ModuleSinglePage;

class ModuleSinglePageRepository {

    protected $moduleSinglePage;
    protected $table;

    public function __construct(ModuleSinglePage $moduleSinglePage) {
        $this->moduleSinglePage = $moduleSinglePage;
        $this->table = $this->moduleSinglePage->getTable();
    }

    # List

    # Detail

    public function detail($group, $columns = ['*']) {
        return $this->moduleSinglePage
                        ->moduleGroup($group)
                        ->first($columns);
    }

    public function detailBackend($group) {
        $dataRow = $this->moduleSinglePage->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.updated_at",
                    "{$this->table}.module_group",
                    "{$this->table}.content_type",
                    "{$this->table}.files",
                    "{$this->table}.view",
                    "{$this->table}.status",
                ])
                ->selectUpdaterAdmin()
                ->moduleGroup($group)
                ->first();

        return $dataRow;
    }

    public function detailBackendEdit($group) {
        $dataRow = $this->moduleSinglePage->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.updated_at",
                    "{$this->table}.module_group",
                    "{$this->table}.content_type",
                    "{$this->table}.files",
                    "{$this->table}.status",
                ])
                ->moduleGroup($group)
                ->first();

        return $dataRow;
    }

    public function detailFrontend($group, $lang = null) {
        $dataRow = $this->moduleSinglePage->select([
                    "{$this->table}.id",
                    "{$this->table}.created_at",
                    "{$this->table}.module_group",
                    "{$this->table}.content_type",
                    "{$this->table}.files",
                    "{$this->table}.view",
                ])
                ->translate([
                    'title',
                    'content',
                        ], $lang)
                ->moduleGroup($group)
                ->active()
                ->first();

        return $dataRow;
    }

    # others

    public function existGroup($group) {
        return $this->moduleSinglePage->moduleGroup($group)->exists();
    }

    # insert update delete

    public function insert($data) {
        $result = $this->moduleSinglePage->create($data);

        return $result;
    }

    public function update($group, $data) {
        $before = $this->detail($group);
        $result = $this->moduleSinglePage
                ->moduleGroup($group)
                ->update($data);
        $after = $this->detail($group);

        if ($before && $after) {
            return collect([
                'before' => $before,
                'after' => $after,
            ]);
        }
        return null;
    }
    
    public function increaseView($group){
        $is_timestamps = $this->moduleSinglePage->timestamps;
        $this->moduleSinglePage->timestamps = false;
        $this->moduleSinglePage->moduleGroup($group)->Increment('view');
        $this->moduleSinglePage->timestamps = $is_timestamps;
    }

}
