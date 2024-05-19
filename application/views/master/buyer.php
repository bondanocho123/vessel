<!doctype html>
<html lang="en">
  <head>    
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url("css/style.css") ?>" rel="stylesheet">
    <link href="<?= base_url("vendors/datatables.net-bs4/css/dataTables.bootstrap4.css") ?>" rel="stylesheet">
    <link href="<?= base_url("vendors/toastr/css/toastr.min.css") ?>" rel="stylesheet">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <title>Buyer</title>
    <script type="text/javascript">
        $(document).ready(function(){
            SetDataViewBuyer();

            function IsValidTextField(){
                var retBuyerName = true;
                var ret = true;
                if ($("#buyer_name").val() == "" || $("#buyer_name").val() == null) {
                    $("#buyer_name").removeClass("form-control").addClass("form-control is-invalid");
                    $("#invalid-feedback-buyer-name").empty();
                    $("#invalid-feedback-buyer-name").append("This Buyer Name field is required")

                    retBuyerName = false;
                } else {
                    $("#buyer_name").removeClass("form-control is-invalid").addClass("form-control is-valid");
                    $("#invalid-feedback-buyer-name").empty();

                    retBuyerName = true;
                }
                if (retBuyerName == false){
                    ret = false;
                }

                return ret;
            }

            //event jquery
            $("#btnTambahBaru").click(function(){
                ClearFormBuyer();
                $("#modalFormBuyer").modal("show");
                $("#buyer_name").focus();
            })

            $("#btnSimpan").click(function(){
                if (!IsValidTextField()){
                    ShowNotificationToaster("error", "Data gagal disimpan", "Failed")
                    return false;
                } else {
                    data = {
                        buyer_code : $("#buyer_code").val(),
                        buyer_name : $("#buyer_name").val(),
                    }

                    $.ajax({
                        method :"post",
                        url : "<?= base_url("apibuyer/update_data_buyer") ?>",
                        dataType:"json",
                        data : data,
                        async : false,
                        headers: {
                            "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(res){
                            if (res.success == 1) {
                                $("#modalFormBuyer").modal("hide");
                                ShowNotificationToaster("success", res.message, "Success")
                                ClearFormBuyer();
                                SetDataViewBuyer();
                            } else {
                                ShowNotificationToaster("error", res.message, "Failed")
                            }
                        },
                        error : function(xhr, status, error){
                            var errorMessage = xhr.status + " : " + xhr.statusText;
                            alert("Error - " + errorMessage) 
                        },
                        beforeSend : function(){
                            // $('#myModalSpinnerPleaseWait').modal({backdrop: 'static', keyboard: false});	
                        },
                        complete : function(){
                            
                        }
                    })
                }
            })

            $(".entire-selected-row").change(function(){
                if ($(this).is(":checked")){
                    $(".selected-row").prop("checked", true)
                } else {
                    $(".selected-row").prop("checked", false)
                }
            })

            $("#btnDeletingSelected").click(function(){
                var selected = [];
                $(".selected-row").each(function(){
                    if ($(this).is(":checked")){
                        selected.push($(this).val());
                    }
                })
                deleteBuyer(selected);
            })
        })
    </script>
    
    <script type="text/javascript">
        function OnChangeSelectedRow(){
            if (IsCheckEntireSelected()){
                $(".entire-selected-row").prop("checked", true);
            } else {
                $(".entire-selected-row").prop("checked", false);
            }
        }
        function IsCheckEntireSelected(){
            var allSelected = true;
            $(".selected-row").each(function(){
                if (!$(this).is(":checked")){
                    allSelected = false;
                    return false; // exit the loop early if an unchecked element is found
                }
            })
            return allSelected;
        }
        function ShowNotificationToaster(type, message, title) {
            Command: toastr[type](message, title)
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "100",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "swing",
                "showMethod": "slideDown",
                "hideMethod": "slideUp"
            }
        }
        function SetDataViewBuyer(){
            $('#myModalSpinnerPleaseWait').modal({backdrop: 'static', keyboard: false});
            table_buyer.destroy();
            table_buyer = $("#table-buyer").DataTable({
                "ajax" : {
                    "url" : "<?= base_url("apibuyer/gets_master_buyer") ?>",
                    "type" : "get",
                    "headers" : {
                    "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                },
                },
                "initComplete" : function(settings, json){
                    setTimeout(() => {
                        $("#hide-spinner").trigger("click");
                    }, 1000);
                },
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                paging: true,
                ordering : false,
                columnDefs: [
                    { "width": "25px", "targets": [0,2] },
                ],
                "columns" : [
                    {data :  null,
                        render : (data) =>{
                        return '<input type="checkbox" class="form-control-sm selected-row" value="'+data.buyer_code+'" onchange="OnChangeSelectedRow()">';
                        }
                    },
                    {data :  null,
                        render : (data) =>{
                        return data.buyer_name;
                        }
                    },
                    {data :  null,
                        render : (data) =>{
                            id = data.buyer_code
                            param = data.buyer_code+";"+data.buyer_name
                            html ='<div class="dropdown dropleft">'
                            html += '<button class="btn btn-ghost" data-toggle="dropdown">'
                            html += '<span class="fas fa-ellipsis-v"></span>'
                            html += '</button>'
                            html += '<div class="dropdown-menu dropdown-left">'
                            html += "<button class='dropdown-item' onclick='changesBuyerName(\""+param+"\")'><i class='fas fa-edit text-primary'></i> <span class='mx-2 text-primary'>Edit Data</span></button>";
                            html += "<button class='dropdown-item' onclick='deleteBuyer("+id+")'><i class='fas fa-times text-danger'></i> <span class='mx-2 text-danger'>Hapus Data</span></div>";
                            html += '</div>'
                            html += '</div>'
                            return html
                        }
                    },
                ]
            })
        }
            
        function ClearFormBuyer(){
            $("#invalid-feedback-buyer-name").empty();
            $("#buyer_name").removeClass("form-control is-invalid").addClass("form-control");
            $("#buyer_name").removeClass("form-control is-valid").addClass("form-control");
            $("#buyer_name").val("");
            $("#buyer_code").val("");
        }
        function changesBuyerName(params){
            ClearFormBuyer();
            var datas = params.split(";");
            $("#buyer_code").val(datas[0]);
            $("#buyer_name").val(datas[1]);
            $("#modalFormBuyer").modal("show");
            $("#buyer_name").focus();
        }
        function deleteBuyer(id){
            swal({
                title: "Are you sure want to delete it ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                if (willDelete) {
                    data = {
                        buyer_code : id
                    }
                    $.ajax({
                        method :"post",
                        url : "<?= base_url("apibuyer/delete_data_buyer/") ?>",
                        dataType:"json",
                        data : data,
                        async : false,
                        headers: {
                            "X-CSRF-TOKEN" : $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(data){
                            if (data.success == 0) {
                                ShowNotificationToaster("error", data.message, "Failed")
                            } else {
                                ShowNotificationToaster("success", data.message, "Success")
                                SetDataViewBuyer();
                            }
                        },
                        error : function(xhr, status, error){
                            // var errorMessage = xhr.status + " : " + xhr.statusText;
                            // alert("Error - " + errorMessage)
                        }
                    }).done(function(){
                    })
                }
            });
        }
    </script>
  </head>
  <body class="c-app">
    <?php
        $this->load->view("template/sidebar-admin.php");
    ?>
    <div class="c-wrapper c-fixed-components">
        <?php
            $this->load->view("template/topbar-admin.php");
        ?>
        <div class="c-subheader px-3">
          <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item active">Buyer</li>
          </ol>
        </div>
      <div class="c-body">
        <main class="c-main">
          <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0 text-primary">Buyer</h4>
                            </div>
                            <div >
                                <button class="btn default" id="btnTambahBaru"><span class="fas fa-plus"></span><span class="mx-2">Tambah Baru</span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12">
                        <div class="card card-accent-primary">
                            <div class="card-body">
                                <div class="c-chart-wrapper">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped " id="table-buyer">
                                                    <thead>
                                                        <tr>
                                                            <th class="bg-primary text-white" style="border-radius:10px 0px 0px 0px"><input type="checkbox" class="form-control-sm entire-selected-row" ></th>
                                                            <th class="bg-primary text-white" >Buyer Name</th>
                                                            <th class="bg-primary text-white" style="border-radius:0px 10px 0px 0px">#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-12">
                                            <button class="btn btn-danger rounded-pill" id="btnDeletingSelected"><span class="fas fa-times"></span><span class="mx-2">Hapus yang ditandai</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal" id="modalFormBuyer" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable  modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="text-white"><span class="fas fa-edit"></span> <span class="mx-2">Edit Buyer</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="buyer_name" class="control-label">Buyer Name</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><span class="fas fa-edit"></span></span>
                                </div>
                                <input type="text" id="buyer_name" name="buyer_name" class="form-control">
                                <input type="hidden" class="form-control"  id="buyer_code" name="buyer_code">
                                
                                <div class="invalid-feedback" id="invalid-feedback-buyer-code"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnSimpan">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <?php $this->load->view("template/modal-list-data") ?>
        <?php
            $this->load->view("template/footer-admin");
            $this->load->view("template/modal-logout");
            $this->load->view("template/modal-spinner-please-wait");
        ?>
      </div>
    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="<?= base_url("vendors/@coreui/coreui-pro/js/coreui.bundle.min.js") ?>"></script>
    <!--[if IE]><!-->
    <script src="<?= base_url("vendors/@coreui/icons/js/svgxuse.min.js") ?>"></script>
    <!--<![endif]-->
    <!-- Plugins and scripts required by this view-->
    
    <script src="<?= base_url("vendors/datatables.net/js/jquery.dataTables.js") ?>"></script>
    <script src="<?= base_url("vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js") ?>"></script>
    <script src="<?= base_url("js/datatables.js") ?>"></script>
    <script src="<?= base_url("vendors/toastr/js/toastr.js")?>"></script>
    <script>
        var table_buyer = $("#table-buyer").DataTable();
    </script>
    
    
  </body>
</html>