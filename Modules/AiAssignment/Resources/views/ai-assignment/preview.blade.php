@extends('backEnd.master')
@section('title', 'Preview Assignment')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Preview Generated Assignment</h1>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <p><strong>Topic:</strong> {{ $assignment['topic'] ?? 'N/A' }}</p>
        <p><strong>Difficulty:</strong> {{ ucfirst($assignment['difficulty'] ?? 'medium') }}</p>
        <p><strong>Total Marks:</strong> {{ $assignment['total_marks'] ?? 0 }}</p>
    </div>
    @foreach($assignment['questions'] ?? [] as $q)
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex justify-between items-start">
            <span class="font-semibold">Q{{ $q['number'] }}.</span>
            <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ ucfirst($q['type']) }} - {{ $q['marks'] }} marks - {{ ucfirst($q['bloom_level']) }}</span>
        </div>
        <p class="mt-2">{{ $q['question'] }}</p>
        @if(isset($q['options']))
        <ul class="mt-2 ml-6 list-disc">
            @foreach($q['options'] as $opt)
            <li>{{ $opt }}</li>
            @endforeach
        </ul>
        @endif
    </div>
    @endforeach
    <form action="{{ route('ai-assignment.save') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Class</label>
                <select name="class_id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="1">Default Class</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Homework Date</label>
                <input type="date" name="homework_date" class="w-full px-3 py-2 border rounded-lg" value="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Submission Date</label>
                <input type="date" name="submission_date" class="w-full px-3 py-2 border rounded-lg" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
            </div>
        </div>
        <button type="submit" class="mt-4 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">Save as Homework</button>
    </form>
</div>
@endsection
