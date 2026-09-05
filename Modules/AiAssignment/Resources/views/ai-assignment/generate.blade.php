@extends('backEnd.master')
@section('title', 'Generate Assignment')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Generate AI Assignment</h1>
    <form action="{{ route('ai-assignment.generate.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium mb-2">Subject</label>
                <select name="subject_id" class="w-full px-3 py-2 border rounded-lg" required>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}">{{ $s->subject_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Topic</label>
                <input type="text" name="topic" class="w-full px-3 py-2 border rounded-lg" required placeholder="e.g. Algebra, Photosynthesis">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Difficulty</label>
                <select name="difficulty" class="w-full px-3 py-2 border rounded-lg">
                    <option value="easy">Easy</option>
                    <option value="medium" selected>Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Number of Questions</label>
                <input type="number" name="num_questions" value="5" min="1" max="50" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Total Marks</label>
                <input type="number" name="marks" value="20" min="1" class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>
        <button type="submit" class="mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">Generate with AI</button>
    </form>
</div>
@endsection
