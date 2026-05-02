@extends('layouts.admin')

@section('title', 'Kelola Karyawan')

@section('content')

{{-- Tombol Tambah --}}
<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500 text-sm">Total: <span class="font-bold text-gray-800">{{ $karyawan->count() }}</span> karyawan</p>
    <button onclick="tambahKaryawan()"
    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition shadow">
    <i class="fa-solid fa-plus mr-2"></i> Tambah Karyawan
</button>
</div>


{{-- Tabel --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-left">Email</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($karyawan as $index => $k)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">
                    <i class="fa-solid fa-user text-blue-400 mr-2"></i>{{ $k->name }}
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $k->email }}</td>
                <td class="px-6 py-4 text-center">
                    <button onclick="editKaryawan({{ $k->id }}, '{{ $k->name }}', '{{ $k->email }}')"
                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold mr-1 transition">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <button onclick="hapusKaryawan({{ $k->id }})"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                    <i class="fa-solid fa-users-slash text-3xl mb-2 block"></i>
                    Belum ada karyawan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<form id="formTambahHidden" method="POST" action="{{ route('admin.karyawan.store') }}" class="hidden">
    @csrf
    <input type="text" name="name" id="tambahName">
    <input type="email" name="email" id="tambahEmail">
    <input type="password" name="password" id="tambahPassword">
</form>
{{-- Form Edit & Hapus (hidden) --}}
<form id="formEdit" method="POST" class="hidden">
    @csrf
    @method('PUT')
    <input type="text" name="name" id="editName">
    <input type="email" name="email" id="editEmail">
    <input type="password" name="password" id="editPassword">
</form>

<form id="formHapus" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')

<script>
    function tambahKaryawan() {
    Swal.fire({
        title: 'Tambah Karyawan',
        html: `
            <div class="text-left mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                <input id="swalTambahName" class="swal2-input" style="margin:0;width:100%" placeholder="Nama lengkap">
            </div>
            <div class="text-left mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input id="swalTambahEmail" type="email" class="swal2-input" style="margin:0;width:100%" placeholder="email@contoh.com">
            </div>
            <div class="text-left">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input id="swalTambahPassword" type="password" class="swal2-input" style="margin:0;width:100%" placeholder="Minimal 6 karakter">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const name = document.getElementById('swalTambahName').value;
            const email = document.getElementById('swalTambahEmail').value;
            const password = document.getElementById('swalTambahPassword').value;

            if (!name || !email || !password) {
                Swal.showValidationMessage('Semua field wajib diisi!');
                return false;
            }
            if (password.length < 6) {
                Swal.showValidationMessage('Password minimal 6 karakter!');
                return false;
            }

            document.getElementById('tambahName').value = name;
            document.getElementById('tambahEmail').value = email;
            document.getElementById('tambahPassword').value = password;
            document.getElementById('formTambahHidden').submit();
        }
    });
}
function editKaryawan(id, name, email) {
    Swal.fire({
        title: 'Edit Karyawan',
        html: `
            <div class="text-left mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                <input id="swalName" class="swal2-input" value="${name}" style="margin:0;width:100%">
            </div>
            <div class="text-left mb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input id="swalEmail" type="email" class="swal2-input" value="${email}" style="margin:0;width:100%">
            </div>
            <div class="text-left">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                <input id="swalPassword" type="password" class="swal2-input" style="margin:0;width:100%">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            document.getElementById('editName').value = document.getElementById('swalName').value;
            document.getElementById('editEmail').value = document.getElementById('swalEmail').value;
            document.getElementById('editPassword').value = document.getElementById('swalPassword').value;
            const form = document.getElementById('formEdit');
            form.action = `/admin/karyawan/${id}`;
            form.submit();
        }
    });
}

function hapusKaryawan(id) {
    Swal.fire({
        title: 'Hapus Karyawan?',
        text: 'Data yang dihapus tidak bisa dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.getElementById('formHapus');
            form.action = `/admin/karyawan/${id}`;
            form.submit();
        }
    });
}
</script>
@endpush