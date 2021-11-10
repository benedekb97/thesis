@extends('layouts.main')

@section('title', 'Profile')

@section('content')
    <h1 class="page-title">My profile</h1>
    <div class="accordion" id="profile">
        <div class="accordion-item">
            <h2 class="accordion-header" id="profile-data-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#profile-data" data-bs-parent="#profile">
                    My data
                </button>
            </h2>
            <div id="profile-data" class="accordion-collapse collapse show" data-bs-parent="profile">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col text-end">
                            <strong>Name</strong>
                        </div>
                        <div class="col col-10">
                            {{ $user->getName() }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-end">
                            <strong>Email address</strong>
                        </div>
                        <div class="col col-10">
                            {{ $user->getEmail() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="profile-actions-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#profile-actions" data-bs-parent="#profile">
                    Actions
                </button>
            </h2>
            <div id="profile-actions" class="accordion-collapse collapse" data-bs-parent="profile">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col text-end">
                            @if ($user->hasPassword())
                                <strong>Change API password</strong>
                            @else
                                <strong>Set API password</strong>
                            @endif
                        </div>
                        <div class="col col-10">
                            @if ($user->hasPassword())
                                <button class="btn btn-sm btn-light btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#change-password-modal">
                                    Change password
                                </button>
                            @else
                                <button class="btn btn-sm btn-light btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#set-password-modal">
                                    Set password
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($success)
        @if ($success === 'newPassword')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Password changed successfully!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif ($success === 'setPassword')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Password set successfully!</strong>
                <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endisset
@endsection

@push('scripts')
    @isset($error)
        @if ($error === 'invalidPassword')
            <script>
                $(document).ready(
                    function () {
                        $('#set-password-modal').modal('toggle');
                        $('#set-password-password-alert').css('display', 'block');
                        $('#set-password-password-alert').html('Invalid new password supplied. Password must be <b>at least 8</b> characters long.');
                    }
                );
            </script>
        @elseif ($error === 'invalidNewPassword')
            <script>
                $(document).ready(
                    function () {
                        $('#change-password-modal').modal('toggle');
                        $('#change-password-new-password-alert').css('display', 'block');
                        $('#change-password-new-password-alert').html('Invalid new password supplied. Password must be <b>at least 8</b> characters long.')
                    }
                );
            </script>
        @endif
    @endisset
@endpush

@push('modals')
    @if($user->hasPassword())
        <div class="modal fade" id="change-password-modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change API password</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST"
                          oninput='password2.setCustomValidity(password2.value !== password1.value ? "Passwords do not match." : "");
                                   password1.setCustomValidity(password1.value.length < 8 ? "Password must be at least 8 characters!" : "");'
                    >
                        @csrf()
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="password" id="change-password-current" name="current-password" class="form-control" placeholder="Current password" required/>
                                <label for="change-password-current">Current password</label>
                            </div>
                            <hr>
                            <div class="alert alert-danger" style="display:none;" id="change-password-new-password-alert"></div>
                            <div class="form-floating mb-3">
                                <input type="password" id="new-password" name="password1" class="form-control" placeholder="New password" required/>
                                <label for="new-password">New password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" id="new-password-confirmation" name="password2" class="form-control" placeholder="Confirm password" required/>
                                <label for="new-password-confirmation">Confirm password</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary" value="Save"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="set-password-modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Set API password</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('profile.password') }}" method="POST"
                          oninput='password2.setCustomValidity(password2.value !== password1.value ? "Passwords do not match." : "");
                                   password1.setCustomValidity(password1.value.length < 8 ? "Password must be at least 8 characters!" : "");'
                    >
                        @csrf()
                        <div class="modal-body">
                            <div class="alert alert-danger" style="display:none;" id="set-password-password-alert"></div>
                            <div class="form-floating mb-3">
                                <input type="password" id="set-password-password" name="password1" class="form-control" placeholder="New password" required/>
                                <label for="set-password-password">New password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" id="new-password-confirmation" name="password2" class="form-control" placeholder="Confirm password" required/>
                                <label for="new-password-confirmation">Confirm password</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary" value="Save"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endpush
