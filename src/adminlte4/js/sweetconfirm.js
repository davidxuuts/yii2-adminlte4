/**
 * Displays a confirmation dialog.
 * The default implementation simply displays a js confirmation dialog.
 * You may override this by setting `yii.confirm`.
 * @param message the confirmation message.
 * @param ok a callback to be called when the user confirms the message
 * @param cancel a callback to be called when the user cancels the confirmation
 */
yii.confirm = function (message, ok, cancel) {
    const swalButtons = Swal.mixin({
        toast: false,
        showCancelButton: true,
        focusConfirm: false,
        customClass: {
            confirmButton: 'btn btn-success me-1',
            cancelButton: 'btn btn-secondary ms-1'
        },
        buttonsStyling: false,
    })
    swalButtons.fire({
        icon: 'question',
        title: message,
    }).then((result) => {
        if (result.isConfirmed) {
            ok.call()
        } else {
            return false
        }
    });
}