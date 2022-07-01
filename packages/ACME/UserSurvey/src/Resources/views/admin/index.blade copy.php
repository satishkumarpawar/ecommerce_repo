@extends('admin::layouts.master')

@section('page_title')
    Package UserSurvey
@stop

@section('content-wrapper')

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


        <div class="table"><div class="grid-container"><div class="datagrid-filters"><div class="filter-left"></div></div> <div id="datagrid-filters" class="datagrid-filters"><div class="filter-left"><div class="search-filter"><input type="search" id="search-field" placeholder="Search Here..." class="control"> <div class="icon-wrapper"><span class="icon search-icon search-btn"></span></div></div></div> <div class="filter-right"><div class="dropdown-filters per-page"><div class="control-group"><label for="perPage" class="per-page-label">
                                    Items Per Page
                                </label> <select id="perPage" name="perPage" class="control"><option value="10"> 10 </option><option value="20"> 20 </option><option value="30"> 30 </option><option value="40"> 40 </option><option value="50"> 50 </option></select></div></div> <div class="dropdown-filters"><div class="dropdown-toggle"><div class="grid-dropdown-header"><span class="name">Filter</span> <i class="icon arrow-down-icon active"></i></div></div> <div class="dropdown-list dropdown-container" style="display: none;"><ul><li><div class="control-group"><select class="filter-column-select control"><option selected="selected" disabled="disabled">Select Column</option> <option value="customer_id">
                                                            ID
                                                        </option> <option value="full_name">
                                                            User
                                                        </option> <option value="group">
                                                            Group
                                                        </option> 
                                                    </select></div></li> <!----> <!----> <!----> <!----> <!----> <!----> <!----> <!----> <button class="btn btn-sm btn-primary apply-filter">Apply</button></ul></div></div></div></div> <div class="filtered-tags"></div> <table class="table"><!----> <thead><tr style="height: 65px;"><th id="mastercheckbox" class="grid_head" style="width: 50px;"><span class="checkbox"><input type="checkbox"> <label for="checkbox" class="checkbox-view"></label></span></th> 
            <th class="grid_head">
                ID
            </th> <th class="grid_head">
                User
            </th> <th class="grid_head">
                Group
            </th> <th class="grid_head">
                Date Created
            </th> <th class="grid_head">
                Date Modified
            </th> <th>
                Actions
            </th></tr></thead> 
            <tbody id="table_content">
                <tr>
                    <td>
                        <span class="checkbox">
                            <input type="checkbox" value="11"> 
                            <label for="checkbox" class="checkbox-view"></label>
                        </span>
                    </td> 
                <td data-value="ID">11</td> 
                <td data-value="Name">Satish Pawar</td> 
                <td data-value="group">satishkumarpawar@gmail.com</td>
                 <td data-value="datec">-</td> 
                <td data-value="datem">-</td> 
                
                <td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">
                    <div class="action">
                        <a id="11" href="http://localhost:8000/admin/usersurvey/view/11" data-method="GET" data-action="http://localhost:8000/admin/usersurvey/view/11" data-token="{{ csrf_token() }}" title="View Survey"><span class="icon eye-icon"></span></a> 
                        
                        <a id="11" data-method="POST" data-action="http://localhost:8000/admin/usersurvey/delete/11" data-token="{{ csrf_token() }}" title="Delete Survey"><span class="icon trash-icon"></span></a>
                    </div>
                </td>
                </tr> 
                    
                    
                </tbody></table></div> 
                
                <div class="pagination" id="pagination">
                    
                </div>

            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
    <script>

function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "../api/usersurvey/get-list?token=true&limit=1";
   /*var url_string = window.location.href; //
   var url = new URL(url_string);
   var limit = url.searchParams.get("limit");
   var page = url.searchParams.get("page");
   if(limit) = requestURL +="&limit="+limit;
   if(page) = requestURL +="&page="+page;*/

    

	        
   
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
                            table_content +='<td>';
                            table_content +='<span class="checkbox">';
                            table_content +='<input type="checkbox" value="'+data_row["id"]+'">'; 
                            table_content +='<label for="checkbox" class="checkbox-view"></label>';
                            table_content +='</span>';
                            table_content +='</td>'; 
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="Name">'+data_row["user_id"]+'</td>'; 
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
                        document.getElementById("table_content").innerHTML=table_content;

                        var pagination='';
                        var links =data.links;
                        pagination +='<div class="pagination shop mt-50">';
                        if(links.first!=null)pagination +='<a onClick="getData(\''+links.first+'\')" class="page-item previous"  style="cursor:pointer"><i class="icon angle-first-icon">First</i></a>'; 
                        else pagination +='<a  class="page-item previous"><i class="icon angle-first-icon">First</i></a>'; 
                        if(links.prev!=null)pagination +='<a onClick="getData(\''+links.prev+'\')" class="page-item previous"><i class="icon angle-left-icon"></i></a>'; 
                        else pagination +='<a class="page-item previous"><i class="icon angle-left-icon"></i></a>'; 
                        var metalinks =data.meta.links;
                        for(i=1;i<(metalinks.length-1);i++){
                            if(metalinks[i].active)pagination +='<a class="page-item active"> '+metalinks[i].label+' </a>';
                            else pagination +='<a onClick="getData(\''+metalinks[i].url+'\')" class="page-item as"> '+metalinks[i].label+' </a>'; 
                        
                        }
                         
                        if(links.next!=null)pagination +='<a onClick="getData(\''+links.next+'\')" class="page-item next"><i class="icon angle-right-icon"></i></a>';
                        else pagination +='<a id="next" class="page-item next"><i class="icon angle-right-icon"></i></a>';
                        if(links.last!=null)pagination +='<a onClick="getData(\''+links.last+'\')"  class="page-item next"  style="cursor:pointer"><i class="icon angle-last-icon">Last</i></a>';
                        else pagination +='<a id="last" class="page-item next"><i class="icon angle-last-icon">Last</i></a>';
                        pagination +=' </div>';

                        document.getElementById("pagination").innerHTML=pagination;
                        

                        
                    } else {
                        // alert(data.message);
                    }
                    
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown get_survey_list : "+errorThrown );
            }
        });

	 }
	 	 

     getData('');
    </script>

@stop