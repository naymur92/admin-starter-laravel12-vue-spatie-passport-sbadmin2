@extends('layouts.app')

@section('title', 'OAuth Client Management | Create New Client')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create New OAuth Client</h4>

                        @can('oauth-client-list')
                            <x-button.back href="{{ route('oauth-clients.index') }}"></x-button.back>
                        @endcan
                    </div>
                    <div class="card-body">

                        <form action="{{ route('oauth-clients.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form.input name="name" label="Client Name" required="true" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form.input name="provider" label="Provider" placeholder="e.g., users" />
                                        <small class="text-muted">Optional. Specify the user provider to use.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <x-form.checkboxes name="grant_types" :options="$grantTypes" label="Grant Types" required />
                                <span class="text-muted">Select at least one grant type.</span>
                            </div>

                            <div class="mb-3">
                                <x-form.input name="redirect" label="Redirect URL" placeholder="https://example.com/callback" />
                                <small class="text-muted">Required for authorization_code and implicit grant types.</small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <x-button.submit>Create Client</x-button.submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
