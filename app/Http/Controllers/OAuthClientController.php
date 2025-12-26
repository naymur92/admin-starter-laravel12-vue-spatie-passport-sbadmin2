<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Flasher\Prime\flash;

class OAuthClientController extends Controller
{
    /**
     * Available grant types for OAuth clients.
     */
    protected function getGrantTypes()
    {
        return [
            'authorization_code'    => 'Authorization Code',
            'client_credentials'    => 'Client Credentials',
            'password'              => 'Password',
            'refresh_token'         => 'Refresh Token',
            'implicit'              => 'Implicit',
            'device_code'           => 'Device Code',
        ];
    }

    /**
     * Display a listing of OAuth clients.
     */
    public function index()
    {
        $this->authorize('oauth-client-list');

        $clients = DB::table('oauth_clients')
            // ->where('revoked', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.oauth-clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        setUnsetUniqueId();

        $grantTypes = $this->getGrantTypes();
        return view('pages.oauth-clients.create', compact('grantTypes'));
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'provider'      => 'nullable|string|max:255',
            'grant_types'   => 'required|array|min:1',
            'grant_types.*' => 'string|in:authorization_code,client_credentials,password,refresh_token,implicit,device_code',
            'redirect'      => 'nullable|string',
        ]);

        if (!setUnsetUniqueId('get')) {
            flash()->error('Unauthorized operation! Please try again.');
            return redirect()->back()->withInput();
        }

        $clientId = (string) Str::orderedUuid();
        $clientSecret = Str::random(40);

        $grantTypes = json_encode($request->grant_types);

        DB::table('oauth_clients')->insert([
            'id'                        => $clientId,
            'name'                      => $request->name,
            'secret'                    => bcrypt($clientSecret),
            'provider'                  => $request->provider,
            'redirect_uris'             => $request->redirect ? json_encode([$request->redirect]) : '[]',
            'grant_types'               => $grantTypes,
            'revoked'                   => 0,
            'created_at'                => now(),
            'updated_at'                => now(),
        ]);

        flash()->success('Client created successfully!');

        return redirect()->route('oauth-clients.index')
            ->with('client_credentials', [
                'id'        => $clientId,
                'secret'    => $clientSecret,
                'name'      => $request->name
            ]);
    }

    /**
     * Display the specified client.
     */
    public function show($id)
    {
        $client = DB::table('oauth_clients')
            ->where('id', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        return view('pages.oauth-clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit($id)
    {
        $client = DB::table('oauth_clients')
            ->where('id', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        setUnsetUniqueId();

        $grantTypes = $this->getGrantTypes();
        return view('pages.oauth-clients.edit', compact('client', 'grantTypes'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'provider'      => 'nullable|string|max:255',
            'grant_types'   => 'required|array|min:1',
            'grant_types.*' => 'string|in:authorization_code,client_credentials,password,refresh_token,implicit,device_code',
            'redirect'      => 'nullable|string',
        ]);

        if (!setUnsetUniqueId('get')) {
            flash()->error('Unauthorized operation! Please try again.');
            return redirect()->back()->withInput();
        }

        $grantTypes = json_encode($request->grant_types);

        $affected = DB::table('oauth_clients')
            ->where('id', $id)
            ->update([
                'name'                      => $request->name,
                'provider'                  => $request->provider,
                'redirect'                  => $request->redirect ? json_encode([$request->redirect]) : '[]',
                'grant_types'               => $grantTypes,
                'updated_at'                => now(),
            ]);

        if (!$affected) {
            abort(404);
        }

        return redirect()->route('oauth-clients.index')
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Regenerate client secret.
     */
    public function regenerateSecret($id)
    {
        $client = DB::table('oauth_clients')
            ->where('id', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        $newSecret = Str::random(40);

        DB::table('oauth_clients')
            ->where('id', $id)
            ->update([
                'secret'        => bcrypt($newSecret),
                'updated_at'    => now(),
            ]);

        return redirect()->route('oauth-clients.index')
            ->with('success', 'Client secret regenerated successfully!')
            ->with('client_credentials', [
                'id'        => $id,
                'secret'    => $newSecret,
                'name'      => $client->name
            ]);
    }

    /**
     * Remove the specified client from storage (soft delete by revoking).
     */
    public function destroy($id)
    {
        $affected = DB::table('oauth_clients')
            ->where('id', $id)
            ->update([
                'revoked'       => 1,
                'updated_at'    => now(),
            ]);

        if (!$affected) {
            abort(404);
        }

        return redirect()->route('oauth-clients.index')
            ->with('success', 'Client revoked successfully!');
    }
}
