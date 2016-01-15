@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                  API Keys
                  <a class="btn btn-sm btn-primary pull-right" href="{{ route('settings.apiKeys.add') }}">Add Key</a>
                </div>

                <div class="panel-body">
                  <ul class="api-key-list">
                    <?php foreach ($apiKeys as $key): ?>
                      <li>
                        <span class="key">{{ $key->api_key }}</span>
                        <span class="key">{{ $key->active ? 'Active' : 'Inactive' }}</span>
                        <span class="key">{{ $key->created_at->format('F j, Y g:ia') }}</span>
                        <?php if ($key->active): ?>
                          <span class="key"><a href="{{ route('settings.apiKeys.deactivate', [$key->api_key]) }}">Deactivate</a></span>
                        <?php endif; ?>
                      </li>
                    <?php endforeach; ?>
                    
                    <?php if ( ! count($apiKeys)): ?>
                      <li>You do not have any API keys yet. Why don't you <a href="{{ route('settings.apiKeys.add') }}"><b>create one</b></a> now!</li>
                    <?php endif; ?>
                  </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
