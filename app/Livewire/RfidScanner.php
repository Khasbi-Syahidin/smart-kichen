<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AttendanceConsumer;
use App\Models\AttendanceSession;
use App\Models\Consumer;

class RfidScanner extends Component
{
    public $attendanceSessionId;
    public $attendanceSession;
    public $rfid = '';
    public $consumer;

    public function mount($record)
    {
        $this->attendanceSessionId = $record;
        $this->attendanceSession = AttendanceSession::findOrFail($this->attendanceSessionId);
    }

    public function updatedRfid($value)
    {
        if (strlen($value) === 10) {
            $this->submitRfid();
        }
    }

    public function submitRfid()
    {
        $this->consumer = null;

        if (strlen($this->rfid) !== 10) {
            return;
        }

        $consumer = Consumer::where('rfid', $this->rfid)->first();

        if (!$consumer) {
            $this->dispatchBrowserEvent('rfidError', ['message' => 'RFID tidak ditemukan']);
            $this->reset('rfid');
            return;
        }

        $alreadyExists = AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)
            ->where('consumer_id', $consumer->id)
            ->exists();

        if (!$alreadyExists) {
            AttendanceConsumer::create([
                'attendance_session_id' => $this->attendanceSessionId,
                'consumer_id' => $consumer->id,
            ]);
        }

        $this->consumer = $consumer;
        $this->dispatchBrowserEvent('rfidSuccess');
        $this->reset('rfid');
    }


    public function render()
    {
        return view(
            'livewire.rfid-scanner',
            [
                'consumed' => AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)->count(),
                'total' => Consumer::count(),
                'session' => $this->attendanceSession,
            ]
        );
    }
}
