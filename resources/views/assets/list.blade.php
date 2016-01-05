@extends('layouts.app')


@section('header')

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
      <div class="asset-wrapper">
        <img src="{{ $asset->url('t') }}" class="asset" />
        <div class="buttons">
          <a class="btn btn-default preview" href="{{ $asset->url('m') }}">Preview</a>
          <a class="btn btn-primary download" href="{{ route('assets.download', [$asset->id]) }}">Download</a>
        </div>
      </div>
    </div>
    
    <?php if ($count%$cols == 0): ?>
      </div>
    <?php endif; $count++; ?>
  <?php endforeach; ?>
  <?php if ($count%$cols != 1): ?></div><?php endif; ?>
  
</div>

@endsection


@section('footer')

<link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<link rel="stylesheet" href="/assets/js/bootstrap-tokenfield/css/bootstrap-tokenfield.min.css" media="all" type="text/css">
<link rel="stylesheet" href="/assets/js/bootstrap-tokenfield/css/tokenfield-typeahead.min.css" media="all" type="text/css">
<script src="/assets/js/bootstrap-tokenfield/bootstrap-tokenfield.min.js"></script>

<script src="/assets/js/imagelightbox.min.js"></script>

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
  
  $('a.preview').imageLightbox({
		onStart: 	 function() { overlayOn(); },
		onEnd:	 	 function() { overlayOff(); activityIndicatorOff(); },
		onLoadStart: function() { activityIndicatorOn(); },
		onLoadEnd:	 function() { activityIndicatorOff(); }
  });
	// ACTIVITY INDICATOR
	var activityIndicatorOn = function()
	{
		$( '<div id="imagelightbox-loading"><div></div></div>' ).appendTo( 'body' );
	},
	activityIndicatorOff = function()
	{
		$( '#imagelightbox-loading' ).remove();
	},

	// OVERLAY
	overlayOn = function()
	{
		$( '<div id="imagelightbox-overlay"></div>' ).appendTo( 'body' );
	},
	overlayOff = function()
	{
		$( '#imagelightbox-overlay' ).remove();
	};

});
</script>

@endsection

