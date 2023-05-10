<?php 
require_once('../../config.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `medicine_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0 ){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">
    <dl>
        <dt>Medicine Name</dt>
        <dd class="ps-4"><?= isset($name) ? $name : "" ?></dd>
        <dt>Description</dt>
        <dd class="ps-4"><?= isset($description) ? $description : "" ?></dd>
    </dl>
</div>
<div class="mt-3 text-end">
    <button class="btn btn-light border bg-gradient btn-sm rounded-0" type="button" data-bs-dismiss="modal">Close</button>
</div>