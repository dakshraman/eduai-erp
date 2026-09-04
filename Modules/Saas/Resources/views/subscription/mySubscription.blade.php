@extends('backEnd.master')

@section('title', 'My Subscription')

@section('mainContent')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Subscription</h4>
                    @if($subscription && $subscription->status === 'active')
                    <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-times"></i> Cancel Subscription
                        </button>
                    </form>
                    @endif
                </div>
                <div class="card-body">
                    @if($subscription)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Current Plan</h6>
                                    <h4 class="mb-0">{{ $activePlan ? $activePlan->name : 'N/A' }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Status</h6>
                                    <h4 class="mb-0">
                                        @if($subscription->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                        @elseif($subscription->status === 'expired')
                                        <span class="badge bg-warning">Expired</span>
                                        @elseif($subscription->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Payment Type</th>
                                    <td>{{ ucfirst(str_replace('_', ' ', $subscription->payment_type)) }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td>{{ ucfirst($subscription->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>${{ number_format($subscription->amount, 2) }} {{ $subscription->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : '-' }}</td>
                                </tr>
                                @if($subscription->trial_ends_at)
                                <tr>
                                    <th>Trial Ends</th>
                                    <td>{{ $subscription->trial_ends_at->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($subscription->transaction_id)
                                <tr>
                                    <th>Transaction ID</th>
                                    <td><code>{{ $subscription->transaction_id }}</code></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($subscription->status === 'expired' || $subscription->status === 'cancelled')
                    <div class="text-center mt-4">
                        <a href="{{ route('subscription.package-list') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo"></i> Renew Subscription
                        </a>
                    </div>
                    @endif
                    @else
                    <div class="text-center p-5">
                        <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Subscription Found</h4>
                        <p class="text-muted">You haven't subscribed to any plan yet.</p>
                        <a href="{{ route('subscription.package-list') }}" class="btn btn-primary btn-lg mt-3">
                            <i class="fas fa-shopping-cart"></i> Browse Plans
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
