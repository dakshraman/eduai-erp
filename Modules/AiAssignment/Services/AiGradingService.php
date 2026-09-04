<?php
namespace Modules\AiAssignment\Services;

class AiGradingService {
    public function bulkGrade(int $homeworkId): array {
        $homework = \App\Models\SmHomework::find($homeworkId);
        if (!$homework) return [];

        $submissions = \App\Models\SmHomeworkStudent::where('homework_id', $homeworkId)->get();
        $results = [];

        foreach ($submissions as $sub) {
            $results[] = [
                'student_id' => $sub->student_id,
                'marks' => $sub->marks ?? 0,
                'status' => $sub->complete_status,
                'feedback' => 'AI grading simulation - review recommended',
            ];
        }

        return ['homework' => $homework, 'results' => $results];
    }
}
