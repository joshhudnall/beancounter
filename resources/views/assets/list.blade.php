@extends('layouts.app')


@section('header')

<link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<link rel="stylesheet" href="/assets/js/bootstrap-tokenfield/css/bootstrap-tokenfield.min.css" media="all" type="text/css">
<link rel="stylesheet" href="/assets/js/bootstrap-tokenfield/css/tokenfield-typeahead.min.css" media="all" type="text/css">
<script src="/assets/js/bootstrap-tokenfield/bootstrap-tokenfield.min.js"></script>

<script>
<?php
$sourceList = [];
foreach (\App\Models\Tag::assetTags() as $slug => $tag) {
  $sourceList[] = $tag;
}
$tagList = [];
?>
var source = <?php echo json_encode($sourceList); ?>;
var postTags = <?php echo json_encode($tagList); ?>;

$(function() {
  $('.tags').tokenfield({
    autocomplete: {
      source: source,
      delay: 100,
    },
    tags: postTags,
    createTokensOnBlur: true,
    showAutocompleteOnFocus: true,
  });
});
</script>

@endsection


@section('content')

<div class="container">
  
  <div class="row">
    <div class="col-md-6">
      {!! Form::open(['class' => 'form inline-search', 'method' => 'get']) !!}
        
        {!! Form::text('tags', Request::get('tags'), ['class' => 'form-control inline-search-input tags']) !!}
        {!! Form::submit('Search', ['class' => 'btn btn-primary inline-search-button']) !!}
        
      {!! Form::close() !!}
    </div>
  </div>

  <?php $cols = 6; $count = 1; foreach ($assets as $asset): ?>
    <?php if ($count%$cols == 1): ?>
      <div class="row asset-row">
    <?php endif; ?>
  
    <div class="col-sm-{{ 12/$cols }}">
      <img src="{{ $asset->url('t') }}" />
    </div>
    
    <?php if ($count%$cols == 0): ?>
      </div>
    <?php endif; $count++; ?>
  <?php endforeach; ?>
  <?php if ($count%$cols != 1): ?></div><?php endif; ?>
  
</div>

@endsection


@section('footer')

@endsection

