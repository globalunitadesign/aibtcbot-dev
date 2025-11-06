$(document).ready(function() {
    $('#exchangeBtn').click(function(e) {
        e.preventDefault();

        confirmModal('환율을 변경하시겠습니까?').then((isConfirmed) => {
            if (isConfirmed) {

                const formData = new FormData($('#ajaxForm')[0]);
                formData.append('mode', 'exchange');

                $.ajax({
                    url: $('#ajaxForm').attr('action'),
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

    $('#checkBtn').click(function(e) {

        e.preventDefault();

        const nodeAmount = $('input[name="node_amount"]').val();

        $('input[name="check_node_amount"]').val(nodeAmount);

        const miningCheckForm = $('#miningCheckForm')[0];
        const miningCheckFormData = new FormData(miningCheckForm);

        $.ajax({
            url: $(miningCheckForm).attr('action'),
            type: 'POST',
            data: miningCheckFormData,
            processData: false,
            contentType: false,
            success: function(checkData) {
                console.log(checkData);

                $('#totalNodeAmount').html(checkData.total_node_amount);
                $('#totalMiningAmount').html(checkData.total_mining_amount);
                $('#totalLevelBonus').html(checkData.total_level_bonus);
                $('#totalLevelMatching').html(checkData.total_level_matching);

            },
            error: function(response) {
                console.log(response);
                alertModal(errorNotice);
            }
        });
    });

    $('#nodeBtn').click(function(e) {
        e.preventDefault();

        confirmModal('채굴량을 변경하시겠습니까?').then((isConfirmed) => {
            if (isConfirmed) {

                const formData = new FormData($('#ajaxForm')[0]);
                formData.append('mode', 'node');

                $.ajax({
                    url: $('#ajaxForm').attr('action'),
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

    $('#marketingSelect').change(function(e) {
        e.preventDefault();

        const beforeId = $('#beforeMarketingId').val();
        const selectedId = $(this).val();

        $.ajax({
            url: '/admin/mining/policy/marketing-benefit-rules/' + selectedId + '/get',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#benefitRules').html(response);
            },
            error: function () {
                alertModal('예기치 못한 오류가 발생했습니다.');
            }
        });
    });
});
