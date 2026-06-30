@extends('layouts.app')

@section('title', 'Keanggotaan')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Keanggotaan Universitas</h1>
            <p class="page-subtitle">Data mahasiswa, dosen, tenaga kependidikan, dan unit kampus.</p>
        </div>
        <a class="btn-primary" href="{{ route('members.create') }}">Tambah Anggota</a>
    </div>

    @if($member)
        <form class="panel mb-8 grid gap-4" method="POST" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}">
            @csrf
            @if($member->exists) @method('PUT') @endif
            <h2 class="section-title">{{ $member->exists ? 'Edit Anggota' : 'Anggota Baru' }}</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <input class="input" name="name" placeholder="Nama lengkap" value="{{ old('name', $member->name) }}" required>
                <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email', $member->email) }}" required>
                <input class="input" name="member_id" placeholder="ID anggota" value="{{ old('member_id', $member->member_id) }}">
                <input class="input" name="identity_number" placeholder="NIM/NIDN/NIP" value="{{ old('identity_number', $member->identity_number) }}" required>
                <input class="input" name="phone" placeholder="No. telepon" value="{{ old('phone', $member->phone) }}">
                <select class="input" name="role" required>
                    <option value="member" @selected(old('role', $member->role) === 'member')>Member</option>
                    <option value="staff" @selected(old('role', $member->role) === 'staff')>Petugas</option>
                </select>
                <input class="input" name="faculty" placeholder="Fakultas" value="{{ old('faculty', $member->faculty) }}" required>
                <input class="input" name="department" placeholder="Jurusan" value="{{ old('department', $member->department) }}">
                <input class="input" name="study_program" placeholder="Program studi/unit" value="{{ old('study_program', $member->study_program) }}" required>
                <select class="input" name="level" required>
                    @foreach(['Mahasiswa', 'Dosen', 'Tenaga Kependidikan', 'Peneliti'] as $level)
                        <option value="{{ $level }}" @selected(old('level', $member->level) === $level)>{{ $level }}</option>
                    @endforeach
                </select>
                <select class="input" name="status" required>
                    @foreach(['active' => 'Aktif', 'suspended' => 'Ditangguhkan', 'graduated' => 'Lulus', 'inactive' => 'Nonaktif'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $member->status ?: 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div></div>
                <input class="input" type="password" name="password" placeholder="Password baru">
                <input class="input" type="password" name="password_confirmation" placeholder="Konfirmasi password">
            </div>
            <div class="flex gap-3">
                <button class="btn-primary" type="submit">Simpan</button>
                <a class="btn-secondary" href="{{ route('members.index') }}">Batal</a>
            </div>
        </form>
    @endif

    <section class="panel">
        <form class="mb-4 flex gap-3" method="GET">
            <input class="input" name="search" placeholder="Cari nama, email, ID anggota, NIM" value="{{ request('search') }}">
            <button class="btn-secondary" type="submit">Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Identitas</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $item)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-sm text-zinc-500">{{ $item->email }}</p>
                            </td>
                            <td>{{ $item->member_id }}<br><span class="text-sm text-zinc-500">{{ $item->identity_number }} · {{ $item->level }}</span></td>
                            <td>{{ $item->faculty }}<br><span class="text-sm text-zinc-500">{{ $item->study_program }}</span></td>
                            <td><span class="badge">{{ $item->status }}</span></td>
                            <td class="whitespace-nowrap">
                                <a class="btn-mini" href="{{ route('members.edit', $item) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('members.destroy', $item) }}" onsubmit="return confirm('Hapus anggota ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-mini-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-zinc-500">Belum ada anggota.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $members->links() }}</div>
    </section>
@endsection
