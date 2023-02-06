@extends('layouts.master')

@section('content')


<!-- <div class="card-table nunito-font"> -->
  <div class="card card-dashboard-table-six">

   <!-- <div class="card-title">
    <h6 class="col-lg-10 text-success">
      <i class="fa fa-home"></i> <span class="text-dark">/ Account settings</span>
    </h6>
  </div> -->

  <div class="card-body">

    <div class="jumbotron bg-glass">
      <div class="table table-responsive">
        <div class="wrapper">
          <div class="wrapper-div-1">
            <h5 class="text-success custom-green">Personal details</h5>
          </div>
          <!-- <div class="wrapper-div-1">
          @isset(Auth::user()->image)
          <img src="{{ asset('uploads/images/'.$user_role.'/'.Auth::user()->image.'') }}" class="profile-avatar " alt="">
          @endisset


          @empty(Auth::user()->image)
          <img src="{{ asset('uploads/images/'.$user_role.'/default/user.png') }}" class="profile-avatar"
          alt="{{Auth::user()->first_name}}" >
          @endempty
        </div> -->

          <div class="wrapper-div-2">
            <a href="{{route('account-settings')}}"><i class="fa fa-pen edit-pen"></i></a>
          </div>
        </div>

        <hr class="dotted-line">
        <table class="table  borderless">

          <tbody>
            <tr>
              <td class="text-muted">Name</td>
              <td class="text-right">{{ Auth::user()->name }}</td>
            </tr>

             <tr>
              <td class="text-muted">Username</td>
              <td class="text-right">{{ Auth::user()->username }}</td>
            </tr>

            <tr>
              <td class="text-muted">Email</td>
              <td class="text-right">{{ Auth::user()->email }}</td>
            </tr>

            <tr>
              <td class="text-muted">Contact</td>
              <td class="text-right">{{ Auth::user()->tel_no }}</td>
            </tr>

            <tr>
              <td class="text-muted">Position</td>
              <td class="text-right">{{ $user_role }}</td>
            </tr>


            <tr>
              <td class="text-muted">Address</td>
              <td class="text-right">{{ Auth::user()->address }}</td>
            </tr>

            <tr>
              <td class="text-muted">Date of Account Creation</td>
              <td class="text-right"><span>{{ Auth::user()->created_at }}</span></td>
            </tr>

          </tbody>


        </table>
      </div>
    </div>

  </div>
</div>
<!-- </div> -->


@endsection
