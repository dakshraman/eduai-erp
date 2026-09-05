@extends('backEnd.master')
@section('title', 'AI Assignment Dashboard')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">AI Assignment Engine</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Total Generations</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_generations'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Total Cost</h3>
            <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_cost'] ?? 0, 4) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold">Quick Actions</h3>
            <a href="{{ route('ai-assignment.generate') }}" class="mt-2 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Generate Assignment</a>
        </div>
    </div>
    @if(isset($stats['recent']) && $stats['recent']->count())
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Generations</h3>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left p-2">Action</th>
                    <th class="text-left p-2">Model</th>
                    <th class="text-left p-2">Cost</th>
                    <th class="text-left p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['recent'] as $log)
                <tr class="border-t">
                    <td class="p-2">{{ $log->action }}</td>
                    <td class="p-2">{{ $log->model_used }}</td>
                    <td class="p-2">${{ number_format($log->cost, 4) }}</td>
                    <td class="p-2">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
