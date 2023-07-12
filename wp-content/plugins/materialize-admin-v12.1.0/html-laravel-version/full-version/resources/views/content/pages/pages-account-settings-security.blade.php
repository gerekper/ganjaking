@extends('layouts/layoutMaster')

@section('title', 'Account settings - Security')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-account-settings.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-security.js')}}"></script>
<script src="{{asset('assets/js/modal-enable-otp.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Security
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-3 gap-2 gap-lg-0">
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-account')}}"><i class="mdi mdi-account-outline mdi-20px me-1"></i> Account</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i> Security</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-billing')}}"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i> Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-notifications')}}"><i class="mdi mdi-bell-outline mdi-20px me-1"></i> Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i> Connections</a></li>
    </ul>
    <!-- Change Password -->
    <div class="card mb-4">
      <h5 class="card-header">Change Password</h5>
      <div class="card-body">
        <form id="formAccountSettings" method="POST" onsubmit="return false">
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" name="currentPassword" id="currentPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="currentPassword">Current Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="mb-4 col-md-6 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="newPassword">New Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              </div>
            </div>
            <div class="mb-4 col-md-6 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                  <label for="confirmPassword">Confirm New Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
              </div>
            </div>
          </div>
          <h6 class="text-body">Password Requirements:</h6>
          <ul class="ps-3 mb-0">
            <li class="mb-1">Minimum 8 characters long - the more, the better</li>
            <li class="mb-1">At least one lowercase character</li>
            <li>At least one number, symbol, or whitespace character</li>
          </ul>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
          </div>
        </form>
      </div>
    </div>
    <!--/ Change Password -->

    <!-- Two-steps verification -->
    <div class="card mb-4">
      <h5 class="card-header">Two-steps verification</h5>
      <div class="card-body">
        <p>Two factor authentication is not enabled yet.</p>
        <p class="w-75">Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.
          <a href="javascript:void(0);">Learn more.</a>
        </p>
        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#enableOTP">Enable two-factor authentication</button>
      </div>
    </div>
    <!-- Modal -->
    @include('_partials/_modals/modal-enable-otp')
    <!-- /Modal -->

    <!--/ Two-steps verification -->

    <!-- Create an API key -->
    <div class="card mb-4">
      <h5 class="card-header">Create an API key</h5>
      <div class="row">
        <div class="col-md-5">
          <div class="card-body">
            <form id="formAccountSettingsApiKey" method="POST" onsubmit="return false">
              <div class="row gy-3">
                <div class="col-12">
                  <div class="form-floating form-floating-outline">
                    <select id="apiAccess" class="select2 form-select">
                      <option value="">Choose Key Type</option>
                      <option value="full">Full Control</option>
                      <option value="modify">Modify</option>
                      <option value="read-execute">Read & Execute</option>
                      <option value="folders">List Folder Contents</option>
                      <option value="read">Read Only</option>
                      <option value="read-write">Read & Write</option>
                    </select>
                    <label for="apiAccess">Choose the Api key type you want to create</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating form-floating-outline">
                    <input type="text" class="form-control" id="apiKey" name="apiKey" placeholder="Server Key 1" />
                    <label for="apiKey">Name the API key</label>
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary me-2 d-grid w-100">Create Key</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7">
          <div class="text-center mt-4 mx-3 mx-md-0">
            <img src="{{asset('assets/img/illustrations/account-settings-security-illustration.png')}}" class="img-fluid" alt="Api Key Image" width="140">
          </div>
        </div>
      </div>
    </div>
    <!--/ Create an API key -->

    <!-- API Key List & Access -->
    <div class="card mb-4">
      <h5 class="card-header pb-4">API Key List & Access</h5>
      <div class="card-body">
        <p>An API key is a simple encrypted string that identifies an application without any principal. They are useful for accessing public data anonymously, and are used to associate API requests with your project for quota and billing.</p>
        <div class="row">
          <div class="col-md-12">
            <div class="bg-lighter rounded p-3 mb-3 position-relative">
              <div class="dropdown api-key-actions">
                <a class="btn dropdown-toggle text-light hide-arrow p-0" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical mdi-24px"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-pencil-outline me-2"></i>Edit</a>
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-trash-can-outline me-2"></i>Delete</a>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <h4 class="mb-0 me-3">Server Key 1</h4>
                <span class="badge bg-label-primary rounded-pill">Full Access</span>
              </div>
              <div class="d-flex align-items-center mb-2">
                <span class="me-2 fw-semibold">23eaf7f0-f4f7-495e-8b86-fad3261282ac</span>
                <span class="text-light cursor-pointer"><i class="mdi mdi-content-copy"></i></span>
              </div>
              <span class="text-muted">Created on 28 Apr 2021, 18:20 GTM+4:10</span>
            </div>
            <div class="bg-lighter rounded p-3 position-relative mb-3">
              <div class="dropdown api-key-actions">
                <a class="btn dropdown-toggle text-light hide-arrow p-0" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical mdi-24px"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-pencil-outline me-2"></i>Edit</a>
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-trash-can-outline me-2"></i>Delete</a>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <h4 class="mb-0 me-3">Server Key 2</h4>
                <span class="badge bg-label-primary rounded-pill">Read Only</span>
              </div>
              <div class="d-flex align-items-center mb-2">
                <span class="me-2 fw-semibold">bb98e571-a2e2-4de8-90a9-2e231b5e99</span>
                <span class="text-light cursor-pointer"><i class="mdi mdi-content-copy"></i></span>
              </div>
              <span class="text-muted">Created on 12 Feb 2021, 10:30 GTM+2:30</span>
            </div>
            <div class="bg-lighter rounded p-3 position-relative">
              <div class="dropdown api-key-actions">
                <a class="btn dropdown-toggle text-light hide-arrow p-0" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical mdi-24px"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-pencil-outline me-2"></i>Edit</a>
                  <a href="javascript:;" class="dropdown-item"><i class="mdi mdi-trash-can-outline me-2"></i>Delete</a>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <h4 class="mb-0 me-3">Server Key 3</h4>
                <span class="badge bg-label-primary rounded-pill">Full Access</span>
              </div>
              <div class="d-flex align-items-center mb-2">
                <span class="me-2 fw-semibold">2e915e59-3105-47f2-8838-6e46bf83b711</span>
                <span class="text-light cursor-pointer"><i class="mdi mdi-content-copy"></i></span>
              </div>
              <span class="text-muted">Created on 28 Dec 2020, 12:21 GTM+4:10</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ API Key List & Access -->

    <!-- Recent Devices -->
    <div class="card mb-4">
      <h5 class="card-header">Recent Devices</h5>
      <div class="table-responsive">
        <table class="table">
          <thead class="table-light">
            <tr>
              <th class="text-truncate">Browser</th>
              <th class="text-truncate">Device</th>
              <th class="text-truncate">Location</th>
              <th class="text-truncate">Recent Activities</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-laptop mdi-20px text-warning me-3'></i>Chrome on Windows</td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">10, July 2021 20:07</td>
            </tr>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-android mdi-20px text-success me-3'></i>Chrome on iPhone</td>
              <td class="text-truncate">iPhone 12x</td>
              <td class="text-truncate">Australia</td>
              <td class="text-truncate">13, July 2021 10:10</td>
            </tr>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-cellphone mdi-20px text-danger me-3'></i>Chrome on Android</td>
              <td class="text-truncate">Oneplus 9 Pro</td>
              <td class="text-truncate">Dubai</td>
              <td class="text-truncate">14, July 2021 15:15</td>
            </tr>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-monitor mdi-20px text-info me-3'></i>Chrome on MacOS</td>
              <td class="text-truncate">Apple iMac</td>
              <td class="text-truncate">India</td>
              <td class="text-truncate">16, July 2021 16:17</td>
            </tr>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-laptop mdi-20px text-warning me-3'></i>Chrome on Windows</td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">20, July 2021 21:01</td>
            </tr>
            <tr>
              <td class="text-truncate text-heading"><i class='mdi mdi-android mdi-20px text-success me-3'></i>Chrome on Android</td>
              <td class="text-truncate">Oneplus 9 Pro</td>
              <td class="text-truncate">Dubai</td>
              <td class="text-truncate">21, July 2021 12:22</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Recent Devices -->

  </div>
</div>

@endsection
