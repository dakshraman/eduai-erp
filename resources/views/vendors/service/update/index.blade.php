@extends('backEnd.master', ['title' => __('service::install.update')])

@section('mainContent')

<section class="admin-visitor-area up_admin_visitor empty_table_tab">
    <div class="container-fluid p-0">
        <div class="white-box sm_mb_20 sm2_mb_20 md_mb_20">
            <div class="main-title">
                <h3 class="mb-30">@lang('setting.Update')</h3>
            </div>
            <div class="card-body">
                @php
                    $currentVersion = $product['current_version'] ?? null;
                    $nextVersion = $product['next_release_version'] ?? null;
                    $nextDate = $product['next_release_date'] ?? null;
                    $nextSize = $product['next_release_size'] ?? 0;
                    $nextBuild = $product['next_release_build'] ?? null;
                    $changeLog = $product['next_release_change_log'] ?? null;
                    $productName = $product['name'] ?? null;
                @endphp

                @if($currentVersion === $nextVersion && $productName)
                    {{-- Already up to date --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="add-visitor">
                                <table class="display school-table school-table-style width-shadow">
                                    <tbody>
                                        <tr><td>Current Installed Version</td><td>{{ $currentVersion }}</td></tr>
                                        <tr><td>Latest version</td><td>{{ $nextVersion }}</td></tr>
                                        <tr><td>Date of Release</td><td>{{ $nextDate }}</td></tr>
                                        <tr><td>{{ __('setting.PHP Version') }}</td><td>{{ phpversion() }}</td></tr>
                                        <tr>
                                            <td>{{ __('setting.Curl Enable') }}</td>
                                            <td>{{ in_array('curl', get_loaded_extensions()) ? 'enable' : 'disable' }}</td>
                                        </tr>
                                        <tr><td>{{ __('setting.Purchase code') }}</td><td>{{ __('Verified') }}</td></tr>
                                        <tr><td>{{ __('setting.Install Domain') }}</td><td>{{ config('configs')->where('key', 'system_domain')->first()->value }}</td></tr>
                                        <tr><td>{{ __('setting.System Activated Date') }}</td><td>{{ config('configs')->where('key', 'system_activated_date')->first()->value }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a href="{{ url('/') }}" class="primary-btn fix-gr-bg">Back To Home</a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Update available --}}
                    <div class="row">
                        <div class="col-md-12">
                            @if($productName)
                                <div class="add-visitor">
                                    <table class="display school-table school-table-style width-shadow">
                                        <tbody>
                                            <tr><td>Current Installed Version</td><td>{{ $currentVersion }}</td></tr>
                                            <tr><td>Version Available for Upgrade</td><td>{{ $nextVersion }}</td></tr>
                                            <tr><td>Date of Release</td><td>{{ $nextDate }}</td></tr>
                                            <tr><td>Update Size</td><td>{{ bytesToSize($nextSize) }}</td></tr>
                                            <tr><td>{{ __('setting.PHP Version') }}</td><td>{{ phpversion() }}</td></tr>
                                            <tr>
                                                <td>{{ __('setting.Curl Enable') }}</td>
                                                <td>{{ in_array('curl', get_loaded_extensions()) ? 'enable' : 'disable' }}</td>
                                            </tr>
                                            <tr><td>{{ __('setting.Purchase code') }}</td><td>{{ __('Verified') }}</td></tr>
                                            <tr><td>{{ __('setting.Install Domain') }}</td><td>{{ config('configs')->where('key', 'system_domain')->first()->value }}</td></tr>
                                            <tr><td>{{ __('setting.System Activated Date') }}</td><td>{{ config('configs')->where('key', 'system_activated_date')->first()->value }}</td></tr>
                                            @if($changeLog)
                                                <tr><td colspan="2">{!! $changeLog !!}</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="primary-btn fix-gr-bg" data-toggle="modal" data-target="#update_modal" data-modal-size="modal-md">Update</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Update Modal --}}
<div class="modal fade admin-query" id="update_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update System</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form method="POST" action="{{ route('service.download') }}" id="content_form" class="form-horizontal">
                        <input type="hidden" name="build" value="{{ $nextBuild }}">
                        <input type="hidden" name="version" value="{{ $nextVersion }}">
                        <div class="row">
                            <div class="col-lg-12">{!! $update_tips !!}</div>
                            <div class="col-md-12" id="download_buttons">
                                <p class="text-center">
                                    Are you sure you want to update to<br>
                                    version {{ $nextVersion }}<br>
                                    Size: {{ bytesToSize($nextSize) }}
                                </p>
                            </div>
                            <div class="col-md-12" id="on_progress" style="display: none;">
                                <p class="text-center alert alert-danger">Don't perform any action while we are updating!</p>
                                <p class="text-center">Update Size ({{ bytesToSize($nextSize) }}) — Updating…</p>
                            </div>
                            <div class="col-lg-12 text-center">
                                <div class="mt-40 d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="primary-btn fix-gr-bg submit" id="update">Update</button>
                                    <button type="button" class="primary-btn fix-gr-bg submitting" style="display: none;" disabled>Updating...</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var updateBtn = document.getElementById('update');
        if (!updateBtn) return;

        updateBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = document.getElementById('content_form');
            var url = form.getAttribute('action');

            document.getElementById('download_buttons').style.display = 'none';
            document.getElementById('on_progress').style.display = 'block';
            form.querySelector('.submit').style.display = 'none';
            form.querySelector('.submitting').style.display = 'inline-block';

            var formData = new FormData(form);
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfMeta ? csrfMeta.getAttribute('content') : '',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof InstallerToast === 'function') {
                    InstallerToast(data.message || 'Updated', 'success');
                }
                if (data.goto) {
                    setTimeout(function () { window.location.href = data.goto; }, 2000);
                }
            })
            .catch(function () {
                form.querySelector('.submit').style.display = 'inline-block';
                form.querySelector('.submitting').style.display = 'none';
                document.getElementById('on_progress').style.display = 'none';
                document.getElementById('download_buttons').style.display = 'block';
                if (typeof InstallerToast === 'function') {
                    InstallerToast('Update failed. Please try again.', 'error');
                }
            });
        });
    });


</script>
@endpush
