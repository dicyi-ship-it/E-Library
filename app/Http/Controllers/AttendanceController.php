<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.index', [
            'visits' => Visit::with('member')->latest('check_in_at')->paginate(15),
            'members' => User::whereIn('role', ['member', 'staff'])->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['nullable', 'exists:users,id'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:100'],
            'visitor_type' => ['nullable', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (! $data['member_id'] && ! $data['guest_name']) {
            return back()->withErrors(['guest_name' => 'Pilih anggota atau isi nama tamu.'])->withInput();
        }

        if (! empty($data['member_id'])) {
            $member = User::find($data['member_id']);
            $data['identity_number'] = $member?->identity_number ?: $member?->member_id;
            $data['visitor_type'] = $member?->level ?: $data['visitor_type'];
        }

        Visit::create($data + [
            'attendance_source' => 'admin',
            'check_in_at' => now(),
        ]);

        return redirect()->route('attendance.index')->with('status', 'Kehadiran berhasil dicatat.');
    }

    public function publicStore(Request $request)
    {
        $redirectRoute = $request->boolean('kiosk_mode') ? 'attendance.kiosk' : 'attendance.public';

        $data = $request->validate([
            'identity_number' => ['required', 'string', 'max:100'],
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'visitor_type' => ['nullable', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'attendance_source' => ['nullable', 'in:manual,qr'],
        ]);

        $identityNumber = strtoupper(trim($data['identity_number']));

        $member = User::query()
            ->where('status', 'active')
            ->where(function ($query) use ($identityNumber) {
                $query->where('member_id', $identityNumber)
                    ->orWhere('identity_number', $identityNumber);
            })
            ->first();

        $activeVisit = Visit::query()
            ->whereDate('check_in_at', today())
            ->whereNull('check_out_at')
            ->where(function ($query) use ($member, $identityNumber) {
                if ($member) {
                    $query->where('member_id', $member->id);
                }

                $query->orWhere('identity_number', $identityNumber);
            })
            ->first();

        if ($activeVisit) {
            return redirect()
                ->route($redirectRoute)
                ->with('status', 'Anda sudah tercatat hadir pada '.$activeVisit->check_in_at->format('H:i').'.');
        }

        Visit::create([
            'member_id' => $member?->id,
            'guest_name' => $member?->name ?: ($data['visitor_name'] ?: 'Pengunjung '.$identityNumber),
            'identity_number' => $member?->identity_number ?: $member?->member_id ?: $identityNumber,
            'visitor_type' => $member?->level ?: ($data['visitor_type'] ?: 'Pengunjung'),
            'attendance_source' => $data['attendance_source'] ?? 'manual',
            'purpose' => $data['purpose'],
            'notes' => $data['notes'] ?? null,
            'check_in_at' => now(),
        ]);

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Daftar hadir berhasil dicatat. Selamat datang di '.AppSetting::getValue('library_name').' '.AppSetting::getValue('institution_name').'.');
    }

    public function kioskRegister(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identity_number' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'study_program' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $identityNumber = strtoupper(trim($data['identity_number']));
        $existingMember = User::query()
            ->where('identity_number', $identityNumber)
            ->orWhere('member_id', $identityNumber)
            ->first();

        if ($existingMember) {
            return $this->checkInRegisteredMember($existingMember, $data['purpose'], $data['notes'] ?? null, 'Nomor induk sudah terdaftar. Kehadiran Anda dicatat.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?: $this->kioskEmail($identityNumber),
            'password' => Str::random(24),
            'role' => 'member',
            'member_id' => 'LIB-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
            'identity_number' => $identityNumber,
            'phone' => $data['phone'] ?? null,
            'faculty' => $data['faculty'] ?: AppSetting::getValue('institution_name'),
            'department' => $data['department'] ?? null,
            'study_program' => $data['study_program'] ?: 'Umum',
            'level' => $data['level'],
            'status' => 'active',
            'registered_at' => today(),
        ]);

        return $this->checkInRegisteredMember($user, $data['purpose'], $data['notes'] ?? null, 'Registrasi berhasil dan kehadiran sudah dicatat.');
    }

    private function checkInRegisteredMember(User $member, string $purpose, ?string $notes, string $message)
    {
        $activeVisit = Visit::query()
            ->where('member_id', $member->id)
            ->whereDate('check_in_at', today())
            ->whereNull('check_out_at')
            ->first();

        if ($activeVisit) {
            return redirect()
                ->route('attendance.kiosk')
                ->with('status', 'Anda sudah tercatat hadir pada '.$activeVisit->check_in_at->format('H:i').'.');
        }

        Visit::create([
            'member_id' => $member->id,
            'guest_name' => $member->name,
            'identity_number' => $member->identity_number ?: $member->member_id,
            'visitor_type' => $member->level,
            'attendance_source' => 'registration',
            'purpose' => $purpose,
            'notes' => $notes,
            'check_in_at' => now(),
        ]);

        return redirect()
            ->route('attendance.kiosk')
            ->with('status', $message);
    }

    private function kioskEmail(string $identityNumber): string
    {
        $base = Str::lower(preg_replace('/[^A-Za-z0-9]+/', '', $identityNumber)) ?: 'pengunjung';
        $email = $base.'@kiosk.itech.local';

        if (! User::where('email', $email)->exists()) {
            return $email;
        }

        return $base.'.'.now()->format('YmdHis').'@kiosk.itech.local';
    }

    public function checkout(Visit $visit)
    {
        $visit->update(['check_out_at' => now()]);

        return redirect()->route('attendance.index')->with('status', 'Jam keluar berhasil dicatat.');
    }
}
