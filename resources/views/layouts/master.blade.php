<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="{{ config('app.name') }}">
    <meta name="author" content="DallingtonCompanies">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company->name }}</title>

    <script>
        window.Laravel = {
            csrfToken: 'csrf_token()'
        }
    </script>
    <script src="{{ asset('vendors/js/jquery-3.3.1.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendors/toastr/css/toastr.min.css') }}">
    <script src="{{ asset('vendors/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dtables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendors/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables/dataTables.select.min.js') }}"></script>

    <script src="{{ asset('vendors/notify/notify.js') }}"></script>
    <script src="{{ asset('vendors/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendors/js/customJs.js') }}"></script>
    <script src="{{ asset('vendors/PrintPagePlugin/jquery.printPage.js') }}"></script>
    <script src="{{ asset('vendors/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('vendors/calendar/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendors/calendar/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/calendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('vendors/js/bootstrap3-typeahead.min.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>

    <script src="{{ asset('vendors/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendors/magnific-popup/dist/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset('vendors/wickedpicker/src/wickedpicker.js') }}"></script>
    <script src="{{ asset('vendors/laravel-ckeditor-master/ckeditor.js') }}"></script>

    <script src="{{ asset('vendors/jquery-tabledit/jquery.tabledit.min.js') }}"></script>


    <script src="{{ asset('vendors/js/jquery.flot.js') }}"></script>
    <script src="{{ asset('vendors/js/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('vendors/js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('vendors/js/chart.flot.sampledata.js') }}"></script>
    <script src="{{ asset('vendors/js/azia.js') }}"></script>

    <script src="{{ asset('js/external.min.js') }}"></script>
    <script src="{{ asset('vendors/custom/js.js') }}"></script>


    <link rel="stylesheet" href="{{ asset('vendors/fonts/montserrat/css.css') }}">

    <link href="{{ asset('vendors/calendar/fullcalendar.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nunito.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/azia.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/css.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/notification.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables/dtables/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/datatables/dtables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('vendors/js/dataTables.jqueryui.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('vendors/datatables/buttons.dataTables.min.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('vendors/js/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/fontawesome/css/all.min.css') }}" rel="stylesheet">


    <link href="{{ asset('vendors/ionicons/docs/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/typicons.font/typicons.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/morris.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/wickedpicker/stylesheets/wickedpicker.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/magnific-popup/dist/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-face.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet" media="all">
    <style>
        tbody>tr>td {
            font-weight: normal !important;
        }

        main {
            padding: 0;
        }

        header {
            position: sticky;
            top: 0;
        }

        .cartPanelHeader {
            position: sticky;
            top: 0;
        }

        .fixedTableHead {
            overflow-y: auto;
            height: 350px;
        }

        .fixedTableHead thead th {
            /* position: sticky; */
            top: 0;
        }

        .cart-table {
            border-collapse: collapse;
            width: 100%;
        }

        .tr-exists {
            background-color: red;
        }

        html,
        body {
            max-width: 100%;
        }

        ::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .close {
            background-color: red !important;
            border-radius: 50px;
            margin-right: 1px !important;
            opacity: 1 !important;
            filter: none !important;
            padding: 5px !important;
            margin-top: 1px !important
        }

        .close span {
            color: #fff;
            font-size: 25px !important;
        }
    </style>
</head>


@auth

    <body>
        <div class="az-body az-body-sidebar az-light">
            @include('layouts.sidebar')
        </div>

    </body>
@endauth

</html>
