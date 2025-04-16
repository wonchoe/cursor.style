@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('src_top')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
@endsection

@section('content')


@if(!empty($dates))
<div class="row">
    <div class="col-md-12">
        <div class="bgc-white bd bdrs-3 p-20 mB-20">
            <h4 class="c-grey-900 mB-20">Statistic graph</h4>
            <canvas id="totalChart" height="345" width="1297" class="chartjs-render-monitor" style="display: block; height: 276px; width: 1038px;"></canvas>
        </div>
    </div>
</div>


<script>
    var ctx = document.getElementById('totalChart').getContext('2d');
    function drawChart(dates, total, new_users)
    {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                        label: 'Total',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 199, 132)',
                        data: total,
                        fill: false,
                    }, {
                        label: 'New users',
                        backgroundColor: 'rgb(100, 146, 245)',
                        borderColor: 'rgb(67, 97, 163)',
                        data: new_users,
                        fill: false,
                    }, {
                        label: 'Updates',
                        backgroundColor: 'rgb(255, 77, 3)',
                        borderColor: 'rgb(89, 254, 94)',
                        data: updates,
                        fill: false,
                    }


                ]
            },
            options: {

                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                            }
                        }],
                    yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                            }
                        }]
                }
            }
        });
    }
    dates = '{{ $dates }}';
    responses = '{{ $responses }}';
    new_users = '{{ $new_users }}';
    updates = '{{ $updates }}';
    new_user_real = '{{$new_user_real}}';
    dates = dates.split(',');
    responses = responses.split(',');
    new_users = new_users.split(',');
    updates = updates.split(',');
    new_user_real = new_user_real.split(',');
    dates.forEach(function (el, i) {
        dates[i] = el.slice(8, 10);
    })
    drawChart(dates.reverse(), responses.reverse(), new_users.reverse(), updates.reverse(), new_user_real.reverse());
</script>





<div class="row">
    <div class="col-md-12">
        <div class="bgc-white bd bdrs-3 p-20 mB-20">
            <h4 class="c-grey-900 mB-20">Statistic table</h4>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">New users</th>
                        <th scope="col">New users real</th>
                        <th scope="col">Uninstalls</th>
                        <th scope="col">Updates</th>
                        <th scope="col">HD Mode</th>
                        <th scope="col">Total response</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyticDate as $index => $d)
                    <tr>
                        <th scope="row">{{ $index +1 }}</th>
                        <td>{{ $d->date }}</td>                        
                        <td>{{ $d->new_users }}</td>
                        <td>{{ $d->new_user_real }}</td>
                        <td>{{ $d->count }} ({{ $d->uninst}}%)</td>
                        <td>{{ $d->updates }}</td>
                        <td>{{ $d->hd }}</td>
                        <td>{{ $d->response }}</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@else
<p>no statistic yet</p>
@endif

@endsection