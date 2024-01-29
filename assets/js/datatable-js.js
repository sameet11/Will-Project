(function ($) {

    'use strict';
    function init() { 
	    //dataTable
		if($('#datatable').length>0){
			$('#datatable').DataTable(
			);
		}
		 
	    //dataTable with export to excel stages
		if($('.datatableWithExportStages').length>0){
			$('.datatableWithExportStages').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Stages',
					exportOptions: {
						columns: [0,1]
					}
				}],
				columnDefs: [
					{ targets: [2], orderable: false }
				]
			
			});
		}
	    //dataTable with export to excel holidays
		if($('.datatableWithExportHolidays').length>0){
			$('.datatableWithExportHolidays').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Holidays',
					exportOptions: {
						columns: [0,1,2,3,4]
					}
				}],
				columnDefs: [
					{ targets: [5], orderable: false }
				]
			
			});
		}
	    //dataTable with export to excel document types
		if($('.datatableWithExportDocumentTypes').length>0){
			$('.datatableWithExportDocumentTypes').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Document Types',
					exportOptions: {
						columns: [0]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
	     //dataTable with export to excel industries
		if($('.datatableWithExportIndustries').length>0){
			$('.datatableWithExportIndustries').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Activities',
					exportOptions: {
						columns: [0]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		//dataTable with export to excel Document Type
		if($('.datatableWithExportDocumentType').length>0){
			$('.datatableWithExportDocumentType').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Document Type',
					exportOptions: {
						columns: [0]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		 
		
		     //dataTable with export to excel proposals
        if($('.datatableWithExportproposal').length>0){
			$('.datatableWithExportproposal').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Proposals',
					exportOptions: {
						columns: [0,1,2,3,4,5]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
	    //dataTable with export to excel segments
		if($('.datatableWithExportSegment').length>0){
			$('.datatableWithExportSegment').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Segments',
					exportOptions: {
						columns: [0]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
	    //dataTable with export to excel enquiries
		if($('.datatableWithExportEnquiries').length>0){
			$('.datatableWithExportEnquiries').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Enquiries',
					exportOptions: {
						columns: [0,1,2,3,4]
					}
				}],
				columnDefs: [
					{ targets: [5], orderable: false }
				]
			
			});
		}
		
	    //dataTable with export to excel finance reminders
		if($('.datatableWithReminders').length>0){
			$('.datatableWithReminders').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Finance Reminders',
					exportOptions: {
						columns: [0,1,2,3,4,5,6]
					}
				}]
			
			});
		}
		
		 //dataTable with export to excel to do list
		if($('.datatableWithExportToDoList').length>0){
			$('.datatableWithExportToDoList').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'To Do List',
					exportOptions: {
						columns: [0,1,2]
					}
				}],
				columnDefs: [
					{ targets: [3], orderable: false }
				]
			
			});
		}
		 //dataTable with export to excel expenses
		if($('.datatableWithExportExpenses').length>0){
			$('.datatableWithExportExpenses').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Expenses',
					exportOptions: {
						columns: [0,1,2,3,4]
					}
				}],
				columnDefs: [
					{ targets: [5], orderable: false }
				]
			
			});
		}
		//dataTable with export to excel Consultant and Vendor Category
		if($('.datatableWithExportVendor').length>0){
			$('.datatableWithExportVendor').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Consultant and Vendor Category',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		//dataTable with export to excel country
		if($('.datatableWithExportCountry').length>0){
			$('.datatableWithExportCountry').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Countries',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		 //dataTable with export to excel consultant and vendors
		if($('.datatableWithExportConsultantandvendor').length>0){
			$('.datatableWithExportConsultantandvendor').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Consultant and Vendors',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		//dataTable with export to excel product category
		if($('.datatableWithExportproductcategory').length>0){
			$('.datatableWithExportproductcategory').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Product Category Names',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		//dataTable with export to excel project category
		if($('.datatableWithExportprojectcategory').length>0){
			$('.datatableWithExportprojectcategory').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Project Category',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		//dataTable with export to excel department
		if($('.datatableWithExportDepartment').length>0){
			$('.datatableWithExportDepartment').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Department',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}

		//dataTable with export to excel designation
		if($('.datatableWithExportDesignation').length>0){
			$('.datatableWithExportDesignation').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Designation',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		//dataTable with export to excel Relationship
		if($('.datatableWithExportRelationship').length>0){
			$('.datatableWithExportRelationship').DataTable({ 
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Relationship',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		//dataTable with export to excel TypeOfService
		if($('.datatableWithExportTypeOfService').length>0){
			$('.datatableWithExportTypeOfService').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Type Of Service',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		
		
		//dataTable with export to excel TypeOfService
		if($('.datatableWithExportExpenseType').length>0){
			$('.datatableWithExportExpenseType').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Expense Type',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		//dataTable with export to excel EmployeeStatus
		if($('.datatableWithExportEmployeeStatus').length>0){
			$('.datatableWithExportEmployeeStatus').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Employee Status',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		
    //dataTable with export to excel tasks
		if($('.datatableWithExporttasks').length>0){
			$('.datatableWithExporttasks').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Tasks',
					exportOptions: {
						columns: [0,1]
					}
				}],
				
				columnDefs: [
					{ targets: [2], orderable: false }
				]
			
			});
		}
		
		
		//dataTable with export to excel subtasks
		if($('.datatableWithExportsubtasks').length>0){
			$('.datatableWithExportsubtasks').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Subtasks',
					exportOptions: {
						columns: [0]
					}
				}],
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
		
		
		
		//dataTable with export to excel business type
		if($('.datatableWithExportBusinessType').length>0){
			$('.datatableWithExportBusinessType').DataTable({
				dom: 'Blfrtip',
				lengthChange: true,
				buttons: [
				{
					extend: 'excel',
					text: '<i class="mdi-file-excel" data-toggle="tooltip" title="Export To Excel"></i>',
					title:'Business Type',
					exportOptions: {
						columns: [0]
					}
				}] ,
				columnDefs: [
					{ targets: [1], orderable: false }
				]
			
			});
		}
   
    }

    init();
	


})(jQuery)
