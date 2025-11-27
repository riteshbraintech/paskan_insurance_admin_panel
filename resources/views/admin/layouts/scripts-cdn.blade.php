<!-- Bootstrap bundle JS -->
<script>
    var baseUrl = '{{ url('') }}';
</script>

<script src="{{ loadAssets('js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ loadAssets('js/jquery.min.js') }}"></script>
{{-- <script src="{{ loadAssets('plugins/simplebar/js/simplebar.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/metismenu/js/metisMenu.min.js') }}"></script> --}}
<script src="{{ loadAssets('plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
{{-- <script src="{{ loadAssets('plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('js/pace.min.js') }}"></script> --}}
<script src="{{ loadAssets('plugins/chartjs/js/Chart.min.js') }}"></script>
<script src="{{ loadAssets('plugins/chartjs/js/Chart.extension.js') }}"></script>
{{-- <script src="{{ loadAssets('plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('js/form-file-upload.js') }}"></script> --}}

{{-- <script src="{{ loadAssets('plugins/fancy-file-uploader/jquery.ui.widget.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/fancy-file-uploader/jquery.fileupload.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/fancy-file-uploader/jquery.iframe-transport.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/Drag-And-Drop/dist/imageuploadify.min.js') }}"></script> --}}

<!--notification js -->
{{-- <script src="{{ loadAssets('plugins/notifications/js/lobibox.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/notifications/js/notifications.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/notifications/js/notification-custom-script.js') }}"></script> --}}
<!-- database -->
{{-- <script src="{{ loadAssets('plugins/datatable/js/jquery.dataTables.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('js/table-datatable.js') }}"></script> --}}

<script src="{{ loadAssets('plugins/datetimepicker/js/legacy.js') }}"></script>
<script src="{{ loadAssets('plugins/datetimepicker/js/picker.js') }}"></script>
<script src="{{ loadAssets('plugins/datetimepicker/js/picker.time.js') }}"></script>
<script src="{{ loadAssets('plugins/datetimepicker/js/picker.date.js') }}"></script>
<script src="{{ loadAssets('plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script
    src="{{ loadAssets('plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}">
</script>
<script src="{{ loadAssets('js/form-date-time-pickes.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- <script src="{{ loadAssets('plugins/select2/js/select2.min.js') }}"></script> --}}
{{-- <script src="{{ loadAssets('js/form-select2.js') }}"></script> --}}


<script src="{{ loadAssets('plugins/input-tags/js/tagsinput.js') }}"></script>

{{-- //facebox --}}

<script src="{{ loadAssets('facebox/facebox.js') }}"></script>

<!--app-->

<script>
    // new PerfectScrollbar(".best-product")
</script>

<script>
    let extraSearchItem = '';

    function ajaxTableData(sortBy = '', direction = '') {
        let perPage = $('#filter-per-page').val();
        let page = $('#filter-page').val();
        let search = $('#filter-search-data').val();
        let url = "{{ URL::current() }}";
        let imageUrl = 'https://i.gifer.com/origin/b4/b4d657e7ef262b88eb5f7ac021edda87_w200.gif';
        let dataF = {
            'search': search,
            'perPage': perPage,
            'page': page,
            'method': 'ajax'
        };

        if (sortBy == '') {} else {
            dataF.sort = sortBy;
            dataF.direction = direction;
        }

        $.ajax({
            url: url + '?' + extraSearchItem,
            data: dataF,
            beforeSend: function(req) {
                $('.load-table-data').html(`
                <div class="text-center mt-5 mb-5">
                <img src="${imageUrl}" height="50" />
                </div>
            `);
            },
            success: function(result) {
                $('.load-table-data').html(result.html);

                // Now initialize facebox on the newly loaded content
                $('a[rel*=facebox]').facebox();
            }
        });

    }

    function selectAllCheckbox() {
        let stattus = document.getElementById('selectAllCheckbox').checked;
        if (stattus) {
            $('.checkbox-input').prop('checked', true);
        } else {
            $('.checkbox-input').prop('checked', false);
        }
    }

    function deleteItem(url) {
        let text = "Are you sure !";
        if (confirm(text) == true) {
            window.location = url;
        }
    }

    $(document).ready(function() {
        $('.metismenu a').click(function() {
            $(this).parent().toggleClass('mm-active')
            $(this).siblings().slideToggle();
        });

        $('[data-toggle="tooltip"]').tooltip();
    });

    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(document).on('click', '#global_delete', function() {
        let message = $(this).attr('data-message');
        let route = $(this).attr('data-route');
        let id = $(this).attr('data-id');

        $('#delete-confirm-popup').find('#message').html(message);
        $('#delete-confirm-popup').find('#id').val(id);
        $('#delete-confirm-popup').find('#route').val(route);
        $('#delete-confirm-popup').show();
    });

    function showFlashMessage(type, message) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${type.toUpperCase()}!</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        $("#flashMessageContainer").html(alertHtml);

        // Auto close after 3 seconds
        setTimeout(function() {
            $(".alert").alert("close");
        }, 3000);
    }
</script>

<script type="text/javascript">
    var baseUrl = "{{ loadAssets('facebox') }}";

    jQuery(document).ready(function($) {
        $('a[rel*=facebox]').facebox({
            loadingImage: baseUrl + '/loading.gif',
            closeImage: baseUrl + '/closelabel.png'
        })
    })
</script>

<script>
    $(document).ready(function() {
        $('.form-select').select2({
            width: '100%'
        });
    });
</script>
