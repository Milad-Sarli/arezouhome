<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = $request->user()->tickets()->with('messages')->latest()->paginate(10);

        return $tickets->through(fn ($t) => $this->row($t));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
            'attachment' => ['nullable', 'image', 'max:2048'],
        ]);

        $ticket = $request->user()->tickets()->create([
            'subject' => $data['subject'],
            'status' => 'open',
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'attachment' => $path,
        ]);

        return response()->json($this->row($ticket->fresh(['messages'])), 201);
    }

    public function show(Request $request, int $id)
    {
        $ticket = $request->user()->tickets()->with(['messages.user'])->findOrFail($id);

        return $this->row($ticket);
    }

    public function reply(Request $request, int $id)
    {
        $ticket = $request->user()->tickets()->findOrFail($id);

        if (in_array($ticket->status, ['closed'])) {
            return response()->json(['message' => 'تیکت بسته شده است.'], 422);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'attachment' => ['nullable', 'image', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'attachment' => $path,
        ]);

        if ($ticket->status === 'answered') {
            $ticket->update(['status' => 'open']);
        }

        return response()->json($this->row($ticket->fresh(['messages.user'])));
    }

    private function row(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'status_label' => match ($ticket->status) {
                'open' => 'باز',
                'answered' => 'پاسخ داده شده',
                'closed' => 'بسته',
                default => $ticket->status,
            },
            'messages' => $ticket->messages->map(fn ($m) => [
                'id' => $m->id,
                'user_name' => $m->user?->name ?? 'پشتیبانی',
                'message' => $m->message,
                'attachment' => $m->attachment ? asset('storage/'.$m->attachment) : null,
                'is_mine' => $m->user_id === $ticket->user_id,
                'created_at' => $m->created_at?->toIso8601String(),
            ])->values(),
            'created_at' => $ticket->created_at?->toIso8601String(),
        ];
    }
}
