<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = User::query()
            ->whereIn('role', ['member', 'staff'])
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', [
            'members' => $members,
            'member' => null,
        ]);
    }

    public function create()
    {
        return view('members.index', [
            'members' => User::whereIn('role', ['member', 'staff'])->latest()->paginate(10),
            'member' => new User(['role' => 'member', 'status' => 'active']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = $data['password'] ?: 'password';
        $data['member_id'] = $data['member_id'] ?: 'LIB-'.now()->format('YmdHis');
        $data['registered_at'] = today();

        User::create($data);

        return redirect()->route('members.index')->with('status', 'Anggota berhasil ditambahkan.');
    }

    public function edit(User $member)
    {
        return view('members.index', [
            'members' => User::whereIn('role', ['member', 'staff'])->latest()->paginate(10),
            'member' => $member,
        ]);
    }

    public function update(Request $request, User $member)
    {
        $data = $this->validated($request, $member);
        if (! $data['password']) {
            unset($data['password']);
        }

        $member->update($data);

        return redirect()->route('members.index')->with('status', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $member)
    {
        $member->delete();

        return redirect()->route('members.index')->with('status', 'Anggota berhasil dihapus.');
    }

    private function validated(Request $request, ?User $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($member?->id ?? 'NULL')],
            'password' => [$member ? 'nullable' : 'nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:member,staff'],
            'member_id' => ['nullable', 'string', 'max:255', 'unique:users,member_id,'.($member?->id ?? 'NULL')],
            'identity_number' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'faculty' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'study_program' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:active,suspended,graduated,inactive'],
        ]);
    }
}
