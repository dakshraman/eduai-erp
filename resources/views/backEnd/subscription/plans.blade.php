@extends('backEnd.master')

@section('title')
    Subscription Plans
@endsection

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="main-title">Subscription Plans</h2>
        </div>
    </div>
</div>

<div class="row">
    @foreach($plans as $plan)
    <div class="col-lg-4 col-md-6">
        <div class="card {{ $plan->slug === 'pro' ? 'border-primary' : '' }}">
            @if($plan->slug === 'pro')
            <div class="card-header bg-primary text-white text-center">
                <h5 class="mb-0">Most Popular</h5>
            </div>
            @endif
            <div class="card-body text-center">
                <h4 class="card-title">{{ $plan->name }}</h4>
                <div class="price my-3">
                    <span class="amount">${{ $plan->price }}</span>
                    <span class="period">/month</span>
                </div>
                <ul class="list-unstyled">
                    @if($plan->features)
                        @foreach($plan->features as $feature)
                        <li class="py-2">
                            <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                        </li>
                        @endforeach
                    @endif
                </ul>
                <form action="{{ route('subscription.checkout') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="btn {{ $plan->slug === 'pro' ? 'btn-primary' : 'btn-outline-primary' }} btn-block">
                        Get Started
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
