<input type="checkbox" id="new-cashier-form-modal" class="modal-toggle" />
<div class="modal">
    <form action="/kasir/save_kasir" method="post" class="modal-box max-w-xl" enctype="multipart/form-data">
        <h3 class="font-bold text-xl">Form Registrasi Pegawai Kasir</h3>
        <p class="py-4">You've been selected for a chance to get one year of subscription to use Wikipedia for free!</p>
        <div class="flex flex-col gap-3">
            <div class="flex gap-3">
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Nama Lengkap</span>
                    </label>
                    <input type="text" name="employee-full-name" placeholder="Ketikkan nama lengkap pegawai" class="input w-full input-bordered" required />
                </div>
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">NIK</span>
                    </label>
                    <input type="number" name="employee-nik" maxlength="16" placeholder="XXXXXXXXXXXXXXXX" class="input w-full input-bordered" required />
                </div>
            </div>
            <div class="flex gap-3">
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Nomor Telepon</span>
                    </label>
                    <input type="number" name="employee-phone-number" placeholder="08XXXXXXXXXX" class="input w-full input-bordered" required />
                </div>
            </div>
            <div class="flex gap-3">
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Alamat</span>
                    </label>
                    <textarea class="textarea textarea-bordered h-24 w-full" name="employee-address" placeholder="Ketikkan alamat tempat tinggal" required></textarea>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Upload Foto Profil</span>
                    </label>
                    <input type="file" name="employee-photo" class="file-input file-input-bordered w-full" required />
                </div>
            </div>
            <h4 class="font-semibold text-lg mt-6">Data / Kredential Akun</h4>
            <div class="flex gap-3">
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Username</span>
                    </label>
                    <input type="text" name="employee-username" placeholder="Ketikkan username akun" class="input w-full input-bordered" required />
                </div>
                <div class="form-control flex-grow">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" name="employee-email" placeholder="emailmu@example.com" class="input w-full input-bordered" required />
                </div>
            </div>
            <div class="form-control flex-grow">
                <label class="label">
                    <span class="label-text">Password</span>
                </label>
                <input type="password" name="employee-password" placeholder="Ketikkan password akun" class="input w-full input-bordered" required />
            </div>
            <div class="modal-action">
                <button type="submit" class="btn btn-sm btn-primary">tambah</button>
                <label for="new-cashier-form-modal" class="btn btn-sm btn-primary btn-outline">tutup</label>
            </div>
        </div>
    </form>
</div>