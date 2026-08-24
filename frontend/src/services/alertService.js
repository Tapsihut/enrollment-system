import Swal from 'sweetalert2'

export const successAlert = (title, text) => {
    return Swal.fire({
        icon: 'success',
        title,
        text,
        timer: 2000,
        showConfirmButton: false
    })
}

export const errorAlert = (title, text) => {
    return Swal.fire({
        icon: 'error',
        title,
        text
    })
}

export const warningAlert = (title, text) => {
    return Swal.fire({
        icon: 'warning',
        title,
        text
    })
}

export const infoAlert = (title, text) => {
    return Swal.fire({
        icon: 'info',
        title,
        text
    })
}

export const confirmAlert = (title, text) => {
    return Swal.fire({
        title,
        text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0d6efd'
    })
}