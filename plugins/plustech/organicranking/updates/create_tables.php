<?php namespace Plustech\OrganicRanking\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        Schema::create("plustech_organicranking_lists", function($table) {
            $table->engine = "InnoDB";
            $table->increments("id");
            $table->integer("user_id")->unsigned()->nullable()->index();
            $table->string("name");
            $table->string("slug")->index();
            $table->text("description")->nullable();
            $table->timestamps();
        });

        Schema::create("plustech_organicranking_links", function($table) {
            $table->engine = "InnoDB";
            $table->increments("id");
            $table->integer("list_id")->unsigned()->index();
            $table->integer("user_id")->unsigned()->nullable()->index();
            $table->string("title");
            $table->string("url");
            $table->text("description")->nullable();
            $table->integer("votes_count")->default(0);
            $table->timestamps();
        });

        Schema::create("plustech_organicranking_votes", function($table) {
            $table->engine = "InnoDB";
            $table->increments("id");
            $table->integer("link_id")->unsigned()->index();
            $table->integer("user_id")->unsigned()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists("plustech_organicranking_votes");
        Schema::dropIfExists("plustech_organicranking_links");
        Schema::dropIfExists("plustech_organicranking_lists");
    }
}
