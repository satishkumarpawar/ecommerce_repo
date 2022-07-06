@extends('admin::layouts.master')

@section('page_title')
    Package UserSurvey
@stop
 

@section('content-wrapper')
 <!-- Core theme CSS (includes Bootstrap)-->
 
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.12.1/datatables.min.js" defer></script>
    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>UserSurvey List</h1>
            </div>

            <div class="page-action">
                <a href="http://localhost:8000/admin/customers/create" class="btn btn-lg btn-primary">
                    Create
                </a></div>
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
                <td>ID</td>
                <td>User</td>
                <td>Survey Set</td>
                <td>Date Created</td>
                <td>Date Modified</td>
                <td>Action</td>
            </tr>
        </tbody>
    </table>
     <script>

function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "../api/usersurvey/get-list?token=true&limit=1000";
   
    

	        
   
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
                    console.log("get_survey_list() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr>';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row.user_info["first_name"]+' '+data_row.user_info["last_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row["survey_set_id"]+'</td>';
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a id="11" href="http://localhost:8000/admin/usersurvey/view/'+data_row["id"]+'" data-method="GET" data-action="http://localhost:8000/admin/usersurvey/view/'+data_row["id"]+'" data-token="{{ csrf_token() }}" title="View Survey">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a id="'+data_row["id"]+'1" data-method="POST" data-action="http://localhost:8000/admin/usersurvey/delete/'+data_row["id"]+'" data-token="{{ csrf_token() }}" title="Delete Survey">';
                            table_content +='<span class="icon trash-icon"></span>';
                            table_content +='</a>';
                            table_content +='</div>';
                            table_content +='</td>';
                            table_content +='</tr>';
                        }
                        document.getElementById("table_body").innerHTML=table_content;
                        $('#table_content').DataTable({
                            order: [[3, 'desc']],
                            'aoColumns': [
                null,
                null,
                null,
                null,
                null,
                null
            ]
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
	</script>			
                </div>
            </div>
 
        </div>
    </div>
    

@stop