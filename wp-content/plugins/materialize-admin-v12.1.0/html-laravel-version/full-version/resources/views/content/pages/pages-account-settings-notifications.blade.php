@extends('layouts/layoutMaster')

@section('title', 'Account settings - Pages')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Notifications
</h4>

<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-md-row mb-3 gap-2 gap-lg-0">
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-account')}}"><i class="mdi mdi-account-outline mdi-20px me-1"></i> Account</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-security')}}"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i> Security</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-billing')}}"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i> Billing & Plans</a></li>
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-bell-outline mdi-20px me-1"></i> Notifications</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-connections')}}"><i class="mdi mdi-link mdi-20px me-1"></i> Connections</a></li>
    </ul>
    <div class="card">
      <!-- Notifications -->
      <h5 class="card-header">Recent Devices</h5>
      <div class="card-body">
        <span>We need permission from your browser to show notifications. <a href="javascript:void(0);" class="notificationRequest">Request Permission</a></span>
        <div class="error"></div>
        <div class="table-responsive border rounded my-4">
          <table class="table">
            <thead class="table-light">
              <tr>
                <th class="text-nowrap fw-medium">Type</th>
                <th class="text-nowrap fw-medium text-center">✉️ Email</th>
                <th class="text-nowrap fw-medium text-center">🖥 Browser</th>
                <th class="text-nowrap fw-medium text-center">👩🏻‍💻 App</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-nowrap">New for you</td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck1" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck2" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck3" checked />
                  </div>
                </td>
              </tr>
              <tr>
                <td class="text-nowrap">Account activity</td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck4" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck5" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck6" checked />
                  </div>
                </td>
              </tr>
              <tr>
                <td class="text-nowrap">A new browser used to sign in</td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck7" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck8" checked />
                  </div>
                </td>
                <td>
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck9" />
                  </div>
                </td>
              </tr>
              <tr>
                <td class="text-nowrap border-bottom-0">A new device is linked</td>
                <td class="border-bottom-0">
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck10" checked />
                  </div>
                </td>
                <td class="border-bottom-0">
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck11" />
                  </div>
                </td>
                <td class="border-bottom-0">
                  <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" id="defaultCheck12" />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <h6 class="pt-3">When should we send you notifications?</h6>
        <form action="javascript:void(0);">
          <div class="row">
            <div class="col-sm-6">
              <select id="sendNotification" class="form-select" name="sendNotification">
                <option selected>Only when I'm online</option>
                <option>Anytime</option>
              </select>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-primary me-2">Save changes</button>
              <button type="reset" class="btn btn-outline-secondary">Discard</button>
            </div>
          </div>
        </form>
      </div>
      <!-- /Notifications -->
    </div>
  </div>
</div>

@endsection
