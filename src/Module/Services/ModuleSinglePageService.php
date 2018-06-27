<?php

namespace Noking50\Modules\SinglePage\Services;

use Noking50\Modules\Required\Services\ZipHtmlService;
use Noking50\Modules\Required\Exceptions\ZipHtmlException;
use Noking50\Modules\SinglePage\Models\ModuleSinglePage;
use Noking50\Modules\SinglePage\Repositories\ModuleSinglePageRepository;
use Noking50\Modules\SinglePage\Repositories\ModuleSinglePageLangRepository;
use User;
use File;

class ModuleSinglePageService {

    protected $zipHtmlService;
    protected $moduleSinglePageRepository;
    protected $moduleSinglePageLangRepository;

    public function __construct(ZipHtmlService $zipHtmlService
    , ModuleSinglePageRepository $moduleSinglePageRepository
    , ModuleSinglePageLangRepository $moduleSinglePageLangRepository) {
        $this->zipHtmlService = $zipHtmlService;
        $this->moduleSinglePageRepository = $moduleSinglePageRepository;
        $this->moduleSinglePageLangRepository = $moduleSinglePageLangRepository;
    }

    public function getDetailBackend($group) {
        $dataRow = $this->moduleSinglePageRepository->detailBackend($group);

        if (!empty($dataRow)) {
            $dataRow->files = \FileUpload::getFiles($dataRow->files);
            $dataSet_lang = $this->moduleSinglePageLangRepository->listAll($dataRow->id);
            foreach ($dataSet_lang as $k => $v) {
                $html_dir = 'module_single_page/' . $group . '/' . $v->lang;
                $dataSet_lang[$k]->content = json_decode($v->content, true);
                $dataSet_lang[$k]->content_html_url = $this->zipHtmlService->getHtmlUrl($html_dir);
            }
            $dataRow->lang = $dataSet_lang->keyBy('lang');
        } else {
            $dataRow = new ModuleSinglePage();
            $dataRow->fill([
                'updated_at' => null,
                'files' => [],
                'lang' => collect(),
            ]);
        }

        return $dataRow;
    }

    public function getDetailBackendEdit($group) {
        $dataRow = $this->moduleSinglePageRepository->detailBackendEdit($group);

        if (!empty($dataRow)) {
            $dataSet_lang = $this->moduleSinglePageLangRepository->listAll($dataRow->id);
            $dataRow->lang = $dataSet_lang->keyBy('lang');
        } else {
            $dataRow = new ModuleSinglePage();
            $dataRow->fill([
                'created_at' => null,
                'updated_at' => null,
                'content_type' => 1,
                'files' => '[]',
                'lang' => collect(),
            ]);
        }

        return $dataRow;
    }

    public function getDetailFrontend($group) {
        $dataRow = $this->moduleSinglePageRepository->detailFrontend($group);

        if (!empty($dataRow)) {
            $dataRow->content = json_decode($dataRow->content, true);
            $dataRow->files = \FileUpload::getFiles($dataRow->files);
        } else {
            $dataRow = new ModuleSinglePage();
            $dataRow->fill([
                'id' => null,
                'created_at' => null,
                'content' => [],
                'files' => [],
                'view' => null,
            ]);
        }

        return $dataRow;
    }

    public function isExist($group) {
        $is_exist = $this->moduleSinglePageRepository->existGroup($group);

        return $is_exist;
    }

    #

    public function add($group, $data) {
        $datatable_admin = config('user.group.admin.datatable');
        $data_insert = [
            "create_{$datatable_admin}_id" => User::id(),
            "update_{$datatable_admin}_id" => User::id(),
            'module_group' => $group,
            'content_type' => array_get($data, 'content_type'),
            'files' => array_get($data, 'files', '[]') ?: '[]',
            'view' => 0,
            'status' => 1,
            'publish' => 1,
        ];
        $result = $this->moduleSinglePageRepository->insert($data_insert);

        return $result;
    }

    public function edit($group, $data) {
        $datatable_admin = config('user.group.admin.datatable');
        $data_update = [
            "update_{$datatable_admin}_id" => User::id(),
            'content_type' => array_get($data, 'content_type'),
            'files' => array_get($data, 'files', '[]') ?: '[]',
        ];
        $result = $this->moduleSinglePageRepository->update($group, $data_update);

        return $result;
    }

    public function increaseView($group) {
        $is_click = User::click('module_single_page', $group);
        if ($is_click === true) {
            $this->moduleSinglePageRepository->increaseView($group);
        }

        return $is_click;
    }

    # lang

    public function addLang($parent_id, $data) {
        $parent_table = config('module_single_page.datatable');
        $data_lang = array_get($data, 'lang', []);
        $result = collect();
        foreach ($data_lang as $k => $v) {
            $data_insert = [
                "{$parent_table}_id" => $parent_id,
                'lang' => $k,
                'title' => array_get($v, 'title', '') ?: '',
                'content' => array_get($v, 'content', '[]') ?: '[]',
            ];
            $result->put($k, $this->moduleSinglePageLangRepository->insert($data_insert));
        }

        return $result;
    }

    public function editLang($parent_id, $data) {
        $parent_table = config('module_single_page.datatable');
        $data_compare = $this->langEditCompare($parent_id, $data);
        $result_add = collect();
        $result_edit = collect();
        $result_delete = collect();
        foreach ($data_compare['add'] as $k => $v) {
            $data_insert = [
                "{$parent_table}_id" => $parent_id,
                'lang' => $k,
                'title' => array_get($v, 'title', '') ?: '',
                'content' => array_get($v, 'content', '[]') ?: '[]',
            ];
            $result_add->put($k, $this->moduleSinglePageLangRepository->insert($data_insert));
        }
        foreach ($data_compare['edit'] as $k => $v) {
            $data_update = [
                'title' => array_get($v, 'title', '') ?: '',
                'content' => array_get($v, 'content', '[]') ?: '[]',
            ];
            $result_edit->put($k, $this->moduleSinglePageLangRepository->update($parent_id, $k, $data_update));
        }
        foreach ($data_compare['delete'] as $k => $v) {
            $result_delete->put($k, $this->moduleSinglePageLangRepository->delete($parent_id, $k));
        }

        return collect([
            'add' => $result_add,
            'edit' => $result_edit,
            'delete' => $result_delete,
        ]);
    }

    public function deleteLang($parent_id) {
        $result = $this->moduleSinglePageLangRepository->deleteAll($parent_id);

        return $result;
    }

    protected function langEditCompare($parent_id, $data) {
        $data_new = array_get($data, 'lang', []);
        $data_old = $this->moduleSinglePageLangRepository->listAll($parent_id)->keyBy('lang')->toArray();
        $data_add = collect();
        $data_edit = collect();
        $data_del = collect();
        foreach ($data_new as $k => $v) {
            if (isset($data_old[$k])) {
                $data_edit->put($k, $v);
            } else {
                $data_add->put($k, $v);
            }
        }
        foreach ($data_old as $k => $v) {
            if (!isset($data_new[$k])) {
                $data_del->put($k, $v);
            }
        }

        return collect([
            'add' => $data_add,
            'edit' => $data_edit,
            'delete' => $data_del,
        ]);
    }

    # zip html

    public function zipHtmlCheck($group, $data) {
        $content_type = intval(array_get($data, 'content_type'));
        if ($content_type != 2) {
            return;
        }

        $data_lang = array_get($data, 'lang', []);
        foreach ($data_lang as $k => $v) {
            $content_zip = head(json_decode(array_get($v, 'content_zip', '[]') ?: '[]', true));
            if (is_array($content_zip)) {
                $this->zipHtmlService->extract($content_zip);
            } else {
                $html_dir = 'module_single_page' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $k;
                if (!$this->zipHtmlService->htmlExist($html_dir)) {
                    throw new ZipHtmlException(trans('module_required::exception.zip_html.index_not_found'));
                }
            }
        }
    }

    public function zipHtmlMoveExtract($group, $data) {
        $content_type = intval(array_get($data, 'content_type'));
        if ($content_type != 2) {
            return;
        }

        $data_lang = array_get($data, 'lang', []);
        foreach ($data_lang as $k => $v) {
            $content_zip = head(json_decode(array_get($v, 'content_zip', '[]') ?: '[]', true));
            if (is_array($content_zip)) {
                $html_dir = 'module_single_page' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $k;
                $this->zipHtmlService->move($content_zip, $html_dir);
            }
        }
    }

    public function zipHtmlDeleteUpload($data, $is_delete_source = false) {
        $data_lang = array_get($data, 'lang', []);
        foreach ($data_lang as $k => $v) {
            $content_zip = head(json_decode(array_get($v, 'content_zip', '[]') ?: '[]', true));
            if (is_array($content_zip)) {
                $this->zipHtmlService->deleteUploadExtract($content_zip);
                if ($is_delete_source) {
                    $this->zipHtmlService->deleteUploadSource($content_zip);
                }
            }
        }
    }

}
