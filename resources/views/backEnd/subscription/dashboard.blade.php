@extends('backEnd.master')

@section('title')
    Subscription Dashboard
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="main-title">Subscription Dashboard</h2>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Subscription</h5>
            </div>
            <div class="card-body">
                @if($subscription && $subscription->plan)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted">Plan</label>
                        <p class="fw-bold mb-0">{{ $subscription->plan->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Price</label>
                        <p class="fw-bold mb-0">${{ $subscription->plan->price }}/month</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted">Status</label>
                        <p class="mb-0">
                            @if($subscription->isActive())
                                <span class="badge bg-success">Active</span>
                            @elseif($subscription->isTrialing())
                                <span class="badge bg-info">Trial</span>
                            @elseif($subscription->status === 'past_due')
                                <span class="badge bg-warning">Past Due</span>
                            @elseif($subscription->status === 'canceled')
                                <span class="badge bg-danger">Canceled</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Current Period Ends</label>
                        <p class="fw-bold mb-0">{{ $subscription->current_period_end?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($subscription->isTrialing())
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="text-muted">Trial Ends</label>
                        <p class="fw-bold mb-0">{{ $subscription->trial_ends_at->format('M d, Y') }}</p>
                    </div>
                </div>
                @endif

                @if($subscription->isActive() && ! $subscription->canceled_at)
                <form action="{{ route('subscription.cancel') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger"
                        onclick="return confirm('Are you sure you want to cancel your subscription? It will remain active until the end of the billing period.')">
                        Cancel Subscription
                    </button>
                </form>
                @endif
                @else
                <div class="text-center py-4">
                    <p class="text-muted mb-3">You don't have an active subscription yet.</p>
                    <a href="{{ route('subscription.plans') }}" class="btn btn-primary">View Plans</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('subscription.plans') }}" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-list me-2"></i> View All Plans
                </a>
                <a href="{{ route('subscription.dashboard') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-sync me-2"></i> Refresh Status
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
