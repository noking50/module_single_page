<?php

namespace Noking50\Modules\SinglePage\Models;

use Noking50\Modules\Required\Models\BaseModel;

class ModuleSinglePageLang extends BaseModel {

    public $timestamps = false;
    protected $guarded = [];
    
    public function __construct($attributes = []) {
        $this->table = config('module_single_page.datatable') . '_lang';
        parent::__construct($attributes);
    }
}
