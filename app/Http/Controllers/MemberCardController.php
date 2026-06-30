<?php

namespace App\Http\Controllers;

use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;

class MemberCardController extends Controller
{
    public function show(Request $request)
    {
        $member = $request->user();

        if ($member->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $qrPayload = $member->identity_number ?: $member->member_id ?: $member->email;

        return view('members.card', [
            'member' => $member,
            'qrPayload' => $qrPayload,
            'qrCode' => (new QRCode)->render($qrPayload),
        ]);
    }
}
