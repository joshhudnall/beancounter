@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                  <?php if (\Auth::check()): ?>
                    <ul class="stats">
                      <?php foreach (\Auth::user()->counters as $counter): ?>
                        <li>{{ $counter->name }}: {{ $counter->value }}</li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <a href="{{ url('/login') }}" class="btn btn-primary">Log In to Continue</a>
                  <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
