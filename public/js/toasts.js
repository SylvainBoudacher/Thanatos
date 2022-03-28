document.addEventListener("DOMContentLoaded", (e) => {

    const successToast = document.querySelector("#toast-success");
    const errorToast = document.querySelector("#toast-danger");
    const warningToast = document.querySelector("#toast-warning");
    const toasts = [successToast, errorToast, warningToast];

    toasts.forEach((toast) => {

        if (toast !== null) {
            toast.classList.add("translate-x-[0%]");
        }
    })

})





