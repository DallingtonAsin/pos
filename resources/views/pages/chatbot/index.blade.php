@extends('layouts.master')

@section('content')
<div class="card-table nunito-font">


    <div class="card card-dashboard-table-six">

        <div class="card-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">

                        <div class="row nunito-font">
                            <div class="col-lg-4 text-dark">
                                <h6>
                                    <i class="fa fa-home text-success"> /</i>
                                    <strong>Commands</strong>
                                    <span class="badge">
                                        @isset($number_of_Commands)
                                            {{ $number_of_Commands }}
                                        @endisset
                                    </span>
                                </h6>
                            </div>

                            <div class="col-lg-5">
                                <h5>
                                    <a href="" class="add-link text-decoration-none" data-toggle="modal"
                                        data-target="#modalCommand"><strong>Add Command</strong></a></h5>
                            </div>
                            <div class="col-lg-3">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn border-info text-success bolded form-control text-center dropdown-toggle downloadfilebtn"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="" class="text-info text-decoration-none" data-toggle="modal"
                                        data-target="#importCommands">Import Commands</a>
                                        </li>
                                        <li>
                                            <a href="{{ Route("command.truncate") }}" class="text-info text-decoration-none">Delete all Commands</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel-body">

                    <div class="col-lg-10 text-center nunito-font">
                        @if(session('success'))
                            <div class='alert alert-success alert-dismissible' role='alert'>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span></button>
                                <strong>Yello!</strong> {{ session('success') }}<i
                                    class="fa fa-check-circle"></i>
                            </div>
                        @endif

                        @if(session('fail'))
                            <div class='alert alert-danger alert-dismissible' role='alert'>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span></button>
                                <strong>Oops!</strong> {{ session('fail') }}
                            </div>
                        @endif


                    </div>


                    <div class="table table-responsive nunito-font">
                        <table class="table table-bordered commands-table">

                            <thead>
                                <tr class="success">
                                    <th class="td-sm">No</th>
                                    <th>Command</th>
                                    <th>Description</th>
                                    <th class="td-md">edit</th>
                                    <th class="td-md">delete</th>
                                    <th class="td-md">View</th>
                                </tr>
                            </thead>

                            <tbody>

                                 @foreach ($chatStoredCommands as $cmd)
                                <tr>
                                    <td>{{ $cmd->id }}</td>
                                    <td>{{ $cmd->chat_command }}</td>
                                    <td>{{ $cmd->chat_response }}</td>

                                    <td>
                                        <a href="" class="edit-btn" data-toggle="modal"
                                            data-target="#updateCommand_{{ $cmd->id }}"><span
                                                class="glyphicon glyphicon-pencil"></span></a>
                                    </td>

                                    <td>
                                        <a href="" class="trash-btn" data-toggle="modal"
                                            data-target="#removeCommand_{{ $cmd->id }}"><span
                                                class="glyphicon glyphicon-trash"></span></a>
                                    </td>

                                    <td>
                                        <a href="" class="text-success" data-toggle="modal"
                                            data-target="#viewCommand_{{ $cmd->id }}"><i
                                                class="fa fa-eye"></i></a>
                                    </td>

                                    <!--Modal DeleteCommand -->
                                    <div class="modal fade" id="removeCommand_{{ $cmd->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="ModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header text-center">
                                                    <h5 class="modal-title w-100 font-weight-bold">Delete Command</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">

                                                    <div class="form-group">
                                                        <div class="text-center">
                                                            <label class="text-danger">
                                                                Are you sure you want to remove Command
                                                                <small class="text-dark text-muted bolded">
                                                                    {{ $cmd->chat_command }}
                                                                </small>
                                                                ?
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <form
                                                            action="{{ route('command.destroy', $cmd->id) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-primary"
                                                                name="ConfirmBtn">Yes</button>
                                                            <button type="button" class="btn btn-dark"
                                                                data-dismiss="modal">No</button>
                                                        </form>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of modal DeleteCommand -->


                                    <!-- View Command Details -->
                                    <div class="modal fade" id="viewCommand_{{ $cmd->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-dialog-centered modal-md" role="document">

                                            <div class="modal-content nunito-font border border-custom-dark rounded-0">
                                                <div class="modal-header main-color-bg text-center">
                                                    <h5
                                                        class="modal-title w-100 nunito-font text-white  font-weight-bold">
                                                        <i class="fa fa-info-circle"></i>
                                                        Details of Command</h5>

                                                    <button type="button" class="close view-close text-white"
                                                        data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">

                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <span>Command</span>
                                                            <input type="text" class="form-control bg-white text-dark"
                                                                value="{{ $cmd->chat_command }}" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <span>Description</span>
                                                            <textarea  class="form-control bg-white text-dark" readonly>{{ $cmd->chat_response }}
                                                            </textarea>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of modal ViewCommand-->


                                    <!-- Update Command Details -->
                                    <div class="modal fade nunito-font" id="updateCommand_{{ $cmd->id }}"
                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header text-center">
                                                    <h5 class="modal-title w-100 font-weight-bold">
                                                        Update Command</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">

                                                    @if($errors->any())
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                
                                                                    @foreach($errors as $error)
                                                                    <li>{{ $error }}</li>
                                                                    @endforeach
                                                            </ul>
                                                        </div>
                                                        <br />
                                                    @endif

                                                    <form method="POST"
                                                        action="{{ Route('command.update', $cmd->id) }}">
                                                        @method('PATCH')
                                                        @csrf

                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <span>Command</span>
                                                                <input type="text" class="form-control" name="command"
                                                                    value="{{ $cmd->chat_command }}" Required autofocus>
                                                            </div>

                                                            <div class="form-group">
                                                                <span>Description</span>
                                                                <textarea class="form-control" name="response" Required
                                                                    autofocus>{{ $cmd->chat_response }}</textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-primary"
                                                                    name="UpdateCommandBtn">Update</button>
                                                                <button type="button" class="btn btn-dark"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end of modal UpdateCommand -->


                                </tr>
                        @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!--Import Cashiers -->
<div class="modal fade nunito-font" id="importCommands" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="{{ Route('command.import') }}" method="post" enctype="multipart/form-data"
                name="inportCashiersForm">
                @csrf

                <div class="modal-header text-center">
                    <h5 class="modal-title w-100 font-weight-bold">
                        Import an excel file of commands</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <span>Select file for Upload</span>
                    </div>

                    <div class="form-group">
                        <input type="file" class="form-control-file @error('select_file') is-invalid @enderror"
                            name="select_file" Required autofocus>
                    </div>

                    @error('select_file')
                        <div class='alert alert-danger alert-dismissible text-center' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span></button>
                            <strong>Sorry!</strong> {{ $message }}
                        </div>
                    @enderror

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="AddItemBtn">Upload</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




        <!--Command Registration form -->
        <div class="modal fade nunito-font" id="modalCommand" tabindex="-1" role="dialog"
            aria-labelledby="Modal-Label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <form action="{{ Route('command.store') }}" method="post" name="Commands">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="modal-header text-center">
                            <h5 class="modal-title w-100 font-weight-bold">Add new command</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <span>Command</span>
                                <input type="text" class="form-control" name="command" placeholder="Enter Command..."
                                    Required autofocus>
                            </div>

                            <div class="form-group">
                                <span>Description</span>
                                <textarea type="text" class="form-control" name="response" placeholder="Enter description..."
                                    Required autofocus></textarea>
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-success" name="AddCommandsBtn">Save</button>
                                <button type="reset" class="btn btn-danger">Clear</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function () {
        var table = $('.commands-table');
        var title = "List of registered Commands";
        var columns = [0, 1, 2, 3, 4];
        smartTable(table, title, columns);
    });
</script>

@endsection

