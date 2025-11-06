$(document).ready(function() {

    $('.updateBtn').click(function (e) {
        e.preventDefault();

        confirmModal('정책을 변경하시겠습니까?').then((isConfirmed) => {
            if (isConfirmed) {

                const recoad = $(this).closest('.income_policy');
                const formData = new FormData($('#updateForm')[0]);

                recoad.find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();

                    if (name) {
                        formData.append(name, value);
                    }
                });

                console.log(formData);
                $.ajax({
                    url: $('#updateForm').attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        alertModal(response.message, response.url);
                    },
                    error: function( xhr, status, error) {
                        console.log(error);
                        alertModal('예기치 못한 오류가 발생했습니다.');
                    }
                });
            } else {
               return;
            }
        });
    });
});
