/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

function generateRandomKey(length) {
    length = length || 32;
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_';
    let maxPos = chars.length;
    let str = ''
    for (let i = 0; i < length; i++) {
        str += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return str
}

function getHash(file) {
    return new Promise(function (resolve) {
        let hash = ''
        let reader = new FileReader()
        reader.readAsArrayBuffer(file)
        reader.onload = () => {
            hash = getEtag(reader.result)
            resolve(hash)
        }
    })
}

function getFileInfo(file, basePath) {
    const mimeType = (file.type.split('/', 1)[0]).toLowerCase()
    let fileType = ['image', 'video', 'audio'].includes(mimeType) > 0 ? mimeType : 'other'
    const extension = (file.name.substring(file.name.lastIndexOf('.'))).toLowerCase()
    const key = basePath + fileType + '/' + generateRandomKey() + extension
    const chunkKey = key.replace(/\//g, '_').replace(/\./g, '_')
    return {
        name: file.name,
        extension: extension,
        key: key,
        size: file.size,
        mime_type: file.type,
        file_type: fileType,
        chunk_key: chunkKey
    }
}

function progressBody(percent, progress_class) {
    if (progress_class === '' || progress_class === null || typeof progress_class === 'undefined') {
        progress_class = 'progress-bar-animated progress-bar-striped bg-info'
    }
    return [
        '<div class="progress">',
        '<div class="progress-bar ',
        progress_class,
        ' " ',
        'role="progressbar" aria-valuenow="',
        percent,
        '" aria-valuemin="0" aria-valuemax="100" style="width: ',
        percent,
        '%"> ',
        percent,
        '% </div>',
        '</div>',
    ].join('')
}

sweetAlertToast = Swal.mixin({
    showConfirmButton: false,
    backdrop: `rgba(0, 0, 0, 0.8)`,
    title: '<i class="bi bi-hourglass-split"></i>',
})

Array.prototype.indexOf = function (val) {
    for (let i = 0; i < this.length; i++) {
        if (this[i] === val) return i;
    }
    return -1;
}
Array.prototype.remove = function (val) {
    let index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
}

