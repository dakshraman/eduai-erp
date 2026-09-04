@extends('backEnd.master')

@section('title', 'SaaS Settings')

@section('mainContent')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="mb-0">SaaS Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('saas.settings.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Setting</th>
                                        <th>Value</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $defaultSettings = [
                                        ['key' => 'manage_subscription', 'label' => 'Enable Subscriptions', 'type' => 'boolean'],
                                        ['key' => 'trial_enabled', 'label' => 'Enable Free Trials', 'type' => 'boolean'],
                                        ['key' => 'default_trial_days', 'label' => 'Default Trial Days', 'type' => 'text'],
                                        ['key' => 'stripe_enabled', 'label' => 'Enable Stripe Payments', 'type' => 'boolean'],
                                        ['key' => 'currency', 'label' => 'Currency', 'type' => 'text'],
                                    ];
                                    @endphp

                                    @foreach($defaultSettings as $index => $setting)
                                    <tr>
                                        <td>{{ $setting['label'] }}</td>
                                        <td>
                                            <input type="hidden" name="settings[{{ $index }}][key]" value="{{ $setting['key'] }}">
                                            <input type="hidden" name="settings[{{ $index }}][type]" value="{{ $setting['type'] }}">
                                            @if($setting['type'] === 'boolean')
                                            <select name="settings[{{ $index }}][value]" class="form-select form-select-sm">
                                                <option value="1" {{ ($settings->get($setting['key'])->value ?? '') == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ ($settings->get($setting['key'])->value ?? '') == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                            @else
                                            <input type="text" name="settings[{{ $index }}][value]" class="form-control form-control-sm" value="{{ $settings->get($setting['key'])->value ?? '' }}">
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($setting['type']) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
