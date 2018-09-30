@extends('layouts.backend.app')

@section('title','Settings')

@push('css')

@endpush

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>SETTINGS</h2>
            </div>
            <div class="body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tab-nav-right" role="tablist">
                    <li role="presentation" class="active">
                      <a href="#update_profile" data-toggle="tab" aria-expanded="true">
                        <i class="material-icons">face</i> UPDATE PROFILE
                      </a>
                    </li>
                    <li role="presentation" class="">
                      <a href="#change_password" data-toggle="tab" aria-expanded="false">
                        <i class="material-icons">change_history</i> CHANGE PASSWORD
                      </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="update_profile">

                        <!-- Form for update_profile -->
                        <form class="form-horizontal" method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                          @csrf
                          @method('PUT')

                          <div class="row clearfix">
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  <label for="name">Name :</label>
                              </div>
                              <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                  <div class="form-group">
                                      <div class="form-line">
                                          <input type="text" id="name" class="form-control" placeholder="Enter your name" name="name" value="{{ Auth::user()->name }}">
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="row clearfix">
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  <label for="email">Email Address :</label>
                              </div>
                              <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                  <div class="form-group">
                                      <div class="form-line">
                                          <input type="text" id="email_address_2" class="form-control" placeholder="Enter your email address" name="email" value="{{ Auth::user()->email }}">
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="row clearfix">
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  <label for="profile">Profile Image :</label>
                              </div>
                              <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                  <div class="form-group">
                                      <div class="form-line">
                                          <input type="file" name="image">
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="row clearfix">
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  <label for="about">About :</label>
                              </div>
                              <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                  <div class="form-group">
                                      <div class="form-line">
                                          <textarea rows="5" name="about" class="form-control">{{ Auth::user()->about }}</textarea>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="row clearfix">
                              <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                                  <button type="submit" class="btn btn-primary m-t-15 waves-effect">UPDATE PROFILE</button>
                              </div>
                          </div>

                        </form>
                        <!-- Form for update_profile -->
                    </div><!-- End:tab-pane (1st)-->

                    <div role="tabpanel" class="tab-pane fade" id="change_password">
                      <!-- Form for change_password -->
                      <form class="form-horizontal" method="post" action="{{ route('admin.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row clearfix">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                <label for="old_password">Old Password :</label>
                            </div>
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="password" id="old_password" class="form-control" placeholder="Enter old password" name="old_password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                <label for="password">New Password :</label>
                            </div>
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="password" id="password" class="form-control" placeholder="Enter new password" name="password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                <label for="confirm_password">Confirm Password :</label>
                            </div>
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="password" id="confirm_password" class="form-control" placeholder="Confirm your password" name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">UPDATE PASSWORD</button>
                            </div>
                        </div>

                      </form>
                      <!-- Form for change_password -->
                    </div><!-- End:tab-pane (2nd) -->
                </div>
            </div>
        </div>
      </div><!-- End:col -->
    </div>
  </div>
@endsection

@push('js')

@endpush
