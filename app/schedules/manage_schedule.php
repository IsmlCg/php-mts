<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `schedule_list` where id = '{$_GET['id']}' ");
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
        <h2 class="fw-bolder text-center"><b><?= isset($id) ? "Edit Schedule" : "Add New Schedule" ?></b></h2>
        <hr>
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
                <form action="" id="schedule-form" class="py-3">
                    <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
                    <input type="hidden" name="user_id" value="<?=  $_settings->userdata('id') ?>">
                    <div class="input-group mb-4 input-group-static is-filled">
                        <label for="medicine_id" class="form-label">Medicine <span class="text-primary">*</span></label>
                        <select id="medicine_id" name="medicine_id" value="<?= isset($location) ? $location : "" ?>" class="form-select" required>
                        <?php 
                        $categories = $conn->query("SELECT * FROM `medicine_list` where user_id = '{$_settings->userdata('id')}' order by `name` asc");
                        while($row = $categories->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>" <?= isset($cats) && in_array($row['id'], $cats) ? "selected" : "" ?>><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group mb-4 input-group-static is-filled">
                        <label for="day" class="form-label">Day <span class="text-primary">*</span></label>
                        <select id="day" name="day[]" multiple class="form-select" required>
                            <option <?= isset($day) && strpos(strtolower($day), 'sunday') > -1 ? "selected" : "" ?>>Sunday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'monday') > -1 ? "selected" : "" ?>>Monday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'tuesday') > -1 ? "selected" : "" ?>>Tuesday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'wednesday') > -1 ? "selected" : "" ?>>Wednesday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'thursday') > -1 ? "selected" : "" ?>>Thursday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'friday') > -1 ? "selected" : "" ?>>Friday</option>
                            <option <?= isset($day) && strpos(strtolower($day), 'saturday') > -1 ? "selected" : "" ?>>Saturday</option>
                        </select>
                    </div>
                    <div class="input-group mb-4 input-group-dynamic is-filled">
                        <label for="date_start" class="form-label">Date Started <span class="text-primary">*</span></label>
                        <input type="date" id="date_start" name="date_start" value="<?= isset($date_start) ? $date_start : "" ?>" class="form-control" required>
                    </div>
                    <div class="input-group input-group-dynamic is-filled">
                        <label for="until" class="form-label">Medication Ends At <span class="text-primary">*</span></label>
                        <input type="date" id="until" name="until" value="<?= isset($until) ? $until : "" ?>" class="form-control">
                    </div>
                    <div class="mb-4 row justify-content-center align-items-center">
                        <label for="lifetime_schedule">
                            <input type="checkbox" name="lifetime_schedule" id="lifetime_schedule" <?= isset($date_start) && !isset($until) ? "checked" : "" ?>>
                            Lifetime Maintenance
                        </label>
                    </div>
                    <div class="input-group mb-4 input-group-dynamic is-filled">
                        <label for="remarks" class="form-label">Remarks <span class="text-primary">*</span></label>
                        <textarea rows="4" id="remarks" name="remarks" class="form-control border rounded-0 px-2 py-1" required><?= isset($remarks) ? $remarks : "" ?></textarea>
                    </div>
                    <div class="input-group mb-4 input-group-dynamic is-filled">
                        <label for="time" class="form-label">Time <span class="text-primary">*</span></label>
                        <input type="time" id="time" name="time" value="<?= isset($time) ? $time : "" ?>" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn bg-primary bg-gradient btn-sm text-light w-25"><span class="material-icons">save</span> Save</button>
                            <a href="./?page=schedules" class="btn bg-deafult border bg-gradient btn-sm w-25"><span class="material-icons">keyboard_arrow_left</span> Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    var fuser_ajax;
    $(function(){
        $('#lifetime_schedule').change(function(){
            if($(this).is(':checked') == true){
                $('#until').val("")
                $('#until').attr("readonly",true)
            }else{
                $('#until').removeAttr("readonly")
            }
        })
        $('#medicine_id').select2({
            placeholder:"Please Select Here",
            width:"100%",
        })
        $('#day').select2({
            placeholder:"Please Select Day Shedule",
            width:"100%",
            dropdownCssClass:'form-control'
        })
        $('#schedule-form').submit(function(e){
            e.preventDefault()
            $('.pop-alert').remove()
            var _this = $(this)
            var el = $('<div>')
            el.addClass("pop-alert alert alert-danger text-light")
            el.hide()
            start_loader()
            $.ajax({
                url:'../classes/Master.php?f=save_schedule',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.error(err)
                    el.text("An error occured while saving data")
                    _this.prepend(el)
                    el.show('slow')
                    $('html, body').scrollTop(_this.offset().top - '150')
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.href= './?page=schedules/view_details&id='+resp.sid;
                    }else if(!!resp.msg){
                        el.text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $('html, body').scrollTop(_this.offset().top - '150')
                    }else{
                        el.text("An error occured while saving data")
                        _this.prepend(el)
                        el.show('slow')
                        $('html, body').scrollTop(_this.offset().top - '150')
                    }
                    end_loader()
                    console

                }
            })
        })

    })
</script>