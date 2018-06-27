<?php

namespace Noking50\Modules\SinglePage\Services;

use Noking50\Modules\SinglePage\Services\ModuleSinglePageService;
use Noking50\Modules\Required\Services\LanguageService;
use Noking50\Modules\SinglePage\Validations\ModuleSinglePageValidation;
use Request;
use Route;
use DB;
use DBLog;
use FileUpload;

class OutputService {

    protected $moduleSinglePageService;
    protected $languageService;
    protected $moduleSinglePageValidation;

    public function __construct(ModuleSinglePageService $moduleSinglePageService
    , LanguageService $languageService
    , ModuleSinglePageValidation $moduleSinglePageValidation) {
        $this->moduleSinglePageService = $moduleSinglePageService;
        $this->languageService = $languageService;
        $this->moduleSinglePageValidation = $moduleSinglePageValidation;
    }

    public function getBackendDetail($group) {
        $dataRow_module_single_page = $this->moduleSinglePageService->getDetailBackend($group);

        $langs = is_null($dataRow_module_single_page) ? [] : $dataRow_module_single_page->lang->pluck('lang')->toArray();
        $form_choose_lang = $this->languageService->getListFormChoose($langs);

        return [
            'dataRow_module_single_page' => $dataRow_module_single_page,
            'form_choose_lang' => $form_choose_lang,
        ];
    }

    public function getBackendEdit($group) {
        $dataRow_module_single_page = $this->moduleSinglePageService->getDetailBackendEdit($group);

        $langs = is_null($dataRow_module_single_page) ? [] : $dataRow_module_single_page->lang->pluck('lang')->toArray();
        $form_choose_lang = $this->languageService->getListFormChoose($langs);

        return [
            'dataRow_module_single_page' => $dataRow_module_single_page,
            'form_choose_lang' => $form_choose_lang,
        ];
    }

    public function getBackendEditSubmit($group) {
        $this->moduleSinglePageValidation->validate_edit($group);

        DB::beginTransaction();
        try {
            $this->moduleSinglePageService->zipHtmlCheck($group, Request::all());
            
            $isExist = $this->moduleSinglePageService->isExist($group);
            $result = null;
            $result_lang = null;
            if ($isExist) {
                $result = $this->moduleSinglePageService->edit($group, Request::all());
                if ($result) {
                    $result_lang = $this->moduleSinglePageService->editLang($result->get('after')->id, Request::all());
                }
            } else {
                $result = $this->moduleSinglePageService->add($group, Request::all());
                $result_lang = $this->moduleSinglePageService->addLang($result->id, Request::all());
            }
            
            $this->moduleSinglePageService->zipHtmlMoveExtract($group, Request::all());
            $this->moduleSinglePageService->zipHtmlDeleteUpload(Request::all(), true);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->moduleSinglePageService->zipHtmlDeleteUpload(Request::all());
            throw $e;
        }

        $datatable = config('module_single_page.datatable');
        // log
        if ($isExist) {
            DBLog::write($datatable, array_get($result, 'before'), array_get($result, 'after'));
            if ($result_lang) {
                foreach ($result_lang['add'] as $k => $v) {
                    DBLog::write("{$datatable}_lang", null, $v);
                }
                foreach ($result_lang['edit'] as $k => $v) {
                    DBLog::write("{$datatable}_lang", array_get($v, 'before'), array_get($v, 'after'));
                }
                foreach ($result_lang['delete'] as $k => $v) {
                    DBLog::write("{$datatable}_lang", $v, null);
                }
            }
        } else {
            DBLog::write($datatable, null, $result);
            foreach ($result_lang as $k => $v) {
                DBLog::write("{$datatable}_lang", null, $v);
            }
        }

        // upload
        if ($isExist) {
            if ($result) {
                FileUpload::handleFile(array_get($result, 'after')->files, array_get($result, 'before')->files);
            }
            if ($result_lang) {
                foreach ($result_lang['add'] as $k => $v) {
                    if ($v) {
                        FileUpload::handleEditor($v->content);
                    }
                }
                foreach ($result_lang['edit'] as $k => $v) {
                    if (array_get($v, 'before') && array_get($v, 'after')) {
                        FileUpload::handleEditor(array_get($v, 'after')->content, array_get($v, 'before')->content);
                    }
                }
                foreach ($result_lang['delete'] as $k => $v) {
                    if ($v) {
                        FileUpload::handleEditor(null, $v->content);
                    }
                }
            }
        } else {
            FileUpload::handleFile($result->files);
            foreach ($result_lang as $k => $v) {
                FileUpload::handleEditor($v->content);
            }
        }

        return [
            'msg' => trans('message.success.edit'),
        ];
    }

    #

    public function getFrontendDetail($group) {
        $dataRow_module_single_page = $this->moduleSinglePageService->getDetailFrontend($group);

        return [
            'dataRow_module_single_page' => $dataRow_module_single_page,
        ];
    }

}
