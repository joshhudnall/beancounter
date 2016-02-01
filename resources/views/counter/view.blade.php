@extends('layouts.app')

@section('content')
<div class="container spark-screen">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $counter->name }} <div class="pull-right">{{ $counter->type == \App\Models\Counter::CounterTypeCount ? 'Total' : 'Average' }}: <b>{{ $counter->value }}</b></div></div>

                <div class="panel-body">
                  <table class="items table table-striped table-hover">
                    <?php foreach ($counter->beans()->orderBy('created_at', 'desc')->get() as $bean): ?>
                      <tr>
                        <td>{{ $bean->created_at->timezone('America/Denver')->format('D, M j, Y g:ia') }}:</td>
                        <td>{{ $bean->value }}</td>
                      </tr>
                    <?php endforeach; ?>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
