<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PDF;

class PoliceController extends Controller
{
    public function showDashboard()
    {

        $viewData = [
            'meta_title' => 'Dashboard | CAMS',
            'meta_desc' => 'CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.',
            'meta_image' => url('logo.png'),
            'stats' => $stats,
        ];

        return view('admin.dashboard', $viewData);
    }
}
