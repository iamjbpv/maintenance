@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Maintenance <button class="float-right btn btn-success" id="add_info">Add</button></div>
                <div class="card-body">
                    <table class="table" id="tbl_maintenance">
                        <thead>
                            <tr>
                                <th scope="col">Area Code</th>
                                <th scope="col">Description</th>
                                <th scope="col">Floor</th>
                                <th scope="col">Row</th>
                                <th scope="col">Column</th>
                                <th class="text-center" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_add_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold txn_title">Add Maintenance</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-3">
        <form id="frm_maintenance">
          <div class="md-form mb-3">
            <i class="fas fa-user prefix grey-text"></i>
            <input type="text" name="area_code" data-error="Area Code" id="area-code" class="form-control mx-0 w-100" required>
            <label for="area-code" class="mx-0">Area Code*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <i class="fas fa-envelope prefix grey-text"></i>
            <input type="text" name="description" data-error="Description" id="description" class="form-control mx-0 w-100" required>
            <label for="description" class="mx-0">Description*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <i class="fas fa-envelope prefix grey-text"></i>
            <input type="text" name="floor" data-error="Floor" id="floor" class="form-control mx-0 w-100" required>
            <label for="floor" class="mx-0">Floor*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <i class="fas fa-envelope prefix grey-text"></i>
            <input type="number" name="row" data-error="Row" id="row" class="form-control mx-0 w-100" required>
            <label for="row" class="mx-0">Row*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <i class="fas fa-envelope prefix grey-text"></i>
            <input type="number" name="column" data-error="Column" id="column" class="form-control mx-0 w-100" required>
            <label for="column" class="mx-0">Column*</label>
            <small class="data-error"></small>
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-success" id="btn_create">Add</button>
      </div>
    </div>
  </div>
</div>

<!-- Frame Modal Top -->
<div class="modal fade top" id="frame_delete_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"aria-hidden="true">
  <!-- Add class .modal-frame and then add class .modal-bottom (or other classes from list above) to set a position to the modal -->
  <div class="modal-dialog modal-frame modal-top" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row d-flex justify-content-center align-items-center">

          <p class="pt-3 pr-2">
            <strong>Are you sure you want to delete this?
          </p>

          <button type="button" id="btn_yes" class="btn btn-danger">Yes</button>
          <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Frame Modal Bottom -->


@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function(){
    var dt; var _txnMode; var _selectedID; var _selectRowObj;
    var initializeControls=function(){
      dt=$('#tbl_maintenance').DataTable({
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            data: {!! json_encode($maintenance, true) !!},
            "columns": [
                { targets:[0],data: "area_code" },
                { targets:[1],data: "description" },
                { targets:[2],data: "floor" },
                { targets:[3],data: "row" },
                { targets:[4],data: "column" },
                {
                    targets:[5],
                    render: function (data){
                        return '<center>'+
                        '<button class="btn btn-primary" name="edit_info">Edit</button><button name="remove_info" class="btn btn-danger">Delete</button>'+
                        '</center>';
                    }
                }
            ],
            language: {
                searchPlaceholder: "Search"
            },
        });
    }();

    $('#add_info').click(function(){
        _txnMode="new";
        clearFields($('#frm_maintenance'))
        $('.txn_title').html('Add Maintenance');
        $("#modal_add_item").modal('toggle');
    });

    $('#tbl_maintenance tbody').on('click','button[name="edit_info"]',function(){
        _txnMode="edit";
        $('.txn_title').html('Edit Maintenance');
        _selectRowObj=$(this).closest('tr');
        var data=dt.row(_selectRowObj).data();
        _selectedID=data.id;
        $('input,textarea').each(function(){
            var _elem=$(this);
            $.each(data,function(name,value){
                if(_elem.attr('name')==name){
                    _elem.val(value);
                    _elem.next().addClass('active');
                }
            });
        });

        $('#modal_add_item').modal('toggle');
    });

    $('#tbl_maintenance tbody').on('click','button[name="remove_info"]',function(){
        _selectRowObj=$(this).closest('tr');
        var data=dt.row(_selectRowObj).data();
        _selectedID=data.id;
        $('#frame_delete_item').modal('show');
    });

    $('#btn_yes').click(function(){
        removeMaintenance().done(function(response){
          if(response.stat){
            dt.row(_selectRowObj).remove().draw();
            $('#frame_delete_item').modal('hide');
            toast(response.stat, response.message, response.title);
          }
        });
    });

    $('#btn_create').click(function(){
        if(validateRequiredFields($('#frm_maintenance'))){
            if(_txnMode==="new"){
              createMaintenance().done(function(response){
                  if(response.stat=="error"){
                      toast(response.stat, response.message, response.title);
                      return;
                  }
                  toast(response.stat, response.message, response.title);
                  dt.row.add(response.row_data).draw();
              }).always(function(){
                  $('#modal_add_item').modal('toggle');
              });
              return;
            }
            if(_txnMode==="edit"){
              updateMaintenance().done(function(response){
                  if(response.stat=="error"){
                      toast(response.stat, response.message, response.title);
                      return;
                  }
                  toast(response.stat, response.message, response.title);
                  dt.row(_selectRowObj).data(response.row_data).draw();
              }).always(function(){
                  $('#modal_add_item').modal('toggle');
              });
              return;
            }
        }
    });

    var createMaintenance=function(){
        var _data=$('#frm_maintenance').serializeArray();
        return $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            "dataType":"json",
            "type":"POST",
            "url":"maintenance/store",
            "data":_data,
            // "beforeSend": showSpinningProgress($('#btn_save'))
        });
    };

    var updateMaintenance=function(){
        var _data=$('#frm_maintenance').serializeArray();
        _data.push({name : "id" ,value : _selectedID});
        return $.ajax({
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url:"maintenance/store",
            data:_data,
            // "beforeSend": showSpinningProgress($('#btn_save'))
        });
    };

    var removeMaintenance=function(){
        return $.ajax({
            "dataType":"json",
            "type":"POST",
            "url":"maintenance/delete",
            "data":{
              _token: "{{ csrf_token() }}",
              id : _selectedID
            }
        });
    };

    function toast(type, message, title) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr[type](message, title);
    }

    var validateRequiredFields=function(f){
        var stat=true;
        $('input[required],textarea[required],select[required]',f).removeClass('invalid');
        $('label',f).removeClass('data-error');
        $('input[required],textarea[required],select[required]',f).each(function(){
                if($(this).val()==""){
                    $(this).next().next().html($(this).data('error') + ' is required!');
                    $(f).find('input:first').focus();
                    stat = false;
                }
        });
        return stat;
    };

    var clearFields=function(f){
        $('input,textarea',f).val('');
        $(f).find('input:first').focus();
    };
});
</script>
@endsection