<?php

namespace App\Http\Controllers\Settings;

use App\Dtos\Input\InputAtecoDto;
use App\Http\Controllers\Controller;
use App\Models\AtecoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AtecoController extends Controller
{
    /**
     * Store a newly created ATECO code for the authenticated user.
     */
    public function store(InputAtecoDto $inputAtecoDto): RedirectResponse
    {
        $data = $inputAtecoDto->toModelArray();
        $user = Auth::user();
        $sessionId = $user?->is_demo ? request()->cookie('demo_session_id') : null;

        if (($data['is_primary'] ?? false) === true) {
            AtecoCode::query()
                ->where('user_id', $user?->id)
                ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
                ->update(['is_primary' => false]);
        } else {
            $hasPrimary = AtecoCode::query()
                ->where('user_id', $user?->id)
                ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
                ->where('is_primary', true)
                ->exists();
            if (! $hasPrimary) {
                $data['is_primary'] = true;
            }
        }

        AtecoCode::create($data);

        return to_route('settings.fiscal.edit')->with('success', 'Codice ATECO creato con successo');
    }
}
