@extends('layouts/layoutMaster')

@section('title', 'Selects and tags - Forms')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/typeahead-js/typeahead.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bloodhound/bloodhound.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-selects.js')}}"></script>
<script src="{{asset('assets/js/forms-tagify.js')}}"></script>
<script src="{{asset('assets/js/forms-typeahead.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms /</span> Selects and tags
</h4>

<div class="row">

  <!-- Select2 -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Select2</h5>
      <div class="card-body">
        <div class="row">
          <!-- Basic -->

          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="select2Basic" class="select2 form-select form-select-lg" data-allow-clear="true">
                <option value="AK">Alaska</option>
                <option value="HI">Hawaii</option>
                <option value="CA">California</option>
                <option value="NV">Nevada</option>
                <option value="OR">Oregon</option>
                <option value="WA">Washington</option>
                <option value="AZ">Arizona</option>
                <option value="CO">Colorado</option>
                <option value="ID">Idaho</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NM">New Mexico</option>
                <option value="ND">North Dakota</option>
                <option value="UT">Utah</option>
                <option value="WY">Wyoming</option>
                <option value="AL">Alabama</option>
                <option value="AR">Arkansas</option>
                <option value="IL">Illinois</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="OK">Oklahoma</option>
                <option value="SD">South Dakota</option>
                <option value="TX">Texas</option>
                <option value="TN">Tennessee</option>
                <option value="WI">Wisconsin</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="IN">Indiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="OH">Ohio</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="VT">Vermont</option>
                <option value="VA">Virginia</option>
                <option value="WV">West Virginia</option>
              </select>
              <label for="select2Basic">Basic</label>
            </div>
          </div>
          <!-- Multiple -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="select2Multiple" class="select2 form-select" multiple>
                <optgroup label="Alaskan/Hawaiian Time Zone">
                  <option value="AK">Alaska</option>
                  <option value="HI">Hawaii</option>
                </optgroup>
                <optgroup label="Pacific Time Zone">
                  <option value="CA">California</option>
                  <option value="NV">Nevada</option>
                  <option value="OR">Oregon</option>
                  <option value="WA">Washington</option>
                </optgroup>
                <optgroup label="Mountain Time Zone">
                  <option value="AZ">Arizona</option>
                  <option value="CO" selected>Colorado</option>
                  <option value="ID">Idaho</option>
                  <option value="MT">Montana</option>
                  <option value="NE">Nebraska</option>
                  <option value="NM">New Mexico</option>
                  <option value="ND">North Dakota</option>
                  <option value="UT">Utah</option>
                  <option value="WY">Wyoming</option>
                </optgroup>
                <optgroup label="Central Time Zone">
                  <option value="AL">Alabama</option>
                  <option value="AR">Arkansas</option>
                  <option value="IL">Illinois</option>
                  <option value="IA">Iowa</option>
                  <option value="KS">Kansas</option>
                  <option value="KY">Kentucky</option>
                  <option value="LA">Louisiana</option>
                  <option value="MN">Minnesota</option>
                  <option value="MS">Mississippi</option>
                  <option value="MO">Missouri</option>
                  <option value="OK">Oklahoma</option>
                  <option value="SD">South Dakota</option>
                  <option value="TX">Texas</option>
                  <option value="TN">Tennessee</option>
                  <option value="WI">Wisconsin</option>
                </optgroup>
                <optgroup label="Eastern Time Zone">
                  <option value="CT">Connecticut</option>
                  <option value="DE">Delaware</option>
                  <option value="FL" selected>Florida</option>
                  <option value="GA">Georgia</option>
                  <option value="IN">Indiana</option>
                  <option value="ME">Maine</option>
                  <option value="MD">Maryland</option>
                  <option value="MA">Massachusetts</option>
                  <option value="MI">Michigan</option>
                  <option value="NH">New Hampshire</option>
                  <option value="NJ">New Jersey</option>
                  <option value="NY">New York</option>
                  <option value="NC">North Carolina</option>
                  <option value="OH">Ohio</option>
                  <option value="PA">Pennsylvania</option>
                  <option value="RI">Rhode Island</option>
                  <option value="SC">South Carolina</option>
                  <option value="VT">Vermont</option>
                  <option value="VA">Virginia</option>
                  <option value="WV">West Virginia</option>
                </optgroup>
              </select>
              <label for="select2Multiple">Multiple</label>
            </div>
          </div>
          <!-- Disabled -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="select2Disabled" class="select2 form-select" disabled>
                <option value="1">Option1</option>
                <option value="2" selected>Option2</option>
                <option value="3">Option3</option>
                <option value="4">Option4</option>
              </select>
              <label for="select2Disabled">Disabled</label>
            </div>
          </div>
          <!-- Icons -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="select2Icons" class="select2-icons form-select">
                <optgroup label="Services">
                  <option value="wordpress2" data-icon="mdi mdi-wordpress" selected>WordPress</option>
                  <option value="codepen" data-icon="mdi mdi-codepen">Codepen</option>
                  <option value="drupal" data-icon="mdi mdi-drupal">Drupal</option>
                  <option value="pinterest2" data-icon="mdi mdi-language-css3">CSS3</option>
                  <option value="html5" data-icon="mdi mdi-language-html5">HTML5</option>
                </optgroup>
                <optgroup label="File types">
                  <option value="pdf" data-icon="mdi mdi-file-pdf-box">PDF</option>
                  <option value="word" data-icon="mdi mdi-file-word">Word</option>
                  <option value="excel" data-icon="mdi mdi-file-cloud-outline">JSON</option>
                  <option value="facebook" data-icon="mdi mdi-facebook">Facebook</option>
                </optgroup>
                <optgroup label="Browsers">
                  <option value="chrome" data-icon="mdi mdi-google-chrome">Chrome</option>
                  <option value="firefox" data-icon="mdi mdi-firefox">Firefox</option>
                  <option value="safari" data-icon="mdi mdi-microsoft-edge">Edge</option>
                  <option value="opera" data-icon="mdi mdi-opera">Opera</option>
                  <option value="IE" data-icon="mdi mdi-microsoft-internet-explorer">IE</option>
                </optgroup>
              </select>
              <label for="select2Icons">Icons</label>
            </div>
          </div>
          <!-- Colors -->
          <!-- Primary -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <div class="select2-primary">
                <select id="select2Primary" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Primary">Primary</label>
            </div>
          </div>
          <!-- Success -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <div class="select2-success">
                <select id="select2Success" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Success">Success</label>
            </div>
          </div>
          <!-- Info -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <div class="select2-info">
                <select id="select2Info" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Info">Info</label>
            </div>
          </div>
          <!-- Warning -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <div class="select2-warning">
                <select id="select2Warning" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Warning">Warning</label>
            </div>
          </div>
          <!-- Danger -->
          <div class="col-md-6 mb-4 mb-md-0">
            <div class="form-floating form-floating-outline">
              <div class="select2-danger">
                <select id="select2Danger" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Danger">Danger</label>
            </div>
          </div>
          <!-- Dark -->
          <div class="col-md-6 ">
            <div class="form-floating form-floating-outline">
              <div class="select2-dark">
                <select id="select2Dark" class="select2 form-select" multiple>
                  <option value="1" selected>Option1</option>
                  <option value="2" selected>Option2</option>
                  <option value="3">Option3</option>
                  <option value="4">Option4</option>
                </select>
              </div>
              <label for="select2Dark">Dark</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Select2 -->

  <!-- Tagify -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Tagify</h5>
      <div class="card-body">
        <div class="row">
          <!-- Basic -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <input id="TagifyBasic" class="form-control" name="TagifyBasic" value="Tag1, Tag2, Tag3" />
              <label for="TagifyBasic">Basic</label>
            </div>
          </div>
          <!-- Readonly Mode -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <input id="TagifyReadonly" class="form-control" readonly value="Tag1, Tag2, Tag3" />
              <label for="TagifyReadonly">Readonly</label>
            </div>
          </div>
          <!-- Custom Suggestions: Inline -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <input id="TagifyCustomInlineSuggestion" name="TagifyCustomInlineSuggestion" class="form-control h-auto" placeholder="select technologies" value="css, html, javascript">
              <label for="TagifyCustomInlineSuggestion">Custom Inline Suggestions</label>
            </div>
          </div>
          <!-- Custom Suggestions: List -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <input id="TagifyCustomListSuggestion" name="TagifyCustomListSuggestion" class="form-control h-auto" placeholder="select technologies" value="css, html, php">
              <label for="TagifyCustomListSuggestion">Custom List Suggestions</label>
            </div>
          </div>
          <!-- Users List -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <input id="TagifyUserList" name="TagifyUserList" class="form-control h-auto" value="abatisse2@nih.gov, Justinian Hattersley" />
              <label for="TagifyUserList">Users List</label>
            </div>
          </div>
          <!-- Email -->
          <div class="col-md-6 mb-4">
            <label for="TagifyEmailList" class="form-label d-block">Email List</label>
            <input id="TagifyEmailList" class="tagify-email-list" value="some56.name@website.com">
            <button type="button" class="btn btn-sm rounded-pill btn-icon btn-outline-primary mb-1"> <span class="tf-icons mdi mdi-plus"></span> </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Tagify -->


  <!-- Bootstrap Select -->
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Bootstrap Select</h5>
      <div class="card-body">
        <div class="row">
          <!-- Basic -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerBasic" class="selectpicker w-100" data-style="btn-default">
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerBasic">Basic</label>
            </div>
          </div>
          <!-- Group -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerGroups" class="selectpicker w-100" data-style="btn-default">
                <optgroup label="Movies">
                  <option>Rocky</option>
                  <option>Pulp Fiction</option>
                  <option>The Godfather</option>
                </optgroup>
                <optgroup label="Series">
                  <option>Breaking Bad</option>
                  <option>Black Mirror</option>
                  <option>Money Heist</option>
                </optgroup>
              </select>
              <label for="selectpickerGroups">Groups</label>
            </div>
          </div>
          <!-- Multiple -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerMultiple" class="selectpicker w-100" data-style="btn-default" multiple data-icon-base="mdi" data-tick-icon="mdi-check text-white">
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerMultiple">Multiple</label>
            </div>
          </div>
          <!-- Live Search -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerLiveSearch" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                <option data-tokens="ketchup mustard">Hot Dog, Fries and a Soda</option>
                <option data-tokens="mustard">Burger, Shake and a Smile</option>
                <option data-tokens="frosting">Sugar, Spice and all things nice</option>
              </select>
              <label for="selectpickerLiveSearch">Live Search</label>
            </div>
          </div>
          <!-- Icons -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select class="selectpicker w-100 show-tick" id="selectpickerIcons" data-icon-base="mdi" data-tick-icon="mdi-check" data-style="btn-default">
                <option data-icon="mdi mdi-instagram">Instagram</option>
                <option data-icon="mdi mdi-pinterest">Pinterest</option>
                <option data-icon="mdi mdi-twitch">Twitch</option>
              </select>
              <label for="selectpickerIcons">Icons</label>
            </div>
          </div>
          <!-- Subtext -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerSubtext" class="selectpicker w-100" data-style="btn-default" data-show-subtext="true">
                <option data-subtext="Framework">React</option>
                <option data-subtext="Styles">Sass</option>
                <option data-subtext="Markup">HTML</option>
              </select>
              <label for="selectpickerSubtext">Subtext</label>
            </div>
          </div>
          <!-- Selection Limit -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerSelection" class="selectpicker w-100" data-style="btn-default" multiple data-max-options="2">
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerSelection">Selection Limit</label>
            </div>
          </div>
          <!-- Select / Deselect All -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerSelectDeselect" class="selectpicker w-100" data-style="btn-default" multiple data-actions-box="true">
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerSelectDeselect">Select / Deselect All</label>
            </div>
          </div>
          <!-- Divider -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerDivider" class="selectpicker w-100" data-style="btn-default">
                <option>Rocky</option>
                <option data-divider="true">divider</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerDivider">Divider</label>
            </div>
          </div>
          <!-- Header -->
          <div class="col-md-6 mb-4">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerHeader" class="selectpicker w-100" data-style="btn-default" data-header="Select a Movie">
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerHeader">Header</label>
            </div>
          </div>
          <!-- Disabled -->
          <div class="col-md-6 mb-4 mb-md-0">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerDisabled" class="selectpicker w-100" data-style="btn-default" disabled>
                <option>Rocky</option>
                <option>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerDisabled">Disabled</label>
            </div>
          </div>
          <!-- Disabled Options -->
          <div class="col-md-6">
            <div class="form-floating form-floating-outline">
              <select id="selectpickerDisabledOptions" class="selectpicker w-100" data-style="btn-default">
                <option>Rocky</option>
                <option disabled>Pulp Fiction</option>
                <option>The Godfather</option>
              </select>
              <label for="selectpickerDisabledOptions">Disabled Options</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Bootstrap Select -->

  <!-- Typeahead -->
  <div class="col-12">
    <div class="card">
      <h5 class="card-header">Typeahead</h5>
      <div class="card-body">
        <div class="row">
          <!-- Basic -->
          <div class="col-md-6 mb-4">
            <label for="TypeaheadBasic" class="form-label">Basic</label>
            <input id="TypeaheadBasic" class="form-control typeahead" type="text" autocomplete="off" placeholder="Enter states from USA" />
          </div>
          <!-- Bloodhound -->
          <div class="col-md-6 mb-4">
            <label for="TypeaheadBloodHound" class="form-label">BloodHound (Suggestion Engine)</label>
            <input id="TypeaheadBloodHound" class="form-control typeahead-bloodhound" type="text" autocomplete="off" placeholder="Enter states from USA" />
          </div>
          <!-- Prefetch -->
          <div class="col-md-6 mb-4">
            <label for="TypeaheadPrefetch" class="form-label">Prefetch</label>
            <input id="TypeaheadPrefetch" class="form-control typeahead-prefetch" type="text" autocomplete="off" placeholder="Enter states from USA" />
          </div>
          <!-- Default Suggestions -->
          <div class="col-md-6 mb-4">
            <label for="TypeaheadSuggestions" class="form-label">Default Suggestions</label>
            <input id="TypeaheadSuggestions" class="form-control typeahead-default-suggestions" type="text" autocomplete="off" />
          </div>
          <!-- Custom Template -->
          <div class="col-md-6 mb-4 mb-md-0">
            <label for="TypeaheadCustom" class="form-label">Custom Template</label>
            <input id="TypeaheadCustom" class="form-control typeahead-custom-template" type="text" autocomplete="off" placeholder="Search For Oscar Winner" />
          </div>
          <!-- Multiple Datasets -->
          <div class="col-md-6">
            <label for="TypeaheadMultipleDataset" class="form-label">Multiple Datasets</label>
            <input id="TypeaheadMultipleDataset" class="form-control typeahead-multi-datasets" type="text" autocomplete="off" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Typeahead -->

</div>
@endsection
