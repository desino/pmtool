import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

const showToast = (message, type = 'success', options = {}) => {
    let messageTxt, backgroundColor;
    if (type === 'success') {
         messageTxt = "<i class='bi bi-check2-circle mx-2'></i>" + message;
         backgroundColor = "green";
    } else if(type === 'danger') {
         messageTxt = "<i class='bi bi-x-circle-fill mx-2'></i>" + message;
         backgroundColor = "red";
    } else if(type === 'warning') {
         messageTxt = "<i class='bi bi-exclamation-triangle mx-2'></i>" + message;
         backgroundColor = "orange";
    } else{
        messageTxt = "<i class='bi bi-bell-fill mx-2'></i>" + message;
        backgroundColor = "green";
    }

    Toastify({
        text: messageTxt,
        duration: 2000,
        stopOnFocus: true,
        gravity: "top",
        position: "right",
        backgroundColor: backgroundColor,
        offset: {
            x: 10,
            y: 10
        },
        escapeMarkup: false,
        oldestFirst: false,
    }).showToast();
};

export default showToast;
 