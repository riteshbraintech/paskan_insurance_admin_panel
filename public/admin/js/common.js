// Helper function to inject flash message HTML dynamically
function showFlashMessage(type, message) {
    let icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    let color = type === 'success' ? 'success' : 'danger';
    let bg = type === 'success' ? 'bg-light-success' : 'bg-light-danger';

    let html = `
                <div class="alert border-0 border-${color} border-start border-4 ${bg} alert-dismissible fade show py-2 mt-2">
                    <div class="d-flex align-items-center">
                        <div class="fs-3 text-${color}">
                            <i class="bi ${icon}"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-${color}">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top:-9px;"></button>
                </div>
            `;

    // Inject message and auto-remove old one
    $("#flash-message-container").html(html);

    setTimeout(() => {
        $(".alert").fadeOut('slow', function () {
            $(this).remove();
        });
    }, 2000);
}