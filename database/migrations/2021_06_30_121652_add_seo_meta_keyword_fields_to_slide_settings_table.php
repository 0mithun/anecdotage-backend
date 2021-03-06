<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoMetaKeywordFieldsToSlideSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slide_settings', function (Blueprint $table) {
            $table->text('seo_meta_keyword')->nullable()->after('seo_meta_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slide_settings', function (Blueprint $table) {
            $table->dropColumn('seo_meta_keyword');
        });
    }
}
