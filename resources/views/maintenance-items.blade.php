@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card" id="wrapper_table_preview">
    <div class="card-header align-middle">Table Preview for <strong>{{ $maintenance_desc }}</strong> <a href="{{ route('home') }}" class="float-right btn btn-primary btn-sm" id="back_to_list">Back</a></div>
        <div class="card-body">
            <div class="items-wrapper p-3 text-center d-flex justify-content-center flex-column">
            </div>
        </div>
    </div>
    </div>
</div>

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
<script>
    var _selectedID = "{{ $id }}";
    var _selectedStatusId;
    var items = {!! json_encode($maintenance_items, true) !!};
    var html = '<div class="d-flex flex-row justify-content-start mx-auto">';
    var current_row = 1;
    const map1 = items.map((data) => {
    if(current_row == data.row_position){ 
        html += format(data);
    }
    if(current_row != data.row_position){
        html += '</div>';
        html += '<div class="d-flex flex-row justify-content-start mx-auto">';
        html += format(data);
        current_row = data.row_position;
    }
    });
    html += '</div>';
    $('.items-wrapper').html(html);

    $('.items-wrapper').on('click','button[name="set_status"]',function(){
        _selectedStatusId = $(this).data('id')
        $('#frm_status input,textarea,select').each(function(){
            var _elem=$(this);
            _elem.next().html('');
        });
        $('#modal_change_status').modal('toggle');
    });

    $('#btn_save_status').click(function(){
        updateItemStatus(_selectedStatusId);
    });

    var previewItems=function(){
        return $.ajax({
            dataType:"json",
            type:"GET",
            url:"preview-items/"+_selectedID,
            success: function(response){
              var html = '<div class="d-flex flex-row justify-content-start mx-auto">';
              var current_row = 1;
              const map1 = response.map((data) => {
                if(current_row == data.row_position){ 
                  html += format(data);
                }
                if(current_row != data.row_position){
                  html += '</div>';
                  html += '<div class="d-flex flex-row justify-content-start mx-auto">';
                  html += format(data);
                  current_row = data.row_position;
                }
              });
              html += '</div>';
              $('.items-wrapper').html(html);
            }
        });
    };

    var updateItemStatus=function(){
        var _data=$('#frm_status').serializeArray();
        _data.push({name : "id" ,value : _selectedStatusId});
        return $.ajax({
            dataType:"json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url:"update-item",
            data:_data,
            success: function (response) {
              showSpinningProgress($('#btn_save_status'),false)
              if(response.stat=="error"){
                  toast(response.stat, response.message, response.title);
                  return;
              }
              toast(response.stat, response.message, response.title);
              previewItems();
              $('#modal_change_status').modal('toggle');
            },
            error: function (response) {
                var errors = response.responseJSON.errors;
                showSpinningProgress($('#btn_save_status'),false)
                $('input,textarea,select').each(function(){
                      var _elem=$(this);
                      _elem.next().html('');
                      $.each(errors,function(name,value){
                          if(_elem.attr('name')==name){
                              _elem.next().html( value );
                          }
                      });
                });
            },
            beforeSend: showSpinningProgress($('#btn_save_status'),true)
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

    var showSpinningProgress = function(e,type,text='Save'){
      if(type) {
        e.html('<div class="custom-spinner spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>');
      }else {
        e.html(text);
      }
        
    };

    function format ( data ) {
        return '<div class="item-box d-flex flex-column">'+data.description+
                '<button class="btn btn-primary btn-sm" name="set_status" data-id="'+data.id+'">'+data.tablestatus.name+'</button>'+
                '</div>';
    };
</script>
@endsection