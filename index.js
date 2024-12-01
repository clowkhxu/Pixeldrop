$(function() {
Wstoast.config({autoClose:true,showClose:true,html:true})
$('#domain_name').on('input', function() {
    var domain=$(this).val().trim().replace(/(^\w+:|^)\/\//, '').replace(/\/.*$/, '');
    $(this).val(domain);
});
});
$('#find-domain').click(function(e) {
    e.preventDefault();
    $('.loader').show();
    var domain=$('#domain_name').val().trim();
    if (domain=='') {
        $('.loader').hide();
        Wstoast.error('Vui lòng nhập tên miền!');
        return;
    }
    $.ajax({
        url: 'https://api.thanhdieu.com/whois.php',
        method: 'GET',
        data: {domain:domain},
        dataType: 'json',
        success: function(data) {
            $('.loader').hide();
            if (data.whois) {
                $('.result').show();
                var xyz='<p><b>Thông Tin Tên Miền:</b></p>';
                xyz+='<div class="table-responsive">';
                xyz+='<table class="table table-bordered">';
                xyz+='<tr><th>Tên Miền:</th><td>'+data.whois.Domain_Name+'</td></tr>';
                xyz+='<tr><th>Nhà Đăng Ký:</th><td>'+data.whois.Registrar+'</td></tr>';
                xyz+='<tr><th>Ngày Đăng Ký</th><td>'+data.Registration_Time+'</td></tr>';
                xyz+='<tr><th>Ngày Hết Hạn:</th><td>'+data.Expiration_Time+'</td></tr>';
                xyz+='<tr><th>Trạng Thái Miền:</th><td>'+data.whois.Domain_Status+'</td></tr>';
                xyz+='<tr><th>Name Servers：</th><td>'+data.whois.Name_Server.join("<br/>")+'</td></tr>';
                xyz+='<tr><th>Cập Nhật Lần Cuối </th><td>'+data.last_update+'</td></tr>';
                xyz+='</table></div>';
                $('#result').html(xyz);
            } else if (data.status === 'error') {
                Wstoast.error(data.msg);
            }
        },
        error: function(xhr, status, error) {
            $('.loader').hide();
            Wstoast.error('Lỗi khi kết nối đến máy chủ!');
        }
    });
});
