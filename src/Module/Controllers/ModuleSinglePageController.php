<?php

namespace Noking50\Modules\SinglePage\Controllers;

use Noking50\Modules\Required\Controllers\BaseController;
use Noking50\Modules\SinglePage\Facades\ModuleSinglePage;
use Route;

class ModuleSinglePageController extends BaseController {

    protected $group;

    public function __construct() {
        parent::__construct();
        $this->setResponse('module_single_page');

        $this->group = Route::current()->getAction('module_single_page_group') ?: '';
    }

    public function index() {
        $output = ModuleSinglePage::detailBackend($this->group);

        $this->response->with($output);
        return $this->response;
    }

    public function edit() {
        $output = ModuleSinglePage::detailBackendEdit($this->group);

        $this->response->with($output);
        return $this->response;
    }

    ##

    public function ajax_edit() {
        $output = ModuleSinglePage::actionEdit($this->group);

        $this->response = array_merge($this->response, $output);
        return $this->response;
    }

}
