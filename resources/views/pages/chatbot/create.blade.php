@extends('layouts.master')

@section('content')
<div class="card-table nunito-font">
  <div class="card card-dashboard-table-six">

    <div>
      <h5 class="card-title text-info">Create new command</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ Route('command.store') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group row">
           <div class="col-lg-12">
             <span class="mute">Command</span>
             <input type="text" name="command" class="form-control"
             placeholder="command..." required autofocus="" autocomplete="off">
           </div>

       </div>


     <div class="form-group">
      <span class="mute">Description</span>
      <textarea id="response" name="response" rows="5" class="form-control" 
      required autofocus="" placeholder="type the meaning of the command here..."></textarea>
    </div>


    <div class="form-group">

      <div class="row">

       <div class="col-lg-3">
         <input type="submit" class="btn btn-success" name="submit" value="Save Command">
       </div>

       <div class="col-lg-10 text-center">

          @if(session()->get('success'))
          <div class='alert alert-success alert-dismissible' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Yello!</strong> {{ session()->get('success') }}
             <i class="fa fa-check-circle"></i>
          </div>
          @endif

          @if(session()->get('fail'))
          <div class='alert alert-danger alert-dismissible' role='alert'>
           <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span></button>
            <strong>Oops!</strong> {{ session()->get('fail') }}
          </div>
          @endif

        </div>


    </div>

  </div>

</form>


</div>
</div>
</div>

@endsection
