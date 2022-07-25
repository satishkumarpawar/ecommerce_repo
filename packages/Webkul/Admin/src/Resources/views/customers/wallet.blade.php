@extends('admin::layouts.master')

@section('page_title')
    Package Customers
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
                <h1>Customer Wallet</h1>
            </div>

            <div class="page-action" >
                <a id="add_data" onClick="javascript:addData();" href="javascript:void(0);" class="btn btn-lg btn-primary">
                    Deposit / Withdraw Fund
                </a>
                
            </div>

            
            <div style="width:100%; float:left:clear:both; position:relative; margin-top:50px; margin-left:10px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Customer Name:</b> <span id="holder_name"></span> 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Default Wallet ID:</b>  <span id="wallet_id"></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Balance:</b> ₹ <span id="balance"></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Cash Back Wallet ID:</b>  <span id="wallet_id2"></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Balance:</b> ₹ <span id="balance2"></span>
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
                <th>Wallet</th>
                <th style="text-align:right;">Withdraw</th>
                <th style="text-align:right;">Deposit</th>
                <th style="text-align:center;">Confirmed</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Updated At</th>
                
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
        <h4 class="modal-title" id="heading_text">Deposit / Withdraw Fund</h4>
      </div>
      <div class="modal-body">

            <table id="viewTable" class="table table-striped" data-toggle="table">
            <input type="hidden" id="id" value=""> 
            <tr>
                <td>Amount: (₹)</td>
                <td colspan="2"  style="font-weight:bold;"><input type="text" id="amount" value="0"> </td>
                </tr>

                <tr>
                <td>Transaction Type:</td>
                <td colspan="2" style="font-weight:bold;"><select id="type"><option value="deposit">Deposit</option><option value="withdraw">Withdraw</option></select></td>
                </tr>
                
                <tr>
                <td>Confirmed:</td>
                <td colspan="2" style="font-weight:bold;"><select id="confirmed"><option value="true">Yes</option><option value="false">No</option></select></td>
                </tr>

                <tr>
                <td>Description:</td>
                <td colspan="2" style="font-weight:bold;"><textarea id="description" cols="50" rows="5"></textarea></td>
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
var customer_id ={{$customer_id}};
var alldata = [];
var totalpages = [];
var loadedpages = [];
function getData(requestURL){
   var table_content='';
   if(requestURL=='')requestURL = "{{ url('/') }}/api/wallet/get-list?customer_id="+customer_id+"&token=true&limit=1000";
   
    

	        
   
        console.log("get_transactions : "+requestURL);

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
                    console.log("get_transactions() data  : " + JSON.stringify(data));
                    if(textStatus == 'success') {
                        var holder_name=data.data.holder_name;
                        $("#holder_name").html(holder_name);
                        var wallet = data.data.wallet;
                        var cashbackwallet = data.data.cash_back_wallet;
                        $("#wallet_id").html(wallet.id);
                        $("#balance").html(wallet.balance);
                        $("#wallet_id2").html(cashbackwallet.id);
                        $("#balance2").html(cashbackwallet.balance);
                        var transactions = data.data.transactions;
                        for(let i=0;i<transactions.length;i++){
                            var data_row=transactions[i];
                            table_content +='<tr id="row'+data_row["id"]+'">';
                             
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="ID">'+(cashbackwallet["id"]==data_row["wallet_id"]?cashbackwallet["name"]:wallet["name"])+'</td>'; 
                            table_content +='<td data-value="Amount" style="text-align:right;">'+(data_row["type"]=='withdraw'?"₹ "+Math.abs(data_row["amount"]):'')+'</td>'; 
                            table_content +='<td data-value="Amount" style="text-align:right;">'+(data_row["type"]=='deposit'?"₹ "+data_row["amount"]:'')+'</td>';
                            table_content +='<td data-value="group" style="text-align:center;color:'+(data_row["confirmed"]==true?"green":"red")+'">'+(data_row["confirmed"]==true?"Yes":"No")+'</td>';
                            table_content +='<td data-value="group">'+(data_row["meta"]!=null?data_row["meta"]["description"]:'')+'</td>';
                            table_content +='<td data-value="group">'+ new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="group">'+ new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:viewData('+data_row["id"]+',\'myModalTable\')" title="view transaction">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>';
                            if(data_row["confirmed"]==false){
                            table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit transaction">';
                            table_content +='<span class="icon pencil-lg-icon"></span>';
                            table_content +='</a>'; 
                            
                            table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete transaction">';
                            table_content +='<span class="icon trash-icon"></span>';
                            table_content +='</a>';
                             }
                            table_content +='</div>';
                            table_content +='</td>';
                            table_content +='</tr>';
                        }
                        document.getElementById("table_body").innerHTML=table_content;
                        
                        $('#table_content').DataTable({
                            pagingType: 'full_numbers',
                            responsive: true,
                            order: [[7, 'desc']],
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
                    console.log( "Error Thrown get_transactions : "+errorThrown );
            }
        });

	 }
	 	 

    
$(document).ready(function() {
    getData('');
   
} );


function deleteData(id){
    if(!confirm("Are you sure to delete this transaction?"))return;

   requestURL = "{{ url('/') }}/api/wallet/delete?token=true&id="+id;
   var dt = $('#table_content').DataTable();
        console.log("delete_transaction : "+requestURL);

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
                var d= alldata.data.transactions;
                var index = d.findIndex(function(d) {
                return d.id == id;
                });
                d=null;
                delete alldata.data.transactions[index];
                    console.log("delete_transaction() data  : " + JSON.stringify(data));
                    if(data.message == true) {
                        dt.row("#row"+id).remove().draw();
                        $("#balance").html(data.balance);
                        alert("This transaction record has deleted successfully.");
                    
                    } else {
                         alert(data.message);
                    }
                     
                   
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown delete_transaction : "+errorThrown );
            }
        });

	 }
	
     
    
     function addData(obj){
                
        console.log("get_transaction() Add ");
        // if(data.length>0) {
            var table_content='';
            table_content +='<input type="hidden" id="id" value="">';
            table_content +='<tr>';
            table_content +='<td>Amount: (₹)</td>';
            table_content +='<td colspan="2"  style="font-weight:bold;"><input type="text" id="amount" value="0"> </td>';
            table_content +='</tr>';

            table_content +='<tr>';
            table_content +='<td>Transaction Type:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><select id="type"><option value="deposit">Deposit</option><option value="withdraw">Withdraw</option></select></td>';
            table_content +='</tr>';
                
            table_content +='<tr>';
            table_content +='<td>Confirmed:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><select id="confirmed"><option value="true">Yes</option><option value="false">No</option></select></td>';
            table_content +='</tr>';

            table_content +='<tr>';
            table_content +='<td>Description:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><textarea id="description" cols="50" rows="5"></textarea></td>';
            table_content +='</tr>';

document.getElementById("viewTable").innerHTML=table_content;

        $("#heading_text").val("Deposit / Withdraw Fund");
        $("#id").val("");
        $("#amount").val("0");
        $("#type").val("deposit");
        $("#confirmed").val("true");
        $("textarea#description").val("");
            
        $("#btnSaveData").show();
        $("#viewData").click();

        // }

	 }
     function editData(id,obj){
        var data= alldata.data.transactions;
        var index = data.findIndex(function(d) {
        return d.id == id;
        });
        var data = data[index];

            console.log("get_transaction() data  : " + JSON.stringify(data));
            // if(data.length>0) {


                var table_content='';
            table_content +='<input type="hidden" id="id" value="">';
            
            table_content +='<tr>';
            table_content +='<td>Amount: (₹)</td>';
            table_content +='<td colspan="2"  style="font-weight:bold;"><input type="text" id="amount" value="0"> </td>';
            table_content +='</tr>';

            table_content +='<tr>';
            table_content +='<td>Transaction Type:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><select id="type"><option value="deposit">Deposit</option><option value="withdraw">Withdraw</option></select></td>';
            table_content +='</tr>';
                
            table_content +='<tr>';
            table_content +='<td>Confirmed:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><select id="confirmed"><option value="true">Yes</option><option value="false">No</option></select></td>';
            table_content +='</tr>';

            table_content +='<tr>';
            table_content +='<td>Description:</td>';
            table_content +='<td colspan="2" style="font-weight:bold;"><textarea id="description" cols="50" rows="5"></textarea></td>';
            table_content +='</tr>';

document.getElementById("viewTable").innerHTML=table_content;
            
            $("#heading_text").val("Edit Data");
            $("#id").val(data["id"]);
            $("#amount").val(Math.abs(data["amount"]));
            $("#type").val(data["type"]);
            $("#confirmed").val(data["confirmed"].toString());
            $("textarea#description").val(data["description"]);
        
            $("#btnSaveData").show();    
            $("#viewData").click();

            // }

	 }

function saveData() {
    if($("#amount").val().trim()==''){
        alert("Amount is must required");
        return;
    }
    var act="save";
    var method="post";
    if($("#id").val()!="undefined" && $("#id").val()!=""){
        act="save";
        method="put";
        var data={"customer_id":customer_id,"id":$("#id").val(),"amount":$("#amount").val(),"description":$("textarea#description").val(),"type":$("#type").val(),"confirmed":$("#confirmed").val()};
        requestURL = "{{ url('/') }}/api/wallet/update?token=true";
     } else {
        act="add";
        method="post";
        var data={"customer_id":customer_id,"amount":$("#amount").val(),"description":$("textarea#description").val(),"type":$("#type").val(),"confirmed":$("#confirmed").val()};
        requestURL = "{{ url('/') }}/api/wallet/create?token=true";   
    }
          console.log(act+"_transaction : "+requestURL);
          console.log(act+"_transaction() data  : " + JSON.stringify(data));
                    
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
                
                   console.log(act+"_transaction() data  : " + JSON.stringify(data));
                    if(data.data!="undefined") {
                        var wallet = data.data.wallet;
                        var cashbackwallet = data.data.cash_back_wallet;
                        
                        var data_row=data.data.transactions;
                            table_content="";
                            //table_content +='<tr id="row'+data_row["id"]+'">';
                           
                            table_content +='<td data-value="ID">'+data_row["id"]+'</td>'; 
                            table_content +='<td data-value="ID">'+(data.data.wallet["name"])+'</td>'; 
                            table_content +='<td data-value="Amount" style="text-align:right;">'+(data_row["type"]=='withdraw'?"₹ "+Math.abs(data_row["amount"]):'')+'</td>'; 
                            table_content +='<td data-value="Amount" style="text-align:right;">'+(data_row["type"]=='deposit'?"₹ "+data_row["amount"]:'')+'</td>';
                            table_content +='<td data-value="group" style="text-align:center;">'+(data_row["confirmed"]==true?"True":"False")+'</td>';
                            table_content +='<td data-value="group">'+(data_row["meta"]!=null?data_row["meta"]["description"]:'')+'</td>';
                            table_content +='<td data-value="group">'+ new Date(data_row["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='<td data-value="group">'+ new Date(data_row["updated_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                           
                            table_content +='<td data-value="Actions" class="actions" style="white-space: nowrap; width: 100px;">';
                            table_content +='<div class="action">';
                            table_content +='<a href="javascript:viewData('+data_row["id"]+',\'myModalTable\')" title="view transaction">';
                            table_content +='<span class="icon eye-icon"></span>';
                            table_content +='</a>';
                            if(data_row["confirmed"]==false){
                                table_content +='<a href="javascript:editData('+data_row["id"]+',\'myModalTable\')" title="Edit transaction">';
                                table_content +='<span class="icon pencil-lg-icon"></span>';
                                table_content +='</a>'; 
                                
                                table_content +='<a  href="javascript:deleteData('+data_row["id"]+');" title="Delete transaction">';
                                table_content +='<span class="icon trash-icon"></span>';
                                table_content +='</a>';
                             }
                             table_content +='</div>';
                             table_content +='</td>';
                             //table_content +='</tr>';

                        if(act=="add"){
                           // $("#table_content").append(table_content);
                           $("#table_content").prepend('<tr id="row'+data_row["id"]+'">'+table_content+'</tr>');
                           alldata.data.transactions[alldata.data.transactions.length]=data_row;
                        }else {
                            var d= alldata.data.transactions;
                            var index = d.findIndex(function(d) {
                              return d.id == $("#id").val();
                            });
                            d=null;
                            $("#row"+data_row["id"]).html(table_content);
                           
                            alldata.data.transactions[index]=data_row;
                            
                        }
                        
                        alert(data.message);
                    
                    } else {
                         alert(data.message);
                    }
                     
                    $(".close").click();
            },
            error: function( jqXhr, textStatus, errorThrown ){
                    console.log( "Error Thrown "+act+"_transaction : "+errorThrown );
            }
        });
};

function viewData(id,obj){
                $("#btnSaveData").hide();
                
                var data= alldata.data;
                var transactions= alldata.data.transactions;
                var index = transactions.findIndex(function(d) {
                return d.id == id;
                });
                var transaction = transactions[index];
                        
                    console.log("get_transaction() data  : " + JSON.stringify(data));
                   // if(data.length>0) {
                        var table_content='';
                            table_content +='<tr>';
                            table_content +='<td>Holder Name:</td>';
                            table_content +='<td colspan="2"  style="font-weight:bold;">'+data.holder_name+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Wallet:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+( data.cash_back_wallet["id"]==transaction["wallet_id"]? data.cash_back_wallet["name"]: data.wallet["name"])+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Wallet Balance:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">₹ '+data.wallet["balance"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td colspan="3" style="font-weight:bold;">Transaction Detail:</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Type:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+transaction["type"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Amount:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">₹ '+transaction["amount"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Confirmed:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+transaction["confirmed"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Description:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+(transaction["meta"]!=null?transaction["meta"]["description"]:'')+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Created Date:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+ new Date(transaction["created_at"]).toISOString().slice(0, 19).replace("T"," ")+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>Payable Type:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+ transaction["payable_type"]+'</td>';
                            table_content +='</tr>';
                            table_content +='<tr>';
                            table_content +='<td>UUID:</td>';
                            table_content +='<td colspan="2" style="font-weight:bold;">'+ transaction["uuid"]+'</td>';
                            table_content +='</tr>';
                           
                        document.getElementById("viewTable").innerHTML=table_content;

                        $("#viewData").click();

                       // }

	 }


function closeDataView(obj){
    $("#"+obj).hide();
}
	</script>
@stop