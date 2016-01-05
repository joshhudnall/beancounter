@extends('layouts.app')


@section('header')

@endsection


@section('content')

<div class="container">
  <form action="{{ route('assets.upload') }}" class="dropzone" id="asset-dropzone">
    <div class="fallback">
      <input name="image" type="file" multiple />
    </div>
    <div class="form-group">
      <input class="form-control tags" type="text" name="tags" placeholder="Tags (Separate with commas)">
    </div>
  </form>
</div>

@endsection


@section('footer')

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

Dropzone.options.assetDropzone = {
  paramName: 'image',
  maxFilesize: 200, // MB
  addRemoveLinks: true,
};
</script>

@endsection

