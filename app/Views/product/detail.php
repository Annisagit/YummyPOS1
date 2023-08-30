<div class="w-full">
    <div class="flex items-center gap-10 justify-between mb-10">
        <h1 class="font-semibold text-3xl ">Detail Produk</h1>
        <a href="/produk/list_tabel" class="btn ">
            <img src="<?= base_url("./images/arrow_white.png") ?>" class="w-7" alt="back">
        </a>
    </div>
    <?php include("../app/Views/flash.php") ?>
    <div class="flex flex-col gap-4 border-2 rounded-2xl p-8 w-full">
        <div>
            <h3 class="badge badge-outline font-semibold mb-2">Gambar</h3>
            <img src="data:image/jpg;base64,<?= base64_encode($product->gambar_menu) ?>" class="h-56 w-52 object-cover rounded-xl" alt="product">
        </div>
        <div>
            <h3 class="badge badge-outline font-semibold mb-2">Nama Produk</h3>
            <h5 class=" text-xl  "><?= $product->nama_menu ?></h5>
        </div>
        <div>
            <h3 class="badge badge-outline font-semibold mb-2">Kategori</h3>
            <h5 class=" text-xl  "><?= $product->nama_kategori ?></h5>
        </div>
        <div>
            <h3 class="badge badge-outline font-semibold mb-2">Harga</h3>
            <h5 class=" text-xl  ">Rp. <?= $product->harga ?></h5>
        </div>
        <div>
            <h3 class="badge badge-outline font-semibold mb-2">Deskripsi</h3>
            <h5 class=" text-xl  "><?= $product->deskripsi ?></h5>
        </div>
        <div>

            <div class="overflow-x-auto w-fit">
                <form action="/produk/save_stok_menu" method="post">
                    <div class="flex gap-2 items-center mb-2">
                        <h3 class="badge badge-outline font-semibold">Stok</h3>
                        <button type="submit" id="save-stock-btn" class="btn btn-sm btn-primary hidden">simpan</button>
                        <button type="button" id="close-stock-btn" class="btn btn-sm btn-primary btn-square btn-outline hidden">X</button>
                    </div>
                    <input type="number" name="product-id" class="hidden" value="<?= $product->id_menu ?>">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr id="tr-head">
                                <th><button type="button" id="edit-stock-btn" class="btn btn-sm btn-accent">edit</button></th>
                                <th>Stok</th>
                                <th id="th-action" class="hidden">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-data">
                            <?php $i = 1 ?>
                            <?php foreach ($product_stock as $stock) : ?>
                                <tr id="<?= $stock->id_stok ?>-tr-data" class="tr-data">
                                    <td>Jumlah menu yang tersedia:</td>
                                    <td id="<?= $stock->id_stok ?>-stock-total"><?= $stock->stok ?> Pcs</td>
                                    <td class="td-action hidden gap-2">
                                        <button id="<?= $stock->id_stok ?>-add-stock" type="button" class="add-stock-btn btn btn-sm btn-square btn-info">+</button>
                                        <button id="<?= $stock->id_stok ?>-minus-stock" type="button" class="minus-stock-btn btn btn-sm btn-square btn-error">-</button>
                                        <button id="<?= $stock->id_stok ?>-delete-stock" type="button" class="delete-stock-btn btn btn-sm btn-outline btn-error">kosongi</button>
                                    </td>
                                    <input id="<?= $stock->id_stok ?>-stock" type="number" name="<?= $stock->id_stok ?>-product-stock" class="product-stock hidden" value="<?= $stock->stok ?>">
                                    <input type="number" name="<?= $i - 1 ?>-stock-id" class="hidden" value="<?= $stock->id_stok ?>">
                                </tr>
                                <?php $i++ ?>
                            <?php endforeach ?>
                            <?php if (count($product_stock) == 0) : ?>
                                <tr>
                                    <td colspan="4" class="text-center">- Stok Kosong -</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="text-right">
        <label for="edit-product-form-modal" class="btn btn-accent mt-5">edit detail</label>
    </div>
</div>