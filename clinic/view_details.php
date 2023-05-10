<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `clinic_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0 ){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
        $categories = $conn->query("SELECT c.category_id,cc.name as category FROM clinic_category c inner join category_list cc on c.category_id = cc.id where c.clinic_id = '{$id}' ")->fetch_all(MYSQLI_ASSOC);
			$cats = array_column($categories,'category','category_id');
    }
}
?>
<style>
    #doctor-list{
        counter-reset: doctor;
    }
    .doctor-label::after{
        counter-increment: doctor;
        content:" "counter(doctor);
    }
    #contact-list{
        counter-reset: contact;
    }
    .contact-label::after{
        counter-increment: contact;
        content:" "counter(contact);
    }
    #email-list{
        counter-reset: email;
    }
    .email-label::after{
        counter-increment: email;
        content:" "counter(email);
    }
</style>
<section class="py-5">
    <div class="container">
        <h2 class="fw-bolder text-center"><b>Clinic Details</b></h2>
        <hr>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
                <dl class="row">
                    <dt class="col-3 px-2">Location</dt>
                    <dd class="col-9 px-2"><?= isset($location)? $location : "" ?></dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Categories</dt>
                    <dd class="col-9 px-2">
                        <div class="lh-1">
                            <?php if(isset($cats)): ?>
                                <?php foreach($cats as $category): ?>
                                    <p class="mb-0"><?= $category ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Doctor(s)</dt>
                    <dd class="col-9 px-2">
                    <div class="lh-1">
                            <?php if(isset($doctors)): ?>
                                <?php foreach(explode("||", $doctors) as $doctor): ?>
                                    <p class="mb-0"><?= $doctor ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Contact Number(s)</dt>
                    <dd class="col-9 px-2">
                    <div class="lh-1">
                            <?php if(isset($contacts)): ?>
                                <?php foreach(explode("||", $contacts) as $contact): ?>
                                    <p class="mb-0"><?= $contact ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Email(s)</dt>
                    <dd class="col-9 px-2">
                    <div class="lh-1">
                            <?php if(isset($emails)): ?>
                                <?php foreach(explode("||", $emails) as $email): ?>
                                    <p class="mb-0"><?= $email ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Information</dt>
                    <dd class="col-9 px-2"><p class="mb-0"><?= isset($other) ? str_replace(["\n","\r"],'<br>',$other) : '' ?></p></dd>
                    <hr class="dark">
                    <dt class="col-3 px-2">Status</dt>
                    <dd class="col-9 px-2">
                        <?php
                            if(isset($status)){
                                if($status == 1){
                                    echo '<span class="badge bg-primary bg-gradient px-3 rounded-pill">Active</span>';
                                }else{
                                    echo '<span class="badge bg-secondary bg-gradient px-3 rounded-pill">Inactive</span>';
                                }
                            }
                        ?>
                    </dd>
                    <hr class="dark">
                    <div class="d-flex w-100 justify-content-end">
                        <div class="col-auto">
                            <a href="./?page=clinic" class="btn btn-light border bg-gradient btn-sm rounded-0 d-flex align-items-center"><span class="material-icons">chevron_left</span> Back to List</a>
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
            _conf("Are you sure to delete this from list?","delete_clinic",['<?= isset($id) ? $id : '' ?>'])
        })
    })
    function delete_clinic($id){
        start_loader();
        var _this = $(this)
        $('.err-msg').remove();
        var el = $('<div>')
        el.addClass("alert alert-danger err-msg")
        el.hide()
        $.ajax({
            url: '../classes/Master.php?f=delete_clinic',
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
                    location.replace('./?page=clinic')
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