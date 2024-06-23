$(document).ready(function (modalId) {
    $('#modal').on('hidden.bs.modal', function (event) {
        const modal = $(this)
        modal.find('.modal-content').html('Loading ...');
        modal.removeData('bs.modal')
    }).on('show.bs.modal', function (event) {
        const modal = $(this)
        const target = $(event.relatedTarget)
        const modalClass = target.attr('data-bs-modal-class')
        const modalDialog = modal.find('.modal-dialog')
        const modalClasses = ['modal-sm', 'modal-lg', 'modal-xl']
        if ($.inArray(modalClass, modalClasses) > -1) {
            modalDialog.addClass(modalClass)
        }
        // if ($.inArray(target.data('modal-class'), modalClasses) > -1) {
        //     modalDialog.addClass(target.data('modal-class'))
        // }
        modal.find('.modal-content').load(target.attr('href'))
    })
})


function getAjaxUpdateUrl(url) {
    if (url === undefined || url === '') {
        url = location.pathname.split('/')
    } else {
        url = url.split('/')
    }
    url.splice($.inArray(url[url.length - 1]), 1)
    return url.join('/') + '/sort-order'
}

function editOrder(obj) {
    let id = $(obj).attr('data-id')
    if (!id) {
        id = $(obj).parent().parent().attr('id')
    }
    if (!id) {
        id = $(obj).parent().parent().attr('data-key')
    }
    let order = $(obj).val()
    if (isNaN(order)) {
        errorMsg($(obj).attr('data-message'))
        return false
    } else {
        let url = getAjaxUpdateUrl($(obj).attr('data-current-url'))
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            data: {
                id: id,
                order: order,
            },
            success: function (response) {
                console.log(response, typeof response)
                if (parseInt(response.code) !== 200) {
                    errorMsg(response.message)
                }
            }
        })
    }
}
