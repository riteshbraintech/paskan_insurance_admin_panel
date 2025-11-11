<style scoped>
    #delete-confirm-popup{
        display: none;
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
    .delete-btn{
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
        <p>You want to <span id="message"></span>?</p>
    </div>
</div>
<div class="yesNo">
    <input type="hidden" id="id" name="id" value="">
    <input type="hidden" id="route" name="route" value="">
    <input type="button" name="no" id="" value="Cancel" placeholder="NO" class="no dont-delete" autocomplete="off"/>
    <input type="button" name="yes" id="" value="Confirm" placeholder="YES" class="delete yes activeBox" autocomplete="off" />
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        $(document).on("click",".dont-delete",function(){
            $('#delete-confirm-popup').hide();
        });

        $(document).on("click", ".delete", function() {
            $("#spinner").show();
            let url = $('#route').val();
            customer_ajx_listRequest = $.ajax({
                url: url,
                method: "GET",
            }).done(function(msg) {
                window.location.reload();
            }).fail(function(xhr) {
                if (xhr.status == 403) {
                    $('#delete-confirm-popup').hide();
                }
            });
        });
    });
</script>