<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <style>
        body {
            background: #20c997;
        }
        main{
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="id" id="id">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control" name="nama" required id="nama"/>
                                </div>

                                <button type="submit" class="btn btn-primary btn-untuk-simpan">Simpan</button>
                                <button type="submit" class="btn btn-primary btn-untuk-update" style="display:none">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- <strong id="new">0</strong> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.0.5/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    var socket = io('http://localhost:2500', {
        secure: true,
        transports: ['websocket', 'polling']
    });

    socket.emit('newuser');

    socket.on('new', (data) => {
        $('#new').html(data)
    })

    $('form').on('submit', function(e) {

        e.preventDefault();

        var form = $(this);

        if($('#id').val() !== '') {
            $.ajax({
                url: 'proses.php?act=update',
                type: 'post',
                data: form.serializeArray(),
                dataType: 'json',
                success:function(res) {
                    if(res.status === "sukses") {
                        socket.emit('data_masuk', res.pesan);
                        $('.btn-untuk-update').hide()
                        $('.btn-untuk-simpan').show()
                    } else {
                        alert('Gagal');
                    }
                }
            })
        } else {
            $.ajax({
                url: 'proses.php?act=simpan',
                type: 'post',
                data: form.serializeArray(),
                dataType: 'json',
                success:function(res) {
                    if(res.status === "sukses") {
                        socket.emit('data_masuk', res.pesan);
                        $('.btn-untuk-update').hide()
                        $('.btn-untuk-simpan').show()
                    } else {
                        alert('Gagal');
                    }
                }
            })
        }

        $('form')[0].reset()

    })

    untukOpsi = () => {

        $('.btn-ubah').on('click', function(e) {
            e.preventDefault()
            var klik = $(this);
            $.post('proses.php?act=ubah', {
                kode: klik.data('kode')
            }, function(data) {
                var json = JSON.parse(data);
                $("#id").val(json.data.id);
                $("#nama").val(json.data.nama);
                $('.btn-untuk-update').show()
                $('.btn-untuk-simpan').hide()
            })

        })

        $('.btn-hapus').on('click', function(e) {
            e.preventDefault()
            var klik = $(this);
            var konfirmasi = confirm("Anda yakin akan menghapus data ini?");
            if(konfirmasi === true) {
                $.post('proses.php?act=hapus', {
                    kode: klik.data('kode')
                }, function(data) {
                    var json = JSON.parse(data);
                    if(json.status === "sukses") {
                        socket.emit('data_masuk', json.pesan);
                    } else {
                        alert('Gagal');
                    }
                })
            } else {

            }

        })

    }


    load_data = () => {

        $.post('proses.php?act=list', function(data) {
            var json = JSON.parse(data);
            if(json.data.length === 0) {
                $('#data').html('Tidak ada data');
            } else {
                var tampilan = '';
                var no = 0;
                $.each(json.data, function(index, val) {
                    ++no;
                    tampilan += '<tr>';
                    tampilan += '<td>'+(no)+'</td>';
                    tampilan += '<td>'+val.nama+'</td>';
                    tampilan += '<td><a class="btn btn-sm btn-primary btn-ubah" data-kode="'+val.id+'">Ubah</a><a class="btn btn-sm btn-danger btn-hapus" data-kode="'+val.id+'">Hapus</a></td>';
                    tampilan += '</tr>';
                })

                $('table tbody').html(tampilan)

                untukOpsi();
            }
        })

    }

    load_data();

    socket.on('data_masuk', function(data) {
        load_data();
        toastr.success(data, 'Informasi');
    })

</script>

</body>
</html>