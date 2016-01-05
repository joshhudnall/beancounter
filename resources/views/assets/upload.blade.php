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
      <input class="form-control" type="text" name="tags" placeholder="Tags (Separate with commas)">
    </div>
  </form>
</div>

@endsection


@section('footer')

<script type="text/javascript">
Dropzone.options.assetDropzone = {
  paramName: 'image',
  maxFilesize: 200, // MB
  addRemoveLinks: true,
};
</script>

@endsection

