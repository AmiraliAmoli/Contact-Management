document.addEventListener("DOMContentLoaded", function () {

    const deleteButtons = document.querySelectorAll(".delete-btn");

    deleteButtons.forEach(function (button) {

        button.addEventListener("click", function (e) {

            const result = confirm("آیا از حذف این مخاطب مطمئن هستید؟");

            if (!result) {
                e.preventDefault();
            }

        });

    });

});