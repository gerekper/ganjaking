@extends('layouts/layoutMaster')

@section('title', 'Help Center - Pages')

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-help-center.css')}}" />
@endsection

@section('content')
<div class="card">
  <!-- Help Center Header -->
  <div class="help-center-header d-flex flex-column justify-content-center align-items-center">
    <h3 class="text-center text-primary fw-semibold"> Hello, how can we help? </h3>
    <p class="text-center px-3 mb-0">Common troubleshooting topics: eCommerce, Blogging to payment</p>
    <div class="input-wrapper my-3 input-group input-group-lg input-group-merge px-5">
      <span class="input-group-text" id="basic-addon1"><i class="mdi mdi-magnify mdi-20px"></i></span>
      <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1" />
    </div>
  </div>
  <!-- /Help Center Header -->

  <!-- Popular Articles -->
  <div class="help-center-popular-articles py-5">
    <div class="container-xl">
      <h4 class="text-center my-4">Popular Articles</h4>
      <div class="row mb-2">
        <div class="col-lg-10 mx-auto">
          <div class="row">
            <div class="col-md-4 mb-md-0 mb-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/rocket.svg')}}" height="60" alt="Help center landing">
                  <h5>Getting Started</h5>
                  <p> Whether you're new or you're a power user, this article will… </p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-article')}}">Read More</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-md-0 mb-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/gift.svg')}}" height="60" alt="Help center landing">
                  <h5>First Steps</h5>
                  <p> Are you a new customer wondering how to get started? </p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-article')}}">Read More</a>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/keyboard.svg')}}" height="60" alt="Help center landing">
                  <h5>Add External Content</h5>
                  <p> This article will show you how to expand the functionality of... </p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-article')}}">Read More</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Popular Articles -->

  <!-- Knowledge Base -->
  <div class="help-center-knowledge-base bg-help-center py-5 mt-4">
    <div class="container-xl">
      <h4 class="text-center my-4">Knowledge Base</h4>
      <div class="row">
        <div class="col-lg-10 mx-auto mb-2">
          <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-success rounded me-2 w-100">
                        <i class="mdi mdi-rocket-outline mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Getting Started</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">Account</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Authentication</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Billing</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">14 articles</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-info rounded me-2 w-100">
                        <i class="mdi mdi-gift-outline  mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Orders</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">Processing orders</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Payments</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Returns, Refunds and Replacements</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">13 articles</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-primary rounded me-2 w-100">
                        <i class="mdi mdi-lock-open-outline  mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Safety and security</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">Security and hacked accounts</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Privacy</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Spam and fake accounts</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">9 articles</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-danger rounded me-2 w-100">
                        <i class="mdi mdi-content-paste mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Rules and policies</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">General</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Intellectual property</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Guidelines for law enforcement</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">14 articles</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-warning rounded me-2 w-100">
                        <i class="mdi mdi-message-outline mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Chats</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">Account</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Authentication</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Billing</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">14 articles</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-label-secondary  rounded me-2 w-100">
                        <i class="mdi mdi-link mdi-20px">
                        </i>
                      </div>
                    </div>
                    <h5 class="mt-3 ms-2">Connections</h5>
                  </div>
                  <ul>
                    <li class="text-primary py-1"><a href="{{url('pages/help-center-categories')}}">Conversations</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">Jobs</a></li>
                    <li class="text-primary pb-1"><a href="{{url('pages/help-center-categories')}}">People</a></li>
                  </ul>
                  <p class="mb-0">
                    <a href="javascript:void(0);" class="text-body">14 articles</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Knowledge Base -->

  <!-- Keep Learning -->
  <div class="help-center-keep-learning py-5 mb-2">
    <div class="container-xl">
      <h4 class="text-center my-4">Keep Learning</h4>
      <div class="row mb-4">
        <div class="col-lg-10 mx-auto">
          <div class="row">
            <div class="col-md-4 mb-md-0 mb-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/laptop.svg')}}" height="60" alt="Help center landing">
                  <h5>Blogging</h5>
                  <p>Expert tips & tools to improve your website or online store using blog. </p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-categories')}}">Read More</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-md-0 mb-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/lightbulb.svg')}}" height="60" alt="Help center landing">
                  <h5>Inspiration Center</h5>
                  <p>Inspiration from experts to help you start and grow your big ideas.</p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-categories')}}">Read More</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-md-0 mb-4">
              <div class="card border shadow-none">
                <div class="card-body text-center">
                  <img class="mb-3" src="{{asset('assets/svg/icons/discord.svg')}}" height="60" alt="Help center landing">
                  <h5>Community</h5>
                  <p>A group of people living in the same place or having a particular.</p>
                  <a class="btn btn-outline-primary" href="{{url('pages/help-center-categories')}}">Read More</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Keep Learning -->

  <!-- Help Area -->
  <div class="help-center-contact-us bg-help-center">
    <div class="container-xl">
      <div class="row justify-content-center py-5 my-4">
        <div class="col-md-8 col-lg-6 text-center">
          <h4>Still need help?</h4>
          <p class="mb-4"> Our specialists are always happy to help. Contact us during standard business hours or email us 24/7 and we'll get back to you. </p>
          <div class="d-flex justify-content-center flex-wrap gap-4">
            <a href="javascript:void(0);" class="btn btn-primary">Visit our community</a>
            <a href="javascript:void(0);" class="btn btn-primary">Contact us</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Help Area -->
</div>
@endsection
