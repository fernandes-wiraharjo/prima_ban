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
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jkanban/jkanban.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
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
          <i class="bx bx-plus me-1"></i>
          <span class="align-middle">Add new</span>
        </label>
        <input type="text" class="form-control w-px-250 kanban-add-board-input mb-2 d-none" placeholder="Add Board Title" id="kanban-add-board-input" required />
        <div class="mb-3 kanban-add-board-input d-none">
          <button class="btn btn-primary btn-sm me-2">Add</button>
          <button type="button" class="btn btn-label-secondary btn-sm kanban-add-board-cancel-btn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Kanban Wrapper -->
  <div class="kanban-wrapper"></div>

  <!-- Edit Task /Task & Activities -->
  <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
    <div class="offcanvas-header border-bottom py-3 my-1">
      <h5 class="offcanvas-title">Edit Task </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-4">
      <ul class="nav nav-pills tabs-line">
        <li class="nav-item">
          <button class="nav-link active shadow-none" data-bs-toggle="tab" data-bs-target="#tab-update">
            <i class="bx bx-edit me-2"></i>
            <span class="align-middle text-uppercase">Edit</span>
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link shadow-none" data-bs-toggle="tab" data-bs-target="#tab-activity">
            <i class="bx bx-bar-chart me-2"></i>
            <span class="align-middle text-uppercase">Activity</span>
          </button>
        </li>
      </ul>
      <div class="tab-content px-0 pb-0 border-0">
        <!-- Update item/tasks -->
        <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
          <form>
            <div class="mb-3">
              <label class="form-label" for="title">Title</label>
              <input type="text" id="title" class="form-control" placeholder="Enter Title" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="due-date">Due Date</label>
              <input type="text" id="due-date" class="form-control" placeholder="Enter Due Date" />
            </div>
            <div class="mb-3">
              <label class="form-label" for="label"> label</label>
              <select class="select2 select2-label form-select" id="label">
                <option data-color="bg-label-success" value="UX">UX</option>
                <option data-color="bg-label-warning" value="Images">
                  Images
                </option>
                <option data-color="bg-label-info" value="Dashboard">Dashboard</option>
                <option data-color="bg-label-danger" value="Code Review">
                  Code Review
                </option>
                <option data-color="bg-label-secondary" value="App">
                  App
                </option>
                <option data-color="bg-label-primary" value="Charts & Maps">
                  Charts & Maps
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Assigned</label>
              <div class="assigned d-flex flex-wrap avatar-group"></div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="attachments">Attachments</label>
              <div>
                <input type="file" class="form-control" id="attachments" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Comment</label>
              <div class="comment-editor border-bottom-0"></div>
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
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                  Cancel
                </button>
              </div>
            </div>
          </form>
        </div>
        <!-- Activities -->
        <div class="tab-pane fade" id="tab-activity" role="tabpanel">
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Jordan</span> Left the board.
              </p>
              <small class="text-muted">Today 11:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Dianna</span> mentioned
                <span class="fw-medium text-primary">@bruce</span> in
                a comment.
              </p>
              <small class="text-muted">Today 10:20 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Martian</span> added moved
                Charts & Maps task to the done board.
              </p>
              <small class="text-muted">Today 10:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Barry</span> Commented on App
                review task.
              </p>
              <small class="text-muted">Today 8:32 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Bruce</span> was assigned
                task of code review.
              </p>
              <small class="text-muted">Today 8:30 PM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Clark</span> assigned task UX
                Research to
                <span class="fw-medium text-primary">@martian</span>
              </p>
              <small class="text-muted">Today 8:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Ray</span> Added moved
                <span class="fw-medium">Forms & Tables</span> task
                from in progress to done.
              </p>
              <small class="text-muted">Today 7:45 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Barry</span> Complete all the
                tasks assigned to him.
              </p>
              <small class="text-muted">Today 7:17 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-success rounded-circle">HJ</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Jordan</span> added task to
                update new images.
              </p>
              <small class="text-muted">Today 7:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <img src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar" class="rounded-circle" />
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Dianna</span> moved task
                <span class="fw-medium">FAQ UX</span> from in
                progress to done board.
              </p>
              <small class="text-muted">Today 7:00 AM</small>
            </div>
          </div>
          <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-danger rounded-circle">CK</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Clark</span> added new board
                with name <span class="fw-medium">Done</span>.
              </p>
              <small class="text-muted">Yesterday 3:00 PM</small>
            </div>
          </div>
          <div class="media d-flex align-items-center">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
              <span class="avatar-initial bg-label-dark rounded-circle">BW</span>
            </div>
            <div class="media-body">
              <p class="mb-0">
                <span class="fw-medium">Bruce</span> added new task
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
