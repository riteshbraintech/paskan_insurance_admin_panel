<script>
    // index page js
    function filterRole(event, role = '',id = '',status='') {
        let search = "";
        let roleId = "";
        search = $('#filter-search-data').val();
        
        $('.active').removeClass('active');

        if (id != '') {
            $("#" + id).addClass('active');
        }

        if (role == ''){
            roleId = "";
        }else{
            roleId = role;
        }
        extraSearchItem = 'role=' + roleId+'&'+'search='+search+'&'+'status='+status;
        ajaxTableData();
    }
    
    function changeStatus(event,id){
        let url = "{{route('admin.staff.change.status')}}"+"/"+id;
        $.ajax({
            url : url,
            type: "post",
            data: {"_token": "{{ csrf_token() }}"},
            dataType: "json",
            success: function(response){
                if(response.status == "active"){
                    $(`.status-${id}`).html("Active");
                    $(`.status-${id}`).addClass("badge-success");
                    $(`.status-${id}`).removeClass("badge-danger");
                }else{
                    $(`.status-${id}`).html("Inactive");
                    $(`.status-${id}`).addClass("badge-danger");
                    $(`.status-${id}`).removeClass("badge-success");
                }
            }
        }); 
    }

    // crate and edit page js
    
    $(document).ready(function(){
        let role = $(".role_id_select").val();
        showHide(role);
    });

    // function desableEnable() {
    //     if ($("#checkboxId").is(':checked')) {
    //         document.getElementById("TfLroad").disabled = true;
    //     } else {
    //         document.getElementById("TfLroad").disabled = false;
    //     }
    // }

    // desableEnable();

    $('.role_id_select').change(function(){
        let role = $(this).val();
        showHide(role);
    });

    function showHide(role){
        if(role == 'staff'){
            $('.manager-div').show();
            $('.created_by').attr("required");
        }else{
            $('.manager-div').hide();
            $('.created_by').removeAttr("required");    
        }

        if(role == 'manager'){
            $('.qtrly_target_container').removeAttr('style');
        }else{
            $(".qtrly_target").val("");
            $(".minimum_accepted_qtrly_target").val("");
            $(".qtrly_target").siblings("div").html("");
            $(".minimum_accepted_qtrly_target").siblings("div").html("");
            $('.qtrly_target_container').attr('style','display:none');
        }
    }
</script>