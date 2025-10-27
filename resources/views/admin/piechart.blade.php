<style>
    .container {
        width: 80%;
        margin: 15px auto;
    }
    canvas{
        width: 1230px;
        height: 220px;
    }
</style>

@php
    $staffs = [];
    $bid_counts = [];

    foreach($staff_bid_list as $key => $val){
        $staffs[] = $key;
        // $data = "lead:".$val['lead']."/"."bid:".$val['bid']." (".$val['percentage']."%)";
        // array_push($bid_counts,$val['bid']);
        $bid_counts[] = $val['bid'];
    }
@endphp

<div class="container">
    <div id="pie-chart-data"
        data-staffs='@json($staffs)'
        data-bid_counts='@json($bid_counts)'>
    </div>
    <div>
      <canvas id="myChart"></canvas>
    </div>
</div>

@push('scripts')
<script>
    function renderPieChart(){
        const $pieChart = $('#pie-chart-data');
        if ($pieChart.length === 0) 
            return;

        const staffs = JSON.parse($pieChart.attr('data-staffs'));
        const bid_counts = JSON.parse($pieChart.attr('data-bid_counts'));

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                // labels: {{Illuminate\Support\Js::from($staffs)}} ?? [],
                labels: staffs,
                datasets: [{
                    backgroundColor: ["#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c","#34495e"],
                    // data: {{Illuminate\Support\Js::from($bid_counts)}} ?? []
                    data: bid_counts
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: 'right',
                    fullWidth: false,   // shrink legend area so it sits better
                    labels: {
                        padding: 20,
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 30,
                        bottom: 30,
                        left: 30,
                        right: 30
                    }
                }
            }
        });
    }
</script>
@endpush