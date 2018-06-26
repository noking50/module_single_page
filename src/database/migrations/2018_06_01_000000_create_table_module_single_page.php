<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableModuleSinglePage extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $datatable = config('module_single_page.datatable');
        $datatable_admin = config('user.group.admin.datatable');

        Schema::create($datatable, function(Blueprint $table) use ($datatable_admin) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id')->unsigned()->comment('ID');
            $table->dateTime('created_at')->comment('新增日期');
            $table->dateTime('updated_at')->comment('更新日期');
            $table->integer("create_{$datatable_admin}_id")->unsigned()->nullable()->comment('新增人員');
            $table->integer("update_{$datatable_admin}_id")->unsigned()->nullable()->comment('更新人員');
            $table->char("module_group", 10)->collation('ascii_bin')->comment('資料分組');
            $table->tinyInteger('content_type')->unsigned()->comment('內容類型 1:編輯器 2:Zip');
            $table->longText('files')->comment('附件 json格式');
            $table->integer('view')->unsigned()->comment('瀏覽人數');
            $table->boolean('status')->unsigned()->comment('狀態 0:停用 1:啟用');
            $table->boolean('publish')->unsigned()->comment('發布 0:暫存 1:發布');

            $table->index("create_{$datatable_admin}_id");
            $table->index("update_{$datatable_admin}_id");
            $table->unique('module_group');
            $table->index(['status', 'publish']);
        });
        
        Schema::create("{$datatable}_lang", function(Blueprint $table) use ($datatable) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->integer("{$datatable}_id")->unsigned()->comment('父表ID');
            $table->char("lang", 6)->collation('ascii_bin')->comment('語言');
            $table->string('title', 100)->comment('標題');
            $table->longText('content')->comment('編輯器內容 json格式');

            $table->primary(["{$datatable}_id", 'lang'], 'pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $datatable = config('module_single_page.datatable');
        
        Schema::dropIfExists($datatable);
        Schema::dropIfExists("{$datatable}_lang");
    }

}
