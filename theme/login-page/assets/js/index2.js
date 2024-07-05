$(function(e){

 /* Chartjs (#sales-summary) */
 var myCanvas = document.getElementById("sales-summary");
 myCanvas.height="400";
 var myChart = new Chart( myCanvas, {
	 type: 'bar',
	 data: {
		 labels: ["Jan", "Feb", "Mar", "Apr", "May", "June" ,"July", "Aug", "Sep", "Oct", "Nov", "Dec"],
		 datasets: [{
			 label: 'This Month',
			 data: [28, 17, 28, 23, 15, 19, 28, 22, 15, 28, 21, 28],
			 backgroundColor: '#198A29',
			 borderWidth: 1,
			 hoverBackgroundColor: '#198A29',
			 hoverBorderWidth: 0,
			 borderColor: '#198A29',
			 hoverBorderColor: '#198A29',
		 }, {

			 label: 'Last Month',
			 data: [45, 25, 40, 31, 22, 33, 48, 29, 25, 40, 32, 40],
			 backgroundColor: '#ef5050',
			 borderWidth: 1,
			 hoverBackgroundColor: '#ef5050',
			 hoverBorderWidth: 0,
			 borderColor: '#ef5050',
			 hoverBorderColor: '#ef5050',
		 },
		 {
			 data: [50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50],
			 backgroundColor: '#eff0ff',
			 borderWidth: 1,
			 hoverBackgroundColor: '#eff0ff',
			 hoverBorderWidth: 0,
			 borderColor:'#eff0ff',
			 hoverBorderColor: '#eff0ff',
		 }
	   ]
	 },
	 options: {
		 responsive: true,
		 maintainAspectRatio: false,
		 layout: {
			 padding: {
				 left: 0,
				 right: 0,
				 top: 0,
				 bottom: 0
			 }
		 },
		 tooltips: {
			 enabled: false,
		 },
		 scales: {
			 yAxes: [{
				 gridLines: {
					 display: true,
					 drawBorder: false,
					 zeroLineColor: 'rgba(142, 156, 173,0.1)',
					 color: "rgba(142, 156, 173,0.1)",
				 },
				 scaleLabel: {
					 display: false,
				 },
				 ticks: {
					 beginAtZero: true,
					 stepSize: 10,
					 max: 50,
					 fontColor: "#8492a6",
					 fontFamily: 'Poppins',
				 },
			 }],
			 xAxes: [{
				 barPercentage: 0.15,
				 barValueSpacing :3,
				 barDatasetSpacing : 3,
				 barRadius: 5,
				 stacked: true,
				 ticks: {
					 beginAtZero: true,
					 fontColor: "#8492a6",
					 fontFamily: 'Poppins',
				 },
				 gridLines: {
					 color: "rgba(142, 156, 173,0.1)",
					 display: false
				 },

			 }]
		 },
		 legend: {
            display: false,
          position: 'bottom',
            labels: {
                fontColor: '#333',
            },
			padding :{
				top:5,
				bottom:5,
			},
        },
		 elements: {
			 point: {
				 radius: 0
			 }
		 }
	 }
 });
 /* Chartjs (#sales-summary) closed */

 const ps11 = new PerfectScrollbar('#stud-scroll', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});



});
/* doughnut Chart*/ 
Chart.defaults.RoundedDoughnut    = Chart.helpers.clone(Chart.defaults.doughnut);
Chart.controllers.RoundedDoughnut = Chart.controllers.doughnut.extend({
draw: function(ease) {
	var ctx           = this.chart.ctx;
	var easingDecimal = ease || 1;
	var arcs          = this.getMeta().data;
	Chart.helpers.each(arcs, function(arc, i) {
		arc.transition(easingDecimal).draw();

		var pArc   = arcs[i === 0 ? arcs.length - 1 : i - 1];
		var pColor = pArc._view.backgroundColor;

		var vm         = arc._view;
		var radius     = (vm.outerRadius + vm.innerRadius) / 2;
		var thickness  = (vm.outerRadius - vm.innerRadius) / 2;
		var startAngle = Math.PI - vm.startAngle - Math.PI / 2;
		var angle      = Math.PI - vm.endAngle - Math.PI / 2;

		ctx.save();
		ctx.translate(vm.x, vm.y);

		ctx.fillStyle = i === 0 ? vm.backgroundColor : pColor;
		ctx.beginPath();
		ctx.arc(radius * Math.sin(startAngle), radius * Math.cos(startAngle), thickness, 0, 2 * Math.PI);
		ctx.fill();

		ctx.fillStyle = vm.backgroundColor;
		ctx.beginPath();
		ctx.arc(radius * Math.sin(angle), radius * Math.cos(angle), thickness, 0, 2 * Math.PI);
		ctx.fill();

		ctx.restore();
	});
}
});
if ($('#chart').length) {
var ctx = document.getElementById("chart").getContext("2d");
new Chart(ctx, {
	type: 'RoundedDoughnut',
	data: {
		labels: ['Progress', 'Done'],
		datasets: [{
			data           : [70, 30],
				backgroundColor: [
					'#198A29',
					'#ef5050',
				],
				borderWidth    : 0,
			borderColor:'transparent',
			
		}]
	},
	options: {
		legend: {
			display: false
		},
		cutoutPercentage: 85,
	}
});
}	
/* doughnut Chart*/ 

$(".sparkline_bar-2").sparkline([6,2,8,4,3,8,1,3,6,5,7], {
	type: 'bar',
	height: 90,
	colorMap: {
		'9': '#a1a1a1'
	},
	barColor: '#4801FF',
	barSpacing: 7,
	barWidth: 6,
});
/* sparkline_bar end */
$('.data-table-example').DataTable();
