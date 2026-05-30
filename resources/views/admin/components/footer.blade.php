<!-- Scroll To Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

@push('scripts')
<script>
$(document).ready(function () {

    /**
     * Format Rupiah
     */
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    /**
     * Init Select2
     */
    function initSelect2(element = null) {

        let config = {
            placeholder: "-- Pilih --",
            allowClear: true,
            width: "100%"
        };

        if (element) {
            $(element).select2(config);
        } else {

            $('#pelanggan_lama').select2(config);

            $('select[name="id_sales"]').select2(config);

            $('.pilihProduk').select2(config);
        }
    }

    // Init awal
    initSelect2();

    /**
     * Auto tanggal hari ini
     */
    const tanggal = $('input[name="tanggal_order"]');

    if (tanggal.length && !tanggal.val()) {
        tanggal.val(new Date().toISOString().split('T')[0]);
    }

    /**
     * Toggle pelanggan baru/lama
     */
    function togglePelanggan() {

        if ($('#pelanggan_lama').length) {

            let val = $('#pelanggan_lama').val();

            if (val && val !== "") {

                $('#pelangganBaruForm :input')
                    .prop('disabled', true);

            } else {

                $('#pelangganBaruForm :input')
                    .prop('disabled', false);
            }
        }
    }

    $('#pelanggan_lama').on('change', togglePelanggan);

    togglePelanggan();

    /**
     * Hitung Total
     */
    function hitungTotal() {

        let grand = 0;

        $('#tabelProduk tbody tr').each(function () {

            const select = $(this)
                .find('.pilihProduk option:selected');

            const row = $(this).closest('tr');

            const harga = parseInt(select.data('harga')) || 0;

            let qty = parseInt(
                row.find('.qty').val()
            ) || 0;

            const subtotal = qty * harga;

            row.find('.harga')
                .val(formatRupiah(harga));

            row.find('.subtotal')
                .val(formatRupiah(subtotal));

            grand += subtotal;
        });

        $('#grandTotal')
            .text('Rp ' + formatRupiah(grand));
    }

    /**
     * Cek duplikat produk
     */
    function isDuplicate(val) {

        let count = 0;

        $('.pilihProduk').each(function () {

            if ($(this).val() === val) count++;
        });

        return count > 1;
    }

    /**
     * Saat pilih produk
     */
    $(document).on('change', '.pilihProduk', function () {

        let val = $(this).val();

        let row = $(this).closest('tr');

        if (!val) {

            row.find('.qty').val(1);

            row.find('.harga').val('');

            row.find('.subtotal').val('');

            row.find('.stok').val('');

            hitungTotal();

            return;
        }

        if (isDuplicate(val)) {

            alert("Produk ini sudah dipilih di baris lain!");

            $(this).val(null).trigger('change');

            return;
        }

        const option = $('option:selected', this);

        const stok = option.data('stok');

        const harga = option.data('harga');

        row.find('.stok').val(stok);

        row.find('.harga')
            .val(formatRupiah(harga));

        row.find('.qty')
            .val(1)
            .attr('max', stok);

        hitungTotal();
    });

    /**
     * Saat ubah qty
     */
    $(document).on('input', '.qty', function () {

        let val = parseInt($(this).val());

        let max = parseInt($(this).attr('max'));

        if (max > 0 && val > max) {

            alert(`Stok tidak cukup! \nSisa Stok: ${max}`);

            $(this).val(max);
        }

        if (val < 1) {
            $(this).val(1);
        }

        hitungTotal();
    });

    /**
     * Renumber row
     */
    function renumberRow() {

        $('#tabelProduk tbody tr').each(function (i) {

            $(this).find('td:first').text(i + 1);
        });
    }

    /**
     * Tambah row
     */
    $('#tambahRow').on('click', function () {

        let lastRow = $('#tabelProduk tbody tr:first');

        let newRow = lastRow.clone();

        newRow.find('.select2-container').remove();

        newRow.find('select')
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id tabindex aria-hidden')
            .find('option')
            .removeAttr('data-select2-id');

        newRow.find('input').val('');

        newRow.find('.stok').val('');

        newRow.find('.qty')
            .val(1)
            .removeAttr('max');

        newRow.find('select').val('');

        $('#tabelProduk tbody').append(newRow);

        initSelect2(
            newRow.find('.pilihProduk')
        );

        renumberRow();
    });

    /**
     * Hapus row
     */
    $(document).on('click', '.hapusRow', function () {

        if ($('#tabelProduk tbody tr').length > 1) {

            $(this).closest('tr').remove();

            renumberRow();

            hitungTotal();

        } else {

            alert("Minimal harus ada 1 produk!");
        }
    });

    /**
     * Live Search
     */
    $("#liveSearch").on("keyup", function() {

        var value = $(this).val().toLowerCase();

        var targetCols = $(this).data('columns');

        var colIndices = targetCols
            ? targetCols.toString().split(',')
            : null;

        $("#dataTable tbody tr").filter(function() {

            var rowText = "";

            if (colIndices) {

                var $row = $(this);

                colIndices.forEach(function(index) {

                    rowText += " " + $row.find('td')
                        .eq(index)
                        .text();
                });

            } else {

                rowText = $(this).text();
            }

            $(this).toggle(
                rowText.toLowerCase().indexOf(value) > -1
            );
        });
    });

    // Hitung awal
    hitungTotal();

});
</script>
@endpush