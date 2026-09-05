@extends('backEnd.master')
@section('title', 'AI Bulk Grading')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">AI Bulk Grading</h1>
    <form action="{{ route('ai-grading.process') }}" method="POST" class="bg-white rounded-lg shadow p-6 mb-6">
        @csrf
        <label class="block text-sm font-medium mb-2">Select Homework</label>
        <select name="homework_id" class="w-full px-3 py-2 border rounded-lg" required>
            @foreach($homeworks as $hw)
            <option value="{{ $hw->id }}">ID: {{ $hw->id }} - {{ $hw->subjects->subject_name ?? 'N/A' }} ({{ $hw->homework_date }})</option>
            @endforeach
        </select>
        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Grade with AI</button>
    </form>
    @if(isset($results))
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Grading Results</h3>
        @foreach($results['results'] ?? [] as $r)
        <div class="border-t py-2">
            <p>Student ID: {{ $r['student_id'] }} - Marks: {{ $r['marks'] }} - Status: {{ $r['status'] }}</p>
            <p class="text-sm text-gray-600">{{ $r['feedback'] }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
