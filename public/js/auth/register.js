$(document).ready(function() {
    const passwordGuide = $('#msg_password_guide').html();

    $('#accountCheck').click(function() {
        const account = $('#inputAccount').val();

        $('#inputAccountCheck').val(account);

        const accountCheckForm = $('#accountCheckForm')[0];
        const accountCheckFormData = new FormData(accountCheckForm);

        $.ajax({
            url: $(accountCheckForm).attr('action'),
            type: 'POST',
            data: accountCheckFormData,
            processData: false,
            contentType: false,
            success: function(response) {
                alertModal(response.message);
                if(response.status !== 'success') {
                    $('#inputAccount').val('');
                }
            },
            error: function(response) {
                console.log(response);
                if (response.status === 419) {
                    alertModal($('#msg_session_expried').data('label'), '/');
                    setTimeout(() => location.reload(), 2000);
                }
                else {
                    alertModal(errorNotice);
                    $('#inputAccount').val('');
                }
            }
        });
    });

    $('#verifyCode').click(function() {

        const email = $('#inputEmail').val();
        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        if (!emailRegex.test(email)) {
            alertModal($('#msg_email_invalid').data('label'));
            $('#inputEmail').val('');
        } else {

            $('#inputEmailCheck').val(email);

            const emailCheckForm = $('#emailCheckForm')[0];
            const emailCheckFormData = new FormData(emailCheckForm);

            $.ajax({
                url: $(emailCheckForm).attr('action'),
                type: 'POST',
                data: emailCheckFormData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alertModal(response.message);
                    if(response.status !== 'success') {
                        $(self).val('');
                    }
                },
                error: function(response) {
                    console.log(response);
                    alertModal(errorNotice);
                }
            });
        }
    });

    $('#inputName').focusout(function () {
        let self = this;
        const value = $(this).val().trim();
        const isValid = /^[가-힣a-zA-Z\s]+$/.test(value);

        if (!isValid) {
            $(self).val('');
        }
    });

    $('#inputPassword1').focusout(function() {
        let self = this;
        const password1 = $(self).val();

        const validate = validatePassword(password1);

        if(!validate) {
            alertModal(passwordGuide);
            $(self).val('');
        }
    });

    $('#inputPassword2').focusout(function() {
        let self = this;
        const password1 = $('#inputPassword1').val();
        const password2 = $(self).val();

        const validate = validatePassword(password2);

        if(!validate) {
            alertModal(passwordGuide);
            $(self).val('');
        }

        if(password1 !== password2) {
            alertModal($('#msg_password_missmatch').data('label'));
            $(self).val('');
        }
    });

    $('#inputReferrerId').focusout(function() {
        let self = this;
        const referrerId = $(self).val();

        $('#inputReferrerCheck').val(referrerId);

        const referrerCheckForm = $('#referrerCheckForm')[0];
        const referrerCheckFormData = new FormData(referrerCheckForm);

        $.ajax({
            url: $(referrerCheckForm).attr('action'),
            type: 'POST',
            data: referrerCheckFormData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status !== 'success') {
                    alertModal(response.message);
                    $(self).val('');
                }
            },
            error: function(response) {
                console.log(response);
                alertModal(errorNotice);
            }
        });
    });
});

function validatePassword(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d])[A-Za-z\d\S]{8,16}$/;
    return regex.test(password);
}
