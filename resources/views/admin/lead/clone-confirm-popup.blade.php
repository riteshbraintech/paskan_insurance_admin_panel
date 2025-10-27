<style scoped>
    #clone-confirm-popup{
        position: fixed;
        background: #fbfbfb;
        padding: 21px 21px 40px;
        border-radius: 10px;
        text-align: center;
        left: 40%;
        top: 20%;
        border: 1px solid grey;
        width: 390px;
    }

    .yesNo {
        display: block;
        text-align: center;
    }

    .yesNo>input {
        width: 90px;
        height: 30px;
        text-align: center;
        font-size: 12px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border: 2px solid black;
        cursor:pointer;
        border-radius: 5px;
        margin: 5px;
    }

    .activeBox,.activeBox::placeholder {
        background: #2899a1 !important;
        color: white !important;
        border: 1px solid black;
        outline: none;
    }

    .yes:focus::placeholder,.yes:focus,.yes:focus-visible {
        background: #2899a1;
        color: white;
        border: 1px solid black;
        outline: none;
    }

    .top_title {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        margin-bottom: 30px;
    }

    .yesNo>input[type=button]::-webkit-inner-spin-button,
    .yesNo>input[type=button]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }

    .yesNo>input::placeholder {
        color: white;
    }
    
    .no{
        background-color:#e64741;
        color:white;
    }

    .no:hover{
        background: #2899a1;
    }
    .close-btn{
        display: flex;
        justify-content: end;
        cursor:pointer;
    }
    .popup-screen h2 {
        font-size: 2.2rem;
        font-weight: 600;
        color: #000;
        line-height: 36px;
        margin-bottom: 10px;
    }

    .popup-screen p {
        font-size: 1.3rem;
        line-height: 20px;
        color: #333;
        font-weight: 500;
    }
</style>

<div class="popup-screen">
    <h2>Are you sure?</h2>
    <div class="top_title">
        <p>You want to clone this Lead?</p>
    </div>
</div>
<div class="yesNo">
    <input type="hidden" id="id" name="id" value="">
    <input type="button" name="no" id="" value="Cancel" placeholder="NO" class="no dont-clone-region" autocomplete="off"/>
    <input type="button" name="yes" id="" value="Confirm" placeholder="YES" class="clone-region yes activeBox" autocomplete="off" />
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click','#clone_lead', function() {
            let id = $(this).attr('data-id');
            $('#id').val(id);
            $('#clone-confirm-popup').removeClass('d-none');
            $('#clone-confirm-popup').show();
        });

        $(document).on("click","#close_save_popup_btn, .dont-clone-region",function(){
            $('#clone-confirm-popup').hide();
        });

        $(document).on("click", ".clone-region", function() {
            $("#spinner").show();
            let id = $('#id').val();
            customer_ajx_listRequest = $.ajax({
                url: "{{ route('admin.lead.clone') }}",
                method: "GET",
                data: {id:id},
            }).done(function(msg) {
                window.location.reload();
            }).fail(function(xhr) {
                if (xhr.status == 403) {
                    $('#clone-confirm-popup').hide();
                }
            });
        });
    });
</script>