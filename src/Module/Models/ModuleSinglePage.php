<?php

namespace Noking50\Modules\SinglePage\Models;

use Noking50\Modules\Required\Models\BaseModel;

class ModuleSinglePage extends BaseModel {

    protected $guarded = [];

    public function __construct($attributes = []) {
        $this->table = config('module_single_page.datatable');
        parent::__construct($attributes);
    }
    
    /**
     * 資料分組
     * 
     * @param type $query
     * @return type
     */
    public function scopeModuleGroup($query, $group) {
        return $query->where($this->table . '.module_group', '=', $group);
    }

}
