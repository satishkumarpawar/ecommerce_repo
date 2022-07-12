@extends('admin::layouts.master')

@section('page_title')
    Package Usersurveyset
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
                <h1>Survey Question Set List</h1>
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
                <th>Survey Name</th>
                <th>Survey Level</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Date Created</th>
                <th>Date Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <tr>
                <td colspan="8" style="text-align:center;">Loading...</td>
            </tr>
        </tbody>
    </table>
    
    

                </div>
            </div>
 
        </div>
    </div>
  



<div class="modal fade" id="myModalTableview" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Surveyset View</h4>
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
<button id="viewData" type="button" class="btn btn-primary btn-lg" style="display:none" data-toggle="modal" data-target="#myModalTableview">
  Launch demo modal
</button>

<div class="modal fade" id="myModalTable" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="heading_text">Add Surveyset</h4>
      </div>
      <div class="modal-body">

            <table id="viewTable" class="table table-striped" data-toggle="table">
            <input type="hidden" id="id" value=""> 
            <tr>
                <td>Surveyset Name:</td>
                <td colspan="2"  style="font-weight:bold;"><input type="text" id="survey_name" value=""> </td>
                </tr>
                <tr>
                <td>Level:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="survey_level" value=""></td>
                </tr>
                <tr>
                <td>Start Date:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="start_date" value="">(ex.:2022-06-15)</td>
                </tr>
                <tr>
                <td>End Date:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="end_date" value="">(ex.:2022-06-15)</td>
                </tr>
                
                <tr>
                <td>Surveyset Description:</td>
                <td colspan="2" style="font-weight:bold;"><textarea id="survey_desc" cols="50" rows="5"></textarea></td>
                </tr>

                <tr>
                <td  id="qid" colspan="3" ></td>
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
<button id="saveData" type="button" class="btn btn-primary btn-lg" style="display:none" data-toggle="modal" data-target="#myModalTable">
  Launch demo modal
</button>

 <script>
var alldata = [];
var totalpages = [];
var loadedpages = [];
function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "{{ url('/') }}/api/usersurvey/surveyset/get-list?token=true&limit=1000";
   
    

	        
   
        console.log("get_surveyset_list : "+requestURL);

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
                    console.log("get_surveyset_list() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["survey_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row["survey_level"]+'</td>';
                            table_content +='<td data-value="datec">'+(data_row["start_date"]!='0000-00-00 00:00:00'?new Date(data_row["start_date"]).toISOString().slice(0, 19).replace("T"," "):"")+'</td>';
                            table_content +='<td data-value="datem">'+(data_row["end_date"]!='0000-00-00 00:00:00'?new Date(data_row["end_date"]).toISOString().slice(0, 19).replace("T"," "):"")+'</td>'; 
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:viewData('+data_row["id"]+',\'myModalTable\')" title="View Surveyset">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Surveyset">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 

                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Surveyset">';
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
                    console.log( "Error Thrown get_surveyset_list : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this surveyset?"))return;

   requestURL = "{{ url('/') }}/api/usersurvey/surveyset/delete?token=true&id="+id;
   
   var dt = $('#table_content').DataTable();
        console.log("delete_surveyset : "+requestURL);

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
                    console.log("delete_surveyset() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        alert("This surveyset record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_survey : "+errorThrown );
            }
        });

	 }
	
   
     function viewData(id,obj){
                var data= alldata.data;
                var index = data.findIndex(function(d) {
                return d.id == id;
                });
                var data = data[index];

                    console.log("get_surveyset() data  : " + JSON.stringify(data));
                   // if(data.length>0) {
                        var table_content='';
                            table_content +='<tr>';
                            table_content +='<td>Surveyset Name:</td>';
                            table_content +='<td colspan="2"  style="font-weight:bold;">'+data["survey_name"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Lavel:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+data["survey_level"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Start Date:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+(data["start_date"]!='0000-00-00 00:00:00'?new Date(data["start_date"]).toISOString().slice(0, 19).replace("T"," "):'')+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>End Date:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+(data["end_date"]!='0000-00-00 00:00:00'?new Date(data["end_date"]).toISOString().slice(0, 19).replace("T"," "):'')+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Description:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+data["survey_desc"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td colspan="3" style="font-weight:bold;">Question Detail:</td>';
                            table_content +='</tr>';
                            for(i=0;i<data.question_set.length;i++){
                                var qrow = data.question_set[i];
                                table_content +='<tr>';
                                table_content +='<td>&nbsp;</td>';
                                table_content +='<td>Question</td>';
                                table_content +='<td style="font-weight:bold;">'+qrow['question_text']+'</td>';
                                table_content +='</tr>';
                                for(j=0;j<qrow.answer_options.length;j++){
                                var ansrow = qrow.answer_options[j];
                                table_content +='<tr>';
                                table_content +='<td>&nbsp</td>';
                                table_content +='<td>Option '+(j+1)+'</td>';
                                table_content +='<td>'+ansrow['answer_text']+'</td>';
                                table_content +='</tr>';
                                }
                                table_content +='<tr>';
                                table_content +='<td colspan="3">&nbsp</td>'
                                table_content +='</tr>';
                            }
                           
                        document.getElementById("viewTable").innerHTML=table_content;

                        $("#viewData").click();

                       // }

	 }

 function addData(obj){
                
        console.log("get_surveyset() Add ");
        // if(data.length>0) {
        
        $("#heading_text").val("Add Survey Set");
        $("#id").val("");
        $("#survey_name").val("");
        $("#survey_level").val("");
        $("#start_date").val("");
        $("#end_date").val("");
        $("textarea#survey_desc").val("");
        $("#qid").val("");
            
        $("#saveData").click();

        // }

	 }
     function editData(id,obj){
        var data= alldata.data;
        var index = data.findIndex(function(d) {
        return d.id == id;
        });
        var data = data[index];

            console.log("get_surveyset() data  : " + JSON.stringify(data));
            // if(data.length>0) {
            
            $("#heading_text").val("Edit Surveyset");
            $("#id").val(data["id"]);
            $("#survey_name").val(data["survey_name"]);
            $("#survey_desc").val(data["survey_desc"]);
            $("#survey_level").val(data["survey_level"]);
            $("#start_date").val((data["start_date"]!='0000-00-00 00:00:00'?new Date(data["start_date"]).toISOString().slice(0, 19).replace("T"," "):''));
            $("#end_date").val((data["end_date"]!='0000-00-00 00:00:00'?new Date(data["end_date"]).toISOString().slice(0, 19).replace("T"," "):''));
            $("textarea#survey_desc").val(data["survey_desc"]);
            
            var question_content='<table width="100%">';
            for(i=0;i<data.question_set.length;i++){
                var qrow = data.question_set[i];
                question_content +='<tr>';
                question_content +='<td>&nbsp;</td>';
                question_content +='<td>Question</td>';
                question_content +='<td style="font-weight:bold;">'+qrow['question_text']+'</td>';
                question_content +='<td>';
                question_content +='<input type="checkbox" class="_id" value="'+qrow['question_id']+'">';
                question_content +='<span class="icon trash-icon"></span>';
                question_content +='</td>';
                
                question_content +='</tr>';
                for(j=0;j<qrow.answer_options.length;j++){
                var ansrow = qrow.answer_options[j];
                question_content +='<tr>';
                question_content +='<td>&nbsp</td>';
                question_content +='<td>Option '+(j+1)+'</td>';
                question_content +='<td>'+ansrow['answer_text']+'</td>';
                question_content +='<td>&nbsp;</td>';
                question_content +='</tr>';
                }
                question_content +='<tr>';
                question_content +='<td colspan="4">&nbsp</td>'
                question_content +='</tr>';
               
            }
            question_content +='</table>';
            $("#qid").html(question_content);

            $("#saveData").click();

            // }

	 }

function saveData() {
    if($("#survey_name").val().trim()==''){
        alert("Surveyset name is must required");
        return;
    }
    var act="save";
    var method="post";
    if($("#start_date").val()=='')$("#start_date").val('0000-00-00 00:00:00');
    if($("#end_date").val()=='')$("#end_date").val('0000-00-00 00:00:00');
            
    if($("#id").val()!="undefined" && $("#id").val()!=""){
        act="save";
        method="put";
        var question_content=[];
        var k=0;
        var i=0;
        $("._id").each(function() {
            if($(this).prop('checked') == true){
                question_content[k] = {"question_id":$(this).val()};
                k++;
            }
            i++;
        });
        
        var data={"id":$("#id").val(),"survey_name":$("#survey_name").val(),"survey_desc":$("textarea#survey_desc").val(),"survey_level":$("#survey_level").val(),"start_date":$("#start_date").val(),"end_date":$("#end_date").val(),"delete_questions":question_content};
        requestURL = "{{ url('/') }}/api/usersurvey/surveyset/update?token=true";
     } else {
        act="add";
        method="post";
        var question_content=[];
        var data={"survey_name":$("#survey_name").val(),"survey_desc":$("textarea#survey_desc").val(),"survey_level":$("#survey_level").val(),"start_date":$("#start_date").val(),"end_date":$("#end_date").val(),"question_set":question_content};
        requestURL = "{{ url('/') }}/api/usersurvey/surveyset/create?token=true";   
    }
     
        console.log(act+"_surveyset : "+requestURL);
        console.log(act+"_surveyset() data  : " + JSON.stringify(data));
                    
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
                
                   console.log(act+"_surveyset() data  : " + JSON.stringify(data));
                    if(data.data!="undefined") {
                          
                        var data_row=data.data;
                            table_content="";
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["survey_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row["survey_level"]+'</td>';
                            table_content +='<td data-value="datec">'+(data_row["start_date"]!='0000-00-00 00:00:00'?new Date(data_row["start_date"]).toISOString().slice(0, 19).replace("T"," "):"")+'</td>';
                            table_content +='<td data-value="datem">'+(data_row["end_date"]!='0000-00-00 00:00:00'?new Date(data_row["end_date"]).toISOString().slice(0, 19).replace("T"," "):"")+'</td>'; 
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:viewData('+data_row["id"]+',\'myModalTable\')" title="View Surveyset">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Surveyset">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 

                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Surveyset">';
                            table_content +='<span class="icon trash-icon"></span>';
                            table_content +='</a>';
                            table_content +='</div>';
                            table_content +='</td>';
                            table_content +='</tr>';

                            
                        if(act=="add"){
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
                    console.log( "Error Thrown "+act+"_surveyset : "+errorThrown );
            }
        });
};



function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop