<?php
namespace Modules\AiAssignment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SmClass;
use App\Models\SmSubject;
use App\Models\SmAssignSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AiAssignment\Services\AiAssignmentService;
use Modules\AiAssignment\Entities\AiGenerationLog;

class AiAssignmentController extends Controller {
    public function index() {
        $stats = [
            'total_generations' => AiGenerationLog::where('school_id', auth()->user()->school_id ?? 1)->count(),
            'total_cost' => AiGenerationLog::where('school_id', auth()->user()->school_id ?? 1)->sum('cost'),
            'recent' => AiGenerationLog::where('school_id', auth()->user()->school_id ?? 1)->latest()->take(10)->get(),
        ];
        return view('ai-assignment::ai-assignment.dashboard', compact('stats'));
    }

    public function generateForm() {
        $classes = SmClass::where('school_id', auth()->user()->school_id ?? 1)->where('active_status', 1)->get();
        $subjects = SmSubject::where('school_id', auth()->user()->school_id ?? 1)->where('active_status', 1)->get();
        return view('ai-assignment::ai-assignment.generate', compact('classes', 'subjects'));
    }

    public function generate(Request $request) {
        $request->validate([
            'subject_id' => 'required',
            'topic' => 'required',
            'difficulty' => 'required|in:easy,medium,hard',
            'num_questions' => 'required|integer|min:1|max:50',
            'marks' => 'required|integer|min:1',
        ]);

        $service = new AiAssignmentService();
        $result = $service->generateAssignment($request->all());
        $service->logGeneration('generate_assignment', 150, 500, 0.002);

        session(['ai_generated' => $result]);
        return view('ai-assignment::ai-assignment.preview', ['assignment' => $result]);
    }

    public function preview() {
        $assignment = session('ai_generated');
        if (!$assignment) return redirect()->route('ai-assignment.generate');
        return view('ai-assignment::ai-assignment.preview', compact('assignment'));
    }

    public function saveGenerated(Request $request) {
        $assignment = session('ai_generated');
        if (!$assignment) return redirect()->route('ai-assignment.generate');

        $request->validate([
            'homework_date' => 'required',
            'submission_date' => 'required',
        ]);

        $homework = new \App\Models\SmHomework();
        $homework->class_id = $request->class_id ?? 1;
        $homework->section_id = $request->section_id ?? 1;
        $homework->subject_id = $request->subject_id ?? 1;
        $homework->homework_date = $request->homework_date;
        $homework->submission_date = $request->submission_date;
        $homework->marks = $assignment['total_marks'];
        $homework->difficulty = $assignment['difficulty'];
        $homework->learning_objectives = [$assignment['topic']];
        $homework->ai_generated = true;
        $homework->description = json_encode($assignment['questions']);
        $homework->created_by = Auth::id();
        $homework->school_id = auth()->user()->school_id ?? 1;
        $homework->academic_id = getAcademicId();
        $homework->save();

        session()->forget('ai_generated');
        return redirect()->route('ai-assignment.dashboard')->with('success', 'Assignment saved successfully!');
    }

    public function analytics() {
        $competencies = \Modules\AiAssignment\Entities\SmStudentCompetency::with(['student', 'subject'])
            ->where('school_id', auth()->user()->school_id ?? 1)->get();
        return view('ai-assignment::ai-assignment.dashboard', ['competencies' => $competencies, 'stats' => []]);
    }
}
