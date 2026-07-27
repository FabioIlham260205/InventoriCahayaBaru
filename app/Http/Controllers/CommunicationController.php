<?php

namespace App\Http\Controllers;

use App\Models\InventoryAlert;
use App\Models\TeamMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationController extends Controller
{
    public function index(): View
    {
        $messages = TeamMessage::query()->latest()->limit(30)->get();
        $alerts = InventoryAlert::query()->with('fruit')->latest()->limit(15)->get();

        return view('communication.index', compact('messages', 'alerts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sender_name' => ['required', 'string', 'max:120'],
            'channel' => ['required', 'in:gudang,pembelian,penjualan,manajemen'],
            'subject' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        TeamMessage::create($data);

        return back()->with('status', 'Pesan tim berhasil dikirim.');
    }
}
