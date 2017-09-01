$(function() {
    $('.get-json').bind('submit', function() {
        mid = $('.bgm-get #mid').val();
        type = $('.bgm-get #music-type').val();
        $.ajax({
            type: "GET",
            data: {  
                action: 'music_json_get',  
                form: 'admin-ajax.php',
                id: mid,
                type: type
            },
            beforeSend: function() {
                $('.json-data').html('正在解密歌曲信息请耐心等待 ...');
            },
            success: function(data) {
                $('.json-data').html(data);
                $('.sava-setting .mid').attr('value', mid);
                $('.sava-setting .type').attr('value', type);
            }
        });

        return false;
    });
})