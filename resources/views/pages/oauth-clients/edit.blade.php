@extends('layouts.app')

@section('title', 'OAuth Client Management | Edit Client')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit OAuth Client</h4>

                        @can('oauth-client-list')
                            <x-button.back href="{{ route('oauth-clients.index') }}"></x-button.back>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Client ID:</strong> <code>{{ $client->id }}</code>
                        </div>

                        <form action="{{ route('oauth-clients.update', $client->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form.input name="name" label="Client Name" :value="old('name', $client->name)" required />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <x-form.input name="provider" label="Provider" placeholder="e.g., users" :value="old('provider', $client->provider)" />
                                        <small class="text-muted">Optional. Specify the user provider to use.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                @php
                                    $currentGrantTypes = json_decode($client->grant_types, true) ?? [];
                                @endphp
                                <x-form.checkboxes name="grant_types" :options="$grantTypes" :selected="$currentGrantTypes" label="Grant Types" required />
                                <span class="text-muted">Select at least one grant type.</span>
                            </div>

                            <div class="mb-3">
                                @php
                                    $redirectUrls = json_decode($client->redirect_uris, true) ?? [];
                                    $redirectUrl = !empty($redirectUrls) ? $redirectUrls[0] : '';
                                @endphp
                                <x-form.input name="redirect" label="Redirect URL" placeholder="https://example.com/callback" :value="old('redirect', $redirectUrl)" />
                                <small class="text-muted">Required for authorization_code and implicit grant types.</small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <x-button.submit>Update Client</x-button.submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
