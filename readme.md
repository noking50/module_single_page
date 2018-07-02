# Module single page

single page

## Installing

### 1. Install from composer
```
composer required noking50/module_single_page
```

### 2. Publish resoure
```
php artisan vendor:publish
```
It would generate below files
config/module_single_page.php
resources/lang/vendor/module_single_page/
resources/views/vendor/module_single_page/
resources/enum/module_single_page-content_type.php


### 3. configure file config/module_single_page.php
* set data table name
```
'datatable' => 'module_single_page'
```

* set available attach file extension
```
'file_ext' => 'pdf|doc|docx|xls|xlsx|zip',
```

* set each using single page configure
```
'groups' => [
    'group-name' => [
        'validation' => [ // input field status 0: hidden, 1: visible, 2: required
            'files' => 1,
            'title' => 2,
        ],
    ],
    'other_group' => [],
    ...
],
```

### 4. migration
```
php artisan migrate
```
create single page database table, table name will be config file settings above

## Usage
### Package method
* get data detail
```
$output = ModuleSinglePage::detailBackend($group);
```
$output is an array contains key:
'dataRow_module_single_page' - Model data from given id
'form_choose_lang' - list of all avaliable language and indicate each language has setting value
'module_group' - $group value

* get data detail and other required data for edit page
```
$output = ModuleSinglePage::detailBackendEdit($group);
```
$output is an array contains key:
'dataRow_module_single_page' - Model data from given $group
'form_choose_lang' - list of all avaliable language and indicate each language has setting value
'module_group' - $group value

* get data detail frontend
```
$output = ModuleSinglePage::detailFrontend($group);
```
$output is an array contains key:
'dataRow_module_single_page' - Model data from given $group
'module_group' - $group value

* edit data
```
$output = ModuleSinglePage::actionEdit($group);
```
$output is an array contains key:
'msg' - success message

### default controller
using a default controller in Module\Controllers\ModuleSinglePageController
* set Route like below
```
Route::get('/index', [
    'uses' => "\\Noking50\\Modules\\SinglePage\\Controllers\\ModuleSinglePageController@index",
    'module_single_page_group' => 'aboutus',
]);
Route::get('/edit', [
    'uses' => "\\Noking50\\Modules\\SinglePage\\Controllers\\ModuleSinglePageController@edit",
    'module_single_page_group' => 'aboutus',
])
```
'single_group' is which group setting in config will be used

* set view
set views in resources/views/vendor/module_single_page/
