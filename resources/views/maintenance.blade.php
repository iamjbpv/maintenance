@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          @include('maintenance.list')
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
          <div class="md-form mb-3 bg-disabled">
            <input type="text" name="area_code" data-error="Area Code" id="area-code" class="form-control mx-0 w-100 text-center" disabled>
            <label for="area-code" class="mx-0">Area Code</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <input type="text" name="description" data-error="Description" id="description" class="form-control mx-0 w-100" required>
            <label for="description" class="mx-0">Description*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <input type="text" name="floor" data-error="Floor" id="floor" class="form-control mx-0 w-100" required>
            <label for="floor" class="mx-0">Floor*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <input type="number" name="row" data-error="Row" id="row" class="form-control mx-0 w-100" required>
            <label for="row" class="mx-0">Row*</label>
            <small class="data-error"></small>
          </div>
          <div class="md-form mb-3">
            <input type="number" name="column" data-error="Column" id="column" class="form-control mx-0 w-100" required>
            <label for="column" class="mx-0">Column*</label>
            <small class="data-error"></small>
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-success" id="btn_create">Save</button>
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

<!-- Central Modal Small -->
<div class="modal fade" id="modal_change_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">

  <!-- Change class .modal-sm to change the size of the modal -->
  <div class="modal-dialog modal-sm" role="document">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title w-100" id="myModalLabel">Change Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frm_status">
            <div class="form-group">
              <label>Status: </label>
              <select name="table_status_id" class="browser-default custom-select">
                <option value="" selected>Select Status</option>
                @foreach($table_status as $status)
                  <option value="{{ $status->id }}">{{  $status->name }}</option>
                @endforeach
              </select>
              <small class="data-error"></small>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" id="btn_save_status">Save</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Central Modal Small -->
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function(){
    var dt; var _txnMode; var _selectedID; var _selectRowObj; 
    // var _selectedStatusId;
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
                    render: function (data, type, full, meta){
                        return '<center>'+
                          '<button class="btn btn-primary btn-sm btn-xsj mx-0" name="edit_info"><i class="fas fa-edit"></i></button>'+
                          '<button name="remove_info" class="btn btn-danger btn-sm btn-xsj mx-1"><i class="fas fa-trash"></i></button>'+
                          '<a href="/maintenance/items/'+full.id+'" name="preview_items" class="btn btn-secondary btn-sm btn-xsj"><i class="fas fa-list"></i></button>'+
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
        $('#frm_maintenance input,textarea').each(function(){
            var _elem=$(this);
            _elem.next().next().html('');
            $.each(_elem,function(name,value){
             _elem.next().removeClass('active');
            });
        });
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
                _elem.next().next().html('');
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
          showSpinningProgress($('#btn_yes'),false,"YES")
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
              createMaintenance();
              return;
            }
            if(_txnMode==="edit"){
              updateMaintenance();
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
            success: function (response) {
                succesHandler(response);
            },
            error: function (response) {
                errorHandler(response);
            },
            beforeSend: showSpinningProgress($('#btn_create'),true)
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
            success: function (response) {
                succesHandler(response);
            },
            error: function (response) {
                errorHandler(response);
            },
            beforeSend: showSpinningProgress($('#btn_create'),true)
        });
    };
    
    var removeMaintenance=function(){
        return $.ajax({
            dataType:"json",
            type:"POST",
            url:"maintenance/delete",
            data:{
              _token: "{{ csrf_token() }}",
              id : _selectedID
            },
            beforeSend: showSpinningProgress($('#btn_yes'),true)
        });
    };

    //for maintenance form
    function succesHandler(response){
        showSpinningProgress($('#btn_create'),false);
        if(response.stat=="error"){
            toast(response.stat, response.message, response.title);
            return;
        }
        toast(response.stat, response.message, response.title);
        if(_txnMode=="new"){
          dt.row.add(response.row_data).draw(false);
        }
        if(_txnMode=="edit"){
          dt.row(_selectRowObj).data(response.row_data).draw(false);
        }
        $('#modal_add_item').modal('toggle');
    }
    //for maintenance form
    function errorHandler(response){
        var errors = response.responseJSON.errors;
        showSpinningProgress($('#btn_create'),false);
        $('input,textarea,select').each(function(){
              var _elem=$(this);
              _elem.next().next().html('');
              $.each(errors,function(name,value){
                  if(_elem.attr('name')==name){
                      _elem.next().next().html( value );
                  }
              });
        });
    }
});
</script>
@endsection