$(document).ready(function() {

    $("input[name='coin_check']").change(function() {

        const coinId = $(this).val();

        $("input[name='coin']").val(coinId);

        const miningDataForm = $('#miningDataForm')[0];
        const miningDataFormData = new FormData(miningDataForm);

        $.ajax({
            url: $(miningDataForm).attr('action'),
            type: 'POST',
            data: miningDataFormData,
            processData: false,
            contentType: false,
            success: function(miningData) {

                $('#miningDataContainer').html('');

                $.each(miningData, function(index, item) {

                    const $template = $($('#miningDataTemplate').html());
                    const url = `/mining/confirm/${item.id}`;

                    $template.find('.mining-name').text(item.mining_locale_name);
                    $template.find('.mining-limit').text(item.node_limit);
                    $template.find('.mining-period').text(item.reward_limit);
                    $template.find('.mining-btn').attr('onclick', `location.href='${url}'`);

                    $('#miningDataContainer').append($template);
                });

                $('#miningData').removeClass('d-none');
            },
            error: function(response) {
                console.log(response);
                alertModal(errorNotice);
                $('#miningData').addClass('d-none');
            }
        });
    });

    $("#nodeAmount").on("input", function() {

        const inputValue = $(this).val().trim();
        const exchangeRate = parseFloat($("#exchangeRate").val());

        const isNumeric = /^(\d+(\.\d*)?)?$/.test(inputValue);

        if (!isNumeric || parseFloat(inputValue) < 1) {
            $(this).val('');
            $('#coinAmount').val('');
            $('#refundCoinAmount').val('');
            return;
        }

        const nodeAmount = parseFloat(inputValue);
        const coinAmount = 1000 * nodeAmount;
        const refundCoinAmount = coinAmount / exchangeRate;

        $('#coinAmount').val(coinAmount);
        $('#refundCoinAmount').val(refundCoinAmount);
    });
});
