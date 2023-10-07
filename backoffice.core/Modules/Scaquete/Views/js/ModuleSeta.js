$(document).ready(function(){
	$('.autocomplete-products').each(function(){
		var context = $(this);
		$( this ).autocomplete({		 
			source: function( request, response ) {
				$.ajax({
					url: home_uri+"/Modules/Seta/webservice.php",
					dataType: "jsonp",
					data: {
						term: request.term,
						action: "autocomplete-products",
						store_id: storeId,
						account_id: accountId,
						type: context.attr("id")
					},
					success: function( data ) {
	//					console.log(data);
						response( data );
					}
				} );
			},
			minLength: 3,
			select: function( event, ui ) {
				$( this ).val( ui.item.label );
				$("#"+context.attr("id")+"_id").val(ui.item.id);
				return false;
			}
		  
		} );
	
	
		
	})
	
	var table = $("#seta_report_product").DataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 }
        ],
        order: [[0, 'asc']],
        "displayLength": 50,
        rowGroup: {
        	startRender: function ( rows, group ) {
                return '<strong>'+group +' ('+rows.count()+' itens)</strong>';
            },
            
            endRender: function ( rows, group ) {
            	
                var sumQtd = rows
	                .data()
	                .pluck(4)
	                .reduce( function (a, b) {
                    return a + b*1;
                }, 0);
                
                var custAvg = rows
	                .data()
	                .pluck(5)
	                .reduce( function (a, b) {
	                    return a + b*1;
	                }, 0) ;
                cust = $.fn.dataTable.render.number('.', ',', 2).display( custAvg );
                
            
                var priceAvg = rows
                    .data()
                    .pluck(6)
	                .reduce( function (a, b) {
	                    return a + b*1;
	                }, 0);
                price = $.fn.dataTable.render.number('.', ',', 2).display( priceAvg );
                
                
                return $('<tr/>')
	                .append( '<td></td>' )
	                .append( '<td></td>' )
	                .append( '<td></td>' )
	                .append( '<td><b><u>'+sumQtd.toFixed(0)+'</u></b></td>' )
	                .append( '<td><b><u>'+cust+'</u></b></td>' )
	                .append( '<td><b><u>'+price+'</u></b></td>' );
                
 
            },
            dataSrc: 0,
        },
        
		"scrollX": true,
        "language": {
            "lengthMenu": "Visualizar _MENU_ registros",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro encontrado",
            "infoFiltered": "(Localizou _MAX_ registros)",
            "loadingRecords": "Carregando...",
            "processing":     "Processando...",
            "search":         "Procurar:",
            "paginate": {
                "first":      "Primeira",
                "last":       "Última",
                "next":       "Próxima",
                "previous":   "Anterior"
            },
        }
       
    });

//	$('#seta_report_product tbody').on('click', 'tr.group', function () {
//	    var tr = $(this).parents('tr');
//	    var row = table.row( tr );
//	    if ( row.child.isShown() ) {
//	        // This row is already open - close it
//	        row.child.hide();
//	        tr.removeClass('shown');
//	    }
//	    else {
//	        // Open this row (the format() function would return the data to be shown)
//	    	row.child.show();
//	        tr.addClass('shown');
//	    }
//	} );
	
	   // Order by the grouping
    $('#seta_report_product tbody').on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
            table.order( [ 2, 'desc' ] ).draw();
        }
        else {
            table.order( [ 2, 'asc' ] ).draw();
        }
    } );
    
    
//    $('a.toggle-vis').on( 'click', function (e) {
//        e.preventDefault();
// 
//        // Get the column API object
//        var column = table.column( $(this).attr('data-column') );
// 
//        // Toggle the visibility
//        column.visible( ! column.visible() );
//    } );
    
//    table.rows().every( function () {
//        this.child( 'Row details for row: '+this.index() );
//    } );
//     
//    $('#seta_report_product tbody').on( 'click', 'tr', function () {
//        var child = table.row( this ).child;
//     
//        if ( child.isShown() ) {
//            child.hide();
//        }
//        else {
//            child.show();
//        }
//    } );
	
	    
	
})