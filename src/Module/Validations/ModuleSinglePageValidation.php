<?php

namespace Noking50\Modules\SinglePage\Validations;

use Noking50\Modules\Required\Validation\BaseValidation;
use Noking50\FileUpload\Rules\JsonFile;
use Noking50\FileUpload\Rules\JsonEditor;

class ModuleSinglePageValidation extends BaseValidation {

    public function validate_edit($group, $request_data = null) {
        $rules = [
            'content_type' => ['integer', 'required', 'in:1,2'],
            'files' => [new JsonFile()],
            'lang' => ['array'],
        ];
        $rules_lang = [
            'title' => ['string', (config("module_single_page.groups.{$group}.validation.title", 0) == 2 ? 'required' : 'nullable'), 'max:100'],
            'content' => [new JsonEditor()],
        ];
        $attributes = array_merge(
                trans('module_required::validation.attributes')
                , trans('module_single_page::validation.attributes.module_single_page')
        );
        foreach ($rules_lang as $k => $v) {
            $rules['lang.*.' . $k] = $v;
            if (isset($attributes[$k])) {
                $attributes['lang.*.' . $k] = $attributes[$k];
            }
        }

        return $this->validate($rules, $request_data, $attributes, [], [
                    ['lang.*.content', ['required', new JsonEditor(true)], function($input) {
                            return $input->{"content_type"} == 1;
                        }],
        ]);
    }

}
