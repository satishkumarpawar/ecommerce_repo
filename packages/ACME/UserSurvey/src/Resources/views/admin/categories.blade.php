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
                <h1>Survey Categories/ Topics List</h1>
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
                <th>Category name</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Date Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <tr>
                <td colspan="7" style="text-align:center;">Loading...</td>
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
        <h4 class="modal-title" id="heading_text">Add Survey Category</h4>
      </div>
      <div class="modal-body">

            <table id="viewTable" class="table table-striped" data-toggle="table">
            <input type="hidden" id="id" value=""> 
            <tr>
                <td>Category Name:</td>
                <td colspan="2"  style="font-weight:bold;"><input type="text" id="cate_name" value=""> </td>
                </tr>
                <tr>
                <td>Sort:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="cate_order" value=""></td>
                </tr>
                <tr>
                <td>Status:</td>
                <td colspan="2" style="font-weight:bold;"><select id="status"><option value="1">Active</option><option value="0">Inactive</option></select></td>
                </tr>
                <tr>
                <td>Category Description:</td>
                <td colspan="2" style="font-weight:bold;"><textarea id="cate_desc" cols="50" rows="5"></textarea></td>
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
   if(requestURL=='')requestURL = "{{ url('/') }}/api/usersurvey/category/get-list?token=true&limit=1000";
   
    

	        
   
        console.log("get_survey_categories : "+requestURL);

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
                    console.log("get_survey_categories() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["cate_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row["cate_order"]+'</td>';
                            table_content +='<td data-value="group">'+(data_row["status"]?"Active":"Inactive")+'</td>';
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Cateogory">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Category">';
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
                    console.log( "Error Thrown get_survey_categories : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this category?"))return;

   requestURL = "{{ url('/') }}/api/usersurvey/category/delete?token=true&id="+id;
   var dt = $('#table_content').DataTable();
        console.log("delete_category : "+requestURL);

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
                    console.log("delete_category() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        alert("This category record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_category : "+errorThrown );
            }
        });

	 }
	
     
    
     function addData(obj){
                
        console.log("get_category() Add ");
        // if(data.length>0) {
        
        $("#heading_text").val("Add Surve Category");
        $("#id").val("");
        $("#cate_name").val("");
        $("#cate_order").val("");
        $("#status").val(1);
        $("textarea#cate_desc").val("");
            
        $("#viewData").click();

        // }

	 }
     function editData(id,obj){
        var data= alldata.data;
        var index = data.findIndex(function(d) {
        return d.id == id;
        });
        var data = data[index];

            console.log("get_category() data  : " + JSON.stringify(data));
            // if(data.length>0) {
            
            $("#heading_text").val("Edit Survey Category");
            $("#id").val(data["id"]);
            $("#cate_name").val(data["cate_name"]);
            $("#cate_order").val(data["cate_order"]);
            $("#status").val(data["status"]);
            $("textarea#cate_desc").val(data["cate_desc"]);
                
            $("#viewData").click();

            // }

	 }

function saveData() {
    if($("#cate_name").val().trim()==''){
        alert("Category name is must required");
        return;
    }
    var act="save";
    var method="post";
    if($("#id").val()!="undefined" && $("#id").val()!=""){
        act="save";
        method="put";
        var data={"id":$("#id").val(),"cate_name":$("#cate_name").val(),"cate_desc":$("textarea#cate_desc").val(),"cate_order":$("#cate_order").val(),"status":$("#status").val()};
        requestURL = "{{ url('/') }}/api/usersurvey/category/update?token=true";
     } else {
        act="add";
        method="post";
        var data={"cate_name":$("#cate_name").val(),"cate_desc":$("textarea#cate_desc").val(),"cate_order":$("#cate_order").val(),"status":$("#status").val()};
        requestURL = "{{ url('/') }}/api/usersurvey/category/create?token=true";   
    }
          console.log(act+"_category : "+requestURL);
        //console.log(act+"_category() data  : " + JSON.stringify(data));
                    
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
                
                   console.log(act+"_category() data  : " + JSON.stringify(data));
                    if(data.data!="undefined") {
                          
                        var data_row=data.data;
                            /*var action_content ="";
                            action_content +='<div class="action">';
                            action_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Cateogory">';
                            action_content +='<span class="icon pencil-lg-icon"></span>';
                            action_content +='</a>'; 
                            
                            action_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Category">';
                            action_content +='<span class="icon trash-icon"></span>';
                            action_content +='</a>';
                            action_content +='</div>';
                            */
                            table_content="";
                            //table_content +='<tr id="row'+data_row["id"]+'">';
                           
                             table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                             table_content +='<td data-value="Name">'+data_row["cate_name"]+'</td>'; 
                             table_content +='<td data-value="group">'+data_row["cate_order"]+'</td>';
                             table_content +='<td data-value="group">'+(data_row["status"]?"Active":"Inactive")+'</td>';
                             table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                             table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                     
                             table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                             table_content +='<div class="action">';
                             table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Cateogory">';
                             table_content +='<span class="icon pencil-lg-icon"></span>';
                             table_content +='</a>'; 
                             
                             table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Category">';
                             table_content +='<span class="icon trash-icon"></span>';
                             table_content +='</a>';
                             table_content +='</div>';
                             table_content +='</td>';
                             //table_content +='</tr>';

                        if(act=="add"){
                           // dt.row("#row"+data_row["id"]).add([data_row["id"], data_row["cate_name"],data_row["cate_order"],data_row["status"],new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," "),new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," "),action_content]).draw(false);
                           // $("#table_content").append(table_content);
                           $("#table_content").prepend('<tr id="row'+data_row["id"]+'">'+table_content+'</tr>');
                           alldata.data[alldata.data.length]=data_row;
                        }else {
                            var d= alldata.data;
                            var index = d.findIndex(function(d) {
                              return d.id == $("#id").val();
                            });
                            d=null;
                            //dt.row("#row"+data_row["id"]).update([data_row["id"], data_row["cate_name"],data_row["cate_order"],data_row["status"],new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," "),new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," "),action_content]).draw(false);
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
                    console.log( "Error Thrown "+act+"_category : "+errorThrown );
            }
        });
};

function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop