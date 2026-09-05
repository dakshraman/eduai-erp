<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('oauth_clients');

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableMorphs('owner');
            $table->string('name');
            $table->string('secret')->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->boolean('revoked');
            $table->timestamps();
        });

        $redirect_url = url('/');

        $oauth = new App\Models\OauthClient;
        $oauth->id = Str::uuid()->toString();
        $oauth->owner_type = null;
        $oauth->owner_id = null;
        $oauth->name = 'InfixEdu Personal Access Client';
        $oauth->secret = Str::random(40);
        $oauth->provider = 'users';
        $oauth->redirect_uris = json_encode([$redirect_url]);
        $oauth->grant_types = json_encode(["personal_access"]);
        $oauth->revoked = 0;
        $oauth->saveQuietly();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
