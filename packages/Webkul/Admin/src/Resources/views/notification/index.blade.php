@extends('admin::layouts.master')

@section('page_title')
    Package Notification Settings
@stop
 

@section('content-wrapper')
 <!-- Core theme CSS (includes Bootstrap)-->
 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
            
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js" defer></script>
  
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

<div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>Notification Setting List</h1>
            </div>

            <div class="page-action">
                <a id="create" onClick="javascript:addData();" href="javascript:void(0);" class="btn btn-lg btn-primary">
                    Create
                </a>
            </div>
       

        </div>

        <div class="page-content">

  <!-- Page content wrapper-->
  <div id="page-content-wrapper">
                <!-- Page content-->
                <div class="container-fluid">
                    <table id="table_content" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Notification Titile</th>
                <th>Notification Message</th>
                <th>Customer Group</th>
                <th>Notification Type</th>
                <th>Notification Times</th>
                <th>Notification Interval</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <tr>
                <td colspan="9" style="text-align:center;">Loading...</td>
            </tr>
        </tbody>
    </table>
    
    

                </div>
            </div>
 
        </div>
    </div>
  



<div class="modal fade" id="myModalTable" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="heading_text">Add Notification Setting</h4>
      </div>
      <div class="modal-body">

            <table id="viewTable" class="table table-striped" data-toggle="table">
            <input type="hidden" id="id" value=""> 
            <tr>
                <td>Notification Titile:</td>
                <td colspan="2"  style="font-weight:bold;"><input type="text" id="notification_titile" value=""> </td>
                </tr>
                <tr>
                <td>Notification Message:</td>
                <td colspan="2" style="font-weight:bold;"><textarea  id="notification_message"></textarea></td>
                </tr>
                <tr>
                <td>Customer Group:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="customer_group_id" value=""></td>
                </tr>
                <tr>
                <td>Notification type:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="notification_type" value=""></td>
                </tr>
                <tr>
                <td>Notification Times:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="notification_times" value=""></td>
                </tr>
                <tr>
                <td>Notification Interval:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="notification_interval" value=""></td>
                </tr>
                <tr>
                <td>Status:</td>
                <td colspan="2" style="font-weight:bold;"><select id="status"><option value="1">Active</option><option value="0">Inactive</option></select></td>
                </tr>
               
           </table>
         
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSaveData" onClick="javascript:saveData();" class="btn btn-default">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Button trigger modal -->
<button id="viewData" type="button" class="btn btn-primary btn-lg" style="display:none" data-toggle="modal" data-target="#myModalTable">
  Launch demo modal
</button>

 <script>
var alldata = [];
var totalpages = [];
var loadedpages = [];
function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "{{ url('/') }}/api/notification-setting/get-list?token=true&limit=1000";
   
    

	        
   
        console.log("get_notification_settings : "+requestURL);

        var checkCall = $.ajax({
            url: requestURL,
            dataType: 'json',
            type: 'get',
            contentType: 'application/json',
            headers: {
                'Accept':'application/json',
                //'Authorization':'Bearer ' + '***...',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: null,
            processData: false,
            beforeSend: function() { },
            success: function( data, textStatus, jQxhr ){
                alldata=data;
                    console.log("get_notification_settings() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["notification_titile"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["notification_message"]+'</td>';
                            table_content +='<td data-value="group">'+data_row["customer_group_id"]+'</td>';
                            table_content +='<td data-value="group">'+data_row["notification_type"]+'</td>';
                            table_content +='<td data-value="group">'+data_row["notification_times"]+'</td>';
                            table_content +='<td data-value="group">'+data_row["notification_interval"]+'</td>';
                            table_content +='<td data-value="group">'+(data_row["status"]==1?"Active":"Inactive")+'</td>';
                            
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Notification Setting">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Notification Setting">';
                            table_content +='<span class="icon trash-icon"></span>';
                            table_content +='</a>';
                            table_content +='</div>';
                            table_content +='</td>';
                            table_content +='</tr>';
                        }
                        document.getElementById("table_body").innerHTML=table_content;
                        
                        $('#table_content').DataTable({
                            pagingType: 'full_numbers',
                            responsive: true,
                            order: [[3, 'desc']],
                            'aoColumns': [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ],
            drawCallback: function(){
                    $('.paginate_button.last:not(.disabled)', this.api().table().container())          
                        .on('click', function(){
                            //alert('last');
                        });       
                }   
                            
                        });
                        
                    } else {
                        // alert(data.message);
                    }
                    
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown get_notification_settings : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this notification setting?"))return;

   requestURL = "{{ url('/') }}/api/notification-setting/delete?token=true&id="+id;
   var dt = $('#table_content').DataTable();
        console.log("delete_notification_setting : "+requestURL);

        var checkCall = $.ajax({
            url: requestURL,
            dataType: 'json',
            type: 'delete',
            contentType: 'application/json',
            headers: {
                'Accept':'application/json',
                //'Authorization':'Bearer ' + '***...',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: null,
            processData: false,
            beforeSend: function() { },
            success: function( data, textStatus, jQxhr ){
                var d= alldata.data;
                var index = d.findIndex(function(d) {
                return d.id == id;
                });
                d=null;
                delete alldata.data[index];
                    console.log("delete_notification_setting() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        alert("This notification setting record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_notification_setting : "+errorThrown );
            }
        });

	 }
	
     
    
     function addData(obj){
                
        console.log("get_notification_setting() Add ");
        // if(data.length>0) {
        
        $("#heading_text").val("Add Notification Setting");
        $("#id").val("");
        $("#notification_titile").val("");
        $("#notification_message").html("");
        $("#customer_group_id").val("");
        $("#notification_type").val("");
        $("#customer_group_id").val("");
        $("#notification_interval").val("");
        $("#status").val(1);
             
        $("#viewData").click();

        // }

	 }
     function editData(id,obj){
        var data= alldata.data;
        var index = data.findIndex(function(d) {
        return d.id == id;
        });
        var data = data[index];

            console.log("get_notification_setting() data  : " + JSON.stringify(data));
            // if(data.length>0) {
            
            $("#heading_text").val("Edit Notification Setting");
            $("#id").val(data["id"]);
            $("#notification_titile").val(data["notification_titile"]);
            $("#notification_message").html(data["notification_message"]);
            $("#customer_group_id").val(data["customer_group_id"]);
            $("#notification_type").val(data["notification_type"]);
            $("#notification_times").val(data["notification_times"]);
            $("#notification_interval").val(data["notification_interval"]);
            $("#status").val(data["status"]);
                
            $("#viewData").click();

            // }

	 }

function saveData() {
    if($("#notification_titile").val().trim()==''){
        alert("Notification Setting title is must required");
        return;
    }
    var act="save";
    var method="post";
    if($("#id").val()!="undefined" && $("#id").val()!=""){
        act="save";
        method="put";
        var data={"id":$("#id").val(),"notification_titile":$("#notification_titile").val(),"notification_message":$("textarea#notification_message").val(),"customer_group_id":$("#customer_group_id").val(),"notification_type":$("#notification_type").val(),"notification_times":$("#notification_times").val(),"notification_interval":$("#notification_interval").val(),"status":$("#status").val()};
        requestURL = "{{ url('/') }}/api/notification-setting/update?token=true";
     } else {
        act="add";
        method="post";
        var data={"notification_titile":$("#notification_titile").val(),"notification_message":$("textarea#notification_message").val(),"customer_group_id":$("#customer_group_id").val(),"notification_type":$("#notification_type").val(),"notification_times":$("#notification_times").val(),"notification_interval":$("#notification_interval").val(),"status":$("#status").val()};
        requestURL = "{{ url('/') }}/api/notification-setting/create?token=true";   
    }
          console.log(act+"_notification_setting : "+requestURL);
          console.log(act+"_notification_setting() data  : " + JSON.stringify(data));
                    
        var dt = $('#table_content').DataTable();

        var checkCall = $.ajax({
            url: requestURL,
            dataType: 'json',
            type: method,
            contentType: 'application/json',
            headers: {
                'Accept':'application/json',
                //'Authorization':'Bearer ' + '***...',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: JSON.stringify(data),
            processData: false,
            beforeSend: function() { },
            success: function( data, textStatus, jQxhr ){
                
                   console.log(act+"_notification_setting() data  : " + JSON.stringify(data));
                    if(data.data!="undefined") {
                          
                        var data_row=data.data;
                            table_content="";
                            //table_content +='<tr id="row'+data_row["id"]+'">';
                           
                             table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                             table_content +='<td data-value="Name">'+data_row["notification_titile"]+'</td>'; 
                             table_content +='<td data-value="group">'+data_row["notification_message"]+'</td>';
                             table_content +='<td data-value="group">'+data_row["customer_group_id"]+'</td>';
                             table_content +='<td data-value="group">'+data_row["notification_type"]+'</td>';
                             table_content +='<td data-value="group">'+data_row["notification_times"]+'</td>';
                             table_content +='<td data-value="group">'+data_row["notification_interval"]+'</td>';
                             table_content +='<td data-value="group">'+(data_row["status"]==1?"Active":"Inactive")+'</td>';
                             
                             table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                             table_content +='<div class="action">';
                             table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Notification Setting">';
                             table_content +='<span class="icon pencil-lg-icon"></span>';
                             table_content +='</a>'; 
                             
                             table_content +='<a href="javascript:deleteData('+data_row["id"]+');" title="Delete Notification Setting">';
                             table_content +='<span class="icon trash-icon"></span>';
                             table_content +='</a>';
                             table_content +='</div>';
                             table_content +='</td>';
                             //table_content +='</tr>';

                        if(act=="add"){
                           // $("#table_content").append(table_content);
                           $("#table_content").prepend('<tr id="row'+data_row["id"]+'">'+table_content+'</tr>');
                           alldata.data[alldata.data.length]=data_row;
                        }else {
                            var d= alldata.data;
                            var index = d.findIndex(function(d) {
                              return d.id == $("#id").val();
                            });
                            d=null;
                            $("#row"+data_row["id"]).html(table_content);
                           
                            alldata.data[index]=data_row;
                            
                        }
                        
                        alert(data.message);
                    
                    } else {
                         alert(data.message);
                    }
                     
                    $(".close").click();
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown "+act+"_notification_setting : "+errorThrown );
            }
        });
};

function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop