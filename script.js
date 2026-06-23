async function tampilkanData() {
    var response = await fetch('api_pesanan.php');
    var hasil = await response.json();
    var tbody = document.getElementById('tabelPesanan');
    tbody.innerHTML = '';

    for (var i = 0; i < hasil.length; i++) {
        var item = hasil[i];
        var kelasBadge = 'badge-proses';
        
        if (item.status == 'Sampai') {
            kelasBadge = 'badge-sampai';
        } else if (item.status == 'Diterima') {
            kelasBadge = 'badge-diterima';
        }

        var fotoTag = '<span class="no-photo">Belum ada bukti</span>';
        if (item.bukti && item.bukti != '-') {
            fotoTag = '<div class="bukti-box"><img src="uploads/' + item.bukti + '" class="img-profile"></div>';
        }

        tbody.innerHTML += '<tr>' +
            '<td style="text-align: left; padding-left: 20px;"><b>' + item.nama_barang + '</b></td>' +
            '<td><span class="badge ' + kelasBadge + '">' + item.status + '</span></td>' +
            '<td>' + fotoTag + '</td>' +
            '<td class="action-links">' +
                '<button class="link-proses" onclick="gantiStatus(' + item.id + ', \'Diproses\')">Diproses</button> <span>|</span> ' +
                '<button class="link-diterima" onclick="gantiStatus(' + item.id + ', \'Diterima\')">Diterima</button> <span>|</span> ' +
                '<button class="link-sampai" onclick="gantiStatus(' + item.id + ', \'Sampai\')">Sampai</button> <span>|</span> ' +
                '<button class="link-hapus" onclick="hapusBuktiFoto(' + item.id + ')">Hapus Bukti</button>' +
            '</td>' +
        '</tr>';
    }
}

document.getElementById('formPesanan').addEventListener('submit', async function(e) {
    e.preventDefault();
    var form = new FormData();
    form.append('nama_barang', document.getElementById('inputBarang').value);
    form.append('status', document.getElementById('inputStatus').value);
    
    var fileInput = document.getElementById('inputFoto').files[0];
    if (fileInput) {
        form.append('bukti_foto', fileInput);
    }

    var response = await fetch('api_pesanan.php', {
        method: 'POST',
        body: form
    });
    
    var hasil = await response.json();
    alert(hasil.message);
    document.getElementById('formPesanan').reset();
    tampilkanData();
});

// FITUR EDIT DATA STATUS (POST VIA API)
async function gantiStatus(idData, statusBaru) {
    await fetch('api_pesanan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'ubah_status_pesanan=1&id=' + idData + '&status=' + statusBaru
    });
    tampilkanData();
}

// FITUR HAPUS BUKTI FOTO JASTIP (POST VIA API)
async function hapusBuktiFoto(idData) {
    if (confirm('Apakah anda yakin ingin menghapus foto bukti ini?')) {
        var response = await fetch('api_pesanan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'hapus_bukti_foto=1&id=' + idData
        });
        var hasil = await response.json();
        alert(hasil.message);
        tampilkanData();
    }
}

tampilkanData();