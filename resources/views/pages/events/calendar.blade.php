@extends('layouts.master')

@section('content')

<div class="card-table nunito-font">
    <div class="card card-dashboard-table-six">
        <div class="card-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>Calender of Events</h4>
                    </div>
                        <div class="panel-body">
                            <div id='calendar'></div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>



    $(document).ready(function() {
        $('#calendar').fullCalendar({
            themeSystem: 'bootstrap',
            dayMaxEvents: true,
            header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
            },
            events : [
                @foreach($events as $event){
                    title : '{{ $event->title }}',
                    start : '{{ $event->start_date }}',
                },
                @endforeach
            ],

            eventColor: '#378006',
            eventBackgroundColor: '#fc0',
            eventBorderColor: '#006400',
            eventTextColor: '#fff',
            displayEventTime: true,
            display: 'background',
        
        });
    });



</script>



@endsection

