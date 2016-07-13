<?php session_start(); if(!isset($_SESSION['CWH'])){header("Location:login.php"); }
require_once(dirname(__FILE__)."/includes/initialize.php");  
$pageTitle = 'Search Allocation';
$finalKeyNo = '-';
if (isset($_POST['submit'])) {
    $search_term = $_POST['search'];

    $Items = ItemDetails::find_by_apr_id($search_term);
    $PRdetail=PrDetails::find_by_key_no($search_term);
    $POdetails =PoDetails::find_by_po_no($search_term);

    if (!empty($Items)) {
        $onHoverList='';
        $result = '<table class="table table-bordered table-hover table-striped"><thead>
                <tr><th >Key No</th><th>Brand</th><th >Description</th><th >Quantity</th><th >Action</th></tr>
                </thead>
                <tbody>';
        foreach($Items as $Item) {
            $dropdown = ItemDetails::brandDropdown($Item->brand_id, 0);
          
            $PRdetail=PrDetails::find_by_item_id($Item->item_id);
            if (!empty($PRdetail)) {
                $finalKeyNo = $PRdetail->key_no;
            }
            $result .='<tr>
                        <td>'.$finalKeyNo.'</td>
                        <td>'. $dropdown .'s</td>
                        <td>'.$Item->description.'</td>
                        <td>'.$Item->quantity.'</td>
                        <td><a href ="CWHprintAllocation.php?item_id='.$Item->item_id.'"><button type="button" class="btn btn-xs btn-primary">View</button></td>
                        </tr>';
        }

            $result.='</table>';
        $_SESSION['finalResult'] = $result;

    }elseif (!empty($PRdetail)) {
        //$result = $PRdetail;
    }elseif (!empty($POdetails)) {
        //$result = $POdetails;
    }else{
        $_SESSION['finalResult'] = "Details Not Found";
    }

    redirect_to("CWHsearchAllocation.php");
}
require_once(dirname(__FILE__)."/layouts/CWHlayouts/header.php");?>
<div class="row">
        <div class="col-lg-12">
        	<h1 class="page-header">
				Search 
            </h1>
            <ol class="breadcrumb">
            
                <li>
                    <i class="fa fa-dashboard"></i> Search
                </li>
            </ol>
        </div>
</div>
<div class="row" style="margin-bottom:1em;">
    <form  method="post" action ="CWHsearchAllocation.php">
        <div class="col-lg-4">
            <div class="input-group custom-search-form">
              <input type="text" class="form-control" placeholder="Search" name="search" id="search">
              <span class="input-group-btn">
              <button class="btn btn-default" type="submit" name="submit">
              <span class="glyphicon glyphicon-search"></span>
             </button>
             </span>
             </div><!-- /input-group -->
        </div>
            <div class="col-lg-3" id="loader" style="display:none">
            <img src="images/loader2.gif">
    </div>
    </form>
</div>
<div class="row" style="margin-bottom:1em;">
	<div class="col-lg-12 table-responsive">
		
			<div class="searchResult ">
                <?php if (isset($_SESSION['finalResult'])) {
                    echo $_SESSION['finalResult'];

                } ?>
			</div>
		
	</div>
</div>

<script src="js/bootstrapDropdown.js"></script>
<script>
jQuery(function() { 
	$("#save").hide();
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000; 
    jQuery("#search ").keyup(function() {
    clearTimeout(typingTimer);
    if ($(this).val) {

            typingTimer = setTimeout(function(){


            if($("#search").val().length == 12){
            	
                var search_term = $("#search").val();
                var dataString = 'search_term='+search_term+'&KeyNo=true';
                sendRequest(dataString);
            }


            if($("#search").val().length == 8){
                
                var search_term = $("#search").val();
                var dataString = 'search_term='+search_term+'&PoNo=true';
                sendRequest(dataString);
            }

            if($("#search").val().length < 8){
                
                var search_term = $("#search").val();
                var dataString = 'search_term='+search_term+'&approval=true';
                sendRequest(dataString);
            }

            }, doneTypingInterval);
        }
	  
    });
});

function sendRequest(dataString){
    $("#loader").show();
	var data =dataString; 

    $.ajax({
    //Send request
    type:'POST',
    data:data,
   	url:'CWHgetAllocation.php',
    success:function(data) {
    	$(".searchResult").html(data);
        $("#loader").hide();
        }
    });
}
</script>
<?php require_once(dirname(__FILE__)."/layouts/CWHlayouts/footer.php");?>