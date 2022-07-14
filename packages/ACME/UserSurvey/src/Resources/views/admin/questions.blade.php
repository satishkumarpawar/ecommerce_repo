@extends('admin::layouts.master')

@section('page_title')
    Survey Questions
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
                <h1>Survey Question List</h1>
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
                <th>Check</th>
                <th>ID</th>
                <th>Question Text</th>
                <th>Category name</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Is locked</th>
                <th>Date Created</th>
                <th>Date Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="table_body">
            <tr>
                <td colspan="10" style="text-align:center;">Loading...</td>
            </tr>
        </tbody>
    </table>
    <table width="100%">
    <tr style="height: 65px;" class="mass-action">
                <th style="text-arign:right;" colspan="10">
                    <div class="mass-action-wrapper" style="display: flex; flex-direction: row; align-items: center; justify-content: flex-start; float:right;">
                        <div class="control-group" style="width:300px;">
                            <select id="survey_set_id" name="massaction-type" required="required" class="control" style="width:300px; margin-top:30px;"><option value="">Add selected questions to Surveyset</option></select>
                        </div><button type="button"  onClick="javascript:addQuestionTOSurvey();" class="btn btn-sm btn-primary" style="margin-left: 10px;"> Submit </button>
                    </div>
                </th>
            </tr>
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
        <h4 class="modal-title" id="heading_text">Add Survey Question</h4>
      </div>
      <div class="modal-body">

            <table id="viewTable" class="table table-striped" data-toggle="table">
            <input type="hidden" id="id" value=""> 
                <tr>
                <td>Question Text:</td>
                <td colspan="2"  style="font-weight:bold;"><input type="text" id="question_text" value=""> </td>
                </tr>
                <tr>
                <td>Question Category:</td>
                <td colspan="2"  style="font-weight:bold;"><select id="cate_id"></select></td>
                </tr>
                <tr>
                <td>Sort:</td>
                <td colspan="2" style="font-weight:bold;"><input type="text" id="question_order" value=""></td>
                </tr>
                <tr>
                <td>Status:</td>
                <td colspan="2" style="font-weight:bold;"><select id="status"><option value="1">Active</option><option value="0">Inactive</option></select></td>
                </tr>
                <tr>
                <td>Is locked:</td>
                <td colspan="2" style="font-weight:bold;"><select id="question_lock"><option value="0">Unlocked</option><option value="1">Locked</option></select></td>
                </tr>
                <tr>
                <td  id="ansid" colspan="3" ></td>
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
var categoriesdata = [];
var alldata = [];
var totalpages = [];
var loadedpages = [];
var answeroptioncount = 10;
function getCategories(requestURL){
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
                categoriesdata=data.data;
                    console.log("get_survey_categories() data  : " + JSON.stringify(data));
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown get_survey_categories : "+errorThrown );
            }
        });

	 }

function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "{{ url('/') }}/api/usersurvey/question/get-list?token=true&limit=1000";
   
    

	        
   
        console.log("get_survey_questions : "+requestURL);

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
                    console.log("get_survey_questions() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        for(let i=0;i<data.data.length;i++){
                            var data_row=data.data[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="CHK"><span class="chkbox"><input class="chkid"  type="checkbox" value="'+data_row["id"]+'"> <label for="chkbox" class="checkbox-view"></label></span></td>'; 
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["question_text"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["cate_name"]+'</td>'; 
                            table_content +='<td data-value="group">'+data_row["question_order"]+'</td>';
                            table_content +='<td data-value="group">'+(data_row["status"]?"Active":"Inactive")+'</td>';
                            table_content +='<td data-value="group">'+(data_row["question_lock"]==1?"Locked":"Unlocked")+'</td>';
                            table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                    
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Question">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Question">';
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
                    console.log( "Error Thrown get_survey_questions : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getCategories('');
    getSurveySets('');
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this question?"))return;

   requestURL = "{{ url('/') }}/api/usersurvey/question/delete?token=true&id="+id;
   var dt = $('#table_content').DataTable();
        console.log("delete_question : "+requestURL);

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
                    console.log("delete_question() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        alert("This question record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_question : "+errorThrown );
            }
        });

	 }
	
     
    
     function addData(obj){
                
        console.log("get_question() Add ");
        // if(data.length>0) {
        
        $("#heading_text").val("Add Survey question");
        $("#id").val("");
        $("#question_text").val("");
        var category_options='';
        for(i=0;i<categoriesdata.length;i++)category_options +='<option value="'+categoriesdata[i]["id"]+'">'+categoriesdata[i]["cate_name"]+"</option>";
        //console.log( "category_options "+category_options );
        $("#cate_id").html(category_options);
        $("#question_order").val("0");
        $("#status").val(1);
        $("#question_lock").val(0);
        
        var answer_content='<table width="100%">';
        for(i=0;i<answeroptioncount;i++){
            answer_content +='<tr>';
            answer_content +='<td>Answer Option: '+(i+1)+'</td>';
            answer_content +='<td colspan="2"><input type="hidden" id="answer_id'+i+'" value=""> <input type="text" id="answer_text'+i+'" value=""></td>';
            answer_content +='</tr>';
        }
         answer_content +='</table>';

        $("#ansid").html(answer_content);
            
        $("#viewData").click();

        // }

	 }
     function editData(id,obj){
        var data= alldata.data;
        var index = data.findIndex(function(d) {
        return d.id == id;
        });
        var data = data[index];

            console.log("get_question() data  : " + JSON.stringify(data));
            // if(data.length>0) {
            
            $("#heading_text").val("Edit Surve Question");
            $("#id").val(data["id"]);
            $("#question_text").val(data["question_text"]);
            var category_options='';
            for(i=0;i<categoriesdata.length;i++)category_options +='<option value="'+categoriesdata[i]["id"]+'" '+(data["cate_id"]==categoriesdata[i]["id"]?"Selected":"")+'>'+categoriesdata[i]["cate_name"]+"</option>";
            //console.log( "category_options "+category_options );
            $("#cate_id").html(category_options);
            $("#question_order").val(data["question_order"]);
            $("#status").val(data["status"]);
            $("#question_lock").val(data["question_lock"]);
            
            var answer_content='<table width="100%">';
            for(i=0;i<answeroptioncount;i++){
                //console.log("_question() data  : " + JSON.stringify(data["answer_options"][i]["answer_text"]));
                answer_content +='<tr>';
                answer_content +='<td>Answer Option: '+(i+1)+'</td>';
                if(data["answer_options"].length>i){
                answer_content +='<td colspan="2"><input type="hidden" id="answer_id'+i+'" value="'+data["answer_options"][i]["id"]+'"> <input type="text" id="answer_text'+i+'" value="'+data["answer_options"][i]["answer_text"]+'"></td>';
                } else {
                    answer_content +='<td colspan="2"><input type="hidden" id="answer_id'+i+'" value=""> <input type="text" id="answer_text'+i+'" value=""></td>';   
                }
                answer_content +='</tr>';
            }
            answer_content +='</table>';


            $("#ansid").html(answer_content);
            
            if(data["question_lock"]==1) $("#question_lock").prop('disabled', true);
           if(data["question_lock"]==1) $("#btnSaveData").prop('disabled', true);
                
            $("#viewData").click();

            // }

	 }

function saveData() {
    if($("#question_text").val().trim()==''){
        alert("question text is must required");
        return;
    }
    if($("#cate_id").val().trim()==''){
        alert("question category is must required");
        return;
    }
    var act="save";
    var method="post";
    if($("#id").val()!="undefined" && $("#id").val()!=""){
        act="save";
        method="put";
        var answer_content=[];
        var k=0;
        
        for(i=0;i<answeroptioncount;i++){
            if($("#answer_id"+i).val()!=''){
                answer_content[k] = {"id":$("#answer_id"+i).val(),"question_id":$("#id").val(),"answer_text":$("#answer_text"+i).val(),"answer_order":"0","default_ans_flag":"0"};
                k++;
            } else if($("#answer_text"+i).val()!=''){
                answer_content[k] = {"question_id":$("#id").val(),"answer_text":$("#answer_text"+i).val(),"answer_order":"0","default_ans_flag":"0"};
                k++
            }
                
        }
        var data={"id":$("#id").val(),"cate_id":$("#cate_id").val(),"question_text":$("#question_text").val(),"question_order":$("#question_order").val(),"status":$("#status").val(),"question_lock":$("#question_lock").val(),"answer_options":answer_content};
        requestURL = "{{ url('/') }}/api/usersurvey/question/update?token=true";
     } else {
        act="add";
        method="post";
        var answer_content=[];
        var k=0;
        for(i=0;i<answeroptioncount;i++){
            if($("#answer_text"+i).val()!=''){
                answer_content[k] = {"answer_text":$("#answer_text"+i).val(),"answer_order":"0","default_ans_flag":"0"};
                k++;
            } 
        };
        var data={"cate_id":$("#cate_id").val(),"question_text":$("#question_text").val(),"question_order":$("#question_order").val(),"status":$("#status").val(),"question_lock":$("#question_lock").val(),"answer_options":answer_content};
        requestURL = "{{ url('/') }}/api/usersurvey/question/create?token=true";   
    }
          console.log(act+"_question : "+requestURL);
          console.log(act+"_question() data  : " + JSON.stringify(data));
                    
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
                
                   console.log(act+"_question() data  : " + JSON.stringify(data));
                    if(data.data!="undefined") {
                          
                        var data_row=data.data;
                        var index = categoriesdata.findIndex(function(d) {
                            return d.id == data_row["cate_id"];
                            });
                            var cate_name = categoriesdata[index]["cate_name"];

                            table_content="";
                            //table_content +='<tr id="row'+data_row["id"]+'">';
                             table_content +='<td data-value="CHK"><span class="chkbox"><input class="chkid" type="checkbox" value="'+data_row["id"]+'"> <label for="chkbox" class="checkbox-view"></label></span></td>'; 
                             table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                             table_content +='<td data-value="Name">'+data_row["question_text"]+'</td>'; 
                             table_content +='<td data-value="Name">'+cate_name+'</td>'; 
                             table_content +='<td data-value="group">'+data_row["question_order"]+'</td>';
                             table_content +='<td data-value="group">'+(data_row["status"]?"Active":"Inactive")+'</td>';
                             table_content +='<td data-value="group">'+(data_row["question_lock"]==1?"Locked":"Unlocked")+'</td>';
                             table_content +='<td data-value="datec">'+new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                             table_content +='<td data-value="datem">'+new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>'; 
                     
                             table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                             table_content +='<div class="action">';
                             table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit Cateogory">';
                             table_content +='<span class="icon pencil-lg-icon"></span>';
                             table_content +='</a>'; 
                             
                             table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete Question">';
                             table_content +='<span class="icon trash-icon"></span>';
                             table_content +='</a>';
                             table_content +='</div>';
                             table_content +='</td>';
                             //table_content +='</tr>';

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
                    console.log( "Error Thrown "+act+"_question : "+errorThrown );
            }
        });
};



function getSurveySets(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "{{ url('/') }}/api/usersurvey/surveyset/get-list?token=true&short_info=true&limit=1000";
  
        console.log("get_surveysets : "+requestURL);

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
               ;
                    console.log("get_surveysets() data  : " + JSON.stringify(data));
                    var surveyset_options='<option value="">Add selected questions to Surveyset</option>';
                    for(i=0;i<data.length;i++)surveyset_options +='<option value="'+data[i]["id"]+'">'+data[i]["survey_name"]+"</option>";
        
                    $("#survey_set_id").html(surveyset_options);
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown get_surveysets : "+errorThrown );
            }
        });

	 }


function addQuestionTOSurvey(){
    if($("#survey_set_id").val()==''){
        alert("Please select survey topic");
        return;
    }
    if(!confirm("Are you sure to add these questions to surveyset this question?"))return;


   requestURL = "{{ url('/') }}/api/usersurvey/surveyset/add-question?token=true";
   var dt = $('#table_content').DataTable();
        console.log("addQuestionTOSurvey : "+requestURL);

        var question_content=[];
        var k=0;
        var i=0;
        $(".chkid").each(function() {
            if($(this).prop('checked') == true){
                question_content[k] = {"question_id":$(this).val(),"survey_set_id":$("#survey_set_id").val()};
                k++;
            }
            i++;
        });

        if(question_content.length==0){
            alert("Please select questions to add in surveyset");
            return;
         }
         
        var data ={"question_set":question_content};
        console.log("addQuestionTOSurvey() request data  : " + JSON.stringify(data));

        var checkCall = $.ajax({
            url: requestURL,
            dataType: 'json',
            type: 'post',
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
                  console.log("addQuestionTOSurvey() data  : " + JSON.stringify(data));
                   
                         alert(data.message);
                   
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown addQuestionTOSurvey : "+errorThrown );
            }
        });

	 }
function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop