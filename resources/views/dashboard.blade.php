<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Dashboard') }}</h2></x-slot>
<div class="py-12 max-w-7xl mx-auto px-4 space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach(['Links'=>$stats['total_links'],'Clicks'=>number_format($stats['total_clicks']),'Valid views'=>number_format($stats['valid_views']),'Balance'=>number_format($stats['balance']).' đ','Total earned'=>number_format($stats['total_earned']).' đ'] as $label=>$value)
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __($label) }}</div>
            <div class="text-xl font-bold">{{ $value }}</div>
        </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
        <h3 class="font-semibold mb-2">{{ __('Last 30 days') }}</h3>
        <canvas id="chart" height="80"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart').getContext('2d'),{
    type:'line',
    data:{labels:@json($labels), datasets:[
        {label:'Clicks',data:@json($totals),borderColor:'#3b82f6',tension:0.2},
        {label:'Earned (đ)',data:@json($earnings),borderColor:'#10b981',tension:0.2,yAxisID:'y1'},
    ]},
    options:{
        responsive:true, interaction:{mode:'index',intersect:false},
        scales:{
            y:{type:'linear',position:'left',beginAtZero:true},
            y1:{type:'linear',position:'right',beginAtZero:true,grid:{drawOnChartArea:false}},
        }
    }
});
</script>
</x-app-layout>
