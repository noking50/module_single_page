<?php

namespace Noking50\Modules\SinglePage\Controllers;

use Noking50\Modules\Required\Controllers\BaseController;
use Noking50\Modules\SinglePage\Services\OutputService;
use Route;

class ModuleSinglePageController extends BaseController {

    protected $outputService;
    protected $group;

    public function __construct(OutputService $outputService) {
        parent::__construct();
        $this->setResponse('module_single_page');

        $this->outputService = $outputService;
        $this->group = Route::current()->getAction('module_single_page_group') ?: '';
    }

    public function index() {
        $output = $this->outputService->getBackendDetail($this->group);

        $this->response->with($output);
        return $this->response;
    }

    public function edit() {
        $output = $this->outputService->getBackendEdit($this->group);

        $this->response->with($output);
        return $this->response;
    }

    ##

    public function ajax_edit() {
        $output = $this->outputService->getBackendEditSubmit($this->group);

        $this->response = array_merge($this->response, $output);
        return $this->response;
    }

    public function ajax_status() {
        $output = $this->outputService->getBackendStatus($this->group);

        $this->response = array_merge($this->response, $output);
        return $this->response;
    }

}
