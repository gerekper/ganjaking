@extends('layouts/layoutMaster')

@section('title', 'Kanban - Apps')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/jkanban/jkanban.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-kanban.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor//libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor//libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor//libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor//libs/jkanban/jkanban.js')}}"></script>
<script src="{{asset('assets/vendor//libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor//libs/quill/quill.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-kanban.js')}}"></script>
@endsection

@section('content')
<div class="app-kanban">
  <!-- Add new board -->
  <div class="row">
    <div class="col-12">
      <form class="kanban-add-new-board">
        <label class="kanban-add-board-btn" for="kanban-add-board-input">
          <i class="mdi mdi-plus"></i>
          <span class="align-middle">Add new</span>
        </label>
        <input type="text" class="form-control w-px-250 kanban-add-board-input mb-3 d-none" placeholder="Add Board Title" id="kanban-add-board-input" required />
        <div class="mb-3 kanban-add-board-input d-none">
          <button class="btn btn-primary btn-sm me-2">Add</button>
          <button type="button" class="btn btn-outline-secondary btn-sm kanban-add-board-cancel-btn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Kanban Wrapper -->
  <div class="kanban-wrapper"></div>

  <!-- Edit Task/Task & Activities -->
  <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
    <div class="offcanvas-header bg-lighter py-3">
      <h5 class="offcanvas-title">Edit Task</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-3">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-update">
            <i class="mdi mdi-pencil-outline me-2"></i>
            <span class="align-middle">Update</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-activity">
            <i class="mdi mdi-poll me-2"></i>
            <span class="align-middle">Activity</span>
          </button>
        </li>
      </ul>
      <div class="tab-content px-0 pb-0">
        <!-- Update item/tasks -->
        <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
          <form>
            <div class="form-floating form-floating-outline mb-4">
              <input type="text" id="title" class="form-control" placeholder="Enter Title" />
              <label for="title">Title</label>
            </div>
            <div class="form-floating form-floating-outline mb-4">
              <input type="text" id="due-date" class="form-control" placeholder="Enter Due Date" />
              <label for="due-date">Due Date</label>
            </div>
            <div class="form-floating form-floating-outline mb-4">
              <select class="select2 select2-label form-select" id="label">
                <option data-color="bg-label-success" value="UX">UX</option>
                <option data-color="bg-label-warning" value="Images">
                  Images
                </option>
                <option data-color="bg-label-secondary" value="App">App</option>
                <option data-color="bg-label-danger" value="Code Review">
                  Code Review
                </option>
                <option data-color="bg-label-info" value="Info">
                  Info
                </option>
                <option data-color="bg-label-primary" value="Charts & Maps">
                  Charts & Maps
                </option>
              </select>
              <label for="label"> label</label>
            </div>
            <div class="mb-3">
              <label class="form-label">Assigned</label>
              <div class="assigned d-flex flex-wrap"></div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="attachments">Attachments</label>
              <div>
                <input type="file" class="form-control" id="attachments" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Comment</label>
              <div class="comment-editor"></div>
              <div class="d-flex justify-content-end">
                <div class="comment-toolbar">
                  <span class="ql-formats me-0">
                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>
                    <button class="ql-link"></button>
                    <button class="ql-image"></button>
                  </span>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex flex-wrap">
                <button type="button" class="btn btn-primary me-3" data-bs-dismiss="offcanvas">
                  Save
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                  Cancel
                </button>
              </div>
            </div>
          </form>
        </div>
        <!-- Activities -->
        <div class="tab-pane fade" id="tab-activity" role="tabpanel">
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Jordan</span> Left the board.
              </p>
              <small class="text-muted">Today 11:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/6.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Dianna</span> mentioned
                <span class="fw-semibold text-primary">@bruce</span> in
                a comment.
              </p>
              <small class="text-muted">Today 10:20 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Martian</span> added moved
                Charts & Maps task to the done board.
              </p>
              <small class="text-muted">Today 10:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Barry</span> Commented on App
                review task.
              </p>
              <small class="text-muted">Today 8:32 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Bruce</span> was assigned
                task of code review.
              </p>
              <small class="text-muted">Today 8:30 PM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Clark</span> assigned task UX
                Research to
                <span class="fw-semibold text-primary">@martian</span>
              </p>
              <small class="text-muted">Today 8:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/4.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Ray</span> Added moved
                <span class="fw-semibold text-heading">Forms & Tables</span> task
                from in progress to done.
              </p>
              <small class="text-muted">Today 7:45 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Barry</span> Complete all the
                tasks assigned to him.
              </p>
              <small class="text-muted">Today 7:17 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Jordan</span> added task to
                update new images.
              </p>
              <small class="text-muted">Today 7:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <img src="{{ asset('assets/img/avatars/6.png')}}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Dianna</span> moved task
                <span class="fw-semibold text-heading">FAQ UX</span> from in
                progress to done board.
              </p>
              <small class="text-muted">Today 7:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Clark</span> added new board
                with name <span class="fw-semibold text-heading">Done</span>.
              </p>
              <small class="text-muted">Yesterday 3:00 PM</small>
            </div>
          </div>
          <div class="media d-flex align-items-center">
            <div class="avatar me-2 flex-shrink-0">
              <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
            </div>
            <div class="media-body ms-1">
              <p class="mb-0">
                <span class="fw-semibold text-heading">Bruce</span> added new task
                in progress board.
              </p>
              <small class="text-muted">Yesterday 12:00 PM</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
