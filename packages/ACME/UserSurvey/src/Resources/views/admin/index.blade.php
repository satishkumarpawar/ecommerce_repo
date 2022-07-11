@extends('admin::layouts.master')

@section('page_title')
    Package UserSurvey
@stop
 

@section('content-wrapper')
 <!-- Core theme CSS (includes Bootstrap)-->
 
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js" defer></script>
  
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

<div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>UserSurvey List</h1>
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
                <th>User</th>
                <th>Survey Set</th>
                <th>Date Created</th>
                <th>Date Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <tr>
                <td colspan="6" style="text-align:center;">Loading...</td>
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
        <h4 class="modal-title">Survey View</h4>
      </div>
      <div class="modal-body">
            <table id="viewTable" class="table table-striped" data-toggle="table">
           
           </table>
         
      </div>
      <div class="modal-footer">
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
   if(requestURL=='')requestURL = "{{env('APP_URL')}}/api/usersurvey/get-list?token=true&limit=1000";
   
    

	        
   
        console.log("get_survey_list : "+requestURL);

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
                    console.log("get_survey_list() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row.user_info["first_name"]+' '+data_row.user_info["last_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row.survey_set_info["survey_name"]+'</td>';
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:viewData('+data_row["id"]+',\'myModalTable\')" title="View Survey">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Survey">';
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
                    console.log( "Error Thrown get_survey_list : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this survey?"))return;

   requestURL = "{{env('APP_URL')}}/api/usersurvey/delete?token=true&id="+id;
   
   var dt = $('#table_content').DataTable();
        console.log("delete_survey : "+requestURL);

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
                    console.log("delete_survey() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        alert("This survey record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_survey : "+errorThrown );
            }
        });

	 }
	
     
    /*function viewData(id,obj){
        //$("#"+obj).show();
   

        requestURL = "{{env('APP_URL')}}/api/usersurvey/get?token=true&id="+id;
   
        console.log("get_survey : "+requestURL);

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
                    console.log("get_survey() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        var data=data.data[0];
                        var table_content='';
                            table_content +='<tr>';
                            table_content +='<td>Survey Name</td>';
                            table_content +='<td  style="font-weight:bold;">'+data.survey_set_info["survey_name"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Survey User</td>';
                            table_content +='<td  style="font-weight:bold;">'+data.user_info["first_name"]+' '+data.user_info["last_name"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td valign="top">Survey Detail</td>';
                            table_content +='<td><table style="width:100%">';
                            for(i=0;i<data.answer_set.length;i++){
                                var ansrow = data.answer_set[i];
                                table_content +='<tr>';
                                table_content +='<td>Question</td>';
                                table_content +='<td style="font-weight:bold;">'+ansrow['question_text']+'</td>';
                                table_content +='</tr>';
                                table_content +='<tr>';
                                table_content +='<td>Answer</td>';
                                table_content +='<td>'+ansrow['answer_text']+'</td>';
                                table_content +='</tr>';
                                table_content +='<tr>';
                                table_content +='<td>&nbsp;</td>';
                                table_content +='<td>&nbsp;</td>';
                                table_content +='</tr>';
                            }
                            
                            table_content +='</table></td>';
                            table_content +='</tr>';
                      
                        document.getElementById("viewTable").innerHTML=table_content;

                        $("#viewData").click();

                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown get_survey : "+errorThrown );
            }
        });

	 }*/

     function viewData(id,obj){
                var data= alldata.data;
                var index = data.findIndex(function(d) {
                return d.id == id;
                });
                var data = data[index];

                    console.log("get_survey() data  : " + JSON.stringify(data));
                   // if(data.length>0) {
                        var table_content='';
                            table_content +='<tr>';
                            table_content +='<td>Survey Name:</td>';
                            table_content +='<td colspan="2"  style="font-weight:bold;">'+data.survey_set_info["survey_name"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Survey User:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+data.user_info["first_name"]+' '+data.user_info["last_name"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Survey Date:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+new Date(data["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td colspan="3" style="font-weight:bold;">Survey Detail:</td>';
                            table_content +='</tr>';
                            for(i=0;i<data.answer_set.length;i++){
                                var ansrow = data.answer_set[i];
                                table_content +='<tr>';
                                table_content +='<td>&nbsp;</td>';
                                table_content +='<td>Question</td>';
                                table_content +='<td style="font-weight:bold;">'+ansrow['question_text']+'</td>';
                                table_content +='</tr>';
                                table_content +='<tr>';
                                table_content +='<td>&nbsp</td>';
                                table_content +='<td>Answer</td>';
                                table_content +='<td>'+ansrow['answer_text']+'</td>';
                                table_content +='</tr>';
                                table_content +='<tr>';
                                table_content +='<td colspan="3">&nbsp</td>'
                                table_content +='</tr>';
                            }
                           
                        document.getElementById("viewTable").innerHTML=table_content;

                        $("#viewData").click();

                       // }

	 }

function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop