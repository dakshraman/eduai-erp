<?php
namespace Modules\AiAssignment\Services;

use Modules\AiAssignment\Entities\AiGenerationLog;

class AiAssignmentService {
    private array $questionTemplates = [
        'easy' => [
            'mcq' => ['What is the primary function of %s?', 'Which of the following best describes %s?', 'Identify the correct definition of %s.'],
            'short' => ['Define %s in your own words.', 'List three characteristics of %s.'],
        ],
        'medium' => [
            'mcq' => ['Which analysis best explains the relationship between %s and %s?', 'Evaluate the significance of %s in the context of %s.'],
            'short' => ['Explain how %s affects %s.', 'Compare and contrast %s with %s.'],
            'essay' => ['Analyze the impact of %s on %s, providing specific examples.'],
        ],
        'hard' => [
            'mcq' => ['Which critical evaluation of %s is most supported by evidence?', 'Assess the validity of the claim that %s directly causes %s.'],
            'short' => ['Critically evaluate the role of %s in %s.', 'Synthesize the key arguments for and against %s.'],
            'essay' => ['Evaluate the long-term implications of %s on %s, drawing on multiple perspectives.'],
        ],
    ];

    public function generateAssignment(array $params): array {
        $subject = $params['subject'] ?? 'General';
        $topic = $params['topic'] ?? 'General Knowledge';
        $difficulty = $params['difficulty'] ?? 'medium';
        $numQuestions = $params['num_questions'] ?? 5;
        $marks = $params['marks'] ?? 20;

        $questions = [];
        for ($i = 0; $i < $numQuestions; $i++) {
            $type = $this->getQuestionType($difficulty, $i, $numQuestions);
            $questions[] = $this->generateQuestion($subject, $topic, $difficulty, $type, $i + 1, $marks);
        }

        return ['questions' => $questions, 'total_marks' => $marks, 'difficulty' => $difficulty, 'topic' => $topic];
    }

    private function getQuestionType(string $difficulty, int $index, int $total): string {
        $ratio = $index / max($total, 1);
        if ($ratio < 0.4) return 'mcq';
        if ($ratio < 0.7) return 'short';
        return 'essay';
    }

    private function generateQuestion(string $subject, string $topic, string $difficulty, string $type, int $number, int $totalMarks): array {
        $marks = match($type) { 'mcq' => 2, 'short' => 5, 'essay' => 10, default => 3 };
        $templates = $this->questionTemplates[$difficulty][$type] ?? $this->questionTemplates['medium']['mcq'];
        $template = $templates[array_rand($templates)];
        $questionText = sprintf($template, $topic, $subject);

        $data = [
            'number' => $number,
            'type' => $type,
            'question' => $questionText,
            'marks' => $marks,
            'difficulty' => $difficulty,
            'bloom_level' => $this->getBloomLevel($type),
        ];

        if ($type === 'mcq') {
            $data['options'] = [
                "A. {$topic} - Option 1",
                "B. {$topic} - Option 2",
                "C. {$topic} - Option 3",
                "D. {$topic} - Option 4",
            ];
            $data['correct_answer'] = $data['options'][array_rand($data['options'])];
        }

        return $data;
    }

    private function getBloomLevel(string $type): string {
        return match($type) { 'mcq' => 'remember', 'short' => 'understand', 'essay' => 'analyze', default => 'remember' };
    }

    public function logGeneration(string $action, int $inputTokens, int $outputTokens, float $cost): void {
        AiGenerationLog::create([
            'user_id' => auth()->id(),
            'school_id' => auth()->user()->school_id ?? 1,
            'action' => $action,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'cost' => $cost,
        ]);
    }
}
