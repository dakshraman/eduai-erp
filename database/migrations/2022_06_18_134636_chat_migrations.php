<?php

use App\Events\CreateClassGroupChat;
use App\Models\SmAssignSubject;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Migrations\Migration;
use Modules\Chat\Entities\Group;
use Modules\Chat\Entities\GroupUser;

class ChatMigrations extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Group::truncate();
        GroupUser::truncate();
        $subjects = SmAssignSubject::withOutGlobalScope(StatusAcademicSchoolScope::class)->get();
        foreach ($subjects as $subject) {
            event(new CreateClassGroupChat($subject));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
}
