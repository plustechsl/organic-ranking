<?php namespace Plustech\OrganicLinks\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        Schema::create('plustech_organiclinks_categories', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->timestamps();
        });

        Schema::create('plustech_organiclinks_links', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable()->index();
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('title');
            $table->string('url');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('plustech_organiclinks_votes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('link_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('value')->default(1);
            $table->timestamps();
        });

        Schema::create('plustech_organiclinks_user_expertises', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('area');
            $table->string('level')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plustech_organiclinks_user_expertises');
        Schema::dropIfExists('plustech_organiclinks_votes');
        Schema::dropIfExists('plustech_organiclinks_links');
        Schema::dropIfExists('plustech_organiclinks_categories');
    }
}
