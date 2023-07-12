@extends('layouts/layoutMaster')

@section('title', 'Treeview - Extended UI')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/jstree/jstree.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/jstree/jstree.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/extended-ui-treeview.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="fw-light text-muted">Extended UI /</span> Treeview
</h4>

<!-- JSTree -->
<div class="row">
  <!-- Basic -->
  <div class="col-md-6 col-12">
    <div class="card mb-4">
      <h5 class="card-header">Basic</h5>
      <div class="card-body">
        <div id="jstree-basic">
          <ul>
            <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
              css
              <ul>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                  app.css
                </li>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                  style.css
                </li>
              </ul>
            </li>
            <li class="jstree-open" data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
              img
              <ul data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                  bg.jpg
                </li>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                  logo.png
                </li>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
                  avatar.png
                </li>
              </ul>
            </li>
            <li class="jstree-open" data-jstree='{"icon" : "mdi mdi-folder-outline"}'>
              js
              <ul>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>jquery.js</li>
                <li data-jstree='{"icon" : "mdi mdi-folder-outline"}'>app.js</li>
              </ul>
            </li>
            <li data-jstree='{"icon" : "mdi mdi-file-document-outline"}'>
              index.html
            </li>
            <li data-jstree='{"icon" : "mdi mdi-file-document-outline"}'>
              page-one.html
            </li>
            <li data-jstree='{"icon" : "mdi mdi-file-document-outline"}'>
              page-two.html
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- /Basic -->
  <!-- Custom Icons -->
  <div class="col-md-6 col-12">
    <div class="card mb-4">
      <h5 class="card-header">Custom Icons</h5>
      <div class="card-body">
        <div id="jstree-custom-icons"></div>
      </div>
    </div>
  </div>
  <!-- /Custom Icons -->
</div>

<div class="row">
  <!-- Context Menu -->
  <div class="col-md-6 col-12">
    <div class="card mb-4">
      <h5 class="card-header">Context Menu</h5>
      <div class="card-body">
        <div id="jstree-context-menu" class="overflow-auto"></div>
      </div>
    </div>
  </div>
  <!-- /Context Menu -->
  <!-- Drag & Drop -->
  <div class="col-md-6 col-12">
    <div class="card mb-4">
      <h5 class="card-header">Drag & Drop</h5>
      <div class="card-body">
        <div id="jstree-drag-drop" class="overflow-auto"></div>
      </div>
    </div>
  </div>
  <!-- /Drag & Drop -->
</div>

<div class="row">
  <!-- Checkbox -->
  <div class="col-md-6 col-12">
    <div class="card mb-md-0 mb-4">
      <h5 class="card-header">Checkboxes</h5>
      <div class="card-body">
        <div id="jstree-checkbox"></div>
      </div>
    </div>
  </div>
  <!-- /Checkbox -->
  <!-- Ajax -->
  <div class="col-md-6 col-12">
    <div class="card">
      <h5 class="card-header">Ajax</h5>
      <div class="card-body">
        <div id="jstree-ajax"></div>
      </div>
    </div>
  </div>
  <!-- Ajax -->
</div>
<!-- /JSTree -->
@endsection
