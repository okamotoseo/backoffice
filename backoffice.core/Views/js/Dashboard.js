/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/

$(document).ready(function(){

  "use strict";

  //Make the dashboard widgets sortable Using jquery UI
  $(".connectedSortable").sortable({
    placeholder: "sort-highlight",
    connectWith: ".connectedSortable",
    handle: ".box-header, .nav-tabs",
    forcePlaceholderSize: true,
    zIndex: 999999
  });
  $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");



  //bootstrap WYSIHTML5 - text editor

  $('.daterange').daterangepicker({
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate: moment()
  }, function (start, end) {
    window.alert("You chose: " + start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  });

  /* jQueryKnob */
  $(".knob").knob();



//  //Sparkline charts
//  var myvalues = [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021];
//  $('#sparkline-1').sparkline(myvalues, {
//    type: 'line',
//    lineColor: '#92c1dc',
//    fillColor: "#ebf4f9",
//    height: '50',
//    width: '80'
//  });
//  myvalues = [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921];
//  $('#sparkline-2').sparkline(myvalues, {
//    type: 'line',
//    lineColor: '#92c1dc',
//    fillColor: "#ebf4f9",
//    height: '50',
//    width: '80'
//  });
//  myvalues = [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21];
//  $('#sparkline-3').sparkline(myvalues, {
//    type: 'line',
//    lineColor: '#92c1dc',
//    fillColor: "#ebf4f9",
//    height: '50',
//    width: '80'
//  });


  //SLIMSCROLL FOR CHAT WIDGET
  $('#chat-box').slimScroll({
    height: '250px'
  });

  /* Morris.js Charts */
  // Sales chart
//  var teste = [
//      {y: '2011 Q1', item1: 2666, item2: 2666},
//      {y: '2011 Q2', item1: 2778, item2: 2294},
//      {y: '2011 Q3', item1: 4912, item2: 1969},
//      {y: '2011 Q4', item1: 3767, item2: 3597},
//      {y: '2012 Q1', item1: 6810, item2: 1914},
//      {y: '2012 Q2', item1: 5670, item2: 4293},
//      {y: '2012 Q3', item1: 4820, item2: 3795},
//      {y: '2012 Q4', item1: 15073, item2: 5967},
//      {y: '2013 Q1', item1: 10687, item2: 4460},
//      {y: '2013 Q2', item1: 8432, item2: 5713}
//    ];

		$.ajax({
			type: "POST",
			async: true,
			url: home_uri+"/Webservice/App.php",
			data: {action:"dashboard-orders", store_id:storeId},
			success: function(data){
				
				var parts = data.split("|");
				var area = new Morris.Area({
				    element: 'revenue-chart',
				    resize: true,
				    data: JSON.parse(parts[0]),
				    xkey: 'y',
				    ykeys: ['1', '8', '7', '9','2', '4', '6', '3'],
				    labels: ['Mercadolivre', "Tray", "Shopee", "Televendas",'Amazon',"Submarino", "Shoptime", 'Lojas Americanas'], 
				    lineColors: ['#3c8dbc', '#a0d0e0', '#a0d0e0', '#a0d0e0', '#a0d0e0', '#a0d0e0', '#a0d0e0', '#a0d0e0'],
				    hideHover: 'auto'
				  });
				//["#3c8dbc", "#f56954", "#00a65a"],
				  //Donut Chart
//				  var donut = new Morris.Donut({
//				    element: 'sales-chart',
//				    resize: true,
//				    colors: ['#3c8dbc', 'red', '#3c8dbc', 'green', 'blue', 'yellow', 'green','#a0d0e0'],
//				    data: JSON.parse(parts[1]),
//				    hideHover: 'auto'
//				  });
				
				
		
		
			}
			
		});
  
//
//		var area = new Morris.Area({
//		    element: 'revenue-chart',
//		    resize: true,
//		    data: $.ajax({
//				type: "POST",
//				async: false,
//				url: home_uri+"/Webservice/App.php",
//				data: {action:"dashboard-orders", store_id:storeId},
//				success: function(data){ return JSON.parse(data); }}),
//		    xkey: 'y',
//		    ykeys: ['item1', 'item2'],
//		    labels: ['Mercadolivre', 'Ecommerce'],
//		    lineColors: ['#a0d0e0', '#3c8dbc'],
//		    hideHover: 'auto'
//		  });
		
  
  var jsonObjOrdersMonths = [];
  $('.ordersMonth').each(function(){
	  var marketplace = $(this).attr('marketplace');
	  var month = $(this).attr('month');
//	  console.log(month);
	  var valor = $(this).val();
	  var item = {};
	  item ['y'] = month;
	  item ['item1'] = valor;
	  jsonObjOrdersMonths.push(item);
	  
  })
  var line = new Morris.Line({
    element: 'line-chart',
    resize: true,
    data:jsonObjOrdersMonths,
    xkey: 'y',
    ykeys: ['item1'],
    labels: ['Mercadolivre'],
    lineColors: ['#efefef'],
    lineWidth: 2,
    hideHover: 'auto',
    gridTextColor: "#fff",
    gridStrokeWidth: 0.4,
    pointSize: 4,
    pointStrokeColors: ["#efefef"],
    gridLineColor: "#efefef",
    gridTextFamily: "Open Sans",
    gridTextSize: 10
  });

//  Donut Chart
//  var donut = new Morris.Donut({
//    element: 'sales-chart',
//    resize: true,
//    colors: ["#3c8dbc", "#f56954", "#00a65a"],
//    data: [
//      {label: "Download Sales", value: 12},
//      {label: "In-Store Sales", value: 30},
//      {label: "Mail-Order Sales", value: 20}
//    ],
//    hideHover: 'auto'
//  });

  //Fix for charts under tabs
  $('.box ul.nav a').on('shown.bs.tab', function () {
    area.redraw();
//    donut.redraw();
    line.redraw();
  });

  /* The todo list plugin */
  $(".todo-list").todolist({
    onCheck: function (ele) {
      window.console.log("The element has been checked");
      return ele;
    },
    onUncheck: function (ele) {
      window.console.log("The element has been unchecked");
      return ele;
    }
  });

});
