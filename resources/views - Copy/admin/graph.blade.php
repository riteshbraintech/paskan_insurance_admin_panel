@php
    $bids_count = $converted_bids_count = $awarded = "";
    $current_month_dates = [];
    $awarded = $awarded_per_month;
    $bids_count = $record['total_bids'];
    $converted_bids_count = $record['total_converted_bids'];
    if($graph_filter == "daily"){
        foreach($month_dates as $month_date){
            array_push($current_month_dates,date('d',strtotime($month_date)));
        }
    }
    if($graph_filter == "weekly"){
        foreach($month_dates as $month_date){
            $range = date('d',strtotime($month_date[0])).'-'.date('d',strtotime($month_date[sizeOf($month_date)-1]));
            array_push($current_month_dates,$range);
        }
    }
    if($graph_filter == "monthly"){
        foreach($month_dates as $key => $val){
            array_push($current_month_dates,$month_dates[$key]['month']);
        }
    }
@endphp

<style>
    .highcharts-figure,.highcharts-data-table table {
        width: 100%;
        margin: 1em auto;
    }
    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,.highcharts-data-table th,.highcharts-data-table caption {padding: 0.5em;}

    .highcharts-data-table thead tr,.highcharts-data-table tr:nth-child(even) {background: #f8f8f8;}

    .highcharts-data-table tr:hover {background: #f1f7ff;}
</style>

<figure class="highcharts-figure">
    <div class="d-flex flex-row-reverse mx-2">
        <select name="graph_filter" id="graph_filter" class=" p-1 rounded graph-filter" onchange="graphFilter(event);">
            <option value="daily" {{$graph_filter == "daily" ? 'selected' : ''}}>Daily</option>
            <option value="weekly" {{$graph_filter == "weekly" ? 'selected' : ''}}>Weekly</option>
            <option value="monthly" {{$graph_filter == "monthly" ? 'selected' : ''}}>Monthly</option>
        </select>
    </div>

    <div id="graph-data"
        data-dates='@json($current_month_dates)'
        data-bids='@json($bids_count)'
        data-converted='@json($converted_bids_count)'
        data-awarded='@json($awarded)'>
    </div>

    <div id="container-graph"></div>
    <p class="highcharts-description"></p>
</figure>

@push('scripts')
<script>
    function renderGraph() {
        const $graph = $('#graph-data');
        if ($graph.length === 0) 
            return;

        const current_month_dates = JSON.parse($graph.attr('data-dates'));
        const bids_count = JSON.parse($graph.attr('data-bids'));
        const converted_bids_count = JSON.parse($graph.attr('data-converted'));
        const awarded = JSON.parse($graph.attr('data-awarded'));

        Highcharts.chart('container-graph', {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Bids To Lead Converted'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                // categories: {{ Illuminate\Support\Js::from($current_month_dates) }} ?? [],
                categories: current_month_dates,
                accessibility: {
                    description: 'Days of the Month'
                }
            },
            yAxis: {
                title: {
                    text: 'Bids To Lead Converted'
                },
                labels: {
                    format: '{value}'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'Bids',
                marker: {
                    symbol: 'square'
                },
                // data: {{ Illuminate\Support\Js::from($bids_count) }} ?? []
                data: bids_count

            }, {
                name: 'Converted Bids',
                marker: {
                    symbol: 'diamond'
                },
                // data: {{ Illuminate\Support\Js::from($converted_bids_count) }} ?? []
                data: converted_bids_count
            },{
                name: 'Awarded',
                marker: {
                    symbol: 'circle'
                },
                // data: {{ Illuminate\Support\Js::from($awarded) }} ?? []
                data: awarded
            }]
        });
    }

    function graphFilter(){
        let dateRange = '';
        let graph_filter = "";
        let staff_id= "";
        let manager_id = "";
        staff_id = $('.staff-filter').val() != undefined ? $('.staff-filter').val() : '';
        manager_id = $('.manager-filter').val() != undefined? $('.manager-filter').val() : '';
        graph_filter = $('.graph-filter').val() ;
        dateRange = $('#bid-date-filter').val() ;

        $.ajax({
            url : "{{route('admin.dashboard')}}",
            type: 'get',
            data: {'method':'graph-ajax','dateRange':dateRange,'graph_filter':graph_filter,'manager_filter':manager_id,'staff_filter':staff_id},
            dataType: 'json',
            success: function(response){
                $('.graph-container').html(response.html);
                renderGraph();
            }
        });
    }
</script>    
@endpush