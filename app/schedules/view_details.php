<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT sl.*, ml.name as medicine_name, ml.description as med_description FROM `schedule_list` sl inner join `medicine_list` ml on ml.id = sl.medicine_id where sl.id = '{$_GET['id']}'");
    if($qry->num_rows > 0 ){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<section class="py-5">
    <div class="container">
        <h2 class="fw-bolder text-center"><b>Medicine Schedule and Details</b></h2>
        <hr>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
                <dl class="row">
                    <dt class="col-4 px-2">Med. Name</dt>
                    <dd class="col-8 px-2"><?= isset($medicine_name)? $medicine_name : "" ?></dd>
                    <dt class="col-4 px-2">Med. Description</dt>
                    <dd class="col-8 px-2"><?= isset($med_description)? $med_description : "" ?></dd>
                    <hr class="dark">
                    <dt class="col-4 px-2">Day</dt>
                    <dd class="col-8 px-2"><?= isset($day)? $day : "" ?></dd>
                    <hr class="dark">
                    <dt class="col-4 px-2">Medication Started</dt>
                    <dd class="col-8 px-2"><?= isset($date_start) ? date("F d, Y", strtotime($date_start)) : "" ?></dd>
                    <dt class="col-4 px-2">Medication will last/ lasted</dt>
                    <dd class="col-8 px-2"><?= isset($until) && !is_null($until) ? date("F d, Y", strtotime($until)) : "Lifetime Maintenance" ?></dd>
                    <hr class="dark">
                    <dt class="col-4 px-2">Remarks</dt>
                    <dd class="col-8 px-2"><?= $remarks ?? "" ?></dd>
                    <hr class="dark">
                    <div class="d-flex w-100 justify-content-end">
                        <div class="col-auto me-2">
                            <a href=".?page=schedules/manage_schedule&id=<?= isset($id) ? $id : '' ?>" class="btn btn-primary bg-gradient btn-sm rounded-0 d-flex align-items-center"><span class="material-icons">edit</span> Edit</a>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0)" class="btn btn-danger bg-gradient btn-sm rounded-0 d-flex align-items-center" id="delete_data"><span class="material-icons">delete</span> Delete</a>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#delete_data').click(function(){
            _conf("Are you sure to delete this from list?","delete_schedule",['<?= isset($id) ? $id : '' ?>'])
        })
    })
    function delete_schedule($id){
        start_loader();
        var _this = $(this)
        $('.err-msg').remove();
        var el = $('<div>')
        el.addClass("alert alert-danger err-msg")
        el.hide()
        $.ajax({
            url: '../classes/Master.php?f=delete_schedule',
            method: 'POST',
            data: {
                id: $id
            },
            dataType: 'json',
            error: err => {
                console.log(err)
                el.text('An error occurred.')
                el.show('slow')
                end_loader()
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.replace('./?page=schedule')
                } else if (!!resp.msg) {
                    el.text('An error occurred.')
                    el.show('slow')
                } else {
                    el.text('An error occurred.')
                    el.show('slow')
                }
                end_loader()
            }
        })
    }
</script>