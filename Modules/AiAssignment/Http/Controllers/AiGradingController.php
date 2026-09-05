<?php
namespace Modules\AiAssignment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AiAssignment\Services\AiGradingService;

class AiGradingController extends Controller {
    public function bulkGradeForm() {
        return view('ai-assignment::ai-grading.bulkGrade', ['homeworks' => \App\Models\SmHomework::latest()->take(20)->get()]);
    }

    public function bulkGrade(Request $request) {
        $request->validate(['homework_id' => 'required']);
        $service = new AiGradingService();
        $results = $service->bulkGrade($request->homework_id);
        return view('ai-assignment::ai-grading.bulkGrade', ['results' => $results, 'homeworks' => \App\Models\SmHomework::latest()->take(20)->get()]);
    }
}
