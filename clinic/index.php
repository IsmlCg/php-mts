<section class="py-4">
    <div class="container">
        <h3 class="fw-bolder text-center">Clinic List</h3>
        <center>
            <hr class="bg-primary w-25 opacity-100">
        </center>
        <table class="table table-striped table-bordered dt-init">
            <colgroup>
                <col width="10%">
                <col width="20%">
                <col width="60%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Location</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>
<script>
    $(function(){
        $('.dt-init').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:"./classes/Master.php?f=dt_clinics_public",
                method:"POST"
            },
            columns: [{
                    data: 'no',
                    className: 'py-1 px-2 text-center',
                    width:"10%"
                },
                {
                    data: 'location',
                    className: 'py-1 px-2',
                    width:"20%"
                },
                {
                    className: 'py-1 px-2', 
                    render:function(data, type, row, meta){
                        return '<p class="m-0 text-truncate w-100">'+((row.category).substr(0,60))+'...</p>';
                        },
                    width:"60%"
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center py-1 px-2',
                    render: function(data, type, row, meta) {
                        return '<a href="./?page=clinic/view_details&id='+(row.id)+'" class="btn btn-primary btn-sm bg-gradient rounded-0 d-flex align-items-center"><span class="material-icons me-2">wysiwyg</span> View</a>';
                    },
                    width:"10%"
                }
            ],
            columnDefs: [{
                orderable: false,
                targets: [2,3]
            }],
            initComplete: function(settings, json) {
                $('table td, table th').addClass('px-2 py-1 align-middle')
            },
            drawCallback: function(settings) {
                $('table td, table th').addClass('px-2 py-1 align-middle')
            },
            language:{
                oPaginate: {
                    sNext: '<i class="fa fa-angle-right"></i>',
                    sPrevious: '<i class="fa fa-angle-left"></i>',
                    sFirst: '<i class="fa fa-step-backward"></i>',
                    sLast: '<i class="fa fa-step-forward"></i>'
                }
            }
        })
    })
   
</script>