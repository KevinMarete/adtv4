$(function(){
	// Smart Wizard     	
	$('#wizard').smartWizard({
    transitionEffect:'slideleft',
    onLeaveStep:leaveAStepCallback,
    onFinish:onFinishCallback,
    enableFinishButton:true,
    labelFinish:'Start' 
  });

  function leaveAStepCallback(obj){
    var step_num= obj.attr('rel');
    return validateSteps(step_num);
  }
  
  function onFinishCallback(){
   if(validateAllSteps()){
    $('form').submit();
   }
  }

  // Array holding selected row IDs
  var rows_selected = [];
  //Create migration tables
  var table = $('#migration_tbl').DataTable({
    'ajax':'editt/get_tables',
    'columnDefs': [
      {
        'targets': 0,
        'searchable':false,
        'orderable':false,
        'className': 'dt-body-left',
        'render': function (data, type, full, meta){
          return '<input type="checkbox">';
        }
      },
      {
        'width': '350px', 
        'targets': 2 
      }
    ],
    'ordering': false,
    'paging': false,
    'rowCallback': function(row, data, dataIndex){
      // Get row ID
      var rowId = data[0];
      // If row ID is in the list of selected row IDs
      if($.inArray(rowId, rows_selected) !== -1){
        $(row).find('input[type="checkbox"]').prop('checked', true);
        $(row).addClass('selected');
      }
    }
  });

  // Handle click on checkbox
  $('#migration_tbl tbody').on('click', 'input[type="checkbox"]', function(e){
    var $row = $(this).closest('tr');

    // Get row data
    var data = table.row($row).data();
    // Get row ID
    var rowId = data[0];

    // Determine whether row ID is in the list of selected row IDs 
    var index = $.inArray(rowId, rows_selected);
    // If checkbox is checked and row ID is not in list of selected row IDs
    if(this.checked && index === -1){
      rows_selected.push(rowId);
    }// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
    else if (!this.checked && index !== -1){
      rows_selected.splice(index, 1);
    }
    if(this.checked){
      $row.addClass('selected');
    } else {
      $row.removeClass('selected');
    }

    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(table);
    // Prevent click event from propagating to parent
    e.stopPropagation();
  });

  // Handle click on table cells with checkboxes
  $('#migration_tbl').on('click', 'tbody td, thead th:first-child', function(e){
    $(this).parent().find('input[type="checkbox"]').trigger('click');
  });

  // Handle click on "Select all" control
  $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
    if(this.checked){
      $('tbody input[type="checkbox"]:not(:checked)', table.table().container()).trigger('click');
    } else {
      $('tbody input[type="checkbox"]:checked', table.table().container()).trigger('click');
    }
    // Prevent click event from propagating to parent
    e.stopPropagation();
  });

  // Handle table draw event
  table.on('draw', function(){
    // Update state of "Select all" control
    updateDataTableSelectAllCtrl(table);
  });

  // Submit migration form
  $('#migration_frm').submit(function(e){
    e.preventDefault();

    var facility = $('#facility').val();
    var store = $('#store').val();
    var migrationUrl = $(this).attr('action');
    var all_rows = table.rows().data();

    if(rows_selected.length > 0){
      //Hide start button
      $('.buttonFinish').hide();
      $.each(all_rows, function(index, data) {
        //Check if row_id matches selected
        if($.inArray(data[0], rows_selected) != -1){
          var total = $("#"+data[1]+"_bar").attr('total')
          $('#wizard').smartWizard('showMessage','Migration in Progress.....');
          startMigration(migrationUrl, data[1], data[0], total, facility, store);
        }
      });
    }else{
      $('#wizard').smartWizard('showMessage','Please select tables to migrate.');
      $('#wizard').smartWizard('setError',{stepnum:4,iserror:true});
    }
  });  

  //Run after all are done
  $(document).ajaxStop(function(){ 
    if($('#wizard').smartWizard('currentStep') == 4 && rows_selected.length > 0){
      //Set migration complete message
      $('#wizard').smartWizard('showMessage','Migration Complete!');
      //Refresh dataTable
      table.ajax.reload();
      //Re-enable start button
      $('.buttonFinish').show();
      //Reset div height
      $('.stepContainer').css('height', 'auto');
      $('.stepContainer').css('min-height', '300px');
      //Reset rows selected
      rows_selected = [];
    }
  });

  function validateAllSteps(){
   var isStepValid = true;
   
   if(validateStep1() == false){
     isStepValid = false;
     $('#wizard').smartWizard('setError',{stepnum:1,iserror:true});         
   }else{
     $('#wizard').smartWizard('setError',{stepnum:1,iserror:false});
   }
   
   if(validateStep3() == false){
     isStepValid = false;
     $('#wizard').smartWizard('setError',{stepnum:3,iserror:true});         
   }else{
     $('#wizard').smartWizard('setError',{stepnum:3,iserror:false});
   }
   
   if(!isStepValid){
      $('#wizard').smartWizard('showMessage','Please correct the errors in the steps and continue');
   }
          
   return isStepValid;
  }   

  function validateSteps(step){
    var isStepValid = true;
    // validate step 1
    if(step == 1){
      if(validateStep1() == false ){
        isStepValid = false; 
        $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
        $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
      }else{
        isStepValid = establishDBConnection('source');
        if(isStepValid == false){
          $('#wizard').smartWizard('showMessage','Failed to connect to source database, check your parameters.');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});
        }else{
          $('.msgBox').hide();
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        }
      }
    }

    // validate step 2
    if(step == 2){
      if(validateStep2() == false ){
        isStepValid = false; 
        $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
        $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
      }else{
        isStepValid = establishDBConnection('target');
        if(isStepValid == false){
          $('#wizard').smartWizard('showMessage','Failed to connect to target database, check your parameters.');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});
        }else{
          $('.msgBox').hide();
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
          //Load Facilities
          loadSelect2Data('#facility', 'editt/get_facilities')
          //Load Stores
          loadSelect2Data('#store', 'editt/get_stores')
        }
      }
    }
    
    // validate step3
    if(step == 3){
      if(validateStep3() == false ){
        isStepValid = false; 
        $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
        $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
      }else{
        $('.msgBox').hide();
        table.ajax.reload();
        $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
      }
    }
    return isStepValid;
  }

  function validateStep1(){
     var isValid = true; 
     // Validate source_hostname
     var source_hostname = $('#source_hostname').val();
     if(!source_hostname && source_hostname.length <= 0){
       isValid = false;
       $('#msg_source_hostname').html('Please fill hostname').show();
     }else{
       $('#msg_source_hostname').html('').hide();
     }

     // Validate source_port
     var source_port = $('#source_port').val();
     if(!source_port && source_port.length <= 0){
       isValid = false;
       $('#msg_source_port').html('Please fill port').show();
     }else{
       $('#msg_source_port').html('').hide();
     }

     // Validate source_username
     var source_username = $('#source_username').val();
     if(!source_username && source_username.length <= 0){
       isValid = false;
       $('#msg_source_username').html('Please fill user').show();
     }else{
       $('#msg_source_username').html('').hide();
     }

     // Validate source_password
     var source_password = $('#source_password').val();
     if(!source_password && source_password.length <= 0){
       isValid = false;
       $('#msg_source_password').html('Please fill password').show();
     }else{
       $('#msg_source_password').html('').hide();
     }

     // Validate source_database
     var source_database = $('#source_database').val();
     if(!source_database && source_database.length <= 0){
       isValid = false;
       $('#msg_source_database').html('Please fill database name').show();
     }else{
       $('#msg_source_database').html('').hide();
     }
     
     // validate source_driver
     var source_driver = $('#source_driver').val();
     if(!source_driver && source_driver.length <= 0){
       isValid = false;
       $('#msg_source_driver').html('Please fill password').show();         
     }else{
       $('#msg_source_driver').html('').hide();
     }
     return isValid;
  }

  function validateStep2(){
     var isValid = true; 
     // Validate target_hostname
     var target_hostname = $('#target_hostname').val();
     if(!target_hostname && target_hostname.length <= 0){
       isValid = false;
       $('#msg_target_hostname').html('Please fill hostname').show();
     }else{
       $('#msg_target_hostname').html('').hide();
     }

     // Validate target_port
     var target_port = $('#target_port').val();
     if(!target_port && target_port.length <= 0){
       isValid = false;
       $('#msg_target_port').html('Please fill port').show();
     }else{
       $('#msg_target_port').html('').hide();
     }

     // Validate target_username
     var target_username = $('#target_username').val();
     if(!target_username && target_username.length <= 0){
       isValid = false;
       $('#msg_target_username').html('Please fill user').show();
     }else{
       $('#msg_target_username').html('').hide();
     }

     // Validate target_password
     var target_password = $('#target_password').val();
     if(!target_password && target_password.length <= 0){
       isValid = false;
       $('#msg_target_password').html('Please fill password').show();
     }else{
       $('#msg_target_password').html('').hide();
     }

     // Validate target_database
     var target_database = $('#target_database').val();
     if(!target_database && target_database.length <= 0){
       isValid = false;
       $('#msg_target_database').html('Please fill database name').show();
     }else{
       $('#msg_target_database').html('').hide();
     }
     
     // validate target_driver
     var target_driver = $('#target_driver').val();
     if(!target_driver && target_driver.length <= 0){
       isValid = false;
       $('#msg_target_driver').html('Please fill password').show();         
     }else{
       $('#msg_target_driver').html('').hide();
     }
     return isValid;
  }

  function validateStep3(){
    var isValid = true;
    // Validate facility
    var facility = $('#facility').val();
    if(!facility && facility.length <= 0){
      isValid = false;
      $('#msg_facility').html('Please select a facility').show();
    }else{
      $('#msg_facility').html('').hide();
    }
    // Validate store
    var store = $('#store').val();
    if(!store && store.length <= 0){
      isValid = false;
      $('#msg_store').html('Please select a store').show();
    }else{
      $('#msg_store').html('').hide();
    }    
    return isValid;
  }

  function loadSelect2Data(selectClass, dataUrl){
    $(selectClass).select2({
      ajax: {
        url: dataUrl,
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term // search term
          };
        },
        processResults: function (data) {
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });
  }

  function updateDataTableSelectAllCtrl(table){
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

    // If none of the checkboxes are checked
    if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
        chkbox_select_all.indeterminate = false;
      }
    }// If all of the checkboxes are checked
    else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
        chkbox_select_all.indeterminate = false;
      } 
    }// If some of the checkboxes are checked 
    else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
        chkbox_select_all.indeterminate = true;
      }
    }
  }

  function establishDBConnection(category){
    var response = ''
    var params = {
      driver: $('#'+category+'_driver').val(),
      username: $('#'+category+'_username').val(),
      password: $('#'+category+'_password').val(),
      hostname: $('#'+category+'_hostname').val(), 
      port: $('#'+category+'_port').val(), 
      database: $('#'+category+'_database').val()
    }

    //Synchronous post
    $.ajax({
      type: 'POST',
      url: 'editt/get_db_connection/'+category,
      data: params,
      dataType: 'json',
      async: false,
      success: function(data){
        response = data['status'];
      }
    });

    return response;
  }

  function startMigration(migrationURL, sourceTbl, destinationTbl, total, facility, store){
    var migrationURL = migrationURL+'/'+sourceTbl+'/'+destinationTbl+'/'+facility+'/'+store+'/'+total;

    //Progressbar
    var bar = new ProgressBar.Line('#'+sourceTbl+'_bar',  {
      strokeWidth: 2,
      easing: 'easeInOut',
      duration: 1400,
      color: '#EA8511',
      trailColor: '#eee',
      trailWidth: 1,
      svgStyle: {width: '100%', height: '50%'},
      text: {
        style: {
          color: '#000',
          position: 'static',
          right: '0',
          top: '0px',
          padding: 0,
          margin: 0,
          transform: null
        },
        autoStyleContainer: false
      },
      from: {color: '#FFEA82'},
      to: {color: '#ED6A5A'},
      step: (state, bar) => {
        bar.setText(Math.round(bar.value() * 100) + ' %');
      }
    });
    var progress_value = 0
    bar.animate(progress_value);  // Value from 0.0 to 1.0

    runMigration(migrationURL, bar, total, 0);
  }

  function runMigration(migrationURL, bar, total, offset){
    var mainURL = migrationURL +'/'+ offset
    $.ajax({
      type: 'GET',
      url: mainURL,
      dataType: 'json',
      success: function(data){
        offset = data['offset'];
        progress_value = (offset/total);
        bar.animate(progress_value);
        if(progress_value < 1){
          runMigration(migrationURL, bar, total, offset);
        }
      }
    });
  }

});
