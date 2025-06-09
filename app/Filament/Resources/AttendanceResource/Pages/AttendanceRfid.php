<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\Page;
use App\Models\AttendanceConsumer;
use App\Models\AttendanceSession;
use App\Models\Consumer;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class AttendanceRfid extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.attendance-rfid';

    public $attendanceSessionId;
    public $attendanceSession;
    public $rfid = '';

    public $avatar;
    public $consumer;

    public function mount(): void
    {
        $this->attendanceSessionId = request()->query('record');
        $this->attendanceSession = AttendanceSession::findOrFail($this->attendanceSessionId);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Akhiri Absensi')
                ->url(route('filament.admin.resources.attendances.index')),
        ];
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
            Notification::make()
                ->title('Consumer tidak ditemukan')
                ->danger()
                ->send();
            // $this->dispatch('rfidError', 'RFID tidak ditemukan'); // <-- dispatch di sini, benar
            $this->reset('rfid');
            return;
        }

        $alreadyExists = AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)
            ->where('consumer_id', $consumer->id)
            ->exists();

        if ($alreadyExists) {
            Notification::make()
                ->title("{$consumer->name} sudah absen sebelumnya")
                ->info()
                ->send();
            $this->reset('rfid');
            return;
        } else {

            if($consumer->is_active == false) {
                Notification::make()
                    ->title("{$consumer->name} berstatus tidak aktif")
                    ->danger()
                    ->send();
                $this->reset('rfid');
                return;
            }
            if($consumer->created_at > $this->attendanceSession->date) {
                Notification::make()
                    ->title("{$consumer->name} belum bergabung di sesi ini")
                    ->danger()
                    ->send();
                $this->reset('rfid');
                return;
            }
            AttendanceConsumer::create([
                'attendance_session_id' => $this->attendanceSessionId,
                'consumer_id' => $consumer->id,
            ]);
        }

        $this->consumer = $consumer;
        Notification::make()
            ->title('Consumer berhasil absen')
            ->success()
            ->send();
        $this->reset('rfid');
    }

    public function getViewData(): array
    {
        return [
            'consumed' => AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)->count(),
            'total' => Consumer::count(),
            'consumer' => $this->consumer,
            'session' => $this->attendanceSession,
        ];
    }
}
